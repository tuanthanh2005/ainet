<?php

class RecentOrder {
    /**
     * Blend real customer orders and simulated catalog orders.
     * Fresh real orders (< 15 mins) are prioritized first with true elapsed time,
     * while the rest have realistic randomized times (>= 5 mins) and masked names.
     */
    public static function getAll(): array {
        try {
            return Cache::remember('orders.recent_popup', 30, function (): array {
                $db = Database::getInstance();
                
                // 1. Fetch real orders from database (with customer/user details if available)
                $realOrders = [];
                try {
                    $stmt = $db->query(
                        "SELECT o.id, o.product_id, o.product_name, o.amount, o.customer_email, o.created_at,
                                u.name AS user_name
                         FROM orders o
                         LEFT JOIN users u ON u.email = o.customer_email
                         ORDER BY o.created_at DESC
                         LIMIT 15"
                    );
                    $rows = $stmt->fetchAll();
                    if (!empty($rows)) {
                        foreach ($rows as $row) {
                            $formatted = self::formatRealOrder($row);
                            if (!empty($formatted)) {
                                $realOrders[] = $formatted;
                            }
                        }
                    }
                } catch (Throwable $e) {
                    $realOrders = [];
                }

                // 2. Fetch realistic simulated orders from active catalog
                $simulatedOrders = self::getRealisticSimulatedOrders($db, 8);

                // 3. Separate fresh real orders (purchased < 15 mins ago) to show earliest
                $freshReal = [];
                $olderReal = [];
                foreach ($realOrders as $ro) {
                    if (!empty($ro['is_fresh'])) {
                        $freshReal[] = $ro;
                    } else {
                        $olderReal[] = $ro;
                    }
                }

                // 4. Interleave remaining real orders with simulated orders
                $mixed = [];
                $maxCount = max(count($olderReal), count($simulatedOrders));
                for ($i = 0; $i < $maxCount; $i++) {
                    if (isset($olderReal[$i])) {
                        $mixed[] = $olderReal[$i];
                    }
                    if (isset($simulatedOrders[$i])) {
                        $mixed[] = $simulatedOrders[$i];
                    }
                }

                // 5. Combine: Fresh real orders come FIRST with earliest true time, followed by mixed pool
                $final = array_merge($freshReal, $mixed);

                return !empty($final) ? $final : self::getDefaultPool();
            });
        } catch (Throwable $e) {
            return self::getDefaultPool();
        }
    }

    public static function create($data) {
        return true;
    }

    private static function formatRealOrder(array $order): array {
        $email = (string) ($order['customer_email'] ?? '');
        $rawName = (string) ($order['user_name'] ?? '');
        $productName = trim((string) ($order['product_name'] ?? 'Tài khoản bản quyền'));
        $createdAt = (string) ($order['created_at'] ?? '');

        $maskedName = self::maskCustomerName($email, $rawName);
        $initial = self::extractInitial($maskedName);

        // Product URL
        $url = '#';
        $productId = (string) ($order['product_id'] ?? '');
        if ($productId !== '') {
            $url = url('san-pham/' . rawurlencode($productId));
        }

        $timestamp = strtotime($createdAt);
        $diffMins = $timestamp ? (int) floor((time() - $timestamp) / 60) : 999;

        // Fresh real order check (< 15 mins ago)
        $isFresh = ($diffMins <= 15 && $diffMins >= 0);
        if ($isFresh) {
            $timeText = ($diffMins <= 1) ? 'vừa xong' : ($diffMins . ' phút trước');
        } else {
            // Realistic randomized time >= 5 minutes
            $timeText = self::getRandomRealisticTime();
        }

        return [
            'name'     => $maskedName,
            'initial'  => $initial,
            'product'  => $productName,
            'price'    => number_format((float) ($order['amount'] ?? 0), 0, ',', '.') . 'đ',
            'time'     => $timeText,
            'is_fresh' => $isFresh,
            'is_real'  => true,
            'url'      => $url,
            'bg'       => '#10b981',
        ];
    }

    private static function getRandomRealisticTime(): string {
        $times = [5, 7, 9, 12, 14, 16, 19, 23, 28, 34, 42, 51];
        return $times[array_rand($times)] . ' phút trước';
    }

    private static function maskCustomerName(string $email = '', string $rawName = ''): string {
        $source = trim($rawName);
        if ($source === '' && $email !== '') {
            $prefix = strstr($email, '@', true) ?: $email;
            $source = preg_replace('/[0-9._-]+/', ' ', $prefix);
            $source = trim($source);
        }

        if ($source === '') {
            $pool = ['L*', 'Tu***', 'Ng***', 'Minh T***', 'H***', 'Tr***', 'Anh D***', 'Ph***', 'Hoàng N***', 'Quân B***', 'Đ***'];
            return $pool[array_rand($pool)];
        }

        $parts = preg_split('/\s+/u', $source);
        if (count($parts) === 1) {
            $word = $parts[0];
            $len = mb_strlen($word);
            if ($len <= 2) {
                return mb_convert_case($word, MB_CASE_TITLE, "UTF-8") . '*';
            }
            $visible = min(2, $len);
            return mb_convert_case(mb_substr($word, 0, $visible), MB_CASE_TITLE, "UTF-8") . '***';
        }

        // Multiple words: e.g. "Lê Tuấn" -> "L*" or "Tu***"
        $first = $parts[0];
        $last = end($parts);
        if (mb_strlen($first) <= 2) {
            return mb_strtoupper(mb_substr($first, 0, 1)) . '*';
        }
        return mb_convert_case($first, MB_CASE_TITLE, "UTF-8") . ' ' . mb_strtoupper(mb_substr($last, 0, 1)) . '***';
    }

    private static function extractInitial(string $maskedName): string {
        $clean = preg_replace('/[^a-zA-Z\x{00C0}-\x{024F}\x{1EA0}-\x{1EF9}]/u', '', $maskedName);
        if ($clean !== '') {
            return mb_strtoupper(mb_substr($clean, 0, 1));
        }
        return 'L';
    }

    private static function getRealisticSimulatedOrders(PDO $db, int $count): array {
        if ($count <= 0) return [];

        // Fetch active products from DB to ensure genuine product titles & links
        $products = [];
        try {
            $stmt = $db->query("SELECT id, title, seo_slug, price FROM products WHERE status = 'active' ORDER BY rating DESC, sales_count DESC LIMIT 15");
            $products = $stmt->fetchAll();
        } catch (Throwable $e) {}

        if (empty($products)) {
            $products = [
                ['id' => 'chatgpt-plus', 'title' => 'ChatGPT Plus 1 Tháng Dùng Riêng', 'price' => 249000],
                ['id' => 'youtube-premium', 'title' => 'YouTube Premium 1 Năm (Mail Chính Chủ)', 'price' => 320000],
                ['id' => 'canva-pro', 'title' => 'Canva Pro Chính Chủ 1 Năm', 'price' => 199000],
                ['id' => 'netflix-premium', 'title' => 'Netflix Premium 4K Ultra HD 1 Tháng', 'price' => 79000],
                ['id' => 'capcut-pro', 'title' => 'CapCut Pro 1 Năm Bản Quyền', 'price' => 350000],
                ['id' => 'claude-pro', 'title' => 'Claude Pro 1 Tháng Chính Hãng', 'price' => 290000],
                ['id' => 'github-copilot', 'title' => 'Github Copilot (Gói Dev 1 Năm)', 'price' => 450000],
            ];
        }

        $customerPool = [
            ['name' => 'L*', 'initial' => 'L'],
            ['name' => 'Tu***', 'initial' => 'T'],
            ['name' => 'Ng***', 'initial' => 'N'],
            ['name' => 'Minh T***', 'initial' => 'M'],
            ['name' => 'H***', 'initial' => 'H'],
            ['name' => 'Tr***', 'initial' => 'T'],
            ['name' => 'Anh D***', 'initial' => 'A'],
            ['name' => 'Ph***', 'initial' => 'P'],
            ['name' => 'Hoàng N***', 'initial' => 'H'],
            ['name' => 'Quân B***', 'initial' => 'Q'],
            ['name' => 'Đ***', 'initial' => 'Đ'],
            ['name' => 'Thành N***', 'initial' => 'T'],
        ];

        $timePool = [5, 8, 12, 14, 17, 19, 23, 26, 31, 38, 44, 52];
        shuffle($customerPool);
        shuffle($timePool);

        $results = [];
        for ($i = 0; $i < $count; $i++) {
            $cust = $customerPool[$i % count($customerPool)];
            $prod = $products[$i % count($products)];
            $timeMins = $timePool[$i % count($timePool)];

            $url = '#';
            if (!empty($prod['id'])) {
                $slug = !empty($prod['seo_slug']) ? $prod['seo_slug'] . '-' . $prod['id'] : $prod['id'];
                $url = url('san-pham/' . rawurlencode($slug));
            }

            $results[] = [
                'name'     => $cust['name'],
                'initial'  => $cust['initial'],
                'product'  => (string) ($prod['title'] ?? 'Sản phẩm bản quyền'),
                'price'    => number_format((float) ($prod['price'] ?? 0), 0, ',', '.') . 'đ',
                'time'     => $timeMins . ' phút trước',
                'is_fresh' => false,
                'is_real'  => false,
                'url'      => $url,
                'bg'       => '#10b981',
            ];
        }

        return $results;
    }

    private static function getDefaultPool(): array {
        return [
            [
                'name'     => 'L*',
                'initial'  => 'L',
                'product'  => 'ChatGPT Plus 1 Tháng Dùng Riêng',
                'price'    => '249.000đ',
                'time'     => '19 phút trước',
                'is_fresh' => false,
                'is_real'  => false,
                'url'      => url('san-pham'),
                'bg'       => '#10b981',
            ],
            [
                'name'     => 'Tu***',
                'initial'  => 'T',
                'product'  => 'YouTube Premium 1 Năm (Mail Chính Chủ)',
                'price'    => '320.000đ',
                'time'     => '7 phút trước',
                'is_fresh' => false,
                'is_real'  => false,
                'url'      => url('san-pham'),
                'bg'       => '#10b981',
            ],
            [
                'name'     => 'Ng***',
                'initial'  => 'N',
                'product'  => 'Canva Pro Chính Chủ 1 Năm',
                'price'    => '199.000đ',
                'time'     => '14 phút trước',
                'is_fresh' => false,
                'is_real'  => false,
                'url'      => url('san-pham'),
                'bg'       => '#10b981',
            ],
            [
                'name'     => 'Minh T***',
                'initial'  => 'M',
                'product'  => 'Netflix Premium 4K Ultra HD 1 Tháng',
                'price'    => '79.000đ',
                'time'     => '26 phút trước',
                'is_fresh' => false,
                'is_real'  => false,
                'url'      => url('san-pham'),
                'bg'       => '#10b981',
            ],
        ];
    }
}
