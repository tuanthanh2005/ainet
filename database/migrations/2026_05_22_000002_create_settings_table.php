<?php

return new class {
    public function up(PDO $pdo) {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS settings (
                setting_key VARCHAR(100) PRIMARY KEY,
                setting_value TEXT,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $settings = [
            'about_title' => 'Chào mừng đến với <span class="text-primary">AI CỦA TÔI</span>',
            'about_desc' => 'Chúng tôi là đơn vị hàng đầu cung cấp các giải pháp tài khoản Premium và dịch vụ AI cao cấp tại Việt Nam. Cam kết uy tín, bảo hành 1:1 trong suốt quá trình sử dụng.',
            'about_image' => 'https://images.unsplash.com/photo-1677442136019-21780ecad995?auto=format&fit=crop&q=80&w=1000',
            'about_stat_value' => '50K+',
            'about_stat_label' => "Khách hàng tin dùng\ntrên toàn quốc",
            'about_features' => '[{"icon":"fa-solid fa-bolt","color":"text-warning","title":"Nhanh chóng","desc":"Kích hoạt tài khoản trong 5-10 phút"},{"icon":"fa-solid fa-shield-check","color":"text-success","title":"Bảo hành","desc":"Cam kết 1:1 trọn thời gian sử dụng"},{"icon":"fa-solid fa-headset","color":"text-primary","title":"Hỗ trợ 24/7","desc":"Đội ngũ hỗ trợ trực tuyến mọi lúc"},{"icon":"fa-solid fa-tags","color":"text-danger","title":"Giá tốt nhất","desc":"Cạnh tranh nhất thị trường Việt Nam"}]',
            'contact_title' => 'Thông tin liên hệ',
            'contact_desc' => 'Đội ngũ hỗ trợ của AI CỦA TÔI luôn sẵn sàng giải đáp thắc mắc của bạn. Liên hệ ngay qua các kênh bên dưới.',
            'contact_methods' => '[{"icon":"fa-solid fa-envelope","text":"tetuongmmovn@gmail.com"},{"icon":"fa-brands fa-telegram","text":"@specademy"},{"icon":"fa-solid fa-phone","text":"Zalo: 0967037906"}]',
            'social_links_json' => '[{"icon":"fa-brands fa-facebook-f","url":"https://facebook.com"},{"icon":"fa-solid fa-comment-dots","url":"#"},{"icon":"fa-brands fa-tiktok","url":"#"},{"icon":"fa-brands fa-youtube","url":"#"}]',
            'footerDesc' => 'Hệ thống phân phối giải pháp phần mềm, tài khoản dịch vụ số nhanh chóng và uy tín.',
            'copyright' => '2026 AI CỦA TÔI',
            'terms_of_service' => "1. Chấp nhận điều khoản: Bằng cách sử dụng dịch vụ của chúng tôi, bạn đồng ý tuân thủ các điều khoản này.\n2. Dịch vụ: Chúng tôi cung cấp các tài khoản Premium và dịch vụ AI. Việc sử dụng phải tuân thủ quy định của nhà cung cấp gốc.\n3. Bảo hành: Cam kết bảo hành 1 đổi 1 trong suốt thời gian gói dịch vụ còn hiệu lực.\n4. Nghiêm cấm: Không sử dụng dịch vụ cho mục đích vi phạm pháp luật.",
            'privacy_policy' => "1. Thu thập thông tin: Chúng tôi chỉ thu thập email và họ tên để quản lý đơn hàng.\n2. Bảo mật: Mọi dữ liệu của bạn được mã hóa và bảo vệ tuyệt đối, không chia sẻ cho bên thứ ba.\n3. Quyền của bạn: Bạn có quyền yêu cầu xóa dữ liệu cá nhân bất cứ lúc nào qua kênh hỗ trợ.\n4. Cookie: Chúng tôi sử dụng cookie để duy trì phiên đăng nhập của bạn."
        ];

        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
        foreach ($settings as $key => $value) {
            $stmt->execute([$key, $value]);
        }
    }

    public function down(PDO $pdo) {
        $pdo->exec("DROP TABLE IF EXISTS settings;");
    }
};
