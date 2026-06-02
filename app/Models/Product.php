<?php

class Product {
    public static function getAll() {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT *, category_name AS category FROM products ORDER BY created_at DESC");
        $products = $stmt->fetchAll();
        
        foreach ($products as &$product) {
            if (isset($product['options'])) {
                $product['options'] = json_decode($product['options'], true);
            }
        }
        return $products;
    }

    public static function getById($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT *, category_name AS category FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();
        
        if ($product && isset($product['options'])) {
            $product['options'] = json_decode($product['options'], true);
        }
        return $product;
    }

    public static function saveAll($products) {
        $db = Database::getInstance();
        $db->exec("DELETE FROM products"); // Đơn giản cho dashboard hiện tại
        
        $stmt = $db->prepare("INSERT INTO products (id, title, category_slug, category_name, price, status, image, feature_text, feature_icon, rating, sold_count, badge, description, options, is_upgrade) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        foreach ($products as $p) {
            $stmt->execute([
                $p['id'],
                $p['title'],
                $p['category_slug'] ?? '',
                $p['category'] ?? '',
                $p['price'] ?? 0,
                $p['status'] ?? 'active',
                $p['image'] ?? '',
                $p['feature_text'] ?? '',
                $p['feature_icon'] ?? 'fa-box',
                $p['rating'] ?? 5,
                $p['sold_count'] ?? 0,
                $p['badge'] ?? null,
                $p['description'] ?? '',
                isset($p['options']) ? json_encode($p['options'], JSON_UNESCAPED_UNICODE) : '[]',
                $p['is_upgrade'] ?? 0
            ]);
        }
    }
}
