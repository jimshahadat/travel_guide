<?php
require_once __DIR__ . '/../config/database.php';

class User {

    public static function findById(int $id): ?array {
        $stmt = getPDO()->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function findByEmail(string $email): ?array {
        $stmt = getPDO()->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public static function findByRememberToken(string $token): ?array {
        $hashed = hash('sha256', $token);
        $stmt = getPDO()->prepare("SELECT * FROM users WHERE remember_token = ?");
        $stmt->execute([$hashed]);
        return $stmt->fetch() ?: null;
    }

    public static function emailExists(string $email, int $excludeId = 0): bool {
        $stmt = getPDO()->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $excludeId]);
        return (bool)$stmt->fetch();
    }

    public static function create(array $data): int {
        $pdo = getPDO();
        $stmt = $pdo->prepare(
            "INSERT INTO users (name, email, password_hash, role, is_verified, created_at)
             VALUES (?, ?, ?, ?, 0, NOW())"
        );
        $stmt->execute([
            $data['name'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['role'],
        ]);
        return (int)$pdo->lastInsertId();
    }

    public static function createVerified(array $data): int {
        $pdo = getPDO();
        $stmt = $pdo->prepare(
            "INSERT INTO users (name, email, password_hash, role, is_verified, created_at)
             VALUES (?, ?, ?, ?, 1, NOW())"
        );
        $stmt->execute([
            $data['name'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['role'],
        ]);
        return (int)$pdo->lastInsertId();
    }

    public static function update(int $id, array $data): void {
        $pdo = getPDO();
        $stmt = $pdo->prepare(
            "UPDATE users SET name=?, email=?, profile_picture=? WHERE id=?"
        );
        $stmt->execute([$data['name'], $data['email'], $data['profile_picture'], $id]);
    }

    public static function updatePassword(int $id, string $password): void {
        $stmt = getPDO()->prepare("UPDATE users SET password_hash=? WHERE id=?");
        $stmt->execute([password_hash($password, PASSWORD_DEFAULT), $id]);
    }

    public static function setRememberToken(int $id, string $hashedToken): void {
        $stmt = getPDO()->prepare("UPDATE users SET remember_token=? WHERE id=?");
        $stmt->execute([$hashedToken, $id]);
    }

    public static function clearRememberToken(int $id): void {
        $stmt = getPDO()->prepare("UPDATE users SET remember_token=NULL WHERE id=?");
        $stmt->execute([$id]);
    }

    public static function setVerified(int $id, int $verified): void {
        $stmt = getPDO()->prepare("UPDATE users SET is_verified=? WHERE id=?");
        $stmt->execute([$verified, $id]);
    }

    public static function setRole(int $id, string $role): void {
        $stmt = getPDO()->prepare("UPDATE users SET role=? WHERE id=?");
        $stmt->execute([$role, $id]);
    }

    public static function deleteUser(int $id): void {
        $pdo = getPDO();
        // Cascade delete related records
        $pdo->prepare("DELETE FROM wishlist WHERE user_id=?")->execute([$id]);
        $pdo->prepare("DELETE FROM comments WHERE user_id=?")->execute([$id]);
        $pdo->prepare("DELETE FROM post_requests WHERE scout_id=?")->execute([$id]);
        // Remove posts and their related data
        $posts = $pdo->prepare("SELECT id FROM posts WHERE scout_id=?");
        $posts->execute([$id]);
        foreach ($posts->fetchAll() as $p) {
            $pdo->prepare("DELETE FROM wishlist WHERE post_id=?")->execute([$p['id']]);
            $pdo->prepare("DELETE FROM comments WHERE post_id=?")->execute([$p['id']]);
            $pdo->prepare("DELETE FROM cost_estimates WHERE post_id=?")->execute([$p['id']]);
        }
        $pdo->prepare("DELETE FROM posts WHERE scout_id=?")->execute([$id]);
        $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$id]);
    }

    public static function getAll(string $orderBy = 'created_at DESC'): array {
        $allowed = ['created_at DESC','name ASC','role ASC'];
        if (!in_array($orderBy, $allowed)) $orderBy = 'created_at DESC';
        return getPDO()->query("SELECT id,name,email,role,is_verified,profile_picture,created_at FROM users ORDER BY $orderBy")->fetchAll();
    }

    public static function countByRole(): array {
        $stmt = getPDO()->query("SELECT role, COUNT(*) as cnt FROM users GROUP BY role");
        $result = ['admin'=>0,'scout'=>0,'user'=>0];
        foreach ($stmt->fetchAll() as $row) $result[$row['role']] = $row['cnt'];
        return $result;
    }
}
