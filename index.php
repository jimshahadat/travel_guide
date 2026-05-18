<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/database.php';
$latestPosts = [];
if (isLoggedIn() && isVerified()) {
    $stmt = getPDO()->prepare("SELECT id,title,country,genre,cost_level,short_history FROM posts WHERE status='approved' ORDER BY created_at DESC LIMIT 6");
    $stmt->execute();
    $latestPosts = $stmt->fetchAll();
}
require __DIR__ . '/views/home/home.php';
