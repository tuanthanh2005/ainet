<?php

return new class {
    public function up(PDO $pdo) {
        $pdo->exec("
            ALTER TABLE products 
            ADD COLUMN seo_title VARCHAR(255) DEFAULT NULL,
            ADD COLUMN seo_description TEXT DEFAULT NULL,
            ADD COLUMN seo_keywords TEXT DEFAULT NULL,
            ADD COLUMN seo_slug VARCHAR(255) DEFAULT NULL;
        ");
        $pdo->exec("
            ALTER TABLE categories 
            ADD COLUMN seo_title VARCHAR(255) DEFAULT NULL,
            ADD COLUMN seo_description TEXT DEFAULT NULL,
            ADD COLUMN seo_keywords TEXT DEFAULT NULL,
            ADD COLUMN seo_slug VARCHAR(255) DEFAULT NULL;
        ");
        $pdo->exec("
            ALTER TABLE blogs 
            ADD COLUMN seo_title VARCHAR(255) DEFAULT NULL,
            ADD COLUMN seo_description TEXT DEFAULT NULL,
            ADD COLUMN seo_keywords TEXT DEFAULT NULL,
            ADD COLUMN seo_slug VARCHAR(255) DEFAULT NULL;
        ");
    }

    public function down(PDO $pdo) {
        $pdo->exec("
            ALTER TABLE products 
            DROP COLUMN seo_title,
            DROP COLUMN seo_description,
            DROP COLUMN seo_keywords,
            DROP COLUMN seo_slug;
        ");
        $pdo->exec("
            ALTER TABLE categories 
            DROP COLUMN seo_title,
            DROP COLUMN seo_description,
            DROP COLUMN seo_keywords,
            DROP COLUMN seo_slug;
        ");
        $pdo->exec("
            ALTER TABLE blogs 
            DROP COLUMN seo_title,
            DROP COLUMN seo_description,
            DROP COLUMN seo_keywords,
            DROP COLUMN seo_slug;
        ");
    }
};