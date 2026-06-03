<?php

class IndexingService {
    private const SCOPE = 'https://www.googleapis.com/auth/indexing';
    private const ENDPOINT = 'https://indexing.googleapis.com/v3/urlNotifications:publish';

    public static function submitUrl(string $url, string $type = 'URL_UPDATED'): array {
        $url = trim($url);
        $type = $type === 'URL_DELETED' ? 'URL_DELETED' : 'URL_UPDATED';

        if ($url === '' || !self::enabled()) {
            return ['success' => false, 'message' => 'Indexing API is disabled.'];
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
                return ['success' => true, 'message' => 'Submitted', 'response' => $result['body'] ?? null];
            }

            self::log('Indexing API submit failed for ' . $url . ': ' . json_encode($result));
            return ['success' => false, 'message' => 'Google rejected indexing request.', 'response' => $result];
        } catch (Throwable $e) {
            self::log('Indexing API exception for ' . $url . ': ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public static function submitUrls(array $urls, string $type = 'URL_UPDATED'): array {
        $results = [];
        foreach (array_values(array_unique(array_filter($urls))) as $url) {
            $results[$url] = self::submitUrl((string) $url, $type);
        }
        return $results;
    }

    public static function submitAllPublicUrls(): array {
        $urls = [
            Url::home(),
            Url::products(),
            Url::blogs(),
            Url::about(),
            Url::contact(),
            url('sitemap.xml'),
        ];

        try {
            foreach (Category::getAll() as $cat) {
                $slug = trim((string) ($cat['seo_slug'] ?: ($cat['slug'] ?? '')));
                if ($slug !== '') {
                    $urls[] = Url::category($slug);
                }
            }
        } catch (Throwable $e) {
            self::log('Indexing categories collect failed: ' . $e->getMessage());
        }

        try {
            foreach (Product::getAll() as $product) {
                if (($product['status'] ?? 'active') !== 'hidden') {
                    $urls[] = Url::product($product);
                }
            }
        } catch (Throwable $e) {
            self::log('Indexing products collect failed: ' . $e->getMessage());
        }

        try {
            foreach (Blog::getAll() as $blog) {
                $urls[] = Url::blog($blog);
            }
        } catch (Throwable $e) {
            self::log('Indexing blogs collect failed: ' . $e->getMessage());
        }

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
        if (!preg_match('/^[A-Za-z]:[\/\\\\]|^\//', $path)) {
            $path = base_path($path);
        }
        if (!is_file($path) || !is_readable($path)) {
            throw new RuntimeException('Google credentials file is not readable: ' . $path);
        }
        $data = json_decode((string) file_get_contents($path), true);
        if (!is_array($data) || empty($data['client_email']) || empty($data['private_key']) || empty($data['token_uri'])) {
            throw new RuntimeException('Invalid Google service account JSON.');
        }
        return $data;
    }

    private static function accessToken(): string {
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

        return (string) $result['body']['access_token'];
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
