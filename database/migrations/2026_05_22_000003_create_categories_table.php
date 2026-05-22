<?php

return new class {
    public function up(PDO $pdo) {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS categories (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                slug VARCHAR(100) NOT NULL UNIQUE,
                is_pro TINYINT(1) DEFAULT 0,
                icon VARCHAR(50),
                icon_color VARCHAR(50)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $categories = [
            ['name' => 'ChatGPT', 'slug' => 'chatgpt', 'is_pro' => 1, 'icon' => 'fa-robot', 'icon_color' => 'text-primary'],
            ['name' => 'YouTube', 'slug' => 'youtube', 'is_pro' => 0, 'icon' => 'fa-play', 'icon_color' => 'text-danger'],
            ['name' => 'GitHub', 'slug' => 'github', 'is_pro' => 1, 'icon' => 'fa-code', 'icon_color' => 'text-dark']
        ];

        $stmt = $pdo->prepare("INSERT INTO categories (name, slug, is_pro, icon, icon_color) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE name = VALUES(name), is_pro = VALUES(is_pro), icon = VALUES(icon), icon_color = VALUES(icon_color)");
        foreach ($categories as $cat) {
            $stmt->execute([$cat['name'], $cat['slug'], $cat['is_pro'], $cat['icon'], $cat['icon_color']]);
        }
    }

    public function down(PDO $pdo) {
        $pdo->exec("DROP TABLE IF EXISTS categories;");
    }
};
