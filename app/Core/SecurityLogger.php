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

    /**
     * Cơ chế chặn / khóa IP đã bị tắt theo yêu cầu.
     * Luôn trả về danh sách rỗng.
     */
    public static function getBannedIps(): array {
        return [];
    }

    /**
     * Không chặn bất kỳ IP nào.
     */
    public static function isIpBanned(string $ip): bool {
        return false;
    }

    /**
     * Vô hiệu hóa việc cấm / khóa IP.
     */
    public static function banIp(string $ip, string $reason, string $payload = ''): void {
        // Disabled: Cơ chế khóa IP đã tắt.
    }

    /**
     * Gỡ ban nếu có.
     */
    public static function unbanIp(string $ip): void {
        try {
            $db = Database::getInstance();
            $stmt = $db->prepare("DELETE FROM banned_ips WHERE ip = :ip");
            $stmt->execute(['ip' => $ip]);
        } catch (Throwable $ignored) {}

        $file = self::getBannedIpsFile();
        if (file_exists($file)) {
            @unlink($file);
        }
    }

    /**
     * Không ghi nhận session file để tối ưu hiệu năng và tắt log.
     */
    public static function touchSession(string $currentUrl = ''): void {
        // Disabled: Tắt log session.
    }

    public static function getActiveSessions(): array {
        return [];
    }

    /**
     * Vô hiệu hóa toàn bộ log thao tác.
     */
    public static function logActivity(string $actionType, string $details = '', bool $isSuspicious = false): void {
        // Disabled: Toàn bộ ghi log đã tắt theo yêu cầu.
    }

    public static function clearLogs(): void {
        $file = self::getLogsFile();
        if (file_exists($file)) {
            @unlink($file);
        }
        $bannedFile = self::getBannedIpsFile();
        if (file_exists($bannedFile)) {
            @unlink($bannedFile);
        }
        $sessFile = self::getActiveSessionsFile();
        if (file_exists($sessFile)) {
            @unlink($sessFile);
        }
        try {
            $db = Database::getInstance();
            $db->exec("TRUNCATE TABLE security_logs");
        } catch (Throwable $e) {}
        try {
            $db = Database::getInstance();
            $db->exec("TRUNCATE TABLE banned_ips");
        } catch (Throwable $e) {}
    }

    public static function getLogs(int $limit = 200): array {
        return [];
    }

    /**
     * Kiểm tra WAF / Filter: Đã tắt toàn bộ việc chặn / khóa IP.
     */
    public static function inspectAndFilter(): bool {
        // Disabled: Không chặn hoặc khóa IP nào.
        return false;
    }

    /**
     * Bắt buộc đăng nhập khi khách vãng lai đã sử dụng quá 5 phút (300 giây).
     */
    public static function checkGuestSession(): bool {
        if (Auth::check()) {
            unset($_SESSION['guest_started_at'], $_SESSION['guest_expired']);
            if (isset($_COOKIE['ainet_guest_started_at'])) {
                setcookie('ainet_guest_started_at', '', time() - 3600, '/');
                unset($_COOKIE['ainet_guest_started_at']);
            }
            return false;
        }

        $sessionStarted = !empty($_SESSION['guest_started_at']) ? (int)$_SESSION['guest_started_at'] : 0;
        $cookieStarted = !empty($_COOKIE['ainet_guest_started_at']) && is_numeric($_COOKIE['ainet_guest_started_at'])
            ? (int)$_COOKIE['ainet_guest_started_at']
            : 0;

        $currentTime = time();

        if ($sessionStarted > 0 && $cookieStarted > 0) {
            $startedAt = min($sessionStarted, $cookieStarted);
        } elseif ($sessionStarted > 0) {
            $startedAt = $sessionStarted;
        } elseif ($cookieStarted > 0 && $cookieStarted <= $currentTime) {
            $startedAt = $cookieStarted;
        } else {
            $startedAt = $currentTime;
        }

        $_SESSION['guest_started_at'] = $startedAt;

        if (!isset($_COOKIE['ainet_guest_started_at']) || (int)$_COOKIE['ainet_guest_started_at'] !== $startedAt) {
            setcookie('ainet_guest_started_at', (string)$startedAt, [
                'expires' => $currentTime + 7 * 86400,
                'path' => '/',
                'httponly' => false,
                'samesite' => 'Lax'
            ]);
        }

        $elapsedSeconds = $currentTime - $startedAt;
        // 5 phút = 300 giây
        if ($elapsedSeconds >= 300) {
            $_SESSION['guest_expired'] = true;
            return true;
        }

        return false;
    }
}
