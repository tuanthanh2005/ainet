<?php

class RecentOrder {
    public static function getAll() {
        try {
            $db = Database::getInstance();
            $stmt = $db->query(
                "SELECT product_name, amount, customer_email, created_at
                 FROM orders
                 WHERE status = 'completed'
                 ORDER BY updated_at DESC, created_at DESC
                 LIMIT 20"
            );

            return array_map([self::class, 'formatOrder'], $stmt->fetchAll());
        } catch (Throwable $e) {
            return [];
        }
    }

    public static function create($data) {
        return true;
    }

    private static function formatOrder(array $order): array {
        $email = (string) ($order['customer_email'] ?? '');
        $name = $email !== '' ? strstr($email, '@', true) : 'Khach hang';
        $name = $name ?: 'Khach hang';
        $name = mb_substr(preg_replace('/[._-]+/', ' ', $name), 0, 24);
        $initial = mb_strtoupper(mb_substr($name, 0, 1));

        return [
            'name'     => $name,
            'initial'  => $initial ?: 'K',
            'product'  => (string) ($order['product_name'] ?? 'San pham'),
            'price'    => number_format((float) ($order['amount'] ?? 0), 0, ',', '.') . 'd',
            'time'     => self::relativeTime((string) ($order['created_at'] ?? '')),
            'location' => 'Viet Nam',
            'bg'       => self::avatarColor($email . ($order['product_name'] ?? '')),
        ];
    }

    private static function relativeTime(string $createdAt): string {
        $timestamp = strtotime($createdAt);
        if (!$timestamp) {
            return 'vua xong';
        }

        $minutes = max(1, (int) floor((time() - $timestamp) / 60));
        if ($minutes < 60) {
            return $minutes . ' phut truoc';
        }

        $hours = (int) floor($minutes / 60);
        if ($hours < 24) {
            return $hours . ' gio truoc';
        }

        return min(30, (int) floor($hours / 24)) . ' ngay truoc';
    }

    private static function avatarColor(string $seed): string {
        $colors = ['#0d6efd', '#198754', '#dc3545', '#fd7e14', '#6f42c1', '#20c997'];
        return $colors[abs(crc32($seed)) % count($colors)];
    }
}
