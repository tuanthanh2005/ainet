<?php

class ContactMessage {
    public static function create(array $data): bool {
        $db = Database::getInstance();
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
        $stmt = $db->prepare("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function countUnread(): int {
        $db = Database::getInstance();
        return (int) $db->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'new'")->fetchColumn();
    }

    public static function updateStatus(int $id, string $status): bool {
        if (!in_array($status, ['new', 'read', 'archived'], true)) {
            return false;
        }
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE contact_messages SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }
}
