<?php

class ContactMessage {
    public static function create(array $data): bool {
        $db = Database::getInstance();
        self::ensureTable($db);
        $stmt = $db->prepare(
            "INSERT INTO contact_messages (name, email, subject, message, ip_address, user_agent)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        return $stmt->execute([
            $data['name'],
            $data['email'],
            $data['subject'],
            $data['message'],
            $data['ip_address'] ?? null,
            $data['user_agent'] ?? null,
        ]);
    }

    public static function getAll(int $limit = 200): array {
        $db = Database::getInstance();
        self::ensureTable($db);
        $stmt = $db->prepare("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function countUnread(): int {
        $db = Database::getInstance();
        self::ensureTable($db);
        return (int) $db->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'new'")->fetchColumn();
    }

    public static function updateStatus(int $id, string $status): bool {
        if (!in_array($status, ['new', 'read', 'archived'], true)) {
            return false;
        }
        $db = Database::getInstance();
        self::ensureTable($db);
        $stmt = $db->prepare("UPDATE contact_messages SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    private static function ensureTable(PDO $db): void {
        $db->exec("
            CREATE TABLE IF NOT EXISTS contact_messages (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(190) NOT NULL,
                email VARCHAR(190) NOT NULL,
                subject VARCHAR(255) NOT NULL,
                message TEXT NOT NULL,
                status ENUM('new', 'read', 'archived') NOT NULL DEFAULT 'new',
                ip_address VARCHAR(64) DEFAULT NULL,
                user_agent VARCHAR(255) DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_status (status),
                INDEX idx_created_at (created_at),
                INDEX idx_email (email)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    }
}
