<?php
require_once __DIR__ . '/../config/database.php';

class Comment {

    public static function getByPost(int $postId): array {
        $stmt = getPDO()->prepare(
            "SELECT c.*, u.name as user_name
             FROM comments c LEFT JOIN users u ON c.user_id=u.id
             WHERE c.post_id=? ORDER BY c.created_at ASC"
        );
        $stmt->execute([$postId]);
        return $stmt->fetchAll();
    }

    public static function getAll(): array {
        return getPDO()->query(
            "SELECT c.*, u.name as user_name, p.title as post_title
             FROM comments c
             LEFT JOIN users u ON c.user_id=u.id
             LEFT JOIN posts p ON c.post_id=p.id
             ORDER BY c.created_at DESC"
        )->fetchAll();
    }

    public static function create(int $postId, int $userId, string $content): int {
        $pdo = getPDO();
        $stmt = $pdo->prepare(
            "INSERT INTO comments (post_id, user_id, content, created_at) VALUES (?,?,?,NOW())"
        );
        $stmt->execute([$postId, $userId, $content]);
        return (int)$pdo->lastInsertId();
    }

    public static function findById(int $id): ?array {
        $stmt = getPDO()->prepare("SELECT * FROM comments WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function delete(int $id): void {
        getPDO()->prepare("DELETE FROM comments WHERE id=?")->execute([$id]);
    }

    public static function count(): int {
        return (int)getPDO()->query("SELECT COUNT(*) FROM comments")->fetchColumn();
    }

    public static function countByPost(int $postId): int {
        $stmt = getPDO()->prepare("SELECT COUNT(*) FROM comments WHERE post_id=?");
        $stmt->execute([$postId]);
        return (int)$stmt->fetchColumn();
    }
}
