<?php
require_once __DIR__ . '/../config/database.php';

class Post {

    public static function getAll(string $status = ''): array {
        $pdo = getPDO();
        if ($status) {
            $stmt = $pdo->prepare(
                "SELECT p.*, u.name as scout_name
                 FROM posts p LEFT JOIN users u ON p.scout_id=u.id
                 WHERE p.status=? ORDER BY p.created_at DESC"
            );
            $stmt->execute([$status]);
        } else {
            $stmt = $pdo->query(
                "SELECT p.*, u.name as scout_name
                 FROM posts p LEFT JOIN users u ON p.scout_id=u.id
                 ORDER BY p.created_at DESC"
            );
        }
        return $stmt->fetchAll();
    }

    public static function findById(int $id): ?array {
        $stmt = getPDO()->prepare(
            "SELECT p.*, u.name as scout_name
             FROM posts p LEFT JOIN users u ON p.scout_id=u.id
             WHERE p.id=?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function search(string $q): array {
        $like = "%$q%";
        $stmt = getPDO()->prepare(
            "SELECT * FROM posts WHERE status='approved' AND (title LIKE ? OR country LIKE ?) ORDER BY created_at DESC LIMIT 30"
        );
        $stmt->execute([$like, $like]);
        return $stmt->fetchAll();
    }

    public static function filter(array $filters): array {
        $pdo = getPDO();
        $sql = "SELECT * FROM posts WHERE status='approved'";
        $params = [];
        if (!empty($filters['country'])) {
            $sql .= " AND country=?";
            $params[] = $filters['country'];
        }
        if (!empty($filters['genre'])) {
            $sql .= " AND genre=?";
            $params[] = $filters['genre'];
        }
        if (!empty($filters['cost_level'])) {
            $sql .= " AND cost_level=?";
            $params[] = $filters['cost_level'];
        }
        if (!empty($filters['q'])) {
            $sql .= " AND (title LIKE ? OR country LIKE ?)";
            $like = "%{$filters['q']}%";
            $params[] = $like;
            $params[] = $like;
        }
        $sql .= " ORDER BY created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function createFromRequest(array $data, int $requestId): int {
        $pdo = getPDO();
        $stmt = $pdo->prepare(
            "INSERT INTO posts (scout_id,title,short_history,country,genre,cost_level,travel_medium_info,status,created_at,updated_at)
             VALUES (?,?,?,?,?,?,?,'approved',NOW(),NOW())"
        );
        $stmt->execute([
            $data['scout_id'],
            $data['title'],
            $data['short_history'],
            $data['country'],
            $data['genre'],
            $data['cost_level'],
            $data['travel_medium_info'],
        ]);
        $postId = (int)$pdo->lastInsertId();

        // Insert cost estimate
        $costMap = ['low'=>500,'medium'=>1500,'high'=>3000];
        $base = $costMap[$data['cost_level']] ?? 500;
        $pdo->prepare("INSERT INTO cost_estimates (post_id,base_cost,currency,last_updated) VALUES (?,?,'USD',NOW())")
            ->execute([$postId, $base]);

        return $postId;
    }

    public static function update(int $id, array $data): void {
        $stmt = getPDO()->prepare(
            "UPDATE posts SET title=?,short_history=?,country=?,genre=?,cost_level=?,travel_medium_info=?,updated_at=NOW() WHERE id=?"
        );
        $stmt->execute([
            $data['title'],
            $data['short_history'],
            $data['country'],
            $data['genre'],
            $data['cost_level'],
            $data['travel_medium_info'],
            $id,
        ]);
        // Update cost estimate
        $costMap = ['low'=>500,'medium'=>1500,'high'=>3000];
        $base = $costMap[$data['cost_level']] ?? 500;
        $pdo = getPDO();
        $exists = $pdo->prepare("SELECT id FROM cost_estimates WHERE post_id=?");
        $exists->execute([$id]);
        if ($exists->fetch()) {
            $pdo->prepare("UPDATE cost_estimates SET base_cost=?,last_updated=NOW() WHERE post_id=?")->execute([$base,$id]);
        } else {
            $pdo->prepare("INSERT INTO cost_estimates (post_id,base_cost,currency,last_updated) VALUES (?,?,'USD',NOW())")->execute([$id,$base]);
        }
    }

    public static function delete(int $id): void {
        $pdo = getPDO();
        $pdo->prepare("DELETE FROM wishlist WHERE post_id=?")->execute([$id]);
        $pdo->prepare("DELETE FROM comments WHERE post_id=?")->execute([$id]);
        $pdo->prepare("DELETE FROM cost_estimates WHERE post_id=?")->execute([$id]);
        $pdo->prepare("DELETE FROM posts WHERE id=?")->execute([$id]);
    }

    public static function getDistinctCountries(): array {
        return getPDO()->query("SELECT DISTINCT country FROM posts WHERE status='approved' ORDER BY country")->fetchAll(PDO::FETCH_COLUMN);
    }

    public static function count(string $status = ''): int {
        $pdo = getPDO();
        if ($status) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE status=?");
            $stmt->execute([$status]);
        } else {
            $stmt = $pdo->query("SELECT COUNT(*) FROM posts");
        }
        return (int)$stmt->fetchColumn();
    }

    public static function getByScout(int $scoutId): array {
        $stmt = getPDO()->prepare("SELECT * FROM posts WHERE scout_id=? AND status='approved' ORDER BY created_at DESC");
        $stmt->execute([$scoutId]);
        return $stmt->fetchAll();
    }
}
