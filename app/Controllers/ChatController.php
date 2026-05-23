<?php

class ChatController extends Controller {
    public function __construct() {
        if (!Auth::check()) {
            $this->json(['success' => false, 'message' => 'Bạn cần đăng nhập để chat.'], 401);
        }
    }

    /** GET ?action=chatPoll&since=<id> — user side */
    public function chatPoll(): void {
        $user  = $_SESSION['user'];
        $since = (int) ($_GET['since'] ?? 0);

        $messages = Message::thread((int) $user['id'], $since);
        if (!empty($messages)) {
            Message::markRead((int) $user['id'], 'admin');
        }
        $unread = Message::unreadForUser((int) $user['id']);
        $this->json(['success' => true, 'messages' => $messages, 'unread' => $unread]);
    }

    /** POST ?action=sendUserChat — user sends a message (text and/or attachment) */
    public function sendUserChat(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Method not allowed'], 405);
        }
        $user = $_SESSION['user'];
        $body = trim($_POST['body'] ?? '');
        if (mb_strlen($body) > 700) {
            $this->json(['success' => false, 'message' => 'Tin nhắn không được quá 700 ký tự.']);
        }

        $attachment = null;
        try {
            if (!empty($_FILES['chat_file']['name'])) {
                $attachment = ChatUpload::store($_FILES['chat_file']);
            }
        } catch (Throwable $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()]);
        }

        if ($body === '' && !$attachment) {
            $this->json(['success' => false, 'message' => 'Tin nhắn không được trống.']);
        }

        $id = Message::send((int) $user['id'], 'user', $body !== '' ? $body : null, $attachment);
        $this->jsonThenNotifyTelegram($id, $user, $body, $attachment);

    }

    private function jsonThenNotifyTelegram(int $messageId, array $user, string $body, ?array $attachment): void {
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'id' => $messageId]);

        if (function_exists('fastcgi_finish_request')) {
            session_write_close();
            fastcgi_finish_request();
        } else {
            @ob_flush();
            @flush();
        }

        try {
            $attachmentName = $attachment ? ($attachment['name'] ?? null) : null;
            TelegramService::notifyNewChatMessage($user, $body, $attachmentName);
        } catch (Throwable $e) {
            error_log('Telegram chat notification failed: ' . $e->getMessage());
        }
        exit;
    }

    /** GET ?action=chatUnread */
    public function chatUnread(): void {
        $user = $_SESSION['user'];
        $count = Message::unreadForUser((int) $user['id']);
        $this->json(['success' => true, 'unread' => $count]);
    }

    /** GET ?action=chatFile&id=<message_id> — serve attachment with auth check */
    public function chatFile(): void {
        $msgId = (int) ($_GET['id'] ?? 0);
        if ($msgId <= 0) {
            http_response_code(404);
            echo 'Not Found';
            exit;
        }

        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT user_id, attachment_path, attachment_name, attachment_mime FROM messages WHERE id = ?');
        $stmt->execute([$msgId]);
        $msg = $stmt->fetch();
        if (!$msg || empty($msg['attachment_path'])) {
            http_response_code(404);
            echo 'Not Found';
            exit;
        }

        $user = $_SESSION['user'];
        $isAdmin = ($user['role'] ?? 'user') === 'admin';
        if (!$isAdmin && (int) $msg['user_id'] !== (int) $user['id']) {
            http_response_code(403);
            echo 'Forbidden';
            exit;
        }

        $path = ChatUpload::absolutePath($msg['attachment_path']);
        if (!is_file($path)) {
            http_response_code(404);
            echo 'File missing';
            exit;
        }

        $mime = $msg['attachment_mime'] ?: 'application/octet-stream';
        $name = $msg['attachment_name'] ?: basename($msg['attachment_path']);
        $isImage = strpos($mime, 'image/') === 0;
        $disposition = $isImage ? 'inline' : 'attachment';

        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($path));
        header('Cache-Control: private, max-age=3600');
        header(sprintf('Content-Disposition: %s; filename="%s"', $disposition, addslashes($name)));
        readfile($path);
        exit;
    }

    private function json(array $payload, int $status = 200): void {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($payload);
        exit;
    }
}
