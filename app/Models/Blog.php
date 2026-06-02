<?php

class Blog {
    public static function getAll() {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT * FROM blogs ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    public static function getById($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM blogs WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function getBySlugOrId($slugOrId) {
        $db = Database::getInstance();
        
        // Backward compatibility: check if it ends with legacy id format e.g. -123
        $id = '';
        if (preg_match('/-([A-Za-z0-9_]+)$/', $slugOrId, $m)) {
            $id = $m[1];
        }
        
        $stmt = $db->prepare("SELECT * FROM blogs WHERE seo_slug = ? OR id = ? OR (? !== '' AND id = ?)");
        $stmt->execute([$slugOrId, $slugOrId, $id, $id]);
        $blog = $stmt->fetch();
        
        // Fallback to title-based slug
        if (!$blog) {
            $blogs = self::getAll();
            foreach ($blogs as $b) {
                $titleSlug = Seo::slugify($b['title'] ?? '');
                if ($titleSlug === $slugOrId) {
                    return $b;
                }
            }
        }
        
        return $blog ?: null;
    }

    public static function saveAll($blogs) {
        $db = Database::getInstance();
        $db->exec("TRUNCATE TABLE blogs");
        $stmt = $db->prepare("INSERT INTO blogs (title, image, description, seo_title, seo_description, seo_keywords, seo_slug) VALUES (?, ?, ?, ?, ?, ?, ?)");
        foreach ($blogs as $blog) {
            $stmt->execute([
                $blog['title'],
                $blog['image'],
                $blog['desc'] ?? ($blog['description'] ?? ''),
                $blog['seo_title'] ?? null,
                $blog['seo_description'] ?? null,
                $blog['seo_keywords'] ?? null,
                $blog['seo_slug'] ?? null
            ]);
        }
    }
}
