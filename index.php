<?php

// Require Models
require_once 'models/Product.php';
require_once 'models/Setting.php';

// Require Controllers
require_once 'controllers/HomeController.php';
require_once 'controllers/AdminController.php';

// Simple Routing
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Determine which controller to use
if (strpos($action, 'admin') === 0) {
    $controller = new AdminController();
} else {
    $controller = new HomeController();
}

if (method_exists($controller, $action)) {
    $controller->$action();
} else {
    echo "404 Not Found";
}

?>
