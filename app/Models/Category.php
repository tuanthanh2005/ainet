<?php

class Category {
    public static function getAll() {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT * FROM categories ORDER BY id ASC");
        return $stmt->fetchAll();
    }

    public static function saveAll($categories) {
        $db = Database::getInstance();
        // Xóa cũ chèn mới hoặc xử lý update. Để đơn giản cho admin dashboard:
        $db->exec("TRUNCATE TABLE categories");
        $stmt = $db->prepare("INSERT INTO categories (id, name, slug, icon, icon_color, is_pro, seo_title, seo_description, seo_keywords, seo_slug) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($categories as $cat) {
            $stmt->execute([
                $cat['id'] ?? null,
                $cat['name'],
                $cat['slug'],
                $cat['icon'] ?? '',
                $cat['icon_color'] ?? '',
                $cat['is_pro'] ? 1 : 0,
                $cat['seo_title'] ?? null,
                $cat['seo_description'] ?? null,
                $cat['seo_keywords'] ?? null,
                $cat['seo_slug'] ?? null
            ]);
        }
    }
}
