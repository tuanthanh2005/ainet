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
        $totalRevenue = (float) $db->query("SELECT SUM(amount) FROM orders WHERE status = 'completed'")->fetchColumn();
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
            'bannerText', 'zalo', 'footerDesc', 'socialLink', 'copyright',
            'sepay_active', 'sepay_mode', 'sepay_token', 'sepay_merchant_id',
            'sepay_api_key', 'bank_id', 'bank_account', 'bank_name',
            'about_title', 'about_desc', 'about_image', 'about_stat_value',
            'about_stat_label', 'about_features', 'contact_title', 'contact_desc',
            'contact_methods', 'social_links_json', 'terms_of_service', 'privacy_policy',
            'demo_payment_active',
            'telegram_bot_token', 'telegram_chat_id',
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

        $this->jsonSuccess();
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

    private function randomPassword(int $length = 12): string {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789@#$%';
        $max = strlen($alphabet) - 1;
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $alphabet[random_int(0, $max)];
        }
        return $password;
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
