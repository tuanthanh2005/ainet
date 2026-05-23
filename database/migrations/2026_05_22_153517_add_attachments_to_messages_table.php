<?php

return new class {
    public function up(PDO $pdo) {
        $pdo->exec("
            ALTER TABLE messages
            ADD COLUMN attachment_path VARCHAR(255) NULL AFTER body,
            ADD COLUMN attachment_name VARCHAR(255) NULL AFTER attachment_path,
            ADD COLUMN attachment_mime VARCHAR(100) NULL AFTER attachment_name,
            ADD COLUMN attachment_size INT NULL AFTER attachment_mime;
        ");
    }

    public function down(PDO $pdo) {
        $pdo->exec("
            ALTER TABLE messages
            DROP COLUMN attachment_path,
            DROP COLUMN attachment_name,
            DROP COLUMN attachment_mime,
            DROP COLUMN attachment_size;
        ");
    }
};