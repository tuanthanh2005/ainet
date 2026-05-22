<?php

class Order {
    public static function create($data) {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            "INSERT INTO orders
                (id, product_id, product_name, variant_name, variant_idx, amount, quantity,
                 customer_email, status, phone, note, upgrade_email, upgrade_pass, upgrade_link)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        return $stmt->execute([
            $data['id'],
            $data['product_id'],
            $data['product_name'],
            $data['variant_name'],
            (int) ($data['variant_idx'] ?? 0),
            $data['amount'],
            max(1, (int) ($data['quantity'] ?? 1)),
            $data['customer_email'],
            'pending',
            $data['phone'] ?? null,
            $data['note'] ?? null,
            $data['upgrade_email'] ?? null,
            $data['upgrade_pass'] ?? null,
            $data['upgrade_link'] ?? null,
        ]);
    }

    public static function getById($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$id]);
        $order = $stmt->fetch();
        if ($order && !empty($order['delivered_items'])) {
            $decoded = json_decode($order['delivered_items'], true);
            $order['delivered_items'] = is_array($decoded) ? $decoded : [];
        } else if ($order) {
            $order['delivered_items'] = [];
        }
        return $order;
    }

    public static function updateStatus($id, $status, $transactionId = null) {
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE orders SET status = ?, transaction_id = ? WHERE id = ?");
        return $stmt->execute([$status, $transactionId, $id]);
    }

    public static function setDelivered(string $id, array $items): void {
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE orders SET delivered_items = ? WHERE id = ?");
        $stmt->execute([json_encode($items, JSON_UNESCAPED_UNICODE), $id]);
    }

    public static function getAll() {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT * FROM orders ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }
}
