<?php

return new class {
    public function up(PDO $pdo) {
        try {
            $pdo->exec("ALTER TABLE products ADD COLUMN original_price DECIMAL(15, 2) DEFAULT 0.00 AFTER price");
        } catch (Throwable $ignored) {
        }
    }

    public function down(PDO $pdo) {
        try {
            $pdo->exec("ALTER TABLE products DROP COLUMN original_price");
        } catch (Throwable $ignored) {
        }
    }
};
