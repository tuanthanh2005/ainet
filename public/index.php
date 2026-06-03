<?php
/**
 * Front controller. Lives in the document-root folder (e.g. "public_html"
 * on Hostinger or "public" in dev). It must auto-discover the application
 * root regardless of whether you upload the project flat or split it into
 * "public_html" + "aicuatoi" siblings.
 */

$here = __DIR__;
$candidates = [
    dirname($here),                          // dev:           public/index.php  -> project root is dirname
    dirname($here) . '/aicuatoi',            // hostinger:     public_html sibling 'aicuatoi'
    dirname($here) . '/laravel',             // fallback:      older deploy folder name
    dirname(dirname($here)) . '/aicuatoi',   // fallback:      if extra nesting happens
    dirname(dirname($here)) . '/laravel',
];

$appRoot = null;
foreach ($candidates as $c) {
    if (is_file($c . '/config/config.php')) {
        $appRoot = realpath($c);
        break;
    }
}

if ($appRoot === null) {
    http_response_code(500);
    echo 'Application not found. Could not locate config/config.php.';
    exit;
}

define('APP_ROOT', $appRoot);

// Load Configuration
require_once APP_ROOT . '/config/config.php';

// Error reporting based on environment
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', rtrim(sys_get_temp_dir(), '/\\') . DIRECTORY_SEPARATOR . 'aicualtoi_php_errors.log');
}

// Session Setup
$sessionLifetime = SESSION_LIFETIME_DAYS * 24 * 60 * 60;
ini_set('session.gc_maxlifetime', (string) $sessionLifetime);
ini_set('session.use_strict_mode', '1');
ini_set('session.cookie_httponly', '1');

$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')
    || ((int) ($_SERVER['SERVER_PORT'] ?? 0) === 443);

session_set_cookie_params([
    'lifetime' => $sessionLifetime,
    'path'     => '/',
    'httponly' => true,
    'secure'   => $isHttps,
    'samesite' => 'Lax',
]);
session_start();

// Security headers
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Load Core Libraries
require_once APP_ROOT . '/app/Core/Database.php';
require_once APP_ROOT . '/app/Core/Controller.php';
require_once APP_ROOT . '/app/Core/Model.php';
require_once APP_ROOT . '/app/Core/FileSystem.php';
require_once APP_ROOT . '/app/Core/Csrf.php';
require_once APP_ROOT . '/app/Core/Auth.php';
require_once APP_ROOT . '/app/Core/Seo.php';
require_once APP_ROOT . '/app/Core/Url.php';
require_once APP_ROOT . '/app/Core/Upload.php';
require_once APP_ROOT . '/app/Core/TelegramService.php';
require_once APP_ROOT . '/app/Core/GoogleAuth.php';

// Load Models
require_once APP_ROOT . '/app/Models/Product.php';
require_once APP_ROOT . '/app/Models/Setting.php';
require_once APP_ROOT . '/app/Models/User.php';
require_once APP_ROOT . '/app/Models/Category.php';
require_once APP_ROOT . '/app/Models/Blog.php';
require_once APP_ROOT . '/app/Models/RecentOrder.php';
require_once APP_ROOT . '/app/Models/Order.php';
require_once APP_ROOT . '/app/Models/Stock.php';
require_once APP_ROOT . '/app/Models/ContactMessage.php';

// Load Controllers
require_once APP_ROOT . '/app/Controllers/HomeController.php';
require_once APP_ROOT . '/app/Controllers/AdminController.php';
require_once APP_ROOT . '/app/Controllers/CheckoutController.php';
require_once APP_ROOT . '/app/Controllers/SeoController.php';

// Refresh logged-in user's session from DB on every request so role/status changes
// take effect without requiring re-login. Silent if user no longer exists.
if (!empty($_SESSION['user']['id'])) {
    try {
        $freshUser = User::findById($_SESSION['user']['id']);
        if ($freshUser && ($freshUser['status'] ?? '') === 'active') {
            $_SESSION['user'] = [
                'id'    => $freshUser['id'],
                'name'  => $freshUser['name'],
                'email' => $freshUser['email'],
                'role'  => $freshUser['role'],
            ];
        } else {
            // User deleted or blocked: drop session silently
            $_SESSION = [];
        }
    } catch (Throwable $e) {
        // Don't block page load if DB hiccups; keep existing session
        error_log('User session refresh failed: ' . $e->getMessage());
    }
}

// Routing
// Derive raw URL path from query string (Apache rewrite) or REQUEST_URI (PHP built-in server)
$rawUrl = $_GET['url'] ?? '';
if ($rawUrl === '') {
    $reqUri = $_SERVER['REQUEST_URI'] ?? '';
    $reqPath = parse_url($reqUri, PHP_URL_PATH) ?: '';
    // Strip the script name if present (e.g. when accessing via /index.php/...)
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    if ($scriptName !== '' && strpos($reqPath, $scriptName) === 0) {
        $reqPath = substr($reqPath, strlen($scriptName));
    } else {
        $scriptDir = rtrim(dirname($scriptName), '/\\');
        if ($scriptDir !== '' && $scriptDir !== '/' && strpos($reqPath, $scriptDir) === 0) {
            $reqPath = substr($reqPath, strlen($scriptDir));
        }
    }
    $rawUrl = trim($reqPath, '/');
}
$rawUrl = trim($rawUrl, '/');

// Clean URL routing — translate friendly URLs to existing actions
if ($rawUrl !== '' && empty($_GET['action'])) {
    $segments = explode('/', $rawUrl);
    $first    = $segments[0] ?? '';
    $second   = $segments[1] ?? '';

    switch ($first) {
        case 'san-pham':
            if ($second !== '') {
                $_GET['action'] = 'productDetail';
                $_GET['id']     = $second;
            }
            break;
        case 'danh-muc':
            if ($second !== '') {
                $_GET['action']   = 'index';
                $_GET['tab']      = 'products';
                $_GET['category'] = $second;
            }
            break;
        case 'tap-chi':
            if ($second !== '') {
                $_GET['action'] = 'blogDetail';
                $_GET['id']     = $second;
            } else {
                $_GET['action'] = 'index';
                $_GET['tab']    = 'blog';
            }
            break;
        case 'gioi-thieu': $_GET['action'] = 'about'; break;
        case 'lien-he':    $_GET['action'] = 'contact'; break;
        case 'gio-hang':   $_GET['action'] = 'cart'; break;
        case 'sitemap.xml':
            $_GET['action'] = 'sitemap';
            break;
        case 'robots.txt':
            $_GET['action'] = 'robots';
            break;
        case 'webhook':
            if ($second === 'sepay') {
                $_GET['action'] = 'sepayWebhook';
            }
            break;
    }
}

$action = $_GET['action'] ?? 'index';
$action = preg_replace('/[^A-Za-z0-9_\/]/', '', $action);

// Handle clean URL for SePay Webhook
if ($action === 'webhook/sepay') {
    $action = 'sepayWebhook';
}

// CSRF protection on all state-changing requests EXCEPT third-party webhook
$isPost = ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
$csrfExempt = ['sepayWebhook'];
if ($isPost && !in_array($action, $csrfExempt, true)) {
    if (!Csrf::validate()) {
        http_response_code(419);
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang.']);
        } else {
            $_SESSION['flash_error'] = 'Phiên làm việc đã hết hạn. Vui lòng thử lại.';
            header('Location: ' . url());
        }
        exit;
    }
}

// Simple Route Map
if (in_array($action, ['sitemap', 'robots'], true)) {
    $controllerName = 'SeoController';
} elseif (strpos($action, 'admin') === 0) {
    $controllerName = 'AdminController';
} elseif (in_array($action, ['checkout', 'payment', 'sepayWebhook', 'checkOrderStatus', 'createOrderJson', 'history', 'success', 'checkoutPage', 'paymentDemo', 'sepayDebug'])) {
    $controllerName = 'CheckoutController';
} elseif (in_array($action, ['googleLogin', 'googleCallback'], true)) {
    $controllerName = 'HomeController';
} else {
    $controllerName = 'HomeController';
}

if (!class_exists($controllerName)) {
    http_response_code(404);
    echo '404 Not Found';
    exit;
}

$controller = new $controllerName();

// Map friendly action names to internal methods
$method = $action;

if (method_exists($controller, $method) && $method !== '__construct') {
    $controller->$method();
} else {
    http_response_code(404);
    echo '404 Not Found';
}
