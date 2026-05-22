<?php

class FileSystem {
    private $config;
    private $disk;

    public function __construct($diskName = null) {
        $this->config = require __DIR__ . '/../../config/filesystems.php';
        $this->disk = $diskName ?: $this->config['default'];
    }

    public function disk($name) {
        $this->disk = $name;
        return $this;
    }

    private function getDiskConfig() {
        if (!isset($this->config['disks'][$this->disk])) {
            throw new Exception("Disk [{$this->disk}] not found.");
        }
        return $this->config['disks'][$this->disk];
    }

    public function put($path, $content) {
        $config = $this->getDiskConfig();
        $fullPath = $config['root'] . '/' . $path;
        
        // Create directory if not exists
        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        return file_put_contents($fullPath, $content);
    }

    public function url($path) {
        $config = $this->getDiskConfig();
        if (isset($config['url'])) {
            return rtrim($config['url'], '/') . '/' . ltrim($path, '/');
        }
        return $path;
    }
}
