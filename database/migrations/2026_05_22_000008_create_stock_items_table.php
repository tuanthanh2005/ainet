<?php

return new class {
    public function up(PDO $pdo) {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS stock_items (
                id INT AUTO_INCREMENT PRIMARY KEY,
                product_id VARCHAR(60) NOT NULL,
                variant_idx INT NOT NULL DEFAULT 0,
                content TEXT NOT NULL,
                status ENUM('available', 'sold') NOT NULL DEFAULT 'available',
                order_id VARCHAR(40) NULL,
                delivered_at TIMESTAMP NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_pickup (product_id, variant_idx, status, id),
                INDEX idx_order (order_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    }

    public function down(PDO $pdo) {
        $pdo->exec("DROP TABLE IF EXISTS stock_items;");
    }
};
