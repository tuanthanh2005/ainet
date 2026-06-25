<?php

class Seo {
    private static array $data = [];
    private static ?array $config = null;

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

    public static function defaults(string $page): array {
        $config = self::config();
        return $config['pages'][$page] ?? [];
    }

    private static function config(): array {
        if (self::$config !== null) {
            return self::$config;
        }

        $path = APP_ROOT . '/config/seo.php';
        self::$config = is_file($path) ? (require $path) : [];
        return is_array(self::$config) ? self::$config : [];
    }

    /**
     * Build the rendered <head> SEO block.
     * Falls back gracefully when fields are empty.
     */
    public static function render(array $settings = []): string {
        $seoConfig   = self::config();
        $siteConfig  = $seoConfig['site'] ?? [];
        $pageConfig  = self::pageConfig($seoConfig);
        $siteName    = SITENAME;
        $defaultDesc = trim($pageConfig['description'] ?? '')
            ?: trim($siteConfig['description'] ?? '')
            ?: trim($settings['footerDesc'] ?? '')
            ?: ($siteName . ' - cửa hàng dịch vụ AI và tài khoản Premium uy tín tại Việt Nam.');
        $defaultImg  = self::absoluteAsset($siteConfig['image'] ?? ($settings['about_image'] ?? ''));
        $defaultLogo = self::absoluteAsset($siteConfig['logo'] ?? '/assets/images/fvcoin.png');

        $customTitle = self::$data['title'] ?? null;
        if ($customTitle !== null && $customTitle !== '') {
            $title = $customTitle;
        } else {
            $pTitle = $pageConfig['title'] ?? '';
            $title = $pTitle ? ($pTitle . ' | ' . $siteName) : ($siteName . ' - Cửa Hàng Dịch Vụ AI Cao Cấp');
        }
        $description = self::$data['description'] ?? $defaultDesc;
        $keywords    = self::$data['keywords'] ?? ($pageConfig['keywords'] ?? ($siteConfig['keywords'] ?? ''));
        if (is_array($keywords)) {
            $keywords = implode(', ', array_values(array_unique(array_filter(array_map('trim', $keywords)))));
        }
        $canonical = self::normalizeUrl(self::$data['canonical'] ?? self::currentUrl());
        $image     = self::absoluteAsset(self::$data['image'] ?? $defaultImg);
        $type      = self::$data['type'] ?? 'website';
        $robots    = self::$data['robots'] ?? 'index,follow';
        $structured = self::$data['structured'] ?? null;
        $prevUrl = self::$data['prev'] ?? null;
        $nextUrl = self::$data['next'] ?? null;

        $description = self::truncate(strip_tags((string) $description), 200);

        $h = function ($s) {
            return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
        };

        $baseUrl = rtrim(URLROOT, '/');
        $themeColor = $settings['theme_color'] ?? '#ec4899';

        $out  = "    <meta name=\"base-url\" content=\"{$h($baseUrl)}\">\n";
        $out .= "    <meta name=\"theme-color\" content=\"{$h($themeColor)}\">\n";
        $out .= "    <meta name=\"format-detection\" content=\"telephone=no\">\n";
        $out .= "    <title>{$h($title)}</title>\n";
        $out .= "    <meta name=\"description\" content=\"{$h($description)}\">\n";
        if ($keywords !== '') {
            $out .= "    <meta name=\"keywords\" content=\"{$h($keywords)}\">\n";
        }
        $out .= "    <meta name=\"robots\" content=\"{$h($robots)}\">\n";
        $out .= "    <meta name=\"googlebot\" content=\"{$h($robots . ',max-snippet:-1,max-image-preview:large,max-video-preview:-1')}\">\n";
        $out .= "    <meta name=\"author\" content=\"{$h($siteName)}\">\n";
        $out .= "    <link rel=\"canonical\" href=\"{$h($canonical)}\">\n";
        if ($prevUrl) {
            $out .= "    <link rel=\"prev\" href=\"{$h($prevUrl)}\">\n";
        }
        if ($nextUrl) {
            $out .= "    <link rel=\"next\" href=\"{$h($nextUrl)}\">\n";
        }

        // Open Graph
        $out .= "    <meta property=\"og:type\" content=\"{$h($type)}\">\n";
        $out .= "    <meta property=\"og:site_name\" content=\"{$h($siteName)}\">\n";
        $out .= "    <meta property=\"og:title\" content=\"{$h($title)}\">\n";
        $out .= "    <meta property=\"og:description\" content=\"{$h($description)}\">\n";
        $out .= "    <meta property=\"og:url\" content=\"{$h($canonical)}\">\n";
        $out .= "    <meta property=\"og:locale\" content=\"vi_VN\">\n";
        if ($image) {
            $out .= "    <meta property=\"og:image\" content=\"{$h($image)}\">\n";
            $out .= "    <meta property=\"og:image:width\" content=\"1200\">\n";
            $out .= "    <meta property=\"og:image:height\" content=\"630\">\n";
            $out .= "    <meta property=\"og:image:alt\" content=\"{$h($siteName . ' - tài khoản AI Premium')}\">\n";
        }

        // Twitter
        $out .= "    <meta name=\"twitter:card\" content=\"summary_large_image\">\n";
        $out .= "    <meta name=\"twitter:title\" content=\"{$h($title)}\">\n";
        $out .= "    <meta name=\"twitter:description\" content=\"{$h($description)}\">\n";
        if ($image) {
            $out .= "    <meta name=\"twitter:image\" content=\"{$h($image)}\">\n";
        }

        // Resolve telephone & email from site config or settings table
        $telephone = !empty($settings['zalo']) ? trim($settings['zalo']) : ($siteConfig['telephone'] ?? '');
        $email = $siteConfig['email'] ?? '';
        if (empty($email) && !empty($settings['contact_methods'])) {
            $methods = json_decode($settings['contact_methods'], true);
            if (is_array($methods)) {
                foreach ($methods as $m) {
                    $txt = trim($m['text'] ?? '');
                    if (strpos($txt, '@') !== false && strpos($txt, '.') !== false) {
                        $email = $txt;
                        break;
                    }
                }
            }
        }

        // Resolve sameAs links
        $sameAs = $siteConfig['sameAs'] ?? [];
        if (!empty($settings['zalo'])) {
            $cleanZalo = preg_replace('/[^0-9]/', '', $settings['zalo']);
            if ($cleanZalo !== '') {
                $sameAs[] = 'https://zalo.me/' . $cleanZalo;
            }
        }
        if (!empty($settings['contact_methods'])) {
            $methods = json_decode($settings['contact_methods'], true);
            if (is_array($methods)) {
                foreach ($methods as $m) {
                    $txt = trim($m['text'] ?? '');
                    if (strpos($txt, '@') === 0) {
                        $sameAs[] = 'https://t.me/' . ltrim($txt, '@');
                    } elseif (preg_match('/^https?:\/\//i', $txt)) {
                        $sameAs[] = $txt;
                    }
                }
            }
        }
        if (!empty($settings['social_links_json'])) {
            $socialLinks = json_decode((string) $settings['social_links_json'], true);
            if (is_array($socialLinks)) {
                foreach ($socialLinks as $link) {
                    $url = trim((string) ($link['url'] ?? ''));
                    if ($url !== '' && preg_match('/^https?:\/\//i', $url)) {
                        $sameAs[] = $url;
                    }
                }
            }
        }
        $sameAs = array_values(array_unique(array_filter($sameAs)));

        // JSON-LD
        $schemas = [];

        // Site-wide Organization (always included)
        $organization = [
            '@context' => 'https://schema.org',
            '@type'    => 'Organization',
            'name'     => $siteName,
            'url'      => rtrim(URLROOT, '/'),
            'logo'     => $defaultLogo,
        ];

        $contactPoint = [
            '@type' => 'ContactPoint',
            'contactType' => 'customer service',
            'availableLanguage' => 'Vietnamese',
        ];

        if ($telephone !== '') {
            $contactPoint['telephone'] = $telephone;
        }
        if ($email !== '') {
            $contactPoint['email'] = $email;
        }

        if ($telephone !== '' || $email !== '') {
            $organization['contactPoint'] = $contactPoint;
        }

        if (!empty($sameAs)) {
            $organization['sameAs'] = $sameAs;
        }

        $schemas[] = $organization;
        $schemas[] = [
            '@context' => 'https://schema.org',
            '@type'    => 'WebSite',
            'name'     => $siteName,
            'url'      => rtrim(URLROOT, '/'),
            'potentialAction' => [
                '@type'       => 'SearchAction',
                'target'      => rtrim(URLROOT, '/') . '/san-pham?q={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ];
        $schemas[] = self::breadcrumbSchema(self::defaultBreadcrumb($canonical, $title));

        if ($structured) {
            if (isset($structured['@type'])) {
                $schemas[] = $structured;
                if (($structured['@type'] ?? '') === 'Product') {
                    $schemas[] = self::productFaqSchema($structured, $siteName);
                }
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

    private static function pageConfig(array $seoConfig): array {
        $pages = $seoConfig['pages'] ?? [];
        $action = $_GET['action'] ?? 'index';
        $tab = $_GET['tab'] ?? '';

        if ($action === 'about') return $pages['about'] ?? [];
        if ($action === 'contact') return $pages['contact'] ?? [];
        if ($action === 'index' && $tab === 'products') return $pages['products'] ?? [];
        if ($action === 'index' && $tab === 'blog') return $pages['blog'] ?? [];
        return $pages['home'] ?? [];
    }

    private static function absoluteAsset(string $path): string {
        $path = trim($path);
        if ($path === '') return '';
        if (preg_match('/^https?:\/\//i', $path)) return $path;
        return url(ltrim($path, '/'));
    }

    public static function normalizeUrl(string $url): string {
        $url = trim($url);
        if ($url === '') return rtrim(URLROOT, '/') . '/';

        $parts = parse_url($url);
        if (!$parts || empty($parts['host'])) {
            $url = url(ltrim($url, '/'));
            $parts = parse_url($url);
        }

        $scheme = strtolower($parts['scheme'] ?? parse_url(URLROOT, PHP_URL_SCHEME) ?: 'https');
        $host = strtolower($parts['host'] ?? parse_url(URLROOT, PHP_URL_HOST));
        $path = '/' . ltrim($parts['path'] ?? '/', '/');
        $path = preg_replace('#/+#', '/', $path);
        $path = $path !== '/' ? rtrim($path, '/') : '/';
        $query = isset($parts['query']) && $parts['query'] !== '' ? '?' . $parts['query'] : '';
        return $scheme . '://' . $host . $path . $query;
    }

    private static function defaultBreadcrumb(string $canonical, string $title): array {
        $base = rtrim(URLROOT, '/');
        $path = parse_url($canonical, PHP_URL_PATH) ?: '/';
        $items = [['name' => 'Trang chủ', 'url' => $base . '/']];

        if (strpos($path, '/san-pham') === 0) {
            $items[] = ['name' => 'Sản phẩm', 'url' => Url::products()];
        } elseif (strpos($path, '/tap-chi') === 0) {
            $items[] = ['name' => 'Tạp chí', 'url' => Url::blogs()];
        } elseif ($path === '/gioi-thieu') {
            $items[] = ['name' => 'Giới thiệu', 'url' => Url::about()];
        } elseif ($path === '/lien-he') {
            $items[] = ['name' => 'Liên hệ', 'url' => Url::contact()];
        }

        if (count($items) > 1 && end($items)['url'] !== $canonical) {
            $items[] = ['name' => preg_replace('/\s+\|\s+.*$/', '', $title), 'url' => $canonical];
        }

        return $items;
    }

    private static function productFaqSchema(array $product, string $siteName): array {
        $name = trim((string) ($product['name'] ?? 'sản phẩm'));
        $offers = $product['offers'] ?? [];
        $price = $offers['price'] ?? $offers['lowPrice'] ?? null;
        $priceText = $price !== null ? number_format((float) $price, 0, ',', '.') . 'đ' : 'theo gói đang hiển thị trên website';

        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => [
                [
                    '@type' => 'Question',
                    'name' => 'Mua ' . $name . ' tại ' . $siteName . ' có được bảo hành không?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $siteName . ' hỗ trợ bảo hành theo mô tả sản phẩm và chính sách hiển thị trên trang chi tiết.'
                    ],
                ],
                [
                    '@type' => 'Question',
                    'name' => 'Giá ' . $name . ' là bao nhiêu?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Giá hiện tại của ' . $name . ' bắt đầu từ ' . $priceText . '. Giá có thể thay đổi theo từng gói và thời hạn sử dụng.'
                    ],
                ],
                [
                    '@type' => 'Question',
                    'name' => 'Sau khi thanh toán bao lâu thì nhận được sản phẩm?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Hệ thống xử lý đơn hàng tự động. Sau khi thanh toán thành công, khách hàng sẽ nhận thông tin sản phẩm theo hướng dẫn trên website.'
                    ],
                ],
            ],
        ];
    }

    private static function breadcrumbSchema(array $items): array {
        $list = [];
        foreach (array_values($items) as $idx => $item) {
            $list[] = [
                '@type' => 'ListItem',
                'position' => $idx + 1,
                'name' => $item['name'] ?? '',
                'item' => $item['url'] ?? '',
            ];
        }
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $list,
        ];
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
