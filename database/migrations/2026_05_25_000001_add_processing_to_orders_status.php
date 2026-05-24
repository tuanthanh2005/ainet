<?php

return new class {
    public function up(PDO $pdo) {
        $pdo->exec("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'completed', 'processing', 'cancelled') DEFAULT 'pending'");
    }

    public function down(PDO $pdo) {
        $pdo->exec("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending'");
    }
};
