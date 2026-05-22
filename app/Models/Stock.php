<?php

class Stock {
    public static function countAvailable(string $productId, int $variantIdx): int {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            "SELECT COUNT(*) FROM stock_items
             WHERE product_id = ? AND variant_idx = ? AND status = 'available'"
        );
        $stmt->execute([$productId, $variantIdx]);
        return (int) $stmt->fetchColumn();
    }

    public static function listForVariant(string $productId, int $variantIdx, int $limit = 200): array {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            "SELECT id, content, status, order_id, delivered_at, created_at
             FROM stock_items
             WHERE product_id = ? AND variant_idx = ?
             ORDER BY status ASC, id DESC
             LIMIT $limit"
        );
        $stmt->execute([$productId, $variantIdx]);
        return $stmt->fetchAll();
    }

    public static function bulkAdd(string $productId, int $variantIdx, array $lines): int {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            "INSERT INTO stock_items (product_id, variant_idx, content) VALUES (?, ?, ?)"
        );
        $added = 0;
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') continue;
            $stmt->execute([$productId, $variantIdx, $line]);
            $added++;
        }
        return $added;
    }

    public static function delete(int $id): void {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM stock_items WHERE id = ? AND status = 'available'");
        $stmt->execute([$id]);
    }

    /**
     * Atomically claim N available units for an order. Returns array of contents.
     * Uses SELECT ... FOR UPDATE inside a transaction so concurrent webhooks
     * cannot deliver the same row twice.
     */
    public static function claimForOrder(string $orderId, string $productId, int $variantIdx, int $qty): array {
        if ($qty <= 0) return [];
        $db = Database::getInstance();
        $delivered = [];

        $db->beginTransaction();
        try {
            $stmt = $db->prepare(
                "SELECT id, content FROM stock_items
                 WHERE product_id = ? AND variant_idx = ? AND status = 'available'
                 ORDER BY id ASC
                 LIMIT $qty FOR UPDATE"
            );
            $stmt->execute([$productId, $variantIdx]);
            $rows = $stmt->fetchAll();

            if (!empty($rows)) {
                $ids = array_column($rows, 'id');
                $place = implode(',', array_fill(0, count($ids), '?'));
                $up = $db->prepare(
                    "UPDATE stock_items
                     SET status = 'sold', order_id = ?, delivered_at = NOW()
                     WHERE id IN ($place)"
                );
                $up->execute(array_merge([$orderId], $ids));
                $delivered = array_column($rows, 'content');
            }

            $db->commit();
        } catch (Throwable $e) {
            $db->rollBack();
            throw $e;
        }
        return $delivered;
    }
}
