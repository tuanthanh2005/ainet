<?php

class AdminController extends Controller {
    public function __construct() {
        Auth::requireAdmin();
    }

    public function admin() {
        header('Location: ' . url('index.php?action=adminDashboard'));
        exit;
    }

    public function adminDashboard() {
        $db = Database::getInstance();
        
        // Calculate statistics via SQL aggregates (highly optimized, avoids loading all rows)
        $totalRevenue = (float) $db->query("SELECT SUM(amount) FROM orders WHERE status IN ('completed', 'processing')")->fetchColumn();
        $totalOrders = (int) $db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
        $pendingOrders = (int) $db->query("SELECT COUNT(*) FROM orders WHERE status IN ('pending', 'processing')")->fetchColumn();
        
        // Load first page of orders (limit 10)
        $stmt = $db->prepare("SELECT * FROM orders ORDER BY created_at DESC LIMIT 10");
        $stmt->execute();
        $firstPageOrders = $stmt->fetchAll();
        foreach ($firstPageOrders as &$o) {
            $decoded = !empty($o['delivered_items']) ? json_decode($o['delivered_items'], true) : [];
            $o['delivered_items'] = is_array($decoded) ? $decoded : [];
        }
        unset($o);

        $settings   = Setting::getAll();
        $products   = Product::getAll();
        $categories = Category::getAll();
        $blogs      = Blog::getAll();
        $users      = User::getAll();
        $contactMessages = ContactMessage::getAll();
        $unreadContacts = ContactMessage::countUnread();

        $this->view('admin/dashboard', [
            'settings'         => $settings,
            'products'         => $products,
            'categories'       => $categories,
            'orders'           => $firstPageOrders,
            'blogs'            => $blogs,
            'users'            => $users,
            'contactMessages'  => $contactMessages,
            'unreadContacts'   => $unreadContacts,
            'totalRevenue'     => $totalRevenue,
            'totalOrders'      => $totalOrders,
            'pendingOrders'    => $pendingOrders,
            'ordersTotalPages' => max(1, ceil($totalOrders / 10)),
            'currentUser'      => $_SESSION['user'],
        ]);
    }

    public function adminUpdateContactStatus() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonError('Method not allowed', 405);
        }

        $id = (int) ($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? '';
        if ($id <= 0 || !ContactMessage::updateStatus($id, $status)) {
            $this->jsonError('Không thể cập nhật tin liên hệ.');
        }

        $this->jsonSuccess(['unreadContacts' => ContactMessage::countUnread()]);
    }

    public function adminOrdersList() {
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $db = Database::getInstance();
        $total = (int) $db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
        $totalPages = max(1, ceil($total / $limit));

        $stmt = $db->prepare("SELECT * FROM orders ORDER BY created_at DESC LIMIT ? OFFSET ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $orders = $stmt->fetchAll();

        foreach ($orders as &$o) {
            $decoded = !empty($o['delivered_items']) ? json_decode($o['delivered_items'], true) : [];
            $o['delivered_items'] = is_array($decoded) ? $decoded : [];
        }
        unset($o);

        $this->jsonSuccess([
            'orders'       => $orders,
            'currentPage'  => $page,
            'totalPages'   => $totalPages,
            'totalOrders'  => $total
        ]);
    }

    public function adminSaveSettings() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonError('Method not allowed', 405);
        }

        $allowed = [
            'bannerText', 'zalo', 'footerDesc', 'heroDesc', 'socialLink', 'copyright',
            'sepay_active', 'sepay_mode', 'sepay_token', 'sepay_merchant_id',
            'sepay_api_key', 'bank_id', 'bank_account', 'bank_name',
            'about_title', 'about_desc', 'about_image', 'about_stat_value',
            'about_stat_label', 'about_features', 'contact_title', 'contact_desc',
            'contact_methods', 'social_links_json', 'terms_of_service', 'privacy_policy',
            'demo_payment_active',
            'telegram_bot_token', 'telegram_chat_id',
            'smtp_host', 'smtp_port', 'smtp_secure', 'smtp_from_name',
            'smtp_user', 'smtp_pass', 'smtp_from_email',
            'smtp_default_subject', 'smtp_default_body',
        ];

        $data = [];
        foreach ($allowed as $key) {
            if (isset($_POST[$key])) {
                $data[$key] = $_POST[$key];
            }
        }

        // Validate JSON-shaped fields to avoid stored garbage
        foreach (['about_features', 'contact_methods', 'social_links_json'] as $jsonKey) {
            if (isset($data[$jsonKey])) {
                if ($data[$jsonKey] === '') {
                    $data[$jsonKey] = '[]';
                    continue;
                }
                $decoded = json_decode($data[$jsonKey], true);
                if (!is_array($decoded)) {
                    $data[$jsonKey] = '[]';
                }
            }
        }

        // Sepay flags
        if (isset($data['sepay_active'])) {
            $data['sepay_active'] = ($data['sepay_active'] === '1') ? '1' : '0';
        }
        if (isset($data['demo_payment_active'])) {
            $data['demo_payment_active'] = ($data['demo_payment_active'] === '1') ? '1' : '0';
        }
        if (isset($data['sepay_mode'])) {
            if (!in_array($data['sepay_mode'], ['production', 'sandbox'], true)) {
                $data['sepay_mode'] = 'production';
            }
        }

        Setting::saveAll($data);

        $this->jsonSuccess();
    }

    public function adminSaveProduct() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonError('Method not allowed', 405);
        }

        $id       = trim($_POST['id'] ?? '');
        $title    = trim($_POST['title'] ?? '');
        $price    = (float) ($_POST['price'] ?? 0);
        $originalPrice = (float) ($_POST['original_price'] ?? 0);

        if ($title === '' || $price < 0) {
            $this->jsonError('Dữ liệu sản phẩm không hợp lệ.');
        }

        $variants = json_decode($_POST['variants'] ?? '[]', true);
        if (!is_array($variants)) {
            $variants = [];
        }

        $products = Product::getAll();

        $description = Upload::sanitizeHtml(trim((string) ($_POST['description'] ?? '')));

        $imageUrl = trim($_POST['image'] ?? '');
        if (!empty($_FILES['image_file']['name'])) {
            $stored = Upload::store($_FILES['image_file'], 'products', Upload::IMAGE_MIMES);
            $imageUrl = $stored['url'];
        }

        $data = [
            'id'            => $id ?: 'prod_' . time(),
            'title'         => $title,
            'category_slug' => $_POST['category'] ?? '',
            'category'      => $_POST['category_name'] ?? '',
            'price'         => $price,
            'original_price'=> $originalPrice > $price ? $originalPrice : 0,
            'status'        => in_array($_POST['status'] ?? '', ['active', 'out_of_stock', 'hidden'], true) ? $_POST['status'] : 'active',
            'image'         => $imageUrl,
            'feature_text'  => $_POST['desc'] ?? '',
            'card_features' => array_values(array_filter(array_map('trim', [
                $_POST['card_feature_1'] ?? '',
                $_POST['card_feature_2'] ?? '',
                $_POST['card_feature_3'] ?? '',
                $_POST['card_feature_4'] ?? '',
            ]), 'strlen')),
            'description'   => $description,
            'feature_icon'  => 'fa-box',
            'rating'        => 5,
            'sold_count'    => 0,
            'options'       => $variants,
            'is_upgrade'    => isset($_POST['is_upgrade']) ? (int) $_POST['is_upgrade'] : 0,
            'seo_title'       => trim($_POST['seo_title'] ?? ''),
            'seo_description' => trim($_POST['seo_description'] ?? ''),
            'seo_keywords'    => trim($_POST['seo_keywords'] ?? ''),
            'seo_slug'        => trim($_POST['seo_slug'] ?? ''),
        ];

        if ($id) {
            $found = false;
            foreach ($products as &$p) {
                if (($p['id'] ?? '') === $id) {
                    $data['sold_count'] = $p['sold_count'] ?? 0;
                    $data['rating']     = $p['rating'] ?? 5;
                    $p = $data;
                    $found = true;
                    break;
                }
            }
            unset($p);
            if (!$found) {
                $products[] = $data;
            }
        } else {
            $products[] = $data;
        }

        Product::saveAll($products);
        $indexing = IndexingService::submitUrl(Url::product($data), ($data['status'] ?? 'active') === 'hidden' ? 'URL_DELETED' : 'URL_UPDATED');
        $this->jsonSuccess(['indexing' => $indexing]);
    }

    public function adminDeleteProduct() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonError('Method not allowed', 405);
        }

        $id = $_POST['id'] ?? '';
        if ($id === '') {
            $this->jsonError('Thiếu mã sản phẩm.');
        }

        $products = Product::getAll();
        $deletedUrl = '';
        foreach ($products as $p) {
            if (($p['id'] ?? '') === $id) {
                $deletedUrl = Url::product($p);
                break;
            }
        }
        $products = array_values(array_filter($products, function ($p) use ($id) {
            return ($p['id'] ?? '') !== $id;
        }));
        Product::saveAll($products);

        $indexing = $deletedUrl !== '' ? IndexingService::submitUrl($deletedUrl, 'URL_DELETED') : null;
        $this->jsonSuccess(['indexing' => $indexing]);
    }

    public function adminSaveCategory() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonError('Method not allowed', 405);
        }

        $id   = $_POST['id'] ?? '';
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');

        if ($name === '' || $slug === '') {
            $this->jsonError('Tên và slug danh mục không được trống.');
        }

        $categories = Category::getAll();

        foreach ($categories as $existing) {
            $sameSlug = strtolower((string) ($existing['slug'] ?? '')) === strtolower($slug);
            $sameId = $id !== '' && (int) ($existing['id'] ?? 0) === (int) $id;
            if ($sameSlug && !$sameId) {
                $this->jsonError('Slug danh mục đã tồn tại. Vui lòng dùng slug khác.');
            }
        }

        $data = [
            'id'         => (int) ($id ?: time()),
            'name'       => $name,
            'slug'       => $slug,
            'is_pro'     => ($_POST['is_pro'] ?? '0') === '1',
            'icon'       => $_POST['icon'] ?? '',
            'icon_color' => $_POST['icon_color'] ?? '',
            'seo_title'       => trim($_POST['seo_title'] ?? ''),
            'seo_description' => trim($_POST['seo_description'] ?? ''),
            'seo_keywords'    => trim($_POST['seo_keywords'] ?? ''),
            'seo_slug'        => trim($_POST['seo_slug'] ?? ''),
        ];

        if ($id) {
            $found = false;
            foreach ($categories as &$c) {
                if ((int) ($c['id'] ?? 0) === (int) $id) {
                    $c = $data;
                    $found = true;
                    break;
                }
            }
            unset($c);
            if (!$found) {
                $categories[] = $data;
            }
        } else {
            $categories[] = $data;
        }

        try {
            Category::saveAll($categories);
        } catch (Throwable $e) {
            $this->jsonError('Không thể lưu danh mục: ' . $e->getMessage());
        }
        $categorySlug = trim((string) ($data['seo_slug'] ?: ($data['slug'] ?? '')));
        $indexing = $categorySlug !== '' ? IndexingService::submitUrl(Url::category($categorySlug), 'URL_UPDATED') : null;
        $this->jsonSuccess(['indexing' => $indexing]);
    }

    public function adminDeleteCategory() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonError('Method not allowed', 405);
        }

        $id = $_POST['id'] ?? '';
        if ($id === '') {
            $this->jsonError('Thiếu ID danh mục.');
        }

        $categories = Category::getAll();
        $deletedUrl = '';
        foreach ($categories as $cat) {
            if ((int) ($cat['id'] ?? 0) === (int) $id) {
                $slug = trim((string) ($cat['seo_slug'] ?: ($cat['slug'] ?? '')));
                $deletedUrl = $slug !== '' ? Url::category($slug) : '';
                break;
            }
        }
        $categories = array_values(array_filter($categories, function ($c) use ($id) {
            return (int) ($c['id'] ?? 0) !== (int) $id;
        }));
        Category::saveAll($categories);

        $indexing = $deletedUrl !== '' ? IndexingService::submitUrl($deletedUrl, 'URL_DELETED') : null;
        $this->jsonSuccess(['indexing' => $indexing]);
    }

    public function adminSaveBlog() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonError('Method not allowed', 405);
        }

        $id          = trim($_POST['id'] ?? '');
        $title       = trim($_POST['title'] ?? '');
        $imageUrl    = trim($_POST['image'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $content     = trim($_POST['content'] ?? '');

        if ($title === '') {
            $this->jsonError('Tiêu đề bài viết không được trống.');
        }

        // Image upload (file takes precedence over URL field)
        try {
            if (!empty($_FILES['image_file']['name'])) {
                $stored = Upload::store($_FILES['image_file'], 'blogs', Upload::IMAGE_MIMES);
                $imageUrl = $stored['url'];
            }
        } catch (Throwable $e) {
            $this->jsonError($e->getMessage());
        }

        $description = trim(strip_tags($description));
        $content = Upload::sanitizeHtml($content);

        $seoTitle       = trim($_POST['seo_title'] ?? '');
        $seoDescription = trim($_POST['seo_description'] ?? '');
        $seoKeywords    = trim($_POST['seo_keywords'] ?? '');
        $seoSlug        = trim($_POST['seo_slug'] ?? '');

        $db = Database::getInstance();
        if ($id !== '') {
            $stmt = $db->prepare('UPDATE blogs SET title = ?, image = ?, description = ?, content = ?, seo_title = ?, seo_description = ?, seo_keywords = ?, seo_slug = ? WHERE id = ?');
            $stmt->execute([$title, $imageUrl, $description, $content, $seoTitle ?: null, $seoDescription ?: null, $seoKeywords ?: null, $seoSlug ?: null, (int) $id]);
            $blogId = (int) $id;
        } else {
            $stmt = $db->prepare('INSERT INTO blogs (title, image, description, content, seo_title, seo_description, seo_keywords, seo_slug) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$title, $imageUrl, $description, $content, $seoTitle ?: null, $seoDescription ?: null, $seoKeywords ?: null, $seoSlug ?: null]);
            $blogId = (int) $db->lastInsertId();
        }

        $indexing = IndexingService::submitUrl(Url::blog([
            'id' => $blogId,
            'title' => $title,
            'seo_slug' => $seoSlug,
        ]), 'URL_UPDATED');
        $this->jsonSuccess(['image' => $imageUrl, 'indexing' => $indexing]);
    }

    public function adminDeleteBlog() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonError('Method not allowed', 405);
        }

        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            $this->jsonError('Thiếu ID bài viết.');
        }

        $blog = Blog::getById($id);
        $deletedUrl = $blog ? Url::blog($blog) : '';
        $db = Database::getInstance();
        $stmt = $db->prepare('DELETE FROM blogs WHERE id = ?');
        $stmt->execute([$id]);

        $indexing = $deletedUrl !== '' ? IndexingService::submitUrl($deletedUrl, 'URL_DELETED') : null;
        $this->jsonSuccess(['indexing' => $indexing]);
    }

    public function adminPushIndexAll() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonError('Method not allowed', 405);
        }

        $results = IndexingService::submitAllPublicUrls();
        $submitted = 0;
        foreach ($results as $result) {
            if (!empty($result['success'])) {
                $submitted++;
            }
        }

        $this->jsonSuccess([
            'submitted' => $submitted,
            'total' => count($results),
            'results' => $results,
        ]);
    }

    public function adminPushIndexUrls() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonError('Method not allowed', 405);
        }

        $rawUrls = $_POST['urls'] ?? '[]';
        $urls = json_decode((string) $rawUrls, true);
        if (!is_array($urls)) {
            $urls = preg_split('/\r\n|\r|\n/', (string) $rawUrls);
        }

        $siteHost = parse_url(URLROOT, PHP_URL_HOST);
        $cleanUrls = [];
        foreach ($urls as $url) {
            $url = trim((string) $url);
            if ($url === '') {
                continue;
            }
            if (strpos($url, '/') === 0) {
                $url = url($url);
            }
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                continue;
            }
            $host = parse_url($url, PHP_URL_HOST);
            if ($siteHost && $host !== $siteHost) {
                continue;
            }
            $cleanUrls[] = $url;
        }

        $cleanUrls = array_values(array_unique($cleanUrls));
        if (empty($cleanUrls)) {
            $this->jsonError('Không có URL hợp lệ để index.');
        }

        $results = IndexingService::submitUrls($cleanUrls, 'URL_UPDATED');
        $submitted = 0;
        foreach ($results as $result) {
            if (!empty($result['success'])) {
                $submitted++;
            }
        }

        $this->jsonSuccess([
            'submitted' => $submitted,
            'total' => count($results),
            'results' => $results,
        ]);
    }



    /** Telegram: test bot configuration */
    public function adminTelegramTest() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonError('Method not allowed', 405);
        }
        $result = TelegramService::sendTest();
        if ($result['success']) {
            $this->jsonSuccess(['message' => $result['message']]);
        } else {
            $this->jsonError($result['message']);
        }
    }

    /** Stock — list items for one variant */
    public function adminStockList() {
        $productId = $_GET['product_id'] ?? '';
        $variantIdx = (int) ($_GET['variant_idx'] ?? 0);
        if ($productId === '') {
            $this->jsonError('Thiếu product_id.');
        }
        $items = Stock::listForVariant($productId, $variantIdx);
        $available = Stock::countAvailable($productId, $variantIdx);
        $this->jsonSuccess([
            'items'     => $items,
            'available' => $available,
        ]);
    }

    /** Stock — bulk add from textarea (one line = one unit) */
    public function adminStockAdd() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonError('Method not allowed', 405);
        }
        $productId = $_POST['product_id'] ?? '';
        $variantIdx = (int) ($_POST['variant_idx'] ?? 0);
        $raw = (string) ($_POST['lines'] ?? '');
        if ($productId === '') {
            $this->jsonError('Thiếu product_id.');
        }
        $lines = preg_split('/\r\n|\r|\n/', $raw);
        $added = Stock::bulkAdd($productId, $variantIdx, $lines);
        if ($added === 0) {
            $this->jsonError('Không có dòng hợp lệ.');
        }
        $this->jsonSuccess([
            'added'     => $added,
            'available' => Stock::countAvailable($productId, $variantIdx),
        ]);
    }

    public function adminStockDelete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonError('Method not allowed', 405);
        }
        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            $this->jsonError('Thiếu ID.');
        }
        Stock::delete($id);
        $this->jsonSuccess();
    }

    public function adminUpdateOrderStatus() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonError('Method not allowed', 405);
        }

        $id     = $_POST['id'] ?? '';
        $status = $_POST['status'] ?? '';
        if ($id === '' || !in_array($status, ['pending', 'completed', 'processing', 'cancelled'], true)) {
            $this->jsonError('Dữ liệu không hợp lệ.');
        }

        if (Order::updateStatus($id, $status)) {
            $this->jsonSuccess();
        } else {
            $this->jsonError('Không thể cập nhật trạng thái.');
        }
    }

    public function adminUpdateOrderDelivery() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonError('Method not allowed', 405);
        }

        $id   = $_POST['id'] ?? '';
        $raw  = $_POST['lines'] ?? '';
        if ($id === '') {
            $this->jsonError('Thiếu mã đơn hàng.');
        }

        $lines = preg_split('/\r\n|\r|\n/', $raw);
        $items = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line !== '') {
                $items[] = $line;
            }
        }

        // Save delivered items and set status to completed
        Order::setDelivered($id, $items);
        Order::updateStatus($id, 'completed');

        // Check if email sending is requested
        $sendEmail = ($_POST['send_email'] ?? '0') === '1';
        if ($sendEmail) {
            $emailFrom = trim($_POST['email_from'] ?? '');
            $emailTo = trim($_POST['email_to'] ?? '');
            $emailSubject = trim($_POST['email_subject'] ?? '');
            $emailBody = trim($_POST['email_body'] ?? '');

            if ($emailTo === '' || !filter_var($emailTo, FILTER_VALIDATE_EMAIL)) {
                $this->jsonError('Email nhận không hợp lệ.');
            }

            // Retrieve SMTP settings
            $settings = Setting::getAll();
            $host = $settings['smtp_host'] ?? '';
            $port = (int)($settings['smtp_port'] ?? 465);
            $secure = $settings['smtp_secure'] ?? 'ssl';
            $user = $settings['smtp_user'] ?? '';
            $pass = $settings['smtp_pass'] ?? '';
            $fromName = $settings['smtp_from_name'] ?? 'AI CỦA TÔI';

            if (!$host || !$user || !$pass) {
                $this->jsonSuccess([
                    'warning' => 'Đã lưu thông tin bàn giao nhưng không thể gửi Email do cấu hình SMTP chưa hoàn tất.'
                ]);
                return;
            }

            // Prepare attachments
            $attachments = [];
            $tempFilesToClean = [];
            for ($i = 1; $i <= 2; $i++) {
                $fileKey = "email_img" . $i;
                if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] === UPLOAD_ERR_OK) {
                    try {
                        $stored = Upload::store($_FILES[$fileKey], 'email_attachments', Upload::IMAGE_MIMES);
                        $absolutePath = Upload::publicDiskPath($stored['path']);
                        
                        $attachments[] = [
                            'path' => $absolutePath,
                            'name' => $_FILES[$fileKey]['name'],
                            'type' => $_FILES[$fileKey]['type']
                        ];
                        $tempFilesToClean[] = $absolutePath;
                    } catch (Throwable $e) {
                        error_log("Failed to process attachment $fileKey: " . $e->getMessage());
                    }
                }
            }

            // Prepare body HTML - convert text newlines to HTML line breaks
            $bodyHtml = nl2br(htmlspecialchars($emailBody));
            $bodyHtml = "
                <div style='font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e5e7eb; border-radius: 12px;'>
                    <h2 style='color: #111; border-bottom: 2px solid #111; padding-bottom: 10px; margin-top: 0;'>BÀN GIAO ĐƠN HÀNG</h2>
                    <div style='background-color: #f9fafb; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #10b981;'>
                        " . $bodyHtml . "
                    </div>
                    <p style='font-size: 12px; color: #666; text-align: center; margin-top: 30px; border-top: 1px solid #e5e7eb; padding-top: 15px;'>
                        Thư này được gửi tự động từ hệ thống " . SITENAME . ". Vui lòng không trả lời trực tiếp email này.
                    </p>
                </div>
            ";

            require_once APP_ROOT . '/app/Core/SmtpMailer.php';
            $mailer = new SmtpMailer($host, $port, $secure, $user, $pass);

            // Use the sender email specified in the form, fall back to setting's from email
            $fromEmail = $emailFrom !== '' ? $emailFrom : ($settings['smtp_from_email'] ?? $user);

            $sendResult = $mailer->send($fromEmail, $fromName, $emailTo, $emailSubject, $bodyHtml, $attachments);

            // Clean up temporary attachments
            foreach ($tempFilesToClean as $file) {
                if (is_file($file)) {
                    @unlink($file);
                }
            }

            if (!$sendResult) {
                $errors = implode(', ', $mailer->getErrors());
                $this->jsonSuccess([
                    'warning' => 'Đã lưu thông tin bàn giao nhưng gửi Email thất bại: ' . $errors
                ]);
                return;
            }
        }

        $this->jsonSuccess();
    }

    public function adminSmtpTest() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonError('Method not allowed', 405);
        }

        $toEmail = trim($_POST['test_email'] ?? '');
        if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            $this->jsonError('Địa chỉ email không hợp lệ.');
        }

        $settings = Setting::getAll();
        $host = $settings['smtp_host'] ?? '';
        $port = (int)($settings['smtp_port'] ?? 465);
        $secure = $settings['smtp_secure'] ?? 'ssl';
        $user = $settings['smtp_user'] ?? '';
        $pass = $settings['smtp_pass'] ?? '';
        $fromEmail = $settings['smtp_from_email'] ?? '';
        $fromName = $settings['smtp_from_name'] ?? 'AI CỦA TÔI';

        if (!$host || !$user || !$pass || !$fromEmail) {
            $this->jsonError('Vui lòng lưu đầy đủ cấu hình SMTP trước khi test.');
        }

        require_once APP_ROOT . '/app/Core/SmtpMailer.php';
        $mailer = new SmtpMailer($host, $port, $secure, $user, $pass);
        
        $subject = "Kiểm tra kết nối SMTP - " . SITENAME;
        $body = "<h3>Kết nối SMTP hoạt động tốt!</h3><p>Thư này được gửi lúc " . date('Y-m-d H:i:s') . " để kiểm tra cấu hình email của hệ thống.</p>";

        if ($mailer->send($fromEmail, $fromName, $toEmail, $subject, $body)) {
            $this->jsonSuccess(['message' => 'Email thử nghiệm đã được gửi thành công!']);
        } else {
            $errors = implode(', ', $mailer->getErrors());
            $this->jsonError('Gửi email thất bại: ' . $errors);
        }
    }

    public function adminSmtpTestDiag() {
        error_reporting(E_ALL);
        ini_set('display_errors', '1');

        echo "<h1>SMTP Diagnostic Page (Admin Mode)</h1>";
        echo "APP_ROOT resolved to: " . htmlspecialchars(APP_ROOT) . "<br>";
        
        echo "Loading Setting Model...<br>";
        $settings = Setting::getAll();
        echo "Settings loaded successfully!<br>";

        echo "Loading SmtpMailer...<br>";
        try {
            require_once APP_ROOT . '/app/Core/SmtpMailer.php';
            echo "SmtpMailer class loaded successfully!<br>";
        } catch (Throwable $e) {
            echo "<b>Fatal Error loading SmtpMailer:</b> " . htmlspecialchars($e->getMessage()) . "<br>";
            echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
            exit;
        }

        $host = $settings['smtp_host'] ?? '';
        $port = (int)($settings['smtp_port'] ?? 465);
        $secure = $settings['smtp_secure'] ?? 'ssl';
        $user = $settings['smtp_user'] ?? '';
        $pass = $settings['smtp_pass'] ?? '';
        $fromEmail = $settings['smtp_from_email'] ?? '';
        $fromName = $settings['smtp_from_name'] ?? 'Diagnostics Test';

        echo "<h3>SMTP Credentials Loaded:</h3>";
        echo "Host: " . htmlspecialchars($host) . "<br>";
        echo "Port: " . htmlspecialchars($port) . "<br>";
        echo "Secure: " . htmlspecialchars($secure) . "<br>";
        echo "User: " . htmlspecialchars($user) . "<br>";
        echo "From Email: " . htmlspecialchars($fromEmail) . "<br>";
        echo "Password configured: " . (empty($pass) ? "NO" : "YES") . "<br>";

        if (!$host || !$user || !$pass || !$fromEmail) {
            echo "<b style='color:red;'>Error:</b> SMTP settings are not fully configured. Please configure them in your .env or via Admin dashboard.<br>";
            exit;
        }

        echo "<h3>Sending diagnostic email to: " . htmlspecialchars($user) . "</h3>";
        try {
            $mailer = new SmtpMailer($host, $port, $secure, $user, $pass);
            $subject = "Diagnostics SMTP test - " . SITENAME;
            $body = "<h2>Success!</h2><p>Your SMTP mailer is fully operational on Hostinger server.</p><p>Time: " . date('Y-m-d H:i:s') . "</p>";
            
            $result = $mailer->send($fromEmail, $fromName, $user, $subject, $body);
            if ($result) {
                echo "<b style='color:green;'>SUCCESS!</b> The email was successfully sent. Please check your inbox (and spam folder) for: " . htmlspecialchars($user) . "<br>";
            } else {
                echo "<b style='color:red;'>FAILED!</b> SMTP Mailer returned false.<br>";
                echo "<h4>Mailer Errors:</h4>";
                echo "<pre style='background:#f4f4f4;padding:10px;border:1px solid #ccc;'>" . htmlspecialchars(print_r($mailer->getErrors(), true)) . "</pre>";
            }
        } catch (Throwable $e) {
            echo "<b style='color:red;'>Exception Caught:</b> " . htmlspecialchars($e->getMessage()) . "<br>";
            echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        }
        exit;
    }

    public function adminViewLogs() {
        error_reporting(E_ALL);
        ini_set('display_errors', '1');

        echo "<h1>PHP Error Logs (Admin only)</h1>";
        
        $logFiles = [
            'Project Log (storage/logs/php_errors.log)' => APP_ROOT . '/storage/logs/php_errors.log',
            'System Temp Log (aicualtoi_php_errors.log)' => rtrim(sys_get_temp_dir(), '/\\') . DIRECTORY_SEPARATOR . 'aicualtoi_php_errors.log',
        ];

        foreach ($logFiles as $name => $path) {
            echo "<h3>$name: " . htmlspecialchars($path) . "</h3>";
            if (is_file($path)) {
                if (filesize($path) > 0) {
                    $content = file_get_contents($path);
                    $lines = explode("\n", $content);
                    $lastLines = array_slice($lines, -100);
                    echo "<pre style='background:#f4f4f4;padding:10px;border:1px solid #ccc;max-height:400px;overflow:auto;'>" . htmlspecialchars(implode("\n", $lastLines)) . "</pre>";
                } else {
                    echo "<p>Log file is empty.</p>";
                }
            } else {
                echo "<p style='color:red;'>File does not exist or is not readable.</p>";
            }
        }
        exit;
    }

    public function adminSaveUser() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonError('Method not allowed', 405);
        }

        $id = (int) ($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $role = $_POST['role'] ?? 'user';
        $status = $_POST['status'] ?? 'active';

        if ($id <= 0 || $name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->jsonError('Dữ liệu user không hợp lệ.');
        }
        if (!in_array($role, ['admin', 'user'], true) || !in_array($status, ['active', 'blocked'], true)) {
            $this->jsonError('Quyền hoặc trạng thái không hợp lệ.');
        }

        $currentAdminId = (int) ($_SESSION['user']['id'] ?? 0);
        if ($id === $currentAdminId && ($role !== 'admin' || $status !== 'active')) {
            $this->jsonError('Không thể tự hạ quyền hoặc block tài khoản admin đang đăng nhập.');
        }

        $existing = User::findByEmail($email);
        if ($existing && (int) ($existing['id'] ?? 0) !== $id) {
            $this->jsonError('Email này đã được user khác sử dụng.');
        }

        if (!User::updateProfileByAdmin($id, $name, $email, $role, $status)) {
            $this->jsonError('Không thể cập nhật user.');
        }

        $this->jsonSuccess(['user' => User::findById($id)]);
    }

    public function adminToggleUserStatus() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonError('Method not allowed', 405);
        }

        $id = (int) ($_POST['id'] ?? 0);
        $user = $id > 0 ? User::findById($id) : null;
        if (!$user) {
            $this->jsonError('User không tồn tại.');
        }
        if ($id === (int) ($_SESSION['user']['id'] ?? 0)) {
            $this->jsonError('Không thể block tài khoản admin đang đăng nhập.');
        }

        $nextStatus = (($user['status'] ?? 'active') === 'active') ? 'blocked' : 'active';
        if (!User::updateStatus($id, $nextStatus)) {
            $this->jsonError('Không thể cập nhật trạng thái user.');
        }

        $this->jsonSuccess(['status' => $nextStatus]);
    }

    public function adminResetUserPassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonError('Method not allowed', 405);
        }

        $id = (int) ($_POST['id'] ?? 0);
        $user = $id > 0 ? User::findById($id) : null;
        if (!$user) {
            $this->jsonError('User không tồn tại.');
        }

        $password = $this->randomPassword();
        if (!User::updatePassword($id, $password)) {
            $this->jsonError('Không thể reset mật khẩu.');
        }

        $this->jsonSuccess([
            'password' => $password,
            'email'    => $user['email'] ?? '',
            'name'     => $user['name'] ?? '',
        ]);
    }

    public function adminGetKeywords() {
        $path = APP_ROOT . '/config/seo_keywords.json';
        $data = ['keywords' => [], 'aliases' => []];
        if (file_exists($path)) {
            $decoded = json_decode(file_get_contents($path), true);
            if (is_array($decoded)) {
                $data = $decoded;
            }
        }
        $this->jsonSuccess($data);
    }

    public function adminSaveKeyword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonError('Method not allowed', 405);
        }

        $slug = strtolower(trim($_POST['slug'] ?? ''));
        $oldSlug = strtolower(trim($_POST['old_slug'] ?? ''));
        $displayName = trim($_POST['display_name'] ?? '');
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        
        $keywordsRaw = trim($_POST['keywords'] ?? '');
        $keywords = array_values(array_unique(array_filter(array_map('trim', explode(',', $keywordsRaw)))));

        $aliasesRaw = trim($_POST['aliases'] ?? '');
        $aliasesInput = array_values(array_unique(array_filter(array_map('trim', explode(',', $aliasesRaw)))));

        if ($slug === '' || $displayName === '') {
            $this->jsonError('Từ khóa và Tên hiển thị không được trống.');
        }

        if (!preg_match('/^[a-z0-9\-]+$/', $slug)) {
            $this->jsonError('Từ khóa chỉ được chứa chữ thường không dấu, số và dấu gạch ngang.');
        }

        $path = APP_ROOT . '/config/seo_keywords.json';
        $seoData = ['keywords' => [], 'aliases' => []];
        if (file_exists($path)) {
            $decoded = json_decode(file_get_contents($path), true);
            if (is_array($decoded)) {
                $seoData = $decoded;
            }
        }

        if (!isset($seoData['keywords'])) {
            $seoData['keywords'] = [];
        }
        if (!isset($seoData['aliases'])) {
            $seoData['aliases'] = [];
        }

        // If renaming, remove old slug first
        if ($oldSlug !== '' && $oldSlug !== $slug) {
            unset($seoData['keywords'][$oldSlug]);
            // Clean up any old aliases pointing to the old slug
            foreach ($seoData['aliases'] as $aliasKey => $targetSlug) {
                if ($targetSlug === $oldSlug) {
                    unset($seoData['aliases'][$aliasKey]);
                }
            }
        }

        // Add/Update the keyword definition
        $seoData['keywords'][$slug] = [
            'display_name' => $displayName,
            'title' => $title ?: 'Tài khoản ' . ucwords(str_replace('-', ' ', $slug)) . ' giá rẻ - Mua bán ' . ucwords(str_replace('-', ' ', $slug)) . ' tự động',
            'description' => $description ?: 'Cung cấp tài khoản ' . str_replace('-', ' ', $slug) . ' giá rẻ, chính chủ, kích hoạt tự động 24/7. Bảo hành 1 đổi 1.',
            'keywords' => $keywords ?: ['tài khoản ' . str_replace('-', ' ', $slug) . ' giá rẻ', str_replace('-', ' ', $slug)]
        ];

        // Clean existing aliases pointing to this slug
        foreach ($seoData['aliases'] as $aliasKey => $targetSlug) {
            if ($targetSlug === $slug) {
                unset($seoData['aliases'][$aliasKey]);
            }
        }

        // Add new aliases
        foreach ($aliasesInput as $alias) {
            $alias = strtolower(trim($alias));
            if ($alias === '') continue;
            // Prevent mapping an alias that is already an existing primary keyword
            if (isset($seoData['keywords'][$alias]) && $alias !== $slug) {
                continue;
            }
            $seoData['aliases'][$alias] = $slug;
        }

        if (file_put_contents($path, json_encode($seoData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) === false) {
            $this->jsonError('Không thể ghi file seo_keywords.json.');
        }

        // Notify google indexing of new keyword page update
        $indexing = IndexingService::submitUrl(Url::search($slug), 'URL_UPDATED');

        $this->jsonSuccess(['indexing' => $indexing]);
    }

    public function adminDeleteKeyword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonError('Method not allowed', 405);
        }

        $slug = strtolower(trim($_POST['slug'] ?? ''));
        if ($slug === '') {
            $this->jsonError('Thiếu từ khóa cần xóa.');
        }

        $path = APP_ROOT . '/config/seo_keywords.json';
        if (!file_exists($path)) {
            $this->jsonError('Không tìm thấy file cấu hình từ khóa.');
        }

        $seoData = json_decode(file_get_contents($path), true);
        if (!is_array($seoData)) {
            $this->jsonError('File cấu hình lỗi.');
        }

        if (isset($seoData['keywords'][$slug])) {
            unset($seoData['keywords'][$slug]);
        }

        // Clean up aliases pointing to this slug
        if (isset($seoData['aliases']) && is_array($seoData['aliases'])) {
            foreach ($seoData['aliases'] as $aliasKey => $targetSlug) {
                if ($targetSlug === $slug) {
                    unset($seoData['aliases'][$aliasKey]);
                }
            }
        }

        if (file_put_contents($path, json_encode($seoData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) === false) {
            $this->jsonError('Không thể cập nhật cấu hình.');
        }

        // Notify google indexing of page removal
        $indexing = IndexingService::submitUrl(Url::search($slug), 'URL_DELETED');

        $this->jsonSuccess(['indexing' => $indexing]);
    }

    public function adminPushIndexByType() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonError('Method not allowed', 405);
        }

        $type = $_POST['type'] ?? '';
        if (!in_array($type, ['all', 'products', 'categories', 'blogs', 'keywords'], true)) {
            $this->jsonError('Loại tài nguyên không hợp lệ.');
        }

        if ($type === 'all') {
            $results = IndexingService::submitAllPublicUrls();
            $submitted = 0;
            foreach ($results as $r) {
                if (!empty($r['success'])) $submitted++;
            }
            $this->jsonSuccess(['submitted' => $submitted, 'total' => count($results), 'results' => $results]);
        }

        $urls = [];
        $base = rtrim(URLROOT, '/');

        if ($type === 'products') {
            foreach (Product::getAll() as $product) {
                if (($product['status'] ?? 'active') !== 'hidden') {
                    $urls[] = Url::product($product);
                }
            }
        } elseif ($type === 'categories') {
            foreach (Category::getAll() as $cat) {
                $slug = trim((string) ($cat['seo_slug'] ?: ($cat['slug'] ?? '')));
                if ($slug !== '') {
                    $urls[] = Url::category($slug);
                }
            }
        } elseif ($type === 'blogs') {
            foreach (Blog::getAll() as $blog) {
                $urls[] = Url::blog($blog);
            }
        } elseif ($type === 'keywords') {
            $path = APP_ROOT . '/config/seo_keywords.json';
            if (file_exists($path)) {
                $seoData = json_decode(file_get_contents($path), true);
                $keywords = isset($seoData['keywords']) ? array_keys($seoData['keywords']) : [];
                foreach ($keywords as $kw) {
                    $urls[] = Url::search($kw);
                }
            }
        }

        $urls = array_values(array_unique(array_filter($urls)));
        if (empty($urls)) {
            $this->jsonSuccess(['submitted' => 0, 'total' => 0, 'results' => []]);
        }

        $results = IndexingService::submitUrls($urls, 'URL_UPDATED');
        $submitted = 0;
        foreach ($results as $r) {
            if (!empty($r['success'])) $submitted++;
        }

        $this->jsonSuccess(['submitted' => $submitted, 'total' => count($results), 'results' => $results]);
    }

    public function adminSaveKeywordsBulk() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonError('Method not allowed', 405);
        }

        $keywordsJson = $_POST['keywords_json'] ?? '';
        $list = json_decode($keywordsJson, true);
        if (!is_array($list)) {
            $this->jsonError('Dữ liệu không hợp lệ.');
        }

        $replaceAll = ($_POST['replace_all'] ?? '0') === '1';

        $path = APP_ROOT . '/config/seo_keywords.json';
        $seoData = ['keywords' => [], 'aliases' => []];
        if (!$replaceAll && file_exists($path)) {
            $decoded = json_decode(file_get_contents($path), true);
            if (is_array($decoded)) {
                $seoData = $decoded;
            }
        }

        if (!isset($seoData['keywords'])) {
            $seoData['keywords'] = [];
        }
        if (!isset($seoData['aliases'])) {
            $seoData['aliases'] = [];
        }

        $updatedSlugs = [];

        foreach ($list as $item) {
            $slug = strtolower(trim($item['slug'] ?? ''));
            $displayName = trim($item['display_name'] ?? '');
            
            if ($slug === '' || $displayName === '') {
                continue;
            }

            if (!preg_match('/^[a-z0-9\-]+$/', $slug)) {
                continue;
            }

            $title = trim($item['title'] ?? '');
            $description = trim($item['description'] ?? '');

            $keywordsRaw = trim($item['keywords'] ?? '');
            $keywords = array_values(array_filter(array_map('trim', explode(',', $keywordsRaw))));

            $aliasesRaw = trim($item['aliases'] ?? '');
            $aliasesInput = array_values(array_filter(array_map('trim', explode(',', $aliasesRaw))));

            $seoData['keywords'][$slug] = [
                'display_name' => $displayName,
                'title' => $title ?: 'Tài khoản ' . ucwords(str_replace('-', ' ', $slug)) . ' giá rẻ - Mua bán ' . ucwords(str_replace('-', ' ', $slug)) . ' tự động',
                'description' => $description ?: 'Cung cấp tài khoản ' . str_replace('-', ' ', $slug) . ' giá rẻ, chính chủ, kích hoạt tự động 24/7. Bảo hành 1 đổi 1.',
                'keywords' => $keywords ?: ['tài khoản ' . str_replace('-', ' ', $slug) . ' giá rẻ', str_replace('-', ' ', $slug)]
            ];

            foreach ($seoData['aliases'] as $aliasKey => $targetSlug) {
                if ($targetSlug === $slug) {
                    unset($seoData['aliases'][$aliasKey]);
                }
            }

            foreach ($aliasesInput as $alias) {
                $alias = strtolower(trim($alias));
                if ($alias === '') continue;
                if (isset($seoData['keywords'][$alias]) && $alias !== $slug) {
                    continue;
                }
                $seoData['aliases'][$alias] = $slug;
            }

            $updatedSlugs[] = $slug;
        }

        if (file_put_contents($path, json_encode($seoData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) === false) {
            $this->jsonError('Không thể ghi file seo_keywords.json.');
        }

        $indexingResults = [];
        foreach ($updatedSlugs as $slug) {
            try {
                $indexingResults[$slug] = IndexingService::submitUrl(Url::search($slug), 'URL_UPDATED');
            } catch (Throwable $e) {
                $indexingResults[$slug] = ['success' => false, 'message' => $e->getMessage()];
            }
        }

        $this->jsonSuccess(['indexing_results' => $indexingResults, 'count' => count($updatedSlugs)]);
    }

    private function randomPassword(int $length = 12): string {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789@#$%';
        $max = strlen($alphabet) - 1;
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $alphabet[random_int(0, $max)];
        }
        return $password;
    }

    public function adminSystemIndexing() {
        $db = Database::getInstance();
        $pendingOrders = (int) $db->query("SELECT COUNT(*) FROM orders WHERE status IN ('pending', 'processing')")->fetchColumn();
        $unreadContacts = ContactMessage::countUnread();

        $this->view('admin/indexing_report', [
            'currentUser' => $_SESSION['user'],
            'pendingOrders' => $pendingOrders,
            'unreadContacts' => $unreadContacts,
            'google_indexing_enabled' => filter_var(getenv('GOOGLE_INDEXING_ENABLED') ?: 'false', FILTER_VALIDATE_BOOLEAN),
            'google_indexing_credentials' => trim((string) (getenv('GOOGLE_INDEXING_CREDENTIALS') ?: ''))
        ]);
    }

    public function adminGetAllUrls() {
        $urls = [
            Url::home(),
            Url::products(),
            Url::blogs(),
            Url::about(),
            Url::contact(),
            url('sitemap.xml'),
        ];

        try {
            foreach (Category::getAll() as $cat) {
                $slug = trim((string) ($cat['seo_slug'] ?: ($cat['slug'] ?? '')));
                if ($slug !== '') {
                    $urls[] = Url::category($slug);
                }
            }
        } catch (Throwable $e) {}

        try {
            foreach (Product::getAll() as $product) {
                if (($product['status'] ?? 'active') !== 'hidden') {
                    $urls[] = Url::product($product);
                }
            }
        } catch (Throwable $e) {}

        try {
            foreach (Blog::getAll() as $blog) {
                $urls[] = Url::blog($blog);
            }
        } catch (Throwable $e) {}

        try {
            $keywordPath = base_path('config/seo_keywords.json');
            if (file_exists($keywordPath)) {
                $seoData = json_decode(file_get_contents($keywordPath), true);
                if (isset($seoData['keywords']) && is_array($seoData['keywords'])) {
                    foreach (array_keys($seoData['keywords']) as $kw) {
                        $urls[] = Url::search($kw);
                    }
                }
            }
        } catch (Throwable $e) {}

        $urls = array_values(array_unique(array_filter($urls)));
        
        $categorized = [];
        foreach ($urls as $url) {
            $type = 'Khác';
            if ($url === Url::home()) {
                $type = 'Trang chủ';
            } elseif ($url === Url::products()) {
                $type = 'Danh sách sản phẩm';
            } elseif ($url === Url::blogs()) {
                $type = 'Trang tin tức';
            } elseif ($url === Url::about()) {
                $type = 'Giới thiệu';
            } elseif ($url === Url::contact()) {
                $type = 'Liên hệ';
            } elseif (strpos($url, '/sitemap.xml') !== false) {
                $type = 'Sitemap';
            } elseif (strpos($url, '/danh-muc/') !== false) {
                $type = 'Danh mục';
            } elseif (strpos($url, '/san-pham/') !== false) {
                $type = 'Sản phẩm';
            } elseif (strpos($url, '/tap-chi/') !== false) {
                $type = 'Tin tức';
            } elseif (strpos($url, '/tim-kiem/') !== false) {
                $type = 'SEO Keyword';
            }
            $categorized[] = [
                'url' => $url,
                'type' => $type
            ];
        }

        $this->jsonSuccess(['urls' => $categorized]);
    }

    public function adminPushIndexSingleUrl() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonError('Method not allowed', 405);
        }

        $url = trim($_POST['url'] ?? '');
        if ($url === '') {
            $this->jsonError('URL trống.');
        }

        $result = IndexingService::submitUrl($url, 'URL_UPDATED');
        if (!empty($result['success'])) {
            $this->jsonSuccess($result);
        } else {
            $this->jsonSuccess(array_merge(['success' => false], $result));
        }
    }

    public function adminGetIndexingLogs() {
        $logPath = base_path('storage/logs/indexing.log');
        if (!file_exists($logPath)) {
            $this->jsonSuccess(['logs' => 'Chưa có nhật ký indexing nào.']);
        }
        
        $content = @file_get_contents($logPath);
        if ($content === false) {
            $this->jsonSuccess(['logs' => 'Không thể đọc nhật ký.']);
        }
        
        $lines = explode("\n", trim($content));
        $lastLines = array_slice($lines, -150);
        $this->jsonSuccess(['logs' => implode("\n", $lastLines)]);
    }

    private function jsonSuccess(array $payload = []): void {
        header('Content-Type: application/json');
        echo json_encode(array_merge(['success' => true], $payload));
        exit;
    }

    private function jsonError(string $message, int $code = 400): void {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $message]);
        exit;
    }
}
