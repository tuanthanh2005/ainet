<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "<h1>SMTP Diagnostics Script</h1>";

$here = __DIR__;
$candidates = [
    dirname($here),
    dirname($here) . '/aicuatoi',
];

$appRoot = null;
foreach ($candidates as $c) {
    if (is_file($c . '/config/config.php')) {
        $appRoot = realpath($c);
        break;
    }
}

if ($appRoot === null) {
    echo "Error: Could not find application root folder.<br>";
    exit;
}

echo "App Root resolved to: " . htmlspecialchars($appRoot) . "<br>";
define('APP_ROOT', $appRoot);

echo "Loading config...<br>";
require_once APP_ROOT . '/config/config.php';

echo "Loading Database...<br>";
require_once APP_ROOT . '/app/Core/Database.php';

echo "Loading Setting Model...<br>";
require_once APP_ROOT . '/app/Models/Setting.php';

echo "Fetching SMTP settings...<br>";
try {
    $settings = Setting::getAll();
    echo "Settings loaded successfully!<br>";
} catch (Throwable $e) {
    echo "Error loading settings: " . htmlspecialchars($e->getMessage()) . "<br>";
    exit;
}

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
