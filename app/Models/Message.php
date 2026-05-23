<?php

class Message {
    /**
     * Get all messages of a thread (one user). Optionally only since a given id.
     */
    public static function thread(int $userId, int $sinceId = 0): array {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'SELECT id, user_id, sender, body, attachment_path, attachment_name, attachment_mime, attachment_size, is_read, created_at
             FROM messages
             WHERE user_id = ? AND id > ?
             ORDER BY id ASC'
        );
        $stmt->execute([$userId, $sinceId]);
        return $stmt->fetchAll();
    }

    public static function send(int $userId, string $sender, ?string $body, ?array $attachment = null): int {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'INSERT INTO messages (user_id, sender, body, attachment_path, attachment_name, attachment_mime, attachment_size, is_read)
             VALUES (?, ?, ?, ?, ?, ?, ?, 0)'
        );
        $stmt->execute([
            $userId,
            $sender,
            $body !== null && $body !== '' ? $body : null,
            $attachment['path'] ?? null,
            $attachment['name'] ?? null,
            $attachment['mime'] ?? null,
            $attachment['size'] ?? null,
        ]);
        return (int) $db->lastInsertId();
    }

    public static function markRead(int $userId, string $from): void {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'UPDATE messages SET is_read = 1 WHERE user_id = ? AND sender = ? AND is_read = 0'
        );
        $stmt->execute([$userId, $from]);
    }

    public static function unreadForUser(int $userId): int {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            "SELECT COUNT(*) FROM messages WHERE user_id = ? AND sender = 'admin' AND is_read = 0"
        );
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    public static function unreadForAdmin(): int {
        $db = Database::getInstance();
        $stmt = $db->query(
            "SELECT COUNT(*) FROM messages WHERE sender = 'user' AND is_read = 0"
        );
        return (int) $stmt->fetchColumn();
    }

    public static function threadsForAdmin(): array {
        $db = Database::getInstance();
        $sql = "
            SELECT
                u.id AS user_id,
                u.name,
                u.email,
                (SELECT COALESCE(NULLIF(body, ''), CONCAT('[Tệp] ', COALESCE(attachment_name, '')))
                 FROM messages m2 WHERE m2.user_id = u.id ORDER BY m2.id DESC LIMIT 1) AS last_body,
                (SELECT created_at FROM messages m3
                 WHERE m3.user_id = u.id ORDER BY m3.id DESC LIMIT 1)         AS last_at,
                (SELECT COUNT(*) FROM messages m4
                 WHERE m4.user_id = u.id AND m4.sender = 'user' AND m4.is_read = 0) AS unread
            FROM users u
            WHERE EXISTS (SELECT 1 FROM messages mx WHERE mx.user_id = u.id)
            ORDER BY last_at DESC
        ";
        return $db->query($sql)->fetchAll();
    }
}
