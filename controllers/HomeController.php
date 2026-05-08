<?php

class HomeController {
    private $settings;

    public function __construct() {
        $this->settings = Setting::getAll();
    }

    public function index() {
        $products = Product::getAll();
        $settings = $this->settings;
        
        $view = 'views/home.php';
        require_once 'views/layout.php';
    }

    public function blogDetail() {
        $allProducts = Product::getAll();
        $settings = $this->settings;
        shuffle($allProducts);
        $sidebarProducts = array_slice($allProducts, 0, 3);
        
        $view = 'views/blog_detail.php';
        require_once 'views/layout.php';
    }

    public function productDetail() {
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        if (!$id) {
            header('Location: index.php');
            exit;
        }

        $product = Product::getById($id);
        $settings = $this->settings;
        
        if (!$product) {
            echo "Product not found";
            exit;
        }

        $view = 'views/product-detail.php';
        require_once 'views/layout.php';
    }
}
?>
