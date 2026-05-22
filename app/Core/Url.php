<?php

class Url {
    public static function product(array $product): string {
        $slug = Seo::slugify($product['title'] ?? 'san-pham');
        $id   = $product['id'] ?? '';
        $tail = $slug ? ($slug . '-' . $id) : $id;
        return url('san-pham/' . $tail);
    }

    public static function blog(array $blog): string {
        $slug = Seo::slugify($blog['title'] ?? 'bai-viet');
        $id   = $blog['id'] ?? '';
        $tail = $slug ? ($slug . '-' . $id) : $id;
        return url('tap-chi/' . $tail);
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
