<?php

return new class {
    private function addIndex(PDO $pdo, string $table, string $name, string $columns): void {
        $stmt = $pdo->prepare(
            'SELECT COUNT(*) FROM information_schema.statistics
             WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ?'
        );
        $stmt->execute([$table, $name]);
        if ((int) $stmt->fetchColumn() === 0) {
            $pdo->exec("ALTER TABLE `{$table}` ADD INDEX `{$name}` ({$columns})");
        }
    }

    public function up(PDO $pdo) {
        $this->addIndex($pdo, 'products', 'idx_products_created_at', '`created_at`');
        $this->addIndex($pdo, 'products', 'idx_products_category_created', '`category_slug`, `created_at`');
        $this->addIndex($pdo, 'blogs', 'idx_blogs_created_at', '`created_at`');
        $this->addIndex($pdo, 'reviews', 'idx_reviews_created_at', '`created_at`');
        $this->addIndex($pdo, 'reviews', 'idx_reviews_rating', '`rating`');
        $this->addIndex($pdo, 'orders', 'idx_orders_status_updated', '`status`, `updated_at`');
        $this->addIndex($pdo, 'users', 'idx_users_status', '`status`');
    }

    public function down(PDO $pdo) {
        foreach ([
            ['products', 'idx_products_created_at'],
            ['products', 'idx_products_category_created'],
            ['blogs', 'idx_blogs_created_at'],
            ['reviews', 'idx_reviews_created_at'],
            ['reviews', 'idx_reviews_rating'],
            ['orders', 'idx_orders_status_updated'],
            ['users', 'idx_users_status'],
        ] as [$table, $name]) {
            try { $pdo->exec("ALTER TABLE `{$table}` DROP INDEX `{$name}`"); } catch (Throwable $ignored) {}
        }
    }
};
