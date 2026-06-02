<?php

return new class {
    public function up(PDO $pdo) {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS products (
                id VARCHAR(100) PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                category_slug VARCHAR(255) NULL,
                category_name VARCHAR(255) NULL,
                price DECIMAL(15, 2) DEFAULT 0.00,
                status VARCHAR(50) DEFAULT 'active',
                image TEXT NULL,
                feature_text VARCHAR(255) NULL,
                card_features TEXT NULL,
                feature_icon VARCHAR(100) NULL,
                rating DECIMAL(2, 1) DEFAULT 5.0,
                sold_count INT DEFAULT 0,
                badge VARCHAR(100) NULL,
                description TEXT NULL,
                options JSON NULL,
                is_upgrade TINYINT(1) DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $products = [
            [
                'id' => 'chatgpt-plus',
                'title' => 'Tài khoản ChatGPT Plus - VIP',
                'category_slug' => 'chatgpt',
                'category_name' => 'Antigravity PRO',
                'price' => 450000.00,
                'status' => 'active',
                'image' => 'https://tse4.mm.bing.net/th/id/OIP.69Xt1JPRMusHNHsqIaZdEgHaEN?pid=Api&P=0&h=180',
                'feature_text' => 'Cấp tốc 5 phút',
                'feature_icon' => 'fa-box',
                'rating' => 5.0,
                'sold_count' => 1250,
                'badge' => null,
                'description' => '',
                'options' => '[{"name": "Tài khoản cấp mới (Sử dụng mail của shop)", "price": "450000", "stock": "10", "is_upgrade": 0}, {"name": "Gia hạn chính chủ (Sử dụng mail của bạn)", "price": "480000", "stock": "5", "is_upgrade": 1}]',
                'created_at' => '2026-05-19 02:01:32',
                'is_upgrade' => 0,
            ],
            [
                'id' => 'github-copilot',
                'title' => 'Github Copilot (Gói Dev 1 Năm)',
                'category_slug' => 'github',
                'category_name' => 'GitHub',
                'price' => 150000.00,
                'status' => 'active',
                'image' => 'https://images.unsplash.com/photo-1618401471353-b98afee0b2eb?auto=format&fit=crop&q=80&w=400&h=250',
                'feature_text' => 'Coding Assistant',
                'feature_icon' => 'fa-code',
                'rating' => 5.0,
                'sold_count' => 312,
                'badge' => null,
                'description' => 'Trợ lý lập trình AI đỉnh cao từ GitHub. Hỗ trợ tự động hoàn thành code, gợi ý giải pháp và refactor code hiệu quả.',
                'options' => '[{"name": "Gói Developer - Bảo hành 6 Tháng", "price": 150000, "stock": 50}, {"name": "Gói Business - Bảo hành 1 Năm", "price": 350000, "stock": 30}]',
                'created_at' => '2026-05-19 02:01:32',
                'is_upgrade' => 0,
            ],
            [
                'id' => 'netflix-premium',
                'title' => 'Netflix Premium 4K (1 Tháng)',
                'category_slug' => 'netflix',
                'category_name' => 'Netflix',
                'price' => 85000.00,
                'status' => 'out_of_stock',
                'image' => 'https://images.unsplash.com/photo-1522869635100-9f4c5e86aa37?auto=format&fit=crop&q=80&w=400&h=250',
                'feature_text' => 'Profile riêng tư',
                'feature_icon' => 'fa-lock',
                'rating' => 4.5,
                'sold_count' => 2105,
                'badge' => null,
                'description' => 'Đắm chìm vào thế giới điện ảnh với chất lượng Ultra HD 4K sắc nét nhất. Profile riêng tư được bảo mật bằng mã PIN.',
                'options' => '[{"name": "Profile Tiêu Chuẩn (Bảo hành 1 Tháng)", "price": 85000, "stock": 0}, {"name": "Profile Cao Cấp 4K (Bảo hành 3 Tháng)", "price": 240000, "stock": 0}]',
                'created_at' => '2026-05-19 02:01:32',
                'is_upgrade' => 0,
            ],
            [
                'id' => 'prod_1778268440',
                'title' => 'Tài khoản ChatGPT Plus (1 Tháng) - Bảo Hành',
                'category_slug' => 'chatgpt',
                'category_name' => 'ChatGPT Plus',
                'price' => 500000.00,
                'status' => 'active',
                'image' => 'https://tse4.mm.bing.net/th/id/OIP.69Xt1JPRMusHNHsqIaZdEgHaEN?pid=Api&P=0&h=180',
                'feature_text' => 'Profile riêng tư',
                'feature_icon' => 'fa-box',
                'rating' => 5.0,
                'sold_count' => 0,
                'badge' => null,
                'description' => '',
                'options' => '[{"name": "GPT - KBH", "price": "70000", "stock": "1"}, {"name": "GPT-ADD-KBH", "price": "50000", "stock": "1"}]',
                'created_at' => '2026-05-19 02:01:32',
                'is_upgrade' => 0,
            ],
            [
                'id' => 'prod_1779126080',
                'title' => 'Netflix Premium 4K (1 Tháng) admin',
                'category_slug' => 'github',
                'category_name' => 'Tài khoản GitHub Copilot Pro',
                'price' => 50000.00,
                'status' => 'active',
                'image' => 'https://tse4.mm.bing.net/th/id/OIP.69Xt1JPRMusHNHsqIaZdEgHaEN?pid=Api&P=0&h=180',
                'feature_text' => 'Profile riêng tư',
                'feature_icon' => 'fa-box',
                'rating' => 5.0,
                'sold_count' => 0,
                'badge' => null,
                'description' => '',
                'options' => '[{"name": "cấp sẳn ", "price": 20000, "stock": "5", "is_upgrade": 0, "original_price": 50000}, {"name": "addd", "price": 8000, "stock": "5", "is_upgrade": 1, "original_price": 20000}]',
                'created_at' => '2026-05-19 02:01:32',
                'is_upgrade' => 0,
            ],
            [
                'id' => 'youtube-premium',
                'title' => 'YouTube Premium (Mail Chính Chủ)',
                'category_slug' => 'youtube',
                'category_name' => 'YouTube',
                'price' => 250000.00,
                'status' => 'active',
                'image' => 'https://images.unsplash.com/photo-1611162617474-5b21e879e113?auto=format&fit=crop&q=80&w=400&h=250',
                'feature_text' => 'Bảo hành trọn đời',
                'feature_icon' => 'fa-shield-check',
                'rating' => 4.5,
                'sold_count' => 840,
                'badge' => null,
                'description' => 'Xem video không quảng cáo, tải xuống video để xem ngoại tuyến và sử dụng YouTube Music Premium miễn phí.',
                'options' => '[{"name": "Gói Gia đình (Family) - 6 Tháng", "price": 250000, "stock": 20}, {"name": "Gói Cá nhân - 1 Năm", "price": 550000, "stock": 15}]',
                'created_at' => '2026-05-19 02:01:32',
                'is_upgrade' => 0,
            ],
        ];

        $stmt = $pdo->prepare("
            INSERT INTO products (id, title, category_slug, category_name, price, status, image, feature_text, feature_icon, rating, sold_count, badge, description, options, is_upgrade, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE title = VALUES(title), category_slug = VALUES(category_slug), category_name = VALUES(category_name), price = VALUES(price), status = VALUES(status), image = VALUES(image), feature_text = VALUES(feature_text), feature_icon = VALUES(feature_icon), rating = VALUES(rating), sold_count = VALUES(sold_count), badge = VALUES(badge), description = VALUES(description), options = VALUES(options), is_upgrade = VALUES(is_upgrade);
        ");

        foreach ($products as $p) {
            $stmt->execute([
                $p['id'],
                $p['title'],
                $p['category_slug'],
                $p['category_name'],
                $p['price'],
                $p['status'],
                $p['image'],
                $p['feature_text'],
                $p['feature_icon'],
                $p['rating'],
                $p['sold_count'],
                $p['badge'],
                $p['description'],
                $p['options'],
                $p['is_upgrade'],
                $p['created_at'],
            ]);
        }
    }

    public function down(PDO $pdo) {
        $pdo->exec("DROP TABLE IF EXISTS products;");
    }
};
