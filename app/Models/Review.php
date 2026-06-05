<?php

class Review {
    private static function ensureReviewTable(PDO $db): void {
        static $done = false;
        if ($done) return;
        $done = true;

        try {
            $db->exec("CREATE TABLE IF NOT EXISTS reviews (
                id INT AUTO_INCREMENT PRIMARY KEY,
                order_id VARCHAR(50) NOT NULL,
                product_id VARCHAR(100) NOT NULL,
                user_id INT NOT NULL,
                rating INT NOT NULL DEFAULT 5,
                content TEXT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX (product_id),
                INDEX (order_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
        } catch (Throwable $ignored) {}
    }

    public static function create(string $orderId, string $productId, int $userId, int $rating, string $content): bool {
        $db = Database::getInstance();
        self::ensureReviewTable($db);
        
        $stmt = $db->prepare("INSERT INTO reviews (order_id, product_id, user_id, rating, content, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $result = $stmt->execute([$orderId, $productId, $userId, $rating, $content]);
        
        if ($result) {
            // Update average rating in products table
            $avgStmt = $db->prepare("SELECT AVG(rating) FROM reviews WHERE product_id = ?");
            $avgStmt->execute([$productId]);
            $avgRating = round((float) $avgStmt->fetchColumn(), 1);
            if ($avgRating > 0) {
                $updStmt = $db->prepare("UPDATE products SET rating = ? WHERE id = ?");
                $updStmt->execute([$avgRating, $productId]);
            }
        }
        
        return $result;
    }

    public static function getByProductId(string $productId): array {
        $db = Database::getInstance();
        self::ensureReviewTable($db);
        
        $stmt = $db->prepare("
            SELECT r.*, u.name as user_name, u.avatar as user_avatar 
            FROM reviews r
            LEFT JOIN users u ON r.user_id = u.id
            WHERE r.product_id = ?
            ORDER BY r.created_at DESC
        ");
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }

    public static function hasReviewed(string $orderId, string $productId): bool {
        $db = Database::getInstance();
        self::ensureReviewTable($db);
        
        $stmt = $db->prepare("SELECT 1 FROM reviews WHERE order_id = ? AND product_id = ? LIMIT 1");
        $stmt->execute([$orderId, $productId]);
        return (bool) $stmt->fetch();
    }

    private static function ensureReviewRepliesTable(PDO $db): void {
        static $done = false;
        if ($done) return;
        $done = true;

        try {
            $db->exec("CREATE TABLE IF NOT EXISTS review_replies (
                id INT AUTO_INCREMENT PRIMARY KEY,
                review_id INT NOT NULL,
                user_id INT NOT NULL,
                content TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_review_id (review_id),
                INDEX idx_user_id (user_id),
                FOREIGN KEY (review_id) REFERENCES reviews(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
        } catch (Throwable $ignored) {}
    }

    public static function getById(int $id): ?array {
        $db = Database::getInstance();
        self::ensureReviewTable($db);
        
        $stmt = $db->prepare("SELECT * FROM reviews WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function getRepliesByReviewId(int $reviewId): array {
        $db = Database::getInstance();
        self::ensureReviewRepliesTable($db);
        
        $stmt = $db->prepare("
            SELECT rp.*, u.name as user_name, u.avatar as user_avatar, u.role as user_role
            FROM review_replies rp
            LEFT JOIN users u ON rp.user_id = u.id
            WHERE rp.review_id = ?
            ORDER BY rp.created_at ASC
        ");
        $stmt->execute([$reviewId]);
        return $stmt->fetchAll();
    }

    public static function createReply(int $reviewId, int $userId, string $content): bool {
        $db = Database::getInstance();
        self::ensureReviewRepliesTable($db);
        
        $stmt = $db->prepare("INSERT INTO review_replies (review_id, user_id, content, created_at) VALUES (?, ?, ?, NOW())");
        return $stmt->execute([$reviewId, $userId, $content]);
    }

    public static function canReply(int $reviewId, ?array $currentUser): bool {
        if (!$currentUser) {
            return false;
        }

        if (($currentUser['role'] ?? 'user') === 'admin') {
            return true;
        }

        $review = self::getById($reviewId);
        if (!$review) {
            return false;
        }

        // Only the author of the review can reply
        if ((int)$review['user_id'] !== (int)$currentUser['id']) {
            return false;
        }

        // Check the replies
        $replies = self::getRepliesByReviewId($reviewId);
        if (empty($replies)) {
            // No replies yet, user cannot reply (must wait for admin)
            return false;
        }

        $lastReply = end($replies);
        // The last reply must be from an admin
        return ($lastReply['user_role'] ?? 'user') === 'admin';
    }

    public static function getRecentReviews(int $limit = 6): array {
        $db = Database::getInstance();
        self::ensureReviewTable($db);
        
        $stmt = $db->prepare("
            SELECT r.*, u.name as user_name, u.avatar as user_avatar
            FROM reviews r
            LEFT JOIN users u ON r.user_id = u.id
            ORDER BY r.created_at DESC
            LIMIT ?
        ");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        $reviews = $stmt->fetchAll();

        // Map product title and image in PHP
        if (!empty($reviews)) {
            $products = [];
            try {
                $pStmt = $db->query("SELECT id, title, image FROM products");
                while ($p = $pStmt->fetch()) {
                    $products[$p['id']] = $p;
                }
            } catch (Throwable $ignored) {}

            foreach ($reviews as &$r) {
                $pid = $r['product_id'] ?? '';
                $r['product_title'] = isset($products[$pid]) ? $products[$pid]['title'] : '';
                $r['product_image'] = isset($products[$pid]) ? $products[$pid]['image'] : '';
            }
            unset($r);
        }

        return $reviews;
    }
}
