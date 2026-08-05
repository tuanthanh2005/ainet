<?php

class Product {
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

    public static function variant(array $product, int $variantIdx = 0): ?array {
        $options = is_array($product['options'] ?? null) ? $product['options'] : [];
        return isset($options[$variantIdx]) && is_array($options[$variantIdx])
            ? $options[$variantIdx]
            : null;
    }

    public static function availableStock(array $product, int $variantIdx = 0): int {
        $variant = self::variant($product, $variantIdx);
        return $variant ? max(0, (int) ($variant['stock'] ?? 0)) : 0;
    }

    public static function isPurchasable(array $product, int $variantIdx = 0, int $quantity = 1): bool {
        return ($product['status'] ?? 'active') === 'active'
            && $quantity > 0
            && self::variant($product, $variantIdx) !== null
            && self::availableStock($product, $variantIdx) >= $quantity;
    }

    public static function firstAvailableVariantIndex(array $product): ?int {
        foreach ((array) ($product['options'] ?? []) as $index => $variant) {
            if (self::isPurchasable($product, (int) $index)) {
                return (int) $index;
            }
        }
        return null;
    }

    public static function getAll() {
        return Cache::remember('products.all', 60, function () {
            $db = Database::getInstance();
            $stmt = $db->query("SELECT p.*, p.category_name AS category,
                COALESCE(r.avg_rating, 0) AS real_rating,
                COALESCE(r.review_count, 0) AS review_real_count
                FROM products p
                LEFT JOIN (
                    SELECT product_id, AVG(rating) AS avg_rating, COUNT(*) AS review_count
                    FROM reviews GROUP BY product_id
                ) r ON r.product_id = p.id
                ORDER BY p.created_at DESC");
            $products = $stmt->fetchAll();

            foreach ($products as &$product) {
                self::decodeProduct($product);
                $product['rating'] = round((float) $product['real_rating'], 1);
                unset($product['real_rating']);
                $product['review_real_count'] = (int) $product['review_real_count'];
            }
            unset($product);
            return $products;
        });
    }

    public static function getById($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT *, category_name AS category FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();

        if ($product) {
            self::decodeProduct($product);
            
            // Get rating dynamically
            $product['rating'] = 0;
            $product['review_real_count'] = 0;
            try {
                $stmtRating = $db->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as cnt FROM reviews WHERE product_id = ?");
                $stmtRating->execute([$id]);
                $row = $stmtRating->fetch();
                if ($row && $row['avg_rating'] !== null) {
                    $product['rating'] = round((float)$row['avg_rating'], 1);
                    $product['review_real_count'] = (int)$row['cnt'];
                }
            } catch (Throwable $ignored) {}
        }
        return $product;
    }

    public static function getBySlugOrId($slugOrId) {
        $db = Database::getInstance();
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
            
            // Get rating dynamically
            $pid = $product['id'] ?? '';
            $product['rating'] = 0;
            $product['review_real_count'] = 0;
            try {
                $stmtRating = $db->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as cnt FROM reviews WHERE product_id = ?");
                $stmtRating->execute([$pid]);
                $row = $stmtRating->fetch();
                if ($row && $row['avg_rating'] !== null) {
                    $product['rating'] = round((float)$row['avg_rating'], 1);
                    $product['review_real_count'] = (int)$row['cnt'];
                }
            } catch (Throwable $ignored) {}
        }
        return $product ?: null;
    }

    public static function saveAll($products) {
        $db = Database::getInstance();
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
        Cache::forget('products.all');
    }

    public static function incrementSoldCount(string $id, int $qty = 1, int $variantIdx = -1): void {
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE products SET sold_count = sold_count + ? WHERE id = ?");
        $stmt->execute([$qty, $id]);
        Cache::forget('products.all');

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
