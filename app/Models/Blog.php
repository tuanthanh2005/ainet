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

    public static function saveAll($blogs) {
        $db = Database::getInstance();
        $db->exec("TRUNCATE TABLE blogs");
        $stmt = $db->prepare("INSERT INTO blogs (title, image, description) VALUES (?, ?, ?)");
        foreach ($blogs as $blog) {
            $stmt->execute([
                $blog['title'],
                $blog['image'],
                $blog['desc'] ?? ($blog['description'] ?? '')
            ]);
        }
    }
}
