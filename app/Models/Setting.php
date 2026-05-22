<?php

class Setting {
    public static function getAll() {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT * FROM settings");
        $rows = $stmt->fetchAll();
        
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    }

    public static function saveAll($data) {
        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
        
        foreach ($data as $key => $value) {
            $stmt->execute([$key, $value]);
        }
    }
}
