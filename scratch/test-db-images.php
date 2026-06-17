<?php
define('APP_ROOT', dirname(__DIR__));
require_once APP_ROOT . '/config/config.php';
require_once APP_ROOT . '/app/Core/Database.php';

try {
    $db = Database::getInstance();
    $stmt = $db->query("SELECT id, title, image FROM products");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($products as $p) {
        echo "ID: {$p['id']} | Title: {$p['title']} | Image Field: '{$p['image']}'\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
