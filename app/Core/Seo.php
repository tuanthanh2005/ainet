<?php

class Seo {
    private static array $data = [];

    /**
     * Set page meta. Call once per page in controller before view().
     * Keys (all optional):
     *   title         => string  (page title, will be appended with site name)
     *   description   => string
     *   keywords      => string|array
     *   canonical     => string  (full URL)
     *   image         => string  (absolute URL of OG image)
     *   type          => string  (website|article|product), default website
     *   robots        => string  (e.g. "index,follow" or "noindex,nofollow")
     *   structured    => array   (JSON-LD payload, can be one schema or list of schemas)
     */
    public static function set(array $data): void {
        self::$data = array_merge(self::$data, $data);
    }

    public static function get(string $key, $default = null) {
        return self::$data[$key] ?? $default;
    }

    /**
     * Build the rendered <head> SEO block.
     * Falls back gracefully when fields are empty.
     */
    public static function render(array $settings = []): string {
        $siteName    = SITENAME;
        $defaultDesc = trim($settings['footerDesc'] ?? '') ?: ($siteName . ' - cửa hàng dịch vụ AI và tài khoản Premium uy tín tại Việt Nam.');
        $defaultImg  = $settings['about_image'] ?? '';

        $title       = self::$data['title'] ?? '';
        $fullTitle   = $title ? ($title . ' | ' . $siteName) : ($siteName . ' - Cửa Hàng Dịch Vụ AI Cao Cấp');
        $description = self::$data['description'] ?? $defaultDesc;
        $keywords    = self::$data['keywords'] ?? '';
        if (is_array($keywords)) {
            $keywords = implode(', ', $keywords);
        }
        $canonical = self::$data['canonical'] ?? self::currentUrl();
        $image     = self::$data['image'] ?? $defaultImg;
        $type      = self::$data['type'] ?? 'website';
        $robots    = self::$data['robots'] ?? 'index,follow';
        $structured = self::$data['structured'] ?? null;

        $description = self::truncate(strip_tags((string) $description), 200);
        $title = $fullTitle;

        $h = function ($s) {
            return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
        };

        $out  = "    <title>{$h($title)}</title>\n";
        $out .= "    <meta name=\"description\" content=\"{$h($description)}\">\n";
        if ($keywords !== '') {
            $out .= "    <meta name=\"keywords\" content=\"{$h($keywords)}\">\n";
        }
        $out .= "    <meta name=\"robots\" content=\"{$h($robots)}\">\n";
        $out .= "    <link rel=\"canonical\" href=\"{$h($canonical)}\">\n";

        // Open Graph
        $out .= "    <meta property=\"og:type\" content=\"{$h($type)}\">\n";
        $out .= "    <meta property=\"og:site_name\" content=\"{$h($siteName)}\">\n";
        $out .= "    <meta property=\"og:title\" content=\"{$h($title)}\">\n";
        $out .= "    <meta property=\"og:description\" content=\"{$h($description)}\">\n";
        $out .= "    <meta property=\"og:url\" content=\"{$h($canonical)}\">\n";
        $out .= "    <meta property=\"og:locale\" content=\"vi_VN\">\n";
        if ($image) {
            $out .= "    <meta property=\"og:image\" content=\"{$h($image)}\">\n";
        }

        // Twitter
        $out .= "    <meta name=\"twitter:card\" content=\"summary_large_image\">\n";
        $out .= "    <meta name=\"twitter:title\" content=\"{$h($title)}\">\n";
        $out .= "    <meta name=\"twitter:description\" content=\"{$h($description)}\">\n";
        if ($image) {
            $out .= "    <meta name=\"twitter:image\" content=\"{$h($image)}\">\n";
        }

        // JSON-LD
        $schemas = [];

        // Site-wide Organization + WebSite (always included)
        $schemas[] = [
            '@context' => 'https://schema.org',
            '@type'    => 'Organization',
            'name'     => $siteName,
            'url'      => rtrim(URLROOT, '/'),
            'logo'     => $image ?: rtrim(URLROOT, '/') . '/assets/logo.png',
        ];
        $schemas[] = [
            '@context' => 'https://schema.org',
            '@type'    => 'WebSite',
            'name'     => $siteName,
            'url'      => rtrim(URLROOT, '/'),
            'potentialAction' => [
                '@type'       => 'SearchAction',
                'target'      => rtrim(URLROOT, '/') . '/?q={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ];

        if ($structured) {
            if (isset($structured['@type'])) {
                $schemas[] = $structured;
            } else {
                foreach ($structured as $s) {
                    if (is_array($s)) $schemas[] = $s;
                }
            }
        }

        $out .= '    <script type="application/ld+json">' . "\n";
        $out .= json_encode($schemas, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        $out .= "\n    </script>\n";

        return $out;
    }

    public static function currentUrl(): string {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        if (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https') $scheme = 'https';
        $host = $_SERVER['HTTP_HOST'] ?? parse_url(URLROOT, PHP_URL_HOST);
        $uri  = $_SERVER['REQUEST_URI'] ?? '/';
        return $scheme . '://' . $host . $uri;
    }

    public static function truncate(string $s, int $limit): string {
        $s = trim(preg_replace('/\s+/u', ' ', $s));
        if (mb_strlen($s) <= $limit) return $s;
        return mb_substr($s, 0, $limit - 1) . '…';
    }

    /**
     * Vietnamese-friendly slugify. Preserves the trailing -id segment for routing.
     */
    public static function slugify(string $text): string {
        $text = (string) $text;
        // Replace Vietnamese diacritics
        $map = [
            'á','à','ả','ã','ạ','ă','ắ','ằ','ẳ','ẵ','ặ','â','ấ','ầ','ẩ','ẫ','ậ',
            'đ',
            'é','è','ẻ','ẽ','ẹ','ê','ế','ề','ể','ễ','ệ',
            'í','ì','ỉ','ĩ','ị',
            'ó','ò','ỏ','õ','ọ','ô','ố','ồ','ổ','ỗ','ộ','ơ','ớ','ờ','ở','ỡ','ợ',
            'ú','ù','ủ','ũ','ụ','ư','ứ','ừ','ử','ữ','ự',
            'ý','ỳ','ỷ','ỹ','ỵ',
        ];
        $rep = [
            'a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a',
            'd',
            'e','e','e','e','e','e','e','e','e','e','e',
            'i','i','i','i','i',
            'o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o',
            'u','u','u','u','u','u','u','u','u','u','u',
            'y','y','y','y','y',
        ];
        $text = mb_strtolower($text, 'UTF-8');
        $text = str_replace($map, $rep, $text);
        // Also handle uppercase forms by applying same after lowercasing (already done)
        $text = preg_replace('/[^a-z0-9\-]+/u', '-', $text);
        $text = preg_replace('/-+/', '-', $text);
        return trim($text, '-');
    }
}
