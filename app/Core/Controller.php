<?php

class Controller {
    public function view($view, $data = []) {
        $template = $view;
        $contentView = $data['view'] ?? null;
        unset($data['view']);

        // Extract data to be used in view
        extract($data);
        $view = $contentView;

        $viewRoot = (defined('APP_ROOT') ? APP_ROOT : dirname(__DIR__, 2)) . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Views';
        $templatePath = $viewRoot . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $template) . '.php';

        if (file_exists($templatePath)) {
            $currentDir = getcwd();
            chdir($viewRoot);
            try {
                require $templatePath;
            } finally {
                chdir($currentDir);
            }
        } else {
            die("View does not exist: " . $template);
        }
    }
    
    public function redirect($url) {
        header('Location: ' . URLROOT . '/' . $url);
        exit;
    }
}
