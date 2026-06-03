<?php

return new class {
    public function up(PDO $pdo) {
        $columns = $pdo->query("SHOW COLUMNS FROM blogs LIKE 'content'")->fetchAll();
        if (empty($columns)) {
            $pdo->exec("ALTER TABLE blogs ADD COLUMN content LONGTEXT DEFAULT NULL AFTER description;");
            $pdo->exec("UPDATE blogs SET content = description WHERE content IS NULL AND description IS NOT NULL;");
        }
    }

    public function down(PDO $pdo) {
        $columns = $pdo->query("SHOW COLUMNS FROM blogs LIKE 'content'")->fetchAll();
        if (!empty($columns)) {
            $pdo->exec("ALTER TABLE blogs DROP COLUMN content;");
        }
    }
};
