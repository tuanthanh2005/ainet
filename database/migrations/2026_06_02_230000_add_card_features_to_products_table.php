<?php

return new class {
    public function up(PDO $pdo) {
        try {
            $pdo->exec("ALTER TABLE products ADD COLUMN card_features TEXT NULL AFTER feature_text");
        } catch (Throwable $ignored) {
        }
    }

    public function down(PDO $pdo) {
        try {
            $pdo->exec("ALTER TABLE products DROP COLUMN card_features");
        } catch (Throwable $ignored) {
        }
    }
};
