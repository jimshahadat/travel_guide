<?php
require_once __DIR__ . '/../config/database.php';

class Wishlist {

    public static function getByUser(int $userId): array {
        $stmt = getPDO()->prepare(
            "SELECT w.*, p.title, p.country, p.genre, p.cost_level, p.short_history
             FROM wishlist w JOIN posts p ON w.post_id=p.id
             WHERE w.user_id=? ORDER BY w.added_at DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public static function exists(int $userId, int $postId): bool {
        $stmt = getPDO()->prepare("SELECT id FROM wishlist WHERE user_id=? AND post_id=?");
        $stmt->execute([$userId, $postId]);
        return (bool)$stmt->fetch();
    }

    public static function add(int $userId, int $postId): void {
        $stmt = getPDO()->prepare("INSERT IGNORE INTO wishlist (user_id,post_id,added_at) VALUES (?,?,NOW())");
        $stmt->execute([$userId, $postId]);
    }

    public static function remove(int $userId, int $postId): void {
        $stmt = getPDO()->prepare("DELETE FROM wishlist WHERE user_id=? AND post_id=?");
        $stmt->execute([$userId, $postId]);
    }
}
