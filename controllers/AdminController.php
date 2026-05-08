<?php

class AdminController {
    public function adminDashboard() {
        $settings = Setting::getAll();
        $products = Product::getAll();
        include 'views/admin/dashboard.php';
    }

    public function adminSaveSettings() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'bannerText' => $_POST['bannerText'] ?? '',
                'zalo' => $_POST['zalo'] ?? '',
                'footerDesc' => $_POST['footerDesc'] ?? '',
                'socialLink' => $_POST['socialLink'] ?? '',
                'copyright' => $_POST['copyright'] ?? ''
            ];
            Setting::saveAll($data);
            
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit;
        }
    }

    public function adminSaveProduct() {
        // Since we don't have a DB yet and Product.php is hardcoded, 
        // we'll implement this properly once we move products to JSON too.
        // For now, let's just return success for the UI.
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }
}
