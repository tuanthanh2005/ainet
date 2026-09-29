<?php

class PasswordReset extends Model {
    /**
     * Ensure password_resets table exists
     */
    private static function ensureTable(PDO $db): void {
        $db->exec("CREATE TABLE IF NOT EXISTS password_resets (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(190) NOT NULL,
            token VARCHAR(100) NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_password_resets_email (email),
            INDEX idx_password_resets_token (token)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }

    /**
     * Create a reset token for the given email (expires any existing tokens for this email).
     */
    public static function createToken(string $email): string {
        $db = Database::getInstance();
        self::ensureTable($db);

        // Delete any existing tokens for this email
        $del = $db->prepare("DELETE FROM password_resets WHERE email = ?");
        $del->execute([$email]);

        // Generate high-entropy token
        $token = bin2hex(random_bytes(32));

        $stmt = $db->prepare("INSERT INTO password_resets (email, token, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$email, $token]);

        return $token;
    }

    /**
     * Find a valid token for an email within expiration window (default 30 minutes).
     */
    public static function findValid(string $email, string $token, int $expireMinutes = 30): ?array {
        $db = Database::getInstance();
        self::ensureTable($db);

        $stmt = $db->prepare("SELECT * FROM password_resets 
                              WHERE email = ? 
                                AND token = ? 
                                AND created_at >= DATE_SUB(NOW(), INTERVAL ? MINUTE)
                              ORDER BY id DESC LIMIT 1");
        $stmt->execute([$email, $token, $expireMinutes]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    /**
     * Delete token(s) by email.
     */
    public static function deleteByEmail(string $email): bool {
        $db = Database::getInstance();
        self::ensureTable($db);

        $stmt = $db->prepare("DELETE FROM password_resets WHERE email = ?");
        return $stmt->execute([$email]);
    }
}
