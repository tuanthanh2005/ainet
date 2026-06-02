<?php

class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        require_once __DIR__ . '/../../config/config.php';
        
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $this->conn = new PDO($dsn, DB_USER, DB_PASS);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->conn->exec("SET time_zone = " . $this->conn->quote(DB_TIMEZONE_OFFSET));

            // One-off migration for orders status ENUM to support 'processing'
            $logDir = __DIR__ . '/../../storage/logs';
            if (!is_dir($logDir)) {
                @mkdir($logDir, 0775, true);
            }
            $lockFile = $logDir . '/migrated_processing.lock';
            if (!file_exists($lockFile)) {
                try {
                    $this->conn->exec("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'completed', 'processing', 'cancelled') DEFAULT 'pending'");
                    @file_put_contents($lockFile, 'done');
                } catch (Throwable $ignored) {}
            }
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance->conn;
    }
}
