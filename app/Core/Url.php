<?php

class Url {
    public static function product(array $product): string {
        $slug = trim($product['seo_slug'] ?? '');
        if ($slug === '') {
            $slug = Seo::slugify($product['title'] ?? 'san-pham');
        }
        $id = trim((string) ($product['id'] ?? ''));
        if ($id !== '' && substr($slug, -strlen('-' . $id)) !== '-' . $id) {
            $slug .= '-' . $id;
        }
        return url('san-pham/' . rawurlencode($slug));
    }

    public static function blog(array $blog): string {
        $slug = trim($blog['seo_slug'] ?? '');
        if ($slug === '') {
            $slug = Seo::slugify($blog['title'] ?? 'bai-viet');
        }
        return url('tap-chi/' . $slug);
    }

    public static function category(string $slug): string {
        return url('danh-muc/' . urlencode($slug));
    }

    public static function about(): string   { return url('gioi-thieu'); }
    public static function contact(): string { return url('lien-he'); }
    public static function cart(): string    { return url('gio-hang'); }
    public static function home(): string    { return url(); }

    /**
     * Extract the trailing id from a "slug-id" path segment.
     * Examples:
     *   "chatgpt-plus-prod_123" => "prod_123"
     *   "bai-viet-5"            => "5"
     */
    public static function extractId(string $segment): string {
        $segment = trim($segment, '/');
        if ($segment === '') return '';
        // Match either numeric id at the end, or the full last token after the last hyphen
        if (preg_match('/-([A-Za-z0-9_]+)$/', $segment, $m)) {
            return $m[1];
        }
        return $segment;
    }
}
