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

    public static function getBySlugOrId($slugOrId) {
        $db = Database::getInstance();
        $slugOrId = trim(rawurldecode((string) $slugOrId));
        
        // Backward compatibility: check if it ends with legacy id format e.g. -prod_123
        $id = '';
        if (preg_match('/-(prod_[A-Za-z0-9_]+|\d+)$/', $slugOrId, $m)) {
            $id = $m[1];
        }
        
        $stmt = $db->prepare("SELECT *, category_name AS category FROM products WHERE seo_slug = ? OR id = ? OR (? != '' AND id = ?)");
        $stmt->execute([$slugOrId, $slugOrId, $id, $id]);
        $product = $stmt->fetch();
        
        // Fallback to generated URL slugs. This supports old URLs without ids and
        // new stable URLs where the product id is appended after the SEO/title slug.
        if (!$product) {
            $products = self::getAll();
            foreach ($products as $p) {
                $titleSlug = Seo::slugify($p['title'] ?? '');
                $seoSlug = trim((string) ($p['seo_slug'] ?? ''));
                $baseSlug = $seoSlug !== '' ? $seoSlug : $titleSlug;
                $productId = trim((string) ($p['id'] ?? ''));
                $stableSlug = $productId !== '' ? ($baseSlug . '-' . $productId) : $baseSlug;

                if ($titleSlug === $slugOrId || $seoSlug === $slugOrId || $stableSlug === $slugOrId) {
                    return $p;
                }
            }
        }
        
        if ($product && isset($product['options'])) {
            if (is_string($product['options'])) {
                $product['options'] = json_decode($product['options'], true);
            }
        }
        return $product ?: null;
    }

    public static function saveAll($products) {
        $db = Database::getInstance();
        $db->exec("DELETE FROM products"); // Đơn giản cho dashboard hiện tại
        
        $stmt = $db->prepare("INSERT INTO products (id, title, category_slug, category_name, price, status, image, feature_text, feature_icon, rating, sold_count, badge, description, options, is_upgrade, seo_title, seo_description, seo_keywords, seo_slug) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
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
                $p['is_upgrade'] ?? 0,
                $p['seo_title'] ?? null,
                $p['seo_description'] ?? null,
                $p['seo_keywords'] ?? null,
                $p['seo_slug'] ?? null
            ]);
        }
    }
}
