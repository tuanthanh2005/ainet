<?php

class Product extends Model {
    // Model mô phỏng việc lấy dữ liệu từ database
    public static function getAll() {
        return [
            [
                'id' => 'chatgpt-plus',
                'title' => 'Tài khoản ChatGPT Plus (1 Tháng)',
                'category' => 'ChatGPT',
                'category_slug' => 'chatgpt',
                'price' => '450.000đ',
                'image' => 'https://images.unsplash.com/photo-1675271591211-126ad94e4958?auto=format&fit=crop&q=80&w=400&h=250',
                'feature_icon' => 'fa-bolt',
                'feature_text' => 'Cấp tốc 5 phút',
                'badge' => 'Bán chạy',
                'description' => 'Trải nghiệm AI mạnh mẽ nhất với GPT-4, DALL-E 3 và khả năng phân tích dữ liệu nâng cao. Đảm bảo tốc độ phản hồi nhanh nhất.',
                'options' => ['Tài khoản cấp mới (Sử dụng mail của shop)', 'Gia hạn chính chủ (Sử dụng mail của bạn)']
            ],
            [
                'id' => 'youtube-premium',
                'title' => 'YouTube Premium (Mail Chính Chủ)',
                'category' => 'YouTube',
                'category_slug' => 'youtube',
                'price' => '250.000đ',
                'image' => 'https://images.unsplash.com/photo-1611162617474-5b21e879e113?auto=format&fit=crop&q=80&w=400&h=250',
                'feature_icon' => 'fa-shield-check',
                'feature_text' => 'Bảo hành trọn đời',
                'badge' => null,
                'description' => 'Xem video không quảng cáo, tải xuống video để xem ngoại tuyến và sử dụng YouTube Music Premium miễn phí.',
                'options' => ['Gói Gia đình (Family) - 6 Tháng', 'Gói Cá nhân - 1 Năm']
            ],
            [
                'id' => 'github-copilot',
                'title' => 'Github Copilot (Gói Dev 1 Năm)',
                'category' => 'GitHub',
                'category_slug' => 'github',
                'price' => '150.000đ',
                'image' => 'https://images.unsplash.com/photo-1618401471353-b98afee0b2eb?auto=format&fit=crop&q=80&w=400&h=250',
                'feature_icon' => 'fa-code',
                'feature_text' => 'Coding Assistant',
                'badge' => null,
                'description' => 'Trợ lý lập trình AI đỉnh cao từ GitHub. Hỗ trợ tự động hoàn thành code, gợi ý giải pháp và refactor code hiệu quả.',
                'options' => ['Gói Developer - Bảo hành 6 Tháng', 'Gói Business - Bảo hành 1 Năm']
            ],
            [
                'id' => 'netflix-premium',
                'title' => 'Netflix Premium 4K (1 Tháng)',
                'category' => 'Netflix',
                'category_slug' => 'netflix',
                'price' => '85.000đ',
                'image' => 'https://images.unsplash.com/photo-1522869635100-9f4c5e86aa37?auto=format&fit=crop&q=80&w=400&h=250',
                'feature_icon' => 'fa-lock',
                'feature_text' => 'Profile riêng tư',
                'badge' => null,
                'description' => 'Đắm chìm vào thế giới điện ảnh với chất lượng Ultra HD 4K sắc nét nhất. Profile riêng tư được bảo mật bằng mã PIN.',
                'options' => ['Profile Tiêu Chuẩn (Bảo hành 1 Tháng)', 'Profile Cao Cấp 4K (Bảo hành 3 Tháng)']
            ]
        ];
    }

    public static function getById($id) {
        $products = self::getAll();
        foreach ($products as $product) {
            if ($product['id'] === $id) {
                return $product;
            }
        }
        return null;
    }
}
?>
