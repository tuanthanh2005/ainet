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

        $envKeys = [
            'smtp_host' => 'SMTP_HOST',
            'smtp_port' => 'SMTP_PORT',
            'smtp_secure' => 'SMTP_SECURE',
            'smtp_from_name' => 'SMTP_FROM_NAME',
            'smtp_user' => 'SMTP_USER',
            'smtp_pass' => 'SMTP_PASS',
            'smtp_from_email' => 'SMTP_FROM_EMAIL',
        ];
        foreach ($envKeys as $setKey => $envKey) {
            $val = getenv($envKey);
            if ($val !== false && $val !== '' && empty($settings[$setKey])) {
                $settings[$setKey] = $val;
            }
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
