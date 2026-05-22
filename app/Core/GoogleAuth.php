<?php

/**
 * GoogleAuth — OAuth 2.0 với Google, không cần Composer
 *
 * Sử dụng:
 *   $url = GoogleAuth::getAuthUrl();         // Redirect user đến đây
 *   $tokens = GoogleAuth::fetchTokens($code); // Lấy access_token từ code
 *   $info = GoogleAuth::fetchUserInfo($token); // Lấy thông tin user
 */
class GoogleAuth {

    private static function clientId(): string {
        return defined('GOOGLE_CLIENT_ID') ? GOOGLE_CLIENT_ID : '';
    }

    private static function clientSecret(): string {
        return defined('GOOGLE_CLIENT_SECRET') ? GOOGLE_CLIENT_SECRET : '';
    }

    private static function redirectUri(): string {
        return defined('GOOGLE_REDIRECT_URI') ? GOOGLE_REDIRECT_URI
            : rtrim(URLROOT, '/') . '/index.php?action=googleCallback';
    }

    /**
     * Tạo URL để redirect user đến Google Authorization
     */
    public static function getAuthUrl(): string {
        $state = bin2hex(random_bytes(16));
        $_SESSION['google_oauth_state'] = $state;

        $params = http_build_query([
            'client_id'     => self::clientId(),
            'redirect_uri'  => self::redirectUri(),
            'response_type' => 'code',
            'scope'         => 'openid email profile',
            'access_type'   => 'online',
            'state'         => $state,
            'prompt'        => 'select_account',
        ]);

        return 'https://accounts.google.com/o/oauth2/v2/auth?' . $params;
    }

    /**
     * Xác minh state để chống CSRF
     */
    public static function verifyState(string $state): bool {
        $stored = $_SESSION['google_oauth_state'] ?? '';
        unset($_SESSION['google_oauth_state']);
        return $stored !== '' && hash_equals($stored, $state);
    }

    /**
     * Đổi authorization code lấy access_token
     * Trả về array ['access_token' => ..., 'id_token' => ...] hoặc null nếu lỗi
     */
    public static function fetchTokens(string $code): ?array {
        $body = http_build_query([
            'code'          => $code,
            'client_id'     => self::clientId(),
            'client_secret' => self::clientSecret(),
            'redirect_uri'  => self::redirectUri(),
            'grant_type'    => 'authorization_code',
        ]);

        $context = stream_context_create([
            'http' => [
                'method'        => 'POST',
                'header'        => "Content-Type: application/x-www-form-urlencoded\r\nContent-Length: " . strlen($body),
                'content'       => $body,
                'timeout'       => 10,
                'ignore_errors' => true,
            ],
            'ssl' => [
                'verify_peer'      => true,
                'verify_peer_name' => true,
            ],
        ]);

        $response = @file_get_contents('https://oauth2.googleapis.com/token', false, $context);
        if (!$response) return null;

        $data = json_decode($response, true);
        if (empty($data['access_token'])) return null;

        return $data;
    }

    /**
     * Lấy thông tin user từ access_token
     * Trả về ['id', 'name', 'email', 'picture'] hoặc null
     */
    public static function fetchUserInfo(string $accessToken): ?array {
        $context = stream_context_create([
            'http' => [
                'method'        => 'GET',
                'header'        => "Authorization: Bearer {$accessToken}",
                'timeout'       => 10,
                'ignore_errors' => true,
            ],
            'ssl' => [
                'verify_peer'      => true,
                'verify_peer_name' => true,
            ],
        ]);

        $response = @file_get_contents('https://www.googleapis.com/oauth2/v3/userinfo', false, $context);
        if (!$response) return null;

        $data = json_decode($response, true);
        if (empty($data['email'])) return null;

        return [
            'id'      => $data['sub'] ?? '',
            'name'    => $data['name'] ?? $data['email'],
            'email'   => strtolower($data['email']),
            'picture' => $data['picture'] ?? '',
            'verified'=> $data['email_verified'] ?? false,
        ];
    }

    /**
     * Kiểm tra Google OAuth có được cấu hình không
     */
    public static function isConfigured(): bool {
        return self::clientId() !== '' && self::clientSecret() !== '';
    }
}
