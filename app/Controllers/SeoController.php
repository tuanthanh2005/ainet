<?php

class SeoController extends Controller {
    private function botFriendlyHeaders(string $contentType): void {
        if (!headers_sent()) {
            header_remove('Set-Cookie');
            header_remove('Expires');
            header_remove('Cache-Control');
            header_remove('Pragma');
            header('Content-Type: ' . $contentType . '; charset=UTF-8');
            header('Cache-Control: public, max-age=1800');
        }
    }

    public function sitemap(): void {
        $this->botFriendlyHeaders('application/xml');

        $base = rtrim(URLROOT, '/');
        // Ensure https in production
        if (APP_ENV === 'production' || strpos($base, 'aicuatoi.net') !== false) {
            $base = preg_replace('#^http://#i', 'https://', $base);
        }
        $now = date('c');

        $urls = [
            ['loc' => Seo::normalizeUrl($base . '/'),           'priority' => '1.0', 'changefreq' => 'daily',   'lastmod' => $now],
            ['loc' => Seo::normalizeUrl(Url::products()),       'priority' => '0.9', 'changefreq' => 'daily',   'lastmod' => $now],
            ['loc' => Seo::normalizeUrl(Url::blogs()),          'priority' => '0.8', 'changefreq' => 'daily',   'lastmod' => $now],
            ['loc' => Seo::normalizeUrl($base . '/gioi-thieu'), 'priority' => '0.6', 'changefreq' => 'monthly', 'lastmod' => $now],
            ['loc' => Seo::normalizeUrl($base . '/lien-he'),    'priority' => '0.6', 'changefreq' => 'monthly', 'lastmod' => $now],
        ];

        try {
            foreach (Product::getAll() as $product) {
                if (($product['status'] ?? 'active') === 'hidden') continue;
                $pUrl = Seo::normalizeUrl(Url::product($product));
                $pImg = !empty($product['image']) ? image_url($product['image']) : '';
                $item = [
                    'loc'        => $pUrl,
                    'priority'   => '0.9',
                    'changefreq' => 'weekly',
                    'lastmod'    => !empty($product['created_at']) ? date('c', strtotime($product['created_at'])) : $now,
                ];
                if ($pImg !== '') {
                    $item['image'] = [
                        'loc'   => Seo::normalizeUrl($pImg),
                        'title' => trim($product['title'] ?? 'Tài khoản AI Premium'),
                    ];
                }
                $urls[] = $item;
            }
        } catch (Throwable $e) { /* ignore */ }

        try {
            foreach (Category::getAll() as $cat) {
                $slug = $cat['seo_slug'] ?: ($cat['slug'] ?? '');
                if ($slug === '') continue;
                $urls[] = [
                    'loc'        => Seo::normalizeUrl(Url::category($slug)),
                    'priority'   => '0.8',
                    'changefreq' => 'weekly',
                    'lastmod'    => $now,
                ];
            }
        } catch (Throwable $e) { /* ignore */ }

        try {
            foreach (Blog::getAll() as $blog) {
                $bUrl = Seo::normalizeUrl(Url::blog($blog));
                $bImg = !empty($blog['image']) ? image_url($blog['image']) : '';
                $item = [
                    'loc'        => $bUrl,
                    'priority'   => '0.7',
                    'changefreq' => 'weekly',
                    'lastmod'    => !empty($blog['created_at']) ? date('c', strtotime($blog['created_at'])) : $now,
                ];
                if ($bImg !== '') {
                    $item['image'] = [
                        'loc'   => Seo::normalizeUrl($bImg),
                        'title' => trim($blog['title'] ?? 'Bài viết hướng dẫn AI'),
                    ];
                }
                $urls[] = $item;
            }
        } catch (Throwable $e) { /* ignore */ }

        // Add static SEO search landing pages to sitemap
        $staticKeywords = [];
        $keywordPath = APP_ROOT . '/config/seo_keywords.json';
        if (file_exists($keywordPath)) {
            $seoData = json_decode(file_get_contents($keywordPath), true);
            $staticKeywords = isset($seoData['keywords']) ? array_keys($seoData['keywords']) : [];
        }
        if (empty($staticKeywords)) {
            $staticKeywords = [
                'gpt', 'gemini', 'ai', 'copilot', 'canva', 'netflix', 'youtube',
                'claude', 'midjourney', 'suno', 'runway', 'luma', 'elevenlabs',
                'perplexity', 'poe', 'capcut', 'freepik', 'adobe', 'cursor', 'gamma'
            ];
        }
        foreach ($staticKeywords as $kw) {
            $urls[] = [
                'loc'        => Seo::normalizeUrl(Url::search($kw)),
                'priority'   => '0.8',
                'changefreq' => 'weekly',
                'lastmod'    => $now,
            ];
        }

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
        echo '        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";
        foreach ($urls as $u) {
            echo "  <url>\n";
            echo '    <loc>' . htmlspecialchars($u['loc'], ENT_XML1, 'UTF-8') . "</loc>\n";
            echo '    <lastmod>' . $u['lastmod'] . "</lastmod>\n";
            echo '    <changefreq>' . $u['changefreq'] . "</changefreq>\n";
            echo '    <priority>' . $u['priority'] . "</priority>\n";
            if (!empty($u['image']['loc'])) {
                echo "    <image:image>\n";
                echo '      <image:loc>' . htmlspecialchars($u['image']['loc'], ENT_XML1, 'UTF-8') . "</image:loc>\n";
                echo '      <image:title>' . htmlspecialchars($u['image']['title'], ENT_XML1, 'UTF-8') . "</image:title>\n";
                echo "    </image:image>\n";
            }
            echo "  </url>\n";
        }
        echo '</urlset>';
    }

    public function robots(): void {
        $this->botFriendlyHeaders('text/plain');
        $isProd = APP_ENV === 'production';
        $base = rtrim(URLROOT, '/');
        if ($isProd || strpos($base, 'aicuatoi.net') !== false) {
            $base = preg_replace('#^http://#i', 'https://', $base);
        }

        echo "User-agent: *\n";
        if ($isProd) {
            echo "Allow: /\n";
            echo "Allow: /assets/\n";
            echo "Allow: /*.css$\n";
            echo "Allow: /*.js$\n";
            echo "Allow: /*.png$\n";
            echo "Allow: /*.jpg$\n";
            echo "Allow: /*.jpeg$\n";
            echo "Allow: /*.webp$\n";
            echo "Allow: /*.svg$\n";
            echo "Disallow: /admin\n";
            echo "Disallow: /admin/\n";
            echo "Disallow: /checkout\n";
            echo "Disallow: /payment\n";
            echo "Disallow: /profile\n";
            echo "Disallow: /order-history\n";
            echo "Disallow: /gio-hang\n";
            echo "Disallow: /api/\n";
            echo "Disallow: /webhook/\n";
            echo "Disallow: /*?action=*\n";
            echo "Disallow: /*&action=*\n";
            echo "Disallow: /*?sort=*\n";
            echo "Disallow: /*&sort=*\n";
            echo "Disallow: /*?tab=*\n";
        } else {
            echo "Disallow: /\n";
        }
        echo "\n";
        echo "User-agent: Googlebot\n";
        echo "Allow: /\n";
        echo "Allow: /assets/\n";
        echo "\n";
        echo "User-agent: Googlebot-Image\n";
        echo "Allow: /\n";
        echo "Allow: /assets/\n";
        echo "\n";
        echo "Sitemap: {$base}/sitemap.xml\n";
    }
}
