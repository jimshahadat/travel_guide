<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Base path for all links/redirects
define('BASE', '/travel_guide');

// Remember Me auto-login
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
    require_once __DIR__ . '/database.php';
    require_once __DIR__ . '/../models/User.php';
    $user = User::findByRememberToken($_COOKIE['remember_token']);
    if ($user) {
        $_SESSION['user_id']    = $user['id'];
        $_SESSION['name']       = $user['name'];
        $_SESSION['role']       = $user['role'];
        $_SESSION['is_verified']= $user['is_verified'];
    }
}

function isLoggedIn(): bool { return isset($_SESSION['user_id']); }
function isVerified(): bool  { return isLoggedIn() && $_SESSION['is_verified'] == 1; }
function isRole(string $r): bool { return isLoggedIn() && $_SESSION['role'] === $r; }

function requireLogin(): void {
    if (!isLoggedIn()) { header('Location: '.BASE.'/login.php'); exit; }
}
function requireVerified(): void {
    requireLogin();
    if (!isVerified()) { header('Location: '.BASE.'/index.php'); exit; }
}
function requireRole(string $role): void {
    requireLogin();
    if ($_SESSION['role'] !== $role) { header('Location: '.BASE.'/index.php'); exit; }
}

function setFlash(string $key, string $msg): void { $_SESSION['flash'][$key] = $msg; }
function getFlash(string $key): string {
    $msg = $_SESSION['flash'][$key] ?? '';
    unset($_SESSION['flash'][$key]);
    return $msg;
}

function csrfToken(): string {
    if (empty($_SESSION['csrf_token']))
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}
function verifyCsrf(): void {
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403); die('CSRF token mismatch.');
    }
}

function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
