<?php

class ChatMessage {
    private static bool $tableChecked = false;

    private static function ensureTableExists(): void {
        if (self::$tableChecked) {
            return;
        }

        try {
            $db = Database::getInstance();
            $sql = "CREATE TABLE IF NOT EXISTS chat_messages (
                id INT AUTO_INCREMENT PRIMARY KEY,
                session_id VARCHAR(64) NOT NULL,
                sender_type ENUM('user', 'admin') NOT NULL,
                sender_name VARCHAR(100) DEFAULT NULL,
                user_id INT DEFAULT NULL,
                message TEXT NOT NULL,
                is_read TINYINT(1) NOT NULL DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_session (session_id, id),
                INDEX idx_read (is_read, sender_type)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
            $db->exec($sql);
            self::$tableChecked = true;
        } catch (Throwable $e) {
            error_log('Error checking/creating chat_messages table: ' . $e->getMessage());
        }
    }

    public static function getMessagesBySession(string $sessionId, int $limit = 100): array {
        self::ensureTableExists();
        $db = Database::getInstance();
        $stmt = $db->prepare(
            "SELECT * FROM chat_messages 
             WHERE session_id = ? 
             ORDER BY created_at ASC, id ASC 
             LIMIT ?"
        );
        $stmt->bindValue(1, $sessionId, PDO::PARAM_STR);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll() ?: [];
    }

    public static function sendMessage(
        string $sessionId, 
        string $senderType, 
        string $message, 
        ?int $userId = null, 
        ?string $senderName = null
    ): bool {
        self::ensureTableExists();
        $message = trim($message);
        if ($message === '') {
            return false;
        }

        $db = Database::getInstance();
        $stmt = $db->prepare(
            "INSERT INTO chat_messages (session_id, sender_type, sender_name, user_id, message, is_read, created_at) 
             VALUES (?, ?, ?, ?, ?, 0, NOW())"
        );
        return $stmt->execute([
            $sessionId,
            $senderType,
            $senderName ?: ($senderType === 'admin' ? 'Admin' : 'Khách'),
            $userId,
            $message
        ]);
    }

    public static function getConversations(): array {
        self::ensureTableExists();
        $db = Database::getInstance();
        
        $sql = "SELECT 
                    m1.session_id,
                    m1.sender_name,
                    m1.user_id,
                    m1.message AS last_message,
                    m1.sender_type AS last_sender_type,
                    m1.created_at AS last_time,
                    COALESCE(u.unread_count, 0) AS unread_count
                FROM chat_messages m1
                INNER JOIN (
                    SELECT session_id, MAX(id) as max_id
                    FROM chat_messages
                    GROUP BY session_id
                ) m2 ON m1.id = m2.max_id
                LEFT JOIN (
                    SELECT session_id, COUNT(*) as unread_count
                    FROM chat_messages
                    WHERE sender_type = 'user' AND is_read = 0
                    GROUP BY session_id
                ) u ON m1.session_id = u.session_id
                ORDER BY m1.created_at DESC";

        $stmt = $db->query($sql);
        return $stmt->fetchAll() ?: [];
    }

    public static function markAsRead(string $sessionId, string $senderTypeToMark = 'user'): bool {
        self::ensureTableExists();
        $db = Database::getInstance();
        $stmt = $db->prepare(
            "UPDATE chat_messages 
             SET is_read = 1 
             WHERE session_id = ? AND sender_type = ? AND is_read = 0"
        );
        return $stmt->execute([$sessionId, $senderTypeToMark]);
    }

    public static function countTotalUnreadForAdmin(): int {
        self::ensureTableExists();
        $db = Database::getInstance();
        $sql = "SELECT COUNT(DISTINCT session_id) 
                FROM chat_messages 
                WHERE sender_type = 'user' AND is_read = 0";
        return (int) $db->query($sql)->fetchColumn();
    }

    public static function getConsecutiveUserMessageCount(string $sessionId): int {
        self::ensureTableExists();
        $db = Database::getInstance();
        $stmt = $db->prepare(
            "SELECT sender_type FROM chat_messages 
             WHERE session_id = ? 
             ORDER BY id DESC 
             LIMIT 15"
        );
        $stmt->execute([$sessionId]);
        $rows = $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
        
        $count = 0;
        foreach ($rows as $senderType) {
            if ($senderType === 'user') {
                $count++;
            } else {
                break;
            }
        }
        return $count;
    }

    public static function isWaitingForAdminReply(string $sessionId, int $maxLimit = 10): bool {
        return self::getConsecutiveUserMessageCount($sessionId) >= $maxLimit;
    }
}
