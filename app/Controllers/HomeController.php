<?php

class HomeController extends Controller {
    private $settings;

    public function __construct() {
        $this->settings = Setting::getAll();
    }

    public function index() {
        $products = Product::getAll();
        $settings = $this->settings;
        
        $this->view('layout', [
            'view' => 'home',
            'products' => $products,
            'settings' => $settings
        ]);
    }

    public function blogDetail() {
        $allProducts = Product::getAll();
        $settings = $this->settings;
        shuffle($allProducts);
        $sidebarProducts = array_slice($allProducts, 0, 3);
        
        $this->view('layout', [
            'view' => 'blog_detail',
            'sidebarProducts' => $sidebarProducts,
            'settings' => $settings
        ]);
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

        $this->view('layout', [
            'view' => 'product-detail',
            'product' => $product,
            'settings' => $settings
        ]);
    }
}
?>
