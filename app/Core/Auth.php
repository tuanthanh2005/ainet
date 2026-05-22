<?php

class Auth {
    /**
     * Get current user from session, optionally refresh from database.
     * Refreshing ensures role/status changes take effect without re-login.
     */
    public static function user(bool $refreshFromDb = false): ?array {
        $sessionUser = $_SESSION['user'] ?? null;
        if (!$sessionUser) {
            return null;
        }

        if ($refreshFromDb) {
            $fresh = User::findById($sessionUser['id']);
            if (!$fresh) {
                self::logout();
                return null;
            }

            // Re-sync the session with current DB values
            $_SESSION['user'] = [
                'id'    => $fresh['id'],
                'name'  => $fresh['name'],
                'email' => $fresh['email'],
                'role'  => $fresh['role'],
            ];
            $sessionUser = $_SESSION['user'];

            if (($fresh['status'] ?? '') !== 'active') {
                self::logout();
                return null;
            }
        }

        return $sessionUser;
    }

    public static function check(): bool {
        return !empty($_SESSION['user']);
    }

    public static function login(array $user): void {
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id'    => $user['id'],
            'name'  => $user['name'],
            'email' => $user['email'],
            'role'  => $user['role'],
        ];
        // Reset rate-limit counter on success
        unset($_SESSION['login_attempts']);
    }

    public static function logout(): void {
        // Clear session data
        $_SESSION = [];

        // Destroy session cookie
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();

        // Re-start a fresh session for flash messages
        session_start();
        session_regenerate_id(true);
    }

    public static function requireLogin(): void {
        if (!self::check()) {
            $_SESSION['flash_error'] = 'Vui lòng đăng nhập để tiếp tục.';
            header('Location: ' . url());
            exit;
        }
    }

    public static function requireAdmin(): void {
        $user = self::user(true);

        if (!$user) {
            $_SESSION['flash_error'] = 'Vui lòng đăng nhập bằng tài khoản admin.';
            header('Location: ' . url());
            exit;
        }

        if (($user['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo '403 Forbidden - Bạn không có quyền truy cập trang quản trị.';
            exit;
        }
    }

    /**
     * Simple per-session brute-force protection.
     * Returns true if request is allowed, false if temporarily blocked.
     */
    public static function checkLoginRateLimit(int $maxAttempts = 5, int $windowSeconds = 300): bool {
        $attempts = $_SESSION['login_attempts'] ?? ['count' => 0, 'last' => 0];
        if ($attempts['count'] >= $maxAttempts && (time() - $attempts['last']) < $windowSeconds) {
            return false;
        }
        // Reset window if expired
        if ((time() - $attempts['last']) >= $windowSeconds) {
            $_SESSION['login_attempts'] = ['count' => 0, 'last' => time()];
        }
        return true;
    }

    public static function recordFailedLogin(): void {
        $attempts = $_SESSION['login_attempts'] ?? ['count' => 0, 'last' => 0];
        $attempts['count'] = ($attempts['count'] ?? 0) + 1;
        $attempts['last'] = time();
        $_SESSION['login_attempts'] = $attempts;
    }
}
