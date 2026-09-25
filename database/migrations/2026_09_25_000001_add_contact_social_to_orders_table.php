<?php

return new class {
    public function up(PDO $pdo) {
        try {
            $cols = $pdo->query("SHOW COLUMNS FROM orders LIKE 'contact_social'")->fetchAll();
            if (empty($cols)) {
                $pdo->exec("ALTER TABLE orders ADD COLUMN contact_social VARCHAR(255) NULL AFTER phone;");
            }
        } catch (Throwable $e) {}
    }

    public function down(PDO $pdo) {
        try {
            $pdo->exec("ALTER TABLE orders DROP COLUMN contact_social;");
        } catch (Throwable $e) {}
    }
};
