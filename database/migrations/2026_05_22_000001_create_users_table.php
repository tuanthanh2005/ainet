<?php

return new class {
    public function up(PDO $pdo) {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(120) NOT NULL,
                email VARCHAR(190) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
                status ENUM('active', 'blocked') NOT NULL DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // Seed default admin: admin@aicualtoi.com / Admin@123
        $pdo->exec("
            INSERT INTO users (name, email, password, role, status)
            VALUES ('Admin', 'admin@aicualtoi.com', '\$2y\$10\$doJM1/nsSsWex3qLTiuVkuHp0PatNk/XPZs12JM5Lysmq9IzykFkC', 'admin', 'active')
            ON DUPLICATE KEY UPDATE role = 'admin', status = 'active';
        ");
    }

    public function down(PDO $pdo) {
        $pdo->exec("DROP TABLE IF EXISTS users;");
    }
};
