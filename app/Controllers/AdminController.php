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
        $settings   = Setting::getAll();
        $products   = Product::getAll();
        $categories = Category::getAll();
        $orders     = Order::getAll();
        $blogs      = Blog::getAll();

        $this->view('admin/dashboard', [
            'settings'    => $settings,
            'products'    => $products,
            'categories'  => $categories,
            'orders'      => $orders,
            'blogs'       => $blogs,
            'currentUser' => $_SESSION['user'],
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
            $data[$key] = $_POST[$key] ?? '';
        }

        // Validate JSON-shaped fields to avoid stored garbage
        foreach (['about_features', 'contact_methods', 'social_links_json'] as $jsonKey) {
            if ($data[$jsonKey] === '') {
                $data[$jsonKey] = '[]';
                continue;
            }
            $decoded = json_decode($data[$jsonKey], true);
            if (!is_array($decoded)) {
                $data[$jsonKey] = '[]';
            }
        }

        // Sepay flags
        $data['sepay_active'] = ($data['sepay_active'] === '1') ? '1' : '0';
        $data['demo_payment_active'] = ($data['demo_payment_active'] === '1') ? '1' : '0';
        if (!in_array($data['sepay_mode'], ['production', 'sandbox'], true)) {
            $data['sepay_mode'] = 'production';
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

        if ($title === '' || $price < 0) {
            $this->jsonError('Dữ liệu sản phẩm không hợp lệ.');
        }

        $variants = json_decode($_POST['variants'] ?? '[]', true);
        if (!is_array($variants)) {
            $variants = [];
        }

        $products = Product::getAll();

        $description = Upload::sanitizeHtml(trim((string) ($_POST['description'] ?? '')));

        $data = [
            'id'            => $id ?: 'prod_' . time(),
            'title'         => $title,
            'category_slug' => $_POST['category'] ?? '',
            'category'      => $_POST['category_name'] ?? '',
            'price'         => $price,
            'status'        => in_array($_POST['status'] ?? '', ['active', 'out_of_stock', 'hidden'], true) ? $_POST['status'] : 'active',
            'image'         => $_POST['image'] ?? '',
            'feature_text'  => $_POST['desc'] ?? '',
            'description'   => $description,
            'feature_icon'  => 'fa-box',
            'rating'        => 5,
            'sold_count'    => 0,
            'options'       => $variants,
            'is_upgrade'    => isset($_POST['is_upgrade']) ? (int) $_POST['is_upgrade'] : 0,
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
        $this->jsonSuccess();
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
        $products = array_values(array_filter($products, function ($p) use ($id) {
            return ($p['id'] ?? '') !== $id;
        }));
        Product::saveAll($products);

        $this->jsonSuccess();
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
        $this->jsonSuccess();
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
        $categories = array_values(array_filter($categories, function ($c) use ($id) {
            return (int) ($c['id'] ?? 0) !== (int) $id;
        }));
        Category::saveAll($categories);

        $this->jsonSuccess();
    }

    public function adminSaveBlog() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonError('Method not allowed', 405);
        }

        $id          = trim($_POST['id'] ?? '');
        $title       = trim($_POST['title'] ?? '');
        $imageUrl    = trim($_POST['image'] ?? '');
        $description = trim($_POST['description'] ?? '');

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

        $description = Upload::sanitizeHtml($description);

        $db = Database::getInstance();
        if ($id !== '') {
            $stmt = $db->prepare('UPDATE blogs SET title = ?, image = ?, description = ? WHERE id = ?');
            $stmt->execute([$title, $imageUrl, $description, (int) $id]);
        } else {
            $stmt = $db->prepare('INSERT INTO blogs (title, image, description) VALUES (?, ?, ?)');
            $stmt->execute([$title, $imageUrl, $description]);
        }

        $this->jsonSuccess(['image' => $imageUrl]);
    }

    public function adminDeleteBlog() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonError('Method not allowed', 405);
        }

        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            $this->jsonError('Thiếu ID bài viết.');
        }

        $db = Database::getInstance();
        $stmt = $db->prepare('DELETE FROM blogs WHERE id = ?');
        $stmt->execute([$id]);

        $this->jsonSuccess();
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
