<?php

class SecurityLogger {
    private static function getStorageDir(): string {
        $dir = APP_ROOT . '/storage/logs';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        return $dir;
    }

    private static function getBannedIpsFile(): string {
        return self::getStorageDir() . '/banned_ips.json';
    }

    private static function getLogsFile(): string {
        return self::getStorageDir() . '/security_logs.json';
    }

    private static function getActiveSessionsFile(): string {
        return self::getStorageDir() . '/active_sessions.json';
    }

    public static function getClientIp(): string {
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            return trim($_SERVER['HTTP_CF_CONNECTING_IP']);
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        }
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    public static function getBannedIps(): array {
        $file = self::getBannedIpsFile();
        if (file_exists($file)) {
            $data = json_decode(file_get_contents($file), true);
            return is_array($data) ? $data : [];
        }
        return [];
    }

    public static function isIpBanned(string $ip): bool {
        $banned = self::getBannedIps();
        return isset($banned[$ip]);
    }

    public static function banIp(string $ip, string $reason, string $payload = ''): void {
        $banned = self::getBannedIps();
        $entry = [
            'ip' => $ip,
            'reason' => $reason,
            'payload' => $payload,
            'banned_at' => date('Y-m-d H:i:s'),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'url' => $_SERVER['REQUEST_URI'] ?? ''
        ];
        $banned[$ip] = $entry;

        @file_put_contents(self::getBannedIpsFile(), json_encode($banned, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // Try DB table storage as backup
        try {
            $db = Database::getInstance();
            $db->exec("CREATE TABLE IF NOT EXISTS banned_ips (
                id INT AUTO_INCREMENT PRIMARY KEY,
                ip VARCHAR(45) UNIQUE NOT NULL,
                reason VARCHAR(255),
                payload TEXT,
                banned_at DATETIME,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

            $stmt = $db->prepare("INSERT INTO banned_ips (ip, reason, payload, banned_at) VALUES (:ip, :reason, :payload, :banned_at) ON DUPLICATE KEY UPDATE reason = :reason2, payload = :payload2, banned_at = :banned_at2");
            $now = date('Y-m-d H:i:s');
            $stmt->execute([
                'ip' => $ip, 'reason' => $reason, 'payload' => substr($payload, 0, 500), 'banned_at' => $now,
                'reason2' => $reason, 'payload2' => substr($payload, 0, 500), 'banned_at2' => $now
            ]);
        } catch (Throwable $ignored) {}

        // Send Urgent Telegram Notification
        if (class_exists('TelegramService')) {
            $msg = "🚨 *CẢNH BÁO BẢO MẬT: ĐÃ BLOCK HACKER VĨNH VIỄN!*\n\n"
                 . "📌 *IP:* `" . $ip . "`\n"
                 . "🔗 *URL / Payload:* `" . htmlspecialchars(substr($payload ?: ($_SERVER['REQUEST_URI'] ?? ''), 0, 200)) . "`\n"
                 . "👤 *Session:* `" . session_id() . "`\n"
                 . "🕒 *Thời gian:* " . date('Y-m-d H:i:s') . "\n"
                 . "📝 *Lý do:* " . $reason . "\n\n"
                 . "🔒 *Trạng thái:* Đã cấm IP vĩnh viễn truy cập website.";
            TelegramService::sendRaw($msg);
        }
    }

    public static function unbanIp(string $ip): void {
        $banned = self::getBannedIps();
        if (isset($banned[$ip])) {
            unset($banned[$ip]);
            @file_put_contents(self::getBannedIpsFile(), json_encode($banned, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }

        try {
            $db = Database::getInstance();
            $stmt = $db->prepare("DELETE FROM banned_ips WHERE ip = :ip");
            $stmt->execute(['ip' => $ip]);
        } catch (Throwable $ignored) {}
    }

    /**
     * Touch current session to track active online users (realtime 2-minute window)
     */
    public static function touchSession(string $currentUrl = ''): void {
        $sid = session_id();
        if (!$sid) return;

        $ip = self::getClientIp();
        $user = Auth::user();
        $userInfo = $user ? ($user['name'] . ' (' . $user['email'] . ')') : 'Khách vãng lai';
        $now = time();

        $file = self::getActiveSessionsFile();
        $sessions = file_exists($file) ? (json_decode(file_get_contents($file), true) ?: []) : [];

        // Prune sessions inactive for > 120 seconds (move off users to history)
        foreach ($sessions as $key => $sess) {
            if (($now - ($sess['last_seen_time'] ?? 0)) > 120) {
                unset($sessions[$key]);
            }
        }

        $url = $currentUrl !== '' ? $currentUrl : ($_SERVER['REQUEST_URI'] ?? '/');
        
        // Update current active session
        $sessions[$sid] = [
            'session_id' => $sid,
            'ip' => $ip,
            'user_info' => $userInfo,
            'is_logged_in' => !empty($user),
            'current_url' => $url,
            'last_seen' => date('Y-m-d H:i:s'),
            'last_seen_time' => $now,
            'started_at' => $sessions[$sid]['started_at'] ?? date('Y-m-d H:i:s'),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
        ];

        @file_put_contents($file, json_encode($sessions, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public static function getActiveSessions(): array {
        $file = self::getActiveSessionsFile();
        if (!file_exists($file)) return [];

        $sessions = json_decode(file_get_contents($file), true) ?: [];
        $now = time();
        $active = [];

        foreach ($sessions as $sid => $sess) {
            $diff = $now - ($sess['last_seen_time'] ?? 0);
            if ($diff <= 120) { // Active in last 2 minutes
                $sess['online_status'] = 'online';
                $sess['seconds_ago'] = $diff;
                $active[] = $sess;
            }
        }

        usort($active, function($a, $b) {
            return ($b['last_seen_time'] ?? 0) <=> ($a['last_seen_time'] ?? 0);
        });

        return $active;
    }

    public static function logActivity(string $actionType, string $details = '', bool $isSuspicious = false): void {
        // Deduplicate consecutive identical URL logs within 60 seconds unless suspicious
        if (!$isSuspicious) {
            $currentUri = $_SERVER['REQUEST_URI'] ?? '/';
            $lastLog = $_SESSION['last_activity_log'] ?? null;
            $nowTime = time();
            if ($lastLog && ($lastLog['url'] === $currentUri) && ($lastLog['action'] === $actionType) && ($nowTime - $lastLog['time']) < 60) {
                return;
            }
            $_SESSION['last_activity_log'] = [
                'url' => $currentUri,
                'action' => $actionType,
                'time' => $nowTime
            ];
        }

        $ip = self::getClientIp();
        $user = Auth::user();
        $userInfo = $user ? ($user['name'] . ' (' . $user['email'] . ')') : 'Khách vãng lai';

        $logEntry = [
            'id' => uniqid('log_'),
            'timestamp' => date('Y-m-d H:i:s'),
            'ip' => $ip,
            'session_id' => session_id(),
            'user' => $userInfo,
            'is_logged_in' => !empty($user),
            'action_type' => $actionType,
            'url' => $_SERVER['REQUEST_URI'] ?? '/',
            'details' => $details,
            'is_suspicious' => $isSuspicious,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
        ];

        // Save to JSON log file
        $file = self::getLogsFile();
        $logs = [];
        if (file_exists($file)) {
            $logs = json_decode(file_get_contents($file), true) ?: [];
        }
        array_unshift($logs, $logEntry);
        // Keep max 1000 latest logs
        if (count($logs) > 1000) {
            $logs = array_slice($logs, 0, 1000);
        }
        @file_put_contents($file, json_encode($logs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // Save to MySQL table
        try {
            $db = Database::getInstance();
            $db->exec("CREATE TABLE IF NOT EXISTS security_logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                timestamp DATETIME,
                ip VARCHAR(45),
                session_id VARCHAR(128),
                user_info VARCHAR(255),
                is_logged_in TINYINT(1) DEFAULT 0,
                action_type VARCHAR(100),
                url TEXT,
                details TEXT,
                is_suspicious TINYINT(1) DEFAULT 0,
                user_agent VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

            $stmt = $db->prepare("INSERT INTO security_logs (timestamp, ip, session_id, user_info, is_logged_in, action_type, url, details, is_suspicious, user_agent) VALUES (:timestamp, :ip, :session_id, :user_info, :is_logged_in, :action_type, :url, :details, :is_suspicious, :user_agent)");
            $stmt->execute([
                'timestamp' => $logEntry['timestamp'],
                'ip' => $ip,
                'session_id' => session_id(),
                'user_info' => $userInfo,
                'is_logged_in' => $logEntry['is_logged_in'] ? 1 : 0,
                'action_type' => $actionType,
                'url' => substr($logEntry['url'], 0, 500),
                'details' => substr($details, 0, 500),
                'is_suspicious' => $isSuspicious ? 1 : 0,
                'user_agent' => substr($logEntry['user_agent'], 0, 255)
            ]);
        } catch (Throwable $ignored) {}
    }

    public static function getLogs(int $limit = 200): array {
        $file = self::getLogsFile();
        if (file_exists($file)) {
            $data = json_decode(file_get_contents($file), true);
            if (is_array($data)) {
                return array_slice($data, 0, $limit);
            }
        }

        try {
            $db = Database::getInstance();
            $stmt = $db->prepare("SELECT * FROM security_logs ORDER BY id DESC LIMIT :limit");
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Throwable $e) {
            return [];
        }
    }

    /**
     * Inspect request for malicious SQLi/XSS/Probe patterns.
     * Returns true if malicious probe detected, false otherwise.
     */
    public static function inspectAndFilter(): bool {
        $ip = self::getClientIp();

        // 1. Check if IP is already permanently banned
        if (self::isIpBanned($ip)) {
            http_response_code(403);
            header('Content-Type: text/html; charset=utf-8');
            echo '<!DOCTYPE html><html><head><title>403 Access Denied</title><style>body{font-family:sans-serif;background:#0f172a;color:#f8fafc;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;}.card{background:#1e293b;padding:2rem;border-radius:1rem;max-width:500px;text-align:center;box-shadow:0 20px 25px -5px rgba(0,0,0,0.5);}h1{color:#ef4444;margin-top:0;}</style></head><body><div class="card"><h1>⛔ TRUY CẬP BỊ CẤM VĨNH VIỄN</h1><p>Địa chỉ IP <strong>' . htmlspecialchars($ip) . '</strong> của bạn đã bị hệ thống tự động khóa vĩnh viễn do phát hiện hành vi tấn công/thăm dò an ninh mạng.</p><p style="font-size:0.85rem;color:#94a3b8;">Sự cố đã được ghi nhận và gửi thông báo khẩn tới Admin.</p></div></body></html>';
            exit;
        }

        $uri = $_SERVER['REQUEST_URI'] ?? '';
        $rawUrlDecoded = urldecode($uri);
        $fullInput = $rawUrlDecoded . ' ' . json_encode($_GET) . ' ' . json_encode($_POST);

        // Patterns triggering automatic permanent IP Ban
        $maliciousPatterns = [
            '/tesst2jasd/i',
            '/union\s+all\s+select/i',
            '/union\s+select/i',
            '/select\s+.*\s+from\s+information_schema/i',
            '/concat\s*\(/i',
            '/group_concat\s*\(/i',
            '/benchmark\s*\(/i',
            '/sleep\s*\(/i',
            '/\/\*!\d+.*?\*\//i',
            '/\b(drop|alter|truncate)\s+(table|database)\b/i',
            '/\' OR \'1\'=\'1/i',
            '/" OR "1"="1/i',
            '/\' OR 1=1/i',
            '/" OR 1=1/i',
            '/<script/i',
            '/eval\s*\(/i'
        ];

        foreach ($maliciousPatterns as $pattern) {
            if (preg_match($pattern, $fullInput)) {
                $reason = "Phát hiện ký tự/mẫu câu lệnh tấn công web (" . htmlspecialchars($pattern) . ")";
                self::banIp($ip, $reason, $fullInput);
                self::logActivity('ATTACK_BLOCKED', "Tấn công bị chặn: " . $rawUrlDecoded, true);

                http_response_code(403);
                header('Content-Type: text/html; charset=utf-8');
                echo '<!DOCTYPE html><html><head><title>403 Forbidden</title><style>body{font-family:sans-serif;background:#0f172a;color:#f8fafc;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;}.card{background:#1e293b;padding:2rem;border-radius:1rem;max-width:500px;text-align:center;box-shadow:0 20px 25px -5px rgba(0,0,0,0.5);}h1{color:#ef4444;margin-top:0;}</style></head><body><div class="card"><h1>⛔ TRUY CẬP BỊ BLOCK VĨNH VIỄN</h1><p>Hệ thống tự động phát hiện hành vi gõ URL/Payload thử nghiệm hack (<strong>' . htmlspecialchars(substr($rawUrlDecoded, 0, 100)) . '</strong>).</p><p>Địa chỉ IP <strong>' . htmlspecialchars($ip) . '</strong> đã bị cấm vĩnh viễn và thông báo khẩn đã được chuyển tới Admin qua Telegram.</p></div></body></html>';
                exit;
            }
        }

        return false;
    }

    /**
     * Handle 5-minute guest session limit
     */
    public static function checkGuestSession(): bool {
        if (Auth::check()) {
            unset($_SESSION['guest_started_at']);
            unset($_SESSION['guest_expired']);
            return false;
        }

        if (empty($_SESSION['guest_started_at'])) {
            $_SESSION['guest_started_at'] = time();
        }

        $elapsedSeconds = time() - $_SESSION['guest_started_at'];
        // 5 minutes = 300 seconds
        if ($elapsedSeconds > 300) {
            $_SESSION['guest_expired'] = true;
            return true;
        }

        return false;
    }
}
