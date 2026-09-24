<?php

class IndexingService {
    private const SCOPE = 'https://www.googleapis.com/auth/indexing';
    private const ENDPOINT = 'https://indexing.googleapis.com/v3/urlNotifications:publish';
    public const CANONICAL_HOST = 'aicuatoi.net';

    private static ?string $cachedToken = null;
    private static int $tokenExpiresAt = 0;

    public static function canonicalizeForGoogle(string $url): string {
        $url = trim($url);
        if ($url === '') {
            return '';
        }

        // Relative path like /san-pham/xyz
        if (strpos($url, '/') === 0) {
            return 'https://' . self::CANONICAL_HOST . $url;
        }

        // Parse host and path
        $parsed = parse_url($url);
        if (!$parsed || empty($parsed['host'])) {
            return 'https://' . self::CANONICAL_HOST . '/' . ltrim($url, '/');
        }

        $host = strtolower($parsed['host']);
        $path = $parsed['path'] ?? '/';
        $query = isset($parsed['query']) ? '?' . $parsed['query'] : '';

        // If host is localhost, 127.0.0.1 or local dev, map to canonical host
        if ($host === 'localhost' || $host === '127.0.0.1' || str_ends_with($host, '.local') || str_ends_with($host, '.test')) {
            return 'https://' . self::CANONICAL_HOST . $path . $query;
        }

        // Ensure https
        $scheme = $parsed['scheme'] ?? 'https';
        if ($scheme !== 'https') {
            $url = 'https://' . substr($url, strlen($scheme) + 3);
        }

        return $url;
    }

    public static function submitUrl(string $url, string $type = 'URL_UPDATED'): array {
        $url = self::canonicalizeForGoogle($url);
        $type = $type === 'URL_DELETED' ? 'URL_DELETED' : 'URL_UPDATED';

        if ($url === '') {
            self::log('Skipped indexing request: empty URL.');
            return ['success' => false, 'message' => 'Empty URL.', 'url' => ''];
        }

        if (!self::enabled()) {
            self::log('Skipped indexing request because GOOGLE_INDEXING_ENABLED is false: ' . $url);
            return ['success' => false, 'message' => 'Indexing API is disabled in .env.', 'url' => $url];
        }

        try {
            $token = self::accessToken();
            $result = self::postJson(self::ENDPOINT, [
                'url' => $url,
                'type' => $type,
            ], [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json',
            ]);

            if (($result['status'] ?? 0) >= 200 && ($result['status'] ?? 0) < 300) {
                self::log('Indexing API submitted ' . $type . ': ' . $url);
                $notifyTime = $result['body']['urlNotificationMetadata']['latestUpdate']['notifyTime'] ?? date('c');
                return [
                    'success' => true,
                    'message' => 'Google đã tiếp nhận URL',
                    'url' => $url,
                    'notifyTime' => $notifyTime,
                    'response' => $result['body'] ?? null
                ];
            }

            $errMsg = $result['body']['error']['message'] ?? 'Google rejected indexing request.';
            self::log('Indexing API submit failed for ' . $url . ': ' . json_encode($result));
            return [
                'success' => false,
                'message' => $errMsg,
                'url' => $url,
                'status' => $result['status'] ?? 400,
                'response' => $result
            ];
        } catch (Throwable $e) {
            self::log('Indexing API exception for ' . $url . ': ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage(), 'url' => $url];
        }
    }

    public static function submitUrls(array $urls, string $type = 'URL_UPDATED'): array {
        $results = [];
        foreach (array_values(array_unique(array_filter($urls))) as $url) {
            $results[$url] = self::submitUrl((string) $url, $type);
        }
        return $results;
    }

    public static function getStatus(): array {
        $enabled = self::enabled();
        $credsPath = trim((string) (getenv('GOOGLE_INDEXING_CREDENTIALS') ?: ''));
        $resolved = $credsPath !== '' ? self::resolveCredentialsPath($credsPath) : null;
        $clientEmail = '';
        $projectId = '';

        if ($resolved && is_file($resolved)) {
            $data = json_decode((string) file_get_contents($resolved), true);
            if (is_array($data)) {
                $clientEmail = $data['client_email'] ?? '';
                $projectId = $data['project_id'] ?? '';
            }
        }

        return [
            'enabled' => $enabled,
            'configured' => $resolved !== null && $clientEmail !== '',
            'service_account' => $clientEmail,
            'project_id' => $projectId,
            'daily_limit' => 200,
            'canonical_host' => self::CANONICAL_HOST
        ];
    }

    public static function getUrlsByType(string $type): array {
        $list = [];

        if ($type === 'products' || $type === 'all') {
            try {
                foreach (Product::getAll() as $product) {
                    if (($product['status'] ?? 'active') !== 'hidden') {
                        $list[] = [
                            'url' => self::canonicalizeForGoogle(Url::product($product)),
                            'title' => $product['title'] ?? 'Sản phẩm',
                            'type' => 'Sản phẩm',
                            'badge' => 'primary'
                        ];
                    }
                }
            } catch (Throwable $e) {}
        }

        if ($type === 'categories' || $type === 'all') {
            try {
                foreach (Category::getAll() as $cat) {
                    $slug = trim((string) ($cat['seo_slug'] ?: ($cat['slug'] ?? '')));
                    if ($slug !== '') {
                        $list[] = [
                            'url' => self::canonicalizeForGoogle(Url::category($slug)),
                            'title' => $cat['name'] ?? $slug,
                            'type' => 'Danh mục',
                            'badge' => 'info'
                        ];
                    }
                }
            } catch (Throwable $e) {}
        }

        if ($type === 'blogs' || $type === 'all') {
            try {
                foreach (Blog::getAll() as $blog) {
                    $list[] = [
                        'url' => self::canonicalizeForGoogle(Url::blog($blog)),
                        'title' => $blog['title'] ?? 'Bài viết',
                        'type' => 'Tin tức',
                        'badge' => 'success'
                    ];
                }
            } catch (Throwable $e) {}
        }

        if ($type === 'keywords' || $type === 'all') {
            try {
                $path = APP_ROOT . '/config/seo_keywords.json';
                if (file_exists($path)) {
                    $seoData = json_decode(file_get_contents($path), true);
                    if (isset($seoData['keywords']) && is_array($seoData['keywords'])) {
                        foreach ($seoData['keywords'] as $kw => $info) {
                            $list[] = [
                                'url' => self::canonicalizeForGoogle(Url::search($kw)),
                                'title' => $info['display_name'] ?? $kw,
                                'type' => 'Từ khóa SEO',
                                'badge' => 'warning'
                            ];
                        }
                    }
                }
            } catch (Throwable $e) {}
        }

        if ($type === 'all') {
            $core = [
                ['url' => 'https://' . self::CANONICAL_HOST . '/', 'title' => 'Trang chủ', 'type' => 'Trang chính', 'badge' => 'dark'],
                ['url' => 'https://' . self::CANONICAL_HOST . '/san-pham', 'title' => 'Trang sản phẩm', 'type' => 'Trang chính', 'badge' => 'dark'],
                ['url' => 'https://' . self::CANONICAL_HOST . '/tap-chi', 'title' => 'Trang tạp chí', 'type' => 'Trang chính', 'badge' => 'dark'],
                ['url' => 'https://' . self::CANONICAL_HOST . '/gioi-thieu', 'title' => 'Trang giới thiệu', 'type' => 'Trang chính', 'badge' => 'dark'],
                ['url' => 'https://' . self::CANONICAL_HOST . '/lien-he', 'title' => 'Trang liên hệ', 'type' => 'Trang chính', 'badge' => 'dark'],
                ['url' => 'https://' . self::CANONICAL_HOST . '/sitemap.xml', 'title' => 'Sitemap XML', 'type' => 'Hệ thống', 'badge' => 'secondary'],
            ];
            $list = array_merge($core, $list);
        }

        $seen = [];
        $unique = [];
        foreach ($list as $item) {
            if (!isset($seen[$item['url']])) {
                $seen[$item['url']] = true;
                $unique[] = $item;
            }
        }
        return $unique;
    }

    public static function submitAllPublicUrls(): array {
        $urls = array_column(self::getUrlsByType('all'), 'url');
        return self::submitUrls($urls, 'URL_UPDATED');
    }

    private static function enabled(): bool {
        return filter_var(getenv('GOOGLE_INDEXING_ENABLED') ?: 'false', FILTER_VALIDATE_BOOLEAN);
    }

    private static function credentials(): array {
        $path = trim((string) (getenv('GOOGLE_INDEXING_CREDENTIALS') ?: ''));
        if ($path === '') {
            throw new RuntimeException('Missing GOOGLE_INDEXING_CREDENTIALS path.');
        }
        $path = self::resolveCredentialsPath($path);
        if ($path === null) {
            throw new RuntimeException('Google credentials file is not readable. Tried: ' . trim((string) (getenv('GOOGLE_INDEXING_CREDENTIALS') ?: '')));
        }

        $data = json_decode((string) file_get_contents($path), true);
        if (!is_array($data) || empty($data['client_email']) || empty($data['private_key']) || empty($data['token_uri'])) {
            throw new RuntimeException('Invalid Google service account JSON.');
        }
        return $data;
    }

    private static function resolveCredentialsPath(string $path): ?string {
        $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, trim($path));
        $isAbsolute = preg_match('/^[A-Za-z]:[\/\\\\]|^\//', $path);
        $withoutPublicHtml = preg_replace('/^public_html[\/\\\\]/', '', $path);

        $candidates = $isAbsolute ? [$path] : [
            base_path($path),
            public_path($path),
            public_path($withoutPublicHtml),
            dirname(public_path()) . DIRECTORY_SEPARATOR . $path,
        ];

        foreach (array_unique($candidates) as $candidate) {
            if (is_file($candidate) && is_readable($candidate)) {
                return $candidate;
            }
        }

        self::log('Credentials path not found. Configured: ' . $path . ' Tried: ' . implode(' | ', $candidates));
        return null;
    }

    private static function accessToken(): string {
        if (self::$cachedToken !== null && time() < (self::$tokenExpiresAt - 60)) {
            return self::$cachedToken;
        }

        $creds = self::credentials();
        $now = time();
        $jwt = self::base64Url(json_encode(['alg' => 'RS256', 'typ' => 'JWT']))
             . '.'
             . self::base64Url(json_encode([
                 'iss' => $creds['client_email'],
                 'scope' => self::SCOPE,
                 'aud' => $creds['token_uri'],
                 'iat' => $now,
                 'exp' => $now + 3600,
             ]));

        $signature = '';
        if (!openssl_sign($jwt, $signature, $creds['private_key'], OPENSSL_ALGO_SHA256)) {
            throw new RuntimeException('Could not sign Google JWT.');
        }
        $assertion = $jwt . '.' . self::base64Url($signature);

        $result = self::postForm($creds['token_uri'], [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $assertion,
        ]);

        if (($result['status'] ?? 0) < 200 || ($result['status'] ?? 0) >= 300 || empty($result['body']['access_token'])) {
            throw new RuntimeException('Could not get Google access token: ' . json_encode($result));
        }

        self::$cachedToken = (string) $result['body']['access_token'];
        self::$tokenExpiresAt = $now + (int) ($result['body']['expires_in'] ?? 3600);

        return self::$cachedToken;
    }

    private static function postForm(string $url, array $data): array {
        return self::curlRequest($url, http_build_query($data), [
            'Content-Type: application/x-www-form-urlencoded',
        ]);
    }

    private static function postJson(string $url, array $data, array $headers): array {
        return self::curlRequest($url, json_encode($data), $headers);
    }

    private static function curlRequest(string $url, string $body, array $headers): array {
        if (!function_exists('curl_init')) {
            throw new RuntimeException('PHP cURL extension is required for Google Indexing API.');
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_CONNECTTIMEOUT => 8,
            CURLOPT_TIMEOUT => 20,
        ]);
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false) {
            throw new RuntimeException('cURL error: ' . $error);
        }

        $decoded = json_decode((string) $response, true);
        return [
            'status' => $status,
            'body' => is_array($decoded) ? $decoded : $response,
        ];
    }

    private static function base64Url(string $data): string {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function log(string $message): void {
        $dir = base_path('storage/logs');
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        @file_put_contents($dir . '/indexing.log', '[' . date('c') . '] ' . $message . PHP_EOL, FILE_APPEND);
    }
}
