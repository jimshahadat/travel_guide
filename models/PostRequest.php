<?php
require_once __DIR__ . '/../config/database.php';

class PostRequest {

    public static function getAll(string $status = ''): array {
        $pdo = getPDO();
        if ($status) {
            $stmt = $pdo->prepare(
                "SELECT pr.*, u.name as scout_name
                 FROM post_requests pr LEFT JOIN users u ON pr.scout_id=u.id
                 WHERE pr.status=? ORDER BY pr.requested_at DESC"
            );
            $stmt->execute([$status]);
        } else {
            $stmt = $pdo->query(
                "SELECT pr.*, u.name as scout_name
                 FROM post_requests pr LEFT JOIN users u ON pr.scout_id=u.id
                 ORDER BY pr.requested_at DESC"
            );
        }
        return $stmt->fetchAll();
    }

    public static function findById(int $id): ?array {
        $stmt = getPDO()->prepare(
            "SELECT pr.*, u.name as scout_name
             FROM post_requests pr LEFT JOIN users u ON pr.scout_id=u.id
             WHERE pr.id=?"
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row && $row['post_data']) {
            $row['post_data'] = json_decode($row['post_data'], true);
        }
        return $row ?: null;
    }

    public static function approve(int $id): ?int {
        $req = self::findById($id);
        if (!$req) return null;
        if ($req['status'] !== 'pending') return null;

        $raw  = $req['post_data'];
        // Ensure post_data is an array (guard against double-encoding)
        if (is_string($raw)) $raw = json_decode($raw, true) ?? [];

        $data = [
            'scout_id'           => (int)$req['scout_id'],
            'title'              => $raw['title']               ?? 'Untitled',
            'short_history'      => $raw['short_history']       ?? '',
            'country'            => $raw['country']             ?? '',
            'genre'              => $raw['genre']               ?? 'other',
            'cost_level'         => $raw['cost_level']          ?? 'medium',
            'travel_medium_info' => $raw['travel_medium_info']  ?? '',
        ];

        require_once __DIR__ . '/Post.php';
        $postId = Post::createFromRequest($data, $id);

        // Mark request as approved
        $stmt = getPDO()->prepare("UPDATE post_requests SET status='approved' WHERE id=?");
        $stmt->execute([$id]);
        return $postId;
    }

    public static function reject(int $id): void {
        $stmt = getPDO()->prepare("UPDATE post_requests SET status='rejected' WHERE id=?");
        $stmt->execute([$id]);
    }

    public static function delete(int $id): void {
        getPDO()->prepare("DELETE FROM post_requests WHERE id=?")->execute([$id]);
    }

    public static function count(string $status = ''): int {
        $pdo = getPDO();
        if ($status) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM post_requests WHERE status=?");
            $stmt->execute([$status]);
        } else {
            $stmt = $pdo->query("SELECT COUNT(*) FROM post_requests");
        }
        return (int)$stmt->fetchColumn();
    }

    public static function getByScout(int $scoutId): array {
        $stmt = getPDO()->prepare("SELECT * FROM post_requests WHERE scout_id=? ORDER BY requested_at DESC");
        $stmt->execute([$scoutId]);
        $rows = $stmt->fetchAll();
        foreach ($rows as &$r) {
            if ($r['post_data']) $r['post_data'] = json_decode($r['post_data'], true);
        }
        return $rows;
    }

    // Returns approved posts from the posts table for a given scout
    public static function getApprovedByScout(int $scoutId): array {
        $stmt = getPDO()->prepare(
            "SELECT * FROM posts WHERE scout_id=? AND status='approved' ORDER BY created_at DESC"
        );
        $stmt->execute([$scoutId]);
        return $stmt->fetchAll();
    }

    public static function create(array $data): int {
        $pdo = getPDO();
        $stmt = $pdo->prepare(
            "INSERT INTO post_requests (scout_id, post_data, requested_at, status)
             VALUES (?, ?, NOW(), 'pending')"
        );
        $stmt->execute([$data['scout_id'], json_encode($data['post_data'])]);
        return (int)$pdo->lastInsertId();
    }

    public static function update(int $id, array $postData): void {
        $stmt = getPDO()->prepare("UPDATE post_requests SET post_data=? WHERE id=? AND status='pending'");
        $stmt->execute([json_encode($postData), $id]);
    }
}
