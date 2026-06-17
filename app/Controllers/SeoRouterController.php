<?php

class SeoRouterController extends Controller {
    private $settings;

    public function __construct() {
        $this->settings = Setting::getAll();
    }

    /**
     * Item 2: SEO Placeholder & Pre-order Link Hijacking
     * Route: /go/{slug}
     */
    public function seoRedirect(): void {
        $slug = trim($_GET['slug'] ?? '');
        if ($slug === '') {
            header('Location: ' . url());
            exit;
        }

        // 1. Check if the product has been officially posted/published
        $product = Product::getBySlugOrId($slug);
        if ($product && ($product['status'] ?? 'active') !== 'hidden') {
            // Permanent 301 redirect to the official product page, transferring link juice
            header('Location: ' . Url::product($product), true, 301);
            exit;
        }

        // 2. Otherwise, serve a beautiful placeholder pre-order landing page
        $productTitle = ucwords(str_replace('-', ' ', $slug));
        
        $allProducts = Product::getAll();
        shuffle($allProducts);
        $relatedProducts = array_slice($allProducts, 0, 4);

        // Customize premium SEO tags for the placeholder page
        Seo::set([
            'title'       => $productTitle . ' Giá Rẻ - Đăng Ký Sớm Tự Động 24/7',
            'description' => 'Nhận thông tin đăng ký sớm và ưu đãi mua tài khoản ' . $productTitle . ' giá rẻ nhất thị trường. Hệ thống bàn giao tự động 24/7.',
            'keywords'    => ['tài khoản ' . mb_strtolower($productTitle) . ' giá rẻ', 'mua tài khoản ' . mb_strtolower($productTitle), mb_strtolower($productTitle), SITENAME],
            'canonical'   => url('go/' . $slug),
            'type'        => 'website',
            'robots'      => 'index,follow',
        ]);

        $this->view('layout', [
            'view'            => 'seo-placeholder',
            'slug'            => $slug,
            'productTitle'    => $productTitle,
            'relatedProducts' => $relatedProducts,
            'settings'        => $this->settings,
        ]);
    }

    /**
     * Submits a pre-order notification request
     */
    public function submitPreOrder(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url());
            exit;
        }

        $email = strtolower(trim($_POST['email'] ?? ''));
        $slug  = trim($_POST['slug'] ?? '');

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $slug === '') {
            $_SESSION['flash_error'] = 'Vui lòng cung cấp email hợp lệ.';
            header('Location: ' . url('go/' . $slug));
            exit;
        }

        // Utilizes existing ContactMessage table to store subscriptions safely
        $success = ContactMessage::create([
            'name'       => 'Khách hàng đăng ký sớm',
            'email'      => $email,
            'subject'    => 'Đăng ký nhận tin sản phẩm: ' . $slug,
            'message'    => "Khách hàng đăng ký nhận thông báo sớm khi sản phẩm với slug '{$slug}' chính thức mở bán trên hệ thống.",
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => mb_substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
        ]);

        if ($success) {
            $_SESSION['flash_success'] = 'Đăng ký nhận thông báo thành công! Chúng tôi sẽ liên hệ ngay khi mở bán.';
        } else {
            $_SESSION['flash_error'] = 'Có lỗi xảy ra, vui lòng thử lại sau.';
        }

        header('Location: ' . url('go/' . $slug));
        exit;
    }
}
