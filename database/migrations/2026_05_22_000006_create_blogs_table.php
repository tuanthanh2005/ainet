<?php

return new class {
    public function up(PDO $pdo) {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS blogs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                image VARCHAR(500) DEFAULT NULL,
                description TEXT DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $blogs = [
            [
                'title' => 'ChatGPT-5 ra mắt: Bước nhảy vọt mới của trí tuệ nhân tạo',
                'image' => 'https://images.unsplash.com/photo-1677442136019-21780ecad995?auto=format&fit=crop&q=80&w=1200&h=700',
                'description' => 'OpenAI vừa công bố mô hình ChatGPT-5 với khả năng suy luận đa bước, hiểu ngữ cảnh dài hơn và xử lý đa phương tiện ngay trong một lần truy vấn. Người dùng có thể đính kèm hình ảnh, file PDF hay đoạn video ngắn để nhận về phản hồi chi tiết, có dẫn nguồn rõ ràng. So với phiên bản tiền nhiệm, GPT-5 giảm tới 40% lỗi hallucination và tăng tốc độ phản hồi gấp đôi nhờ kiến trúc mixture-of-experts mới. Đây hứa hẹn là cú hích cho hàng loạt ứng dụng từ giáo dục, y tế đến lập trình.'
            ],
            [
                'title' => 'YouTube Premium 2026: Vì sao ngày càng nhiều người Việt nâng cấp?',
                'image' => 'https://images.unsplash.com/photo-1611162616305-c69b3fa7fbe0?auto=format&fit=crop&q=80&w=1200&h=700',
                'description' => 'Theo khảo sát mới nhất, lượng người dùng YouTube Premium tại Việt Nam đã tăng 65% chỉ trong nửa đầu năm 2026. Nguyên nhân chính đến từ trải nghiệm xem không quảng cáo, tải video offline cho các chuyến đi xa và đặc biệt là tính năng phát nhạc nền YouTube Music miễn phí kèm theo. Ngoài ra, các gói gia đình giúp tiết kiệm tới 70% chi phí so với mua riêng lẻ, phù hợp với nhóm bạn hoặc gia đình muốn dùng chung một tài khoản chính chủ.'
            ]
        ];

        $count = $pdo->query("SELECT COUNT(*) FROM blogs")->fetchColumn();
        if ((int)$count === 0) {
            $stmt = $pdo->prepare("INSERT INTO blogs (title, image, description) VALUES (?, ?, ?)");
            foreach ($blogs as $blog) {
                $stmt->execute([$blog['title'], $blog['image'], $blog['description']]);
            }
        }
    }

    public function down(PDO $pdo) {
        $pdo->exec("DROP TABLE IF EXISTS blogs;");
    }
};
