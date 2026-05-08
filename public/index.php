<?php
session_start();

// Load Configuration
require_once '../config/config.php';

// Load Core Libraries
require_once '../app/Core/Database.php';
require_once '../app/Core/Controller.php';
require_once '../app/Core/Model.php';

// Load Models
require_once '../app/Models/Product.php';
require_once '../app/Models/Setting.php';

// Load Controllers
require_once '../app/Controllers/HomeController.php';
require_once '../app/Controllers/AdminController.php';

// Routing
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Simple Route Map
if (strpos($action, 'admin') === 0) {
    $controllerName = 'AdminController';
} else {
    $controllerName = 'HomeController';
}

$controller = new $controllerName();

if (method_exists($controller, $action)) {
    $controller->$action();
} else {
    http_response_code(404);
    echo "404 Not Found";
}

?>
