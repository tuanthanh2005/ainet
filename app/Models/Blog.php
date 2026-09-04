<?php

class Blog {
    public static function getAll() {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT * FROM blogs ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    public static function getSummaries(): array {
        return Cache::remember('blogs.summaries', 120, function () {
            $db = Database::getInstance();
            $stmt = $db->query("SELECT id, title, image, description, seo_title, seo_description, seo_keywords, seo_slug, created_at FROM blogs ORDER BY created_at DESC");
            return $stmt->fetchAll();
        });
    }

    public static function getById($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM blogs WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function getBySlugOrId($slugOrId) {
        $db = Database::getInstance();
        $slugOrId = trim(rawurldecode((string) $slugOrId));
        if ($slugOrId === '') {
            return null;
        }

        // 1. Exact match by seo_slug
        $stmt = $db->prepare("SELECT * FROM blogs WHERE seo_slug = ?");
        $stmt->execute([$slugOrId]);
        $blog = $stmt->fetch();
        if ($blog) {
            return $blog;
        }

        // 2. Exact match by numeric ID if parameter is purely digits
        if (ctype_digit($slugOrId)) {
            $stmt = $db->prepare("SELECT * FROM blogs WHERE id = ?");
            $stmt->execute([(int) $slugOrId]);
            $blog = $stmt->fetch();
            if ($blog) {
                return $blog;
            }
        }

        // 3. Match by trailing numeric ID suffix (e.g. "slug-title-123")
        if (preg_match('/-(\d+)$/', $slugOrId, $m)) {
            $stmt = $db->prepare("SELECT * FROM blogs WHERE id = ?");
            $stmt->execute([(int) $m[1]]);
            $blog = $stmt->fetch();
            if ($blog) {
                return $blog;
            }
        }
        
        // 4. Fallback to title-based slug matching
        $blogs = self::getAll();
        foreach ($blogs as $b) {
            $titleSlug = Seo::slugify($b['title'] ?? '');
            if ($titleSlug === $slugOrId) {
                return $b;
            }
        }
        
        return null;
    }

    public static function saveAll($blogs) {
        $db = Database::getInstance();
        $db->exec("TRUNCATE TABLE blogs");
        $stmt = $db->prepare("INSERT INTO blogs (title, image, description, content, seo_title, seo_description, seo_keywords, seo_slug) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($blogs as $blog) {
            $description = $blog['summary'] ?? ($blog['desc'] ?? ($blog['description'] ?? ''));
            $content = $blog['content'] ?? ($blog['description'] ?? $description);
            $stmt->execute([
                $blog['title'],
                $blog['image'],
                $description,
                $content,
                $blog['seo_title'] ?? null,
                $blog['seo_description'] ?? null,
                $blog['seo_keywords'] ?? null,
                $blog['seo_slug'] ?? null
            ]);
        }
        Cache::forget('blogs.summaries');
    }
}
