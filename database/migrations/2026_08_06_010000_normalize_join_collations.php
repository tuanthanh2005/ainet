<?php

return new class {
    public function up(PDO $pdo) {
        // These columns are compared in product/review JOINs and must use the
        // same character set and collation on MariaDB/MySQL.
        $pdo->exec(
            "ALTER TABLE reviews
             MODIFY product_id VARCHAR(100)
             CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL"
        );
    }

    public function down(PDO $pdo) {
        // Keep the portable collation on rollback; reverting to a server-specific
        // default could reintroduce the production JOIN failure.
    }
};
