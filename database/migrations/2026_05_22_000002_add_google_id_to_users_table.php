<?php

return new class {
    public function up(PDO $pdo) {
        // Add google_id column (IF NOT EXISTS compatible)
        try {
            $pdo->exec("ALTER TABLE users ADD COLUMN google_id VARCHAR(255) NULL DEFAULT NULL AFTER password");
        } catch (Exception $e) {
            // Column may already exist
        }
        // Add avatar column
        try {
            $pdo->exec("ALTER TABLE users ADD COLUMN avatar VARCHAR(500) NULL DEFAULT NULL AFTER google_id");
        } catch (Exception $e) {
            // Column may already exist
        }
        // Add index on google_id for fast lookups
        try {
            $pdo->exec("ALTER TABLE users ADD INDEX idx_google_id (google_id)");
        } catch (Exception $e) {
            // Index may already exist
        }
    }

    public function down(PDO $pdo) {
        try { $pdo->exec("ALTER TABLE users DROP INDEX idx_google_id"); } catch (Exception $e) {}
        try { $pdo->exec("ALTER TABLE users DROP COLUMN avatar"); } catch (Exception $e) {}
        try { $pdo->exec("ALTER TABLE users DROP COLUMN google_id"); } catch (Exception $e) {}
    }
};
