<?php

class RecentOrder {
    /**
     * Get 100% real customer orders from database.
     * Real purchase times and masked names (2-3 characters).
     */
    public static function getAll(): array {
        try {
            return Cache::remember('orders.recent_popup', 30, function (): array {
                $db = Database::getInstance();
                
                // Query real orders from database (prioritize completed/processing/paid, fallback to non-cancelled)
                $realOrders = [];
                try {
                    $stmt = $db->query(
                        "SELECT o.id, o.product_id, o.product_name, o.amount, o.customer_email, o.created_at,
                                u.name AS user_name
                         FROM orders o
                         LEFT JOIN users u ON u.email = o.customer_email
                         WHERE o.status IN ('completed', 'processing', 'paid')
                         ORDER BY o.created_at DESC
                         LIMIT 20"
                    );
                    $rows = $stmt->fetchAll();

                    if (empty($rows)) {
                        // Fallback to any non-cancelled order
                        $stmt = $db->query(
                            "SELECT o.id, o.product_id, o.product_name, o.amount, o.customer_email, o.created_at,
                                    u.name AS user_name
                             FROM orders o
                             LEFT JOIN users u ON u.email = o.customer_email
                             WHERE o.status != 'cancelled'
                             ORDER BY o.created_at DESC
                             LIMIT 20"
                        );
                        $rows = $stmt->fetchAll();
                    }

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

                return $realOrders;
            });
        } catch (Throwable $e) {
            return [];
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

        return [
            'name'     => $maskedName,
            'initial'  => $initial,
            'product'  => $productName,
            'price'    => number_format((float) ($order['amount'] ?? 0), 0, ',', '.') . 'đ',
            'time'     => self::relativeTime($createdAt),
            'url'      => $url,
            'created'  => $createdAt,
        ];
    }

    public static function relativeTime(string $createdAt): string {
        $timestamp = strtotime($createdAt);
        if (!$timestamp) {
            return 'vừa xong';
        }

        $diff = time() - $timestamp;
        if ($diff < 60) {
            return 'vừa xong';
        }

        $minutes = (int) floor($diff / 60);
        if ($minutes < 60) {
            return $minutes . ' phút trước';
        }

        $hours = (int) floor($minutes / 60);
        if ($hours < 24) {
            return $hours . ' giờ trước';
        }

        $days = (int) floor($hours / 24);
        if ($days < 30) {
            return $days . ' ngày trước';
        }

        return date('d/m/Y', $timestamp);
    }

    private static function maskCustomerName(string $email = '', string $rawName = ''): string {
        $source = trim($rawName);
        if ($source === '' && $email !== '') {
            $prefix = strstr($email, '@', true) ?: $email;
            $source = preg_replace('/[0-9._-]+/', ' ', $prefix);
            $source = trim($source);
        }

        if ($source === '') {
            return 'Khách hàng';
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
        return 'K';
    }
}
