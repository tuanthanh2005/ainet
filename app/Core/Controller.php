<?php

class Controller {
    public function view($view, $data = []) {
        // Extract data to be used in view
        extract($data);

        if (file_exists('../app/Views/' . $view . '.php')) {
            require_once '../app/Views/' . $view . '.php';
        } else {
            die("View does not exist: " . $view);
        }
    }
    
    public function redirect($url) {
        header('Location: ' . URLROOT . '/' . $url);
        exit;
    }
}
