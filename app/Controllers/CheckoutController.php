<?php

class CheckoutController extends Controller {
    
    public function __construct() {
        if (($_GET['action'] ?? '') === 'sepayWebhook') {
            return;
        }

        if (!Auth::check()) {
            header('Location: ' . url());
            exit;
        }
    }

    // Trang nhập thông tin thanh toán (Mới)
    public function checkoutPage() {
        $productId = $_GET['product_id'] ?? '';
        $variantIdx = $_GET['variant_idx'] ?? 0;
        
        $product = Product::getById($productId);
        if (!$product) die("Sản phẩm không tồn tại.");
        
        $options = $product['options'] ?? [];
        $variant = $options[$variantIdx] ?? ($options[0] ?? null);
        if (!$variant) die("Gói dịch vụ không tồn tại.");
        
        $this->view('layout', [
            'view' => 'checkout/index',
            'product' => $product,
            'variant' => $variant,
            'settings' => Setting::getAll(),
            'currentUser' => $_SESSION['user']
        ]);
    }

    // Hàm tạo đơn hàng và hiển thị trang thanh toán
    public function checkout() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = $_POST['product_id'] ?? '';
            $variantIdx = (int) ($_POST['variant_idx'] ?? 0);
            $quantity = max(1, (int) ($_POST['quantity'] ?? 1));
            $email = $_POST['email'] ?? '';

            $product = Product::getById($productId);
            if (!$product) die("Sản phẩm không tồn tại.");

            $options = $product['options'] ?? [];
            $variant = $options[$variantIdx] ?? ($options[0] ?? null);
            if (!$variant) die("Gói dịch vụ không tồn tại.");

            $amount = (float)($variant['price'] ?? 0) * $quantity;
            $variantName = $variant['name'] ?? 'Mặc định';
            
            $orderId = 'AC' . date('ymd') . strtoupper(substr(uniqid(), -4));
            
            $orderData = [
                'id' => $orderId,
                'product_id' => $productId,
                'product_name' => $product['title'] ?? 'Sản phẩm',
                'variant_name' => $variantName,
                'variant_idx' => $variantIdx,
                'amount' => $amount,
                'quantity' => $quantity,
                'customer_email' => $email,
                'phone' => $_POST['phone'] ?? '',
                'note' => $_POST['note'] ?? '',
                'upgrade_email' => $_POST['upgrade_email'] ?? null,
                'upgrade_pass' => $_POST['upgrade_pass'] ?? null,
                'upgrade_link' => $_POST['upgrade_link'] ?? null
            ];
            
            if (Order::create($orderData)) {
                // Notify admin via Telegram — new pending order
                try {
                    $orderData['created_at'] = date('Y-m-d H:i:s');
                    $orderData['name'] = $_POST['name'] ?? '';
                    TelegramService::notifyNewOrder($orderData);
                } catch (Throwable $ignored) {}

                $_SESSION['last_customer_email'] = $email;
                header('Location: ' . url('index.php?action=payment&id=' . $orderId));
                exit;
            }
        }
        header('Location: ' . url('index.php'));
    }

    // Trang hiển thị QR Code thanh toán
    public function payment() {
        $orderId = $_GET['id'] ?? '';
        $order = Order::getById($orderId);
        if (!$order) die("Đơn hàng không tồn tại.");

        if (in_array($order['status'] ?? '', ['completed', 'processing'], true)) {
            header('Location: ' . url('index.php?action=success&id=' . $orderId));
            exit;
        }

        $db = Database::getInstance();
        $elapsed = 0;
        try {
            $dbTimeStr = $db->query("SELECT NOW()")->fetchColumn();
            if ($dbTimeStr) {
                $elapsed = max(0, strtotime($dbTimeStr) - strtotime($order['created_at']));
            } else {
                $elapsed = max(0, time() - strtotime($order['created_at']));
            }
        } catch (Throwable $e) {
            $elapsed = max(0, time() - strtotime($order['created_at']));
        }
        $timeLeft = max(0, 300 - $elapsed);
        $isExpired = ($timeLeft <= 0);
        
        $this->view('layout', [
            'view' => 'checkout/payment',
            'order' => $order,
            'settings' => Setting::getAll(),
            'isSuccess' => false,
            'metaRefresh' => !$isExpired,
            'timeLeft' => $timeLeft,
            'isExpired' => $isExpired
        ]);
    }

    // Trang thành công sau khi thanh toán
    public function success() {
        $orderId = $_GET['id'] ?? '';
        $order = Order::getById($orderId);
        $this->view('layout', [
            'view' => 'checkout/payment',
            'order' => $order, 
            'settings' => Setting::getAll(), 
            'isSuccess' => true
        ]);
    }

    // Trang lịch sử thanh toán (legacy) — redirect sang orderHistory chính.
    public function history() {
        header('Location: ' . url('index.php?action=orderHistory'));
        exit;
    }

    public function checkOrderStatus() {
        $orderId = $_GET['id'] ?? '';
        $order = Order::getById($orderId);
        if (!$order) {
            $this->json(['success' => false, 'message' => 'Order not found'], 404);
        }

        if (($order['customer_email'] ?? '') !== ($_SESSION['user']['email'] ?? '_')
            && ($_SESSION['user']['role'] ?? '') !== 'admin') {
            $this->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $this->json([
            'success' => true,
            'status' => $order['status'] ?? 'pending',
            'redirect' => in_array($order['status'] ?? '', ['completed', 'processing'], true)
                ? url('index.php?action=success&id=' . urlencode($orderId))
                : null,
        ]);
    }

    // WEBHOOK SEPAY
    public function sepayWebhook() {
        return $this->handleSePayWebhook();
        $settings = Setting::getAll();

        // Lấy token bảo mật theo nhiều cách để đảm bảo tương thích máy chủ
        $webhookToken = $_SERVER['HTTP_X_SEPAY_TOKEN'] ?? $_SERVER['x-sepay-token'] ?? '';
        if (!$webhookToken && function_exists('getallheaders')) {
            $headers = getallheaders();
            $webhookToken = $headers['x-sepay-token'] ?? $headers['X-Sepay-Token'] ?? '';
        }

        $targetToken = $settings['sepay_token'] ?? '';

        $input = file_get_contents('php://input');
        $logFile = rtrim(sys_get_temp_dir(), '/\\') . DIRECTORY_SEPARATOR . 'aicualtoi_sepay_webhook.log';
        if (APP_DEBUG) {
            $logData = date('Y-m-d H:i:s') . " | Token: $webhookToken | Input: $input\n";
            @file_put_contents($logFile, $logData, FILE_APPEND);
        }

        if ($webhookToken !== $targetToken || empty($targetToken)) {
            http_response_code(401);
            if (APP_DEBUG) @file_put_contents($logFile, "FAILED: Token mismatch or empty\n", FILE_APPEND);
            exit;
        }

        $data = json_decode($input, true);
        if (!$data) {
            http_response_code(400);
            if (APP_DEBUG) @file_put_contents($logFile, "FAILED: Invalid JSON\n", FILE_APPEND);
            exit;
        }

        $memo = $data['content'] ?? '';
        // Regex linh hoạt hơn để bắt mã đơn hàng AC...
        preg_match('/AC[0-9]{6}[A-Z0-9]{4}/i', $memo, $matches);
        $orderId = strtoupper($matches[0] ?? '');

        if ($orderId) {
            $order = Order::getById($orderId);
            if ($order && $order['status'] === 'pending') {
                $transactionId = $data['id'] ?? null;
                if (Order::updateStatus($orderId, 'completed', $transactionId)) {
                    try {
                        $delivered = Stock::claimForOrder(
                            $orderId,
                            (string) $order['product_id'],
                            (int) ($order['variant_idx'] ?? 0),
                            max(1, (int) ($order['quantity'] ?? 1))
                        );
                        if (!empty($delivered)) {
                            Order::setDelivered($orderId, $delivered);
                        }
                        Product::incrementSoldCount((string) $order['product_id'], max(1, (int) ($order['quantity'] ?? 1)), (int) ($order['variant_idx'] ?? 0));
                    } catch (Throwable $e) {
                        if (APP_DEBUG) @file_put_contents($logFile, 'Stock claim error: ' . $e->getMessage() . "\n", FILE_APPEND);
                    }
                    if (APP_DEBUG) @file_put_contents($logFile, "SUCCESS: Order $orderId completed\n", FILE_APPEND);
                    echo json_encode(['success' => true]);
                    exit;
                }
            } else {
                if (APP_DEBUG) @file_put_contents($logFile, "FAILED: Order not found or not pending: $orderId\n", FILE_APPEND);
            }
        } else {
            if (APP_DEBUG) @file_put_contents($logFile, "FAILED: Order ID not found in memo: $memo\n", FILE_APPEND);
        }
        echo json_encode(['success' => false]);
    }

    private function handleSePayWebhook() {
        header('Content-Type: application/json');
        $settings = Setting::getAll();
        $input = file_get_contents('php://input');
        $logFile = $this->sePayLogPath();
        $debug = [
            'time' => date('c'),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
            'headers' => $this->safeHeaders(),
            'raw' => $input,
            'result' => 'received',
        ];

        $this->appendSePayLog($logFile, $debug);

        $data = json_decode($input, true);
        if (!$data) {
            http_response_code(400);
            @file_put_contents($logFile, "[INVALID_JSON]\n", FILE_APPEND);
            $debug['result'] = 'invalid_json';
            $this->writeSePayDebug($debug);
            echo json_encode(['success' => false, 'message' => 'Invalid JSON']);
            exit;
        }
        $debug['payload'] = $data;

        if (!$this->verifySePayAuth($settings, $data)) {
            http_response_code(401);
            @file_put_contents($logFile, "[AUTH_FAILED]\n", FILE_APPEND);
            $debug['result'] = 'unauthorized';
            $this->writeSePayDebug($debug);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $isMerchantIpn = isset($data['notification_type'], $data['order']) && is_array($data['order']);
        if ($isMerchantIpn) {
            if (($data['notification_type'] ?? '') !== 'ORDER_PAID') {
                $debug['result'] = 'ignored_merchant_event';
                $this->writeSePayDebug($debug);
                echo json_encode(['success' => true, 'message' => 'Ignored merchant event']);
                exit;
            }

            $orderId = strtoupper(trim((string) ($data['order']['order_invoice_number'] ?? $data['order']['order_id'] ?? '')));
            $paidAmount = (float) ($data['transaction']['transaction_amount'] ?? $data['order']['order_amount'] ?? 0);
            $transactionId = $data['transaction']['transaction_id']
                ?? $data['transaction']['id']
                ?? $data['order']['order_id']
                ?? null;
            $memo = trim(implode(' ', array_filter([
                $data['order']['order_invoice_number'] ?? '',
                $data['order']['order_id'] ?? '',
                $data['order']['order_description'] ?? '',
            ])));
        } else {
            $transferType = strtolower((string) ($data['transferType'] ?? $data['transfer_type'] ?? 'in'));
            if (!in_array($transferType, ['in', 'credit'], true)) {
                $debug['result'] = 'ignored_non_in';
                $this->writeSePayDebug($debug);
                echo json_encode(['success' => true, 'message' => 'Ignored non-in transaction']);
                exit;
            }

            $memo = trim(implode(' ', array_filter([
                $data['code'] ?? '',
                $data['payment_code'] ?? '',
                $data['content'] ?? '',
                $data['description'] ?? '',
            ])));
            preg_match('/AC[0-9]{6}[A-Z0-9]{4}/i', $memo, $matches);
            $orderId = strtoupper($matches[0] ?? '');
            $paidAmount = (float) ($data['transferAmount'] ?? $data['amount'] ?? 0);
            $transactionId = $data['id']
                ?? $data['transaction_id']
                ?? $data['referenceCode']
                ?? $data['reference_code']
                ?? null;
        }

        if ($orderId !== '' && !preg_match('/^AC[0-9]{6}[A-Z0-9]{4}$/i', $orderId)) {
            preg_match('/AC[0-9]{6}[A-Z0-9]{4}/i', $orderId . ' ' . $memo, $matches);
            $orderId = strtoupper($matches[0] ?? '');
        }

        $debug['ipn_type'] = $isMerchantIpn ? 'merchant_ipn' : 'bank_webhook';
        $debug['memo'] = $memo;
        $debug['order_id'] = $orderId;

        if ($orderId) {
            $order = Order::getById($orderId);
            $debug['order_found'] = (bool) $order;
            $debug['order_status'] = $order['status'] ?? null;
            if ($order && $order['status'] === 'pending') {
                if ($paidAmount < (float) ($order['amount'] ?? 0)) {
                    if (APP_DEBUG) @file_put_contents($logFile, "FAILED: Underpaid $orderId amount=$paidAmount expected={$order['amount']}\n", FILE_APPEND);
                    $debug['result'] = 'underpaid';
                    $debug['paid_amount'] = $paidAmount;
                    $debug['expected_amount'] = (float) ($order['amount'] ?? 0);
                    $this->writeSePayDebug($debug);
                    echo json_encode(['success' => false, 'message' => 'Underpaid']);
                    exit;
                }

                $status = 'processing';
                $delivered = [];
                try {
                    $delivered = Stock::claimForOrder(
                        $orderId,
                        (string) $order['product_id'],
                        (int) ($order['variant_idx'] ?? 0),
                        max(1, (int) ($order['quantity'] ?? 1))
                    );
                    if (!empty($delivered)) {
                        Order::setDelivered($orderId, $delivered);
                        $order['delivered_items'] = $delivered;
                        $status = 'completed';
                    }
                } catch (Throwable $e) {
                    if (APP_DEBUG) @file_put_contents($logFile, 'Stock claim error: ' . $e->getMessage() . "\n", FILE_APPEND);
                    $debug['stock_error'] = $e->getMessage();
                }

                if (Order::updateStatus($orderId, $status, $transactionId)) {
                    $order['status'] = $status;
                    $order['transaction_id'] = $transactionId;
                    
                    try {
                        Product::incrementSoldCount((string) $order['product_id'], max(1, (int) ($order['quantity'] ?? 1)), (int) ($order['variant_idx'] ?? 0));
                    } catch (Throwable $e) {}

                    // Notify admin via Telegram — order completed/processing
                    try {
                        TelegramService::notifyOrderCompleted($order);
                    } catch (Throwable $ignored) {}

                    if (APP_DEBUG) @file_put_contents($logFile, "SUCCESS: Order $orderId set to $status\n", FILE_APPEND);
                    $debug['result'] = $status;
                    $this->writeSePayDebug($debug);
                    echo json_encode(['success' => true]);
                    exit;
                }
            } else {
                if (APP_DEBUG) @file_put_contents($logFile, "FAILED: Order not found or not pending: $orderId\n", FILE_APPEND);
            }
        } else {
            if (APP_DEBUG) @file_put_contents($logFile, "FAILED: Order ID not found in memo: $memo\n", FILE_APPEND);
        }

        $debug['result'] = 'no_matching_pending_order';
        $this->writeSePayDebug($debug);
        echo json_encode(['success' => true, 'message' => 'No matching pending order']);
        exit;
    }

    private function verifySePayAuth(array $settings, array $payload = []): bool {
        $apiKey = trim((string) ($settings['sepay_api_key'] ?? ''));
        $legacyToken = trim((string) ($settings['sepay_token'] ?? ''));

        if ($apiKey === '' && $legacyToken === '') {
            return true;
        }

        $headers = function_exists('getallheaders') ? getallheaders() : [];
        $auth = $_SERVER['HTTP_AUTHORIZATION']
            ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
            ?? $headers['Authorization']
            ?? $headers['authorization']
            ?? '';
        if ($auth !== '') {
            $authToken = preg_replace('/^(Bearer|Apikey)\s+/i', '', trim((string) $auth));
            if ($this->matchesSePayToken($authToken, [$apiKey, $legacyToken])) {
                return true;
            }
            if ($apiKey !== '' && hash_equals('Apikey ' . $apiKey, trim((string) $auth))) {
                return true;
            }
        }

        $secretKey = $_SERVER['HTTP_X_SECRET_KEY']
            ?? $headers['X-Secret-Key']
            ?? $headers['x-secret-key']
            ?? '';
        if ($this->matchesSePayToken($secretKey, [$legacyToken, $apiKey])) {
            return true;
        }

        $apiKeyHeader = $_SERVER['HTTP_X_API_KEY']
            ?? $headers['X-Api-Key']
            ?? $headers['x-api-key']
            ?? '';
        if ($this->matchesSePayToken($apiKeyHeader, [$apiKey, $legacyToken])) {
            return true;
        }

        $webhookToken = $_SERVER['HTTP_X_SEPAY_TOKEN']
            ?? $headers['X-Sepay-Token']
            ?? $headers['X-SePay-Token']
            ?? $headers['x-sepay-token']
            ?? '';
        if ($this->matchesSePayToken($webhookToken, [$legacyToken, $apiKey])) {
            return true;
        }

        return $this->matchesSePayToken($payload['token'] ?? '', [$legacyToken, $apiKey]);
    }

    private function matchesSePayToken($received, array $expectedTokens): bool {
        $received = trim((string) $received);
        if ($received === '') {
            return false;
        }

        foreach ($expectedTokens as $expected) {
            $expected = trim((string) $expected);
            if ($expected !== '' && hash_equals($expected, $received)) {
                return true;
            }
        }

        return false;
    }

    public function sepayDebug() {
        if (($_SESSION['user']['role'] ?? '') !== 'admin') {
            $this->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $path = $this->sePayDebugPath();
        header('Content-Type: application/json');
        if (!is_file($path)) {
            echo json_encode(['success' => false, 'message' => 'No webhook received yet']);
            exit;
        }

        echo file_get_contents($path);
        exit;
    }

    private function writeSePayDebug(array $debug): void {
        @file_put_contents($this->sePayDebugPath(), json_encode($debug, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    private function sePayDebugPath(): string {
        return $this->sePayLogDir() . DIRECTORY_SEPARATOR . 'sepay_last_webhook.json';
    }

    private function sePayLogPath(): string {
        return $this->sePayLogDir() . DIRECTORY_SEPARATOR . 'sepay_webhook.log';
    }

    private function sePayLogDir(): string {
        $root = defined('APP_ROOT') ? APP_ROOT : dirname(__DIR__, 2);
        $dir = $root . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'logs';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        return $dir;
    }

    private function appendSePayLog(string $logFile, array $debug): void {
        $entry = '[' . date('Y-m-d H:i:s') . '] IP: ' . ($debug['ip'] ?? 'Unknown') . "\n";
        $entry .= 'HEADERS: ' . json_encode($debug['headers'] ?? [], JSON_UNESCAPED_UNICODE) . "\n";
        $entry .= 'BODY: ' . ($debug['raw'] ?? '') . "\n";
        $entry .= "------------------------------------------\n";
        @file_put_contents($logFile, $entry, FILE_APPEND);
    }

    private function safeHeaders(): array {
        $headers = function_exists('getallheaders') ? getallheaders() : [];
        foreach ($headers as $key => $value) {
            if (stripos($key, 'authorization') !== false || stripos($key, 'token') !== false || stripos($key, 'key') !== false) {
                $headers[$key] = $value ? '***' . substr((string) $value, -4) : '';
            }
        }
        return $headers;
    }

    /**
     * Demo-only endpoint to mark an order as paid without an actual bank transfer.
     * Gated by Setting('demo_payment_active'). Reuses the same flow as SePay webhook.
     */
    public function paymentDemo() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url());
            exit;
        }
        $settings = Setting::getAll();
        if (($settings['demo_payment_active'] ?? '0') !== '1') {
            $_SESSION['flash_error'] = 'Chế độ demo đang tắt.';
            header('Location: ' . url());
            exit;
        }

        $orderId = $_POST['order_id'] ?? '';
        if ($orderId === '') {
            $_SESSION['flash_error'] = 'Thiếu mã đơn hàng.';
            header('Location: ' . url());
            exit;
        }
        $order = Order::getById($orderId);
        if (!$order) {
            $_SESSION['flash_error'] = 'Đơn hàng không tồn tại.';
            header('Location: ' . url());
            exit;
        }
        if (($order['customer_email'] ?? '') !== ($_SESSION['user']['email'] ?? '_')) {
            $_SESSION['flash_error'] = 'Đơn hàng này không thuộc tài khoản của bạn.';
            header('Location: ' . url());
            exit;
        }
        if ($order['status'] !== 'pending') {
            header('Location: ' . url('index.php?action=success&id=' . $orderId));
            exit;
        }

        $txId = 'DEMO-' . substr(uniqid(), -8);
        $status = 'processing';
        $delivered = [];
        try {
            $delivered = Stock::claimForOrder(
                $orderId,
                (string) $order['product_id'],
                (int) ($order['variant_idx'] ?? 0),
                max(1, (int) ($order['quantity'] ?? 1))
            );
            if (!empty($delivered)) {
                Order::setDelivered($orderId, $delivered);
                $order['delivered_items'] = $delivered;
                $status = 'completed';
            }
        } catch (Throwable $e) {
            // Don't block
        }

        Order::updateStatus($orderId, $status, $txId);
        $order['status'] = $status;
        $order['transaction_id'] = $txId;
        
        try {
            Product::incrementSoldCount((string) $order['product_id'], max(1, (int) ($order['quantity'] ?? 1)), (int) ($order['variant_idx'] ?? 0));
        } catch (Throwable $e) {}

        // Notify admin via Telegram — demo payment completed
        try {
            TelegramService::notifyOrderCompleted($order);
        } catch (Throwable $ignored) {}

        header('Location: ' . url('index.php?action=success&id=' . $orderId));
        exit;
    }

    private function json(array $payload, int $status = 200): void {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($payload);
        exit;
    }
}
