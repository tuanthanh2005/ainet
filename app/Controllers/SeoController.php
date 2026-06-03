<?php

class SeoController extends Controller {
    private function botFriendlyHeaders(string $contentType): void {
        header_remove('Set-Cookie');
        header_remove('Expires');
        header_remove('Cache-Control');
        header_remove('Pragma');
        header('Content-Type: ' . $contentType . '; charset=UTF-8');
        header('Cache-Control: public, max-age=900');
    }

    public function sitemap(): void {
        $this->botFriendlyHeaders('application/xml');

        $base = rtrim(URLROOT, '/');
        $now  = date('c');

        $urls = [
            ['loc' => $base . '/',              'priority' => '1.0', 'changefreq' => 'daily',   'lastmod' => $now],
            ['loc' => Url::products(),          'priority' => '0.9', 'changefreq' => 'daily',   'lastmod' => $now],
            ['loc' => Url::blogs(),             'priority' => '0.8', 'changefreq' => 'daily',   'lastmod' => $now],
            ['loc' => $base . '/gioi-thieu',    'priority' => '0.6', 'changefreq' => 'monthly', 'lastmod' => $now],
            ['loc' => $base . '/lien-he',       'priority' => '0.6', 'changefreq' => 'monthly', 'lastmod' => $now],
        ];

        try {
            foreach (Product::getAll() as $product) {
                if (($product['status'] ?? 'active') === 'hidden') continue;
                $urls[] = [
                    'loc'        => Url::product($product),
                    'priority'   => '0.9',
                    'changefreq' => 'weekly',
                    'lastmod'    => !empty($product['created_at']) ? date('c', strtotime($product['created_at'])) : $now,
                ];
            }
        } catch (Throwable $e) { /* ignore */ }

        try {
            foreach (Category::getAll() as $cat) {
                $slug = $cat['seo_slug'] ?: ($cat['slug'] ?? '');
                if ($slug === '') continue;
                $urls[] = [
                    'loc'        => Url::category($slug),
                    'priority'   => '0.8',
                    'changefreq' => 'weekly',
                    'lastmod'    => $now,
                ];
            }
        } catch (Throwable $e) { /* ignore */ }

        try {
            foreach (Blog::getAll() as $blog) {
                $urls[] = [
                    'loc'        => Url::blog($blog),
                    'priority'   => '0.7',
                    'changefreq' => 'weekly',
                    'lastmod'    => !empty($blog['created_at']) ? date('c', strtotime($blog['created_at'])) : $now,
                ];
            }
        } catch (Throwable $e) { /* ignore */ }

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $u) {
            echo '  <url>' . "\n";
            echo '    <loc>' . htmlspecialchars($u['loc'], ENT_XML1, 'UTF-8') . '</loc>' . "\n";
            echo '    <lastmod>' . $u['lastmod'] . '</lastmod>' . "\n";
            echo '    <changefreq>' . $u['changefreq'] . '</changefreq>' . "\n";
            echo '    <priority>' . $u['priority'] . '</priority>' . "\n";
            echo '  </url>' . "\n";
        }
        echo '</urlset>';
    }

    public function robots(): void {
        $this->botFriendlyHeaders('text/plain');

        $base = rtrim(URLROOT, '/');
        $isProd = APP_ENV === 'production';

        echo "User-agent: *\n";
        if ($isProd) {
            echo "Allow: /\n";
            echo "Disallow: /index.php?action=admin\n";
            echo "Disallow: /index.php?action=checkout\n";
            echo "Disallow: /index.php?action=payment\n";
            echo "Disallow: /index.php?action=cart\n";
            echo "Disallow: /index.php?action=profile\n";
            echo "Disallow: /index.php?action=orderHistory\n";
            echo "Disallow: /gio-hang\n";
        } else {
            echo "Disallow: /\n";
        }
        echo "\n";
        echo "Sitemap: {$base}/sitemap.xml\n";
    }
}
