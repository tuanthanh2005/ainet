<?php

return new class {
    public function up(PDO $pdo) {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS orders (
                id VARCHAR(40) PRIMARY KEY,
                product_id VARCHAR(60),
                product_name VARCHAR(255),
                variant_name VARCHAR(255),
                variant_idx INT NOT NULL DEFAULT 0,
                amount DECIMAL(12, 2) DEFAULT 0,
                quantity INT NOT NULL DEFAULT 1,
                customer_email VARCHAR(190),
                phone VARCHAR(40) DEFAULT NULL,
                note TEXT DEFAULT NULL,
                status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
                transaction_id VARCHAR(100) DEFAULT NULL,
                upgrade_email VARCHAR(190) DEFAULT NULL,
                upgrade_pass VARCHAR(190) DEFAULT NULL,
                upgrade_link TEXT DEFAULT NULL,
                delivered_items JSON DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_status (status),
                INDEX idx_email (customer_email)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    }

    public function down(PDO $pdo) {
        $pdo->exec("DROP TABLE IF EXISTS orders;");
    }
};
