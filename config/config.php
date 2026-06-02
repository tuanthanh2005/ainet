<?php

// Load .env if exists
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        $value = trim($value, '"\''); // Bỏ dấu ngoặc kép
        putenv(sprintf('%s=%s', $name, $value));
        $_ENV[$name] = $value;
    }
}

// Database Configuration
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_DATABASE') ?: 'gpt_plus');
define('DB_USER', getenv('DB_USERNAME') ?: 'root');
define('DB_PASS', getenv('DB_PASSWORD') ?: '');

// App Configuration
define('URLROOT', getenv('APP_URL') ?: 'http://localhost:8000');
define('SITENAME', getenv('APP_NAME') ?: 'AI CỦA TÔI');
define('SESSION_LIFETIME_DAYS', (int) (getenv('SESSION_LIFETIME_DAYS') ?: 365));
define('APP_ENV', getenv('APP_ENV') ?: 'production');
define('APP_DEBUG', filter_var(getenv('APP_DEBUG') ?: 'false', FILTER_VALIDATE_BOOLEAN));

// Path Helpers
function base_path($path = '') {
    return __DIR__ . '/../' . ltrim($path, '/');
}

function public_path($path = '') {
    // Tự động nhận diện thư mục public_html trên Hostinger
    $basePath = dirname(__DIR__);
    $hostingerPublic = dirname($basePath) . '/public_html';

    if (is_dir($hostingerPublic)) {
        return $hostingerPublic . ($path ? DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : '');
    }
    return $basePath . DIRECTORY_SEPARATOR . 'public' . ($path ? DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : '');
}

// Helpers cho URL và Assets
function url($path = '') {
    return rtrim(URLROOT, '/') . '/' . ltrim($path, '/');
}

function image_url($path = '') {
    if (empty($path)) return '';
    if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) {
        return $path;
    }
    return url($path);
}

function asset($path = '') {
    // Dùng đường dẫn gốc từ tên miền để đảm bảo luôn đúng ở mọi trang con
    // Thêm ?v= để xóa cache trình duyệt khi bạn cập nhật code mới
    $version = '1.3.2'; // Bump khi update CSS/JS để bypass cache trình duyệt
    return url('assets/' . ltrim($path, '/') . '?v=' . $version);
}
