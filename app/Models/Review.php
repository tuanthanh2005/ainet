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
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
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
}
