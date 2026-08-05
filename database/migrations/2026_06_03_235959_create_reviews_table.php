<?php

return new class {
    public function up(PDO $pdo) {
        $pdo->exec("CREATE TABLE IF NOT EXISTS reviews (
            id INT AUTO_INCREMENT PRIMARY KEY,
            order_id VARCHAR(50) NOT NULL,
            product_id VARCHAR(100) NOT NULL,
            user_id INT NOT NULL,
            rating INT NOT NULL DEFAULT 5,
            content TEXT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_reviews_product (product_id),
            INDEX idx_reviews_order (order_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }

    public function down(PDO $pdo) {
        $pdo->exec("DROP TABLE IF EXISTS reviews");
    }
};
