<?php

class Product {
    private static function ensureProductColumns(PDO $db): void {
        static $done = false;
        if ($done) return;
        $done = true;

        try {
            $db->exec("ALTER TABLE products ADD COLUMN card_features TEXT NULL AFTER feature_text");
        } catch (Throwable $ignored) {
        }
        try {
            $db->exec("ALTER TABLE products ADD COLUMN original_price DECIMAL(15, 2) DEFAULT 0.00 AFTER price");
        } catch (Throwable $ignored) {
        }
    }

    private static function decodeProduct(&$product): void {
        if (isset($product['options']) && is_string($product['options'])) {
            $decoded = json_decode($product['options'], true);
            $product['options'] = is_array($decoded) ? $decoded : [];
        }

        if (isset($product['card_features']) && is_string($product['card_features'])) {
            $decoded = json_decode($product['card_features'], true);
            $product['card_features'] = is_array($decoded) ? array_values(array_filter($decoded, 'strlen')) : [];
        } elseif (!isset($product['card_features']) || !is_array($product['card_features'])) {
            $product['card_features'] = [];
        }
    }

    public static function getAll() {
        $db = Database::getInstance();
        self::ensureProductColumns($db);
        $stmt = $db->query("SELECT *, category_name AS category FROM products ORDER BY created_at DESC");
        $products = $stmt->fetchAll();

        foreach ($products as &$product) {
            self::decodeProduct($product);
        }
        unset($product);
        return $products;
    }

    public static function getById($id) {
        $db = Database::getInstance();
        self::ensureProductColumns($db);
        $stmt = $db->prepare("SELECT *, category_name AS category FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();

        if ($product) {
            self::decodeProduct($product);
        }
        return $product;
    }

    public static function getBySlugOrId($slugOrId) {
        $db = Database::getInstance();
        self::ensureProductColumns($db);
        $slugOrId = trim(rawurldecode((string) $slugOrId));

        $id = '';
        if (preg_match('/-(prod_[A-Za-z0-9_]+|\d+)$/', $slugOrId, $m)) {
            $id = $m[1];
        }

        $stmt = $db->prepare("SELECT *, category_name AS category FROM products WHERE seo_slug = ? OR id = ? OR (? != '' AND id = ?)");
        $stmt->execute([$slugOrId, $slugOrId, $id, $id]);
        $product = $stmt->fetch();

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

        if ($product) {
            self::decodeProduct($product);
        }
        return $product ?: null;
    }

    public static function saveAll($products) {
        $db = Database::getInstance();
        self::ensureProductColumns($db);
        $db->exec("DELETE FROM products");

        $stmt = $db->prepare("INSERT INTO products (id, title, category_slug, category_name, price, original_price, status, image, feature_text, card_features, feature_icon, rating, sold_count, badge, description, options, is_upgrade, seo_title, seo_description, seo_keywords, seo_slug) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        foreach ($products as $p) {
            $stmt->execute([
                $p['id'],
                $p['title'],
                $p['category_slug'] ?? '',
                $p['category'] ?? '',
                $p['price'] ?? 0,
                $p['original_price'] ?? 0,
                $p['status'] ?? 'active',
                $p['image'] ?? '',
                $p['feature_text'] ?? '',
                isset($p['card_features']) ? json_encode(array_values(array_filter((array) $p['card_features'], 'strlen')), JSON_UNESCAPED_UNICODE) : '[]',
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

    public static function incrementSoldCount(string $id, int $qty = 1, int $variantIdx = -1): void {
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE products SET sold_count = sold_count + ? WHERE id = ?");
        $stmt->execute([$qty, $id]);

        if ($variantIdx >= 0) {
            $stmt = $db->prepare("SELECT options FROM products WHERE id = ?");
            $stmt->execute([$id]);
            $optionsJson = $stmt->fetchColumn();
            
            if ($optionsJson) {
                $options = json_decode($optionsJson, true);
                if (is_array($options) && isset($options[$variantIdx])) {
                    $currentStock = (int)($options[$variantIdx]['stock'] ?? 0);
                    $newStock = max(0, $currentStock - $qty);
                    $options[$variantIdx]['stock'] = $newStock;
                    
                    $updateStmt = $db->prepare("UPDATE products SET options = ? WHERE id = ?");
                    $updateStmt->execute([json_encode($options, JSON_UNESCAPED_UNICODE), $id]);
                }
            }
        }
    }
}
