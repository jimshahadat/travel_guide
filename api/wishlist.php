<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Wishlist.php';
require_once __DIR__ . '/../models/Post.php';

header('Content-Type: application/json');
requireLogin();
if (!isVerified() || $_SESSION['role'] !== 'user') {
    echo json_encode(['success'=>false,'message'=>'Not authorized']); exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$userId = (int)$_SESSION['user_id'];

if ($action === 'add') {
    verifyCsrf();
    $postId = (int)($_POST['post_id'] ?? 0);
    $post = Post::findById($postId);
    if (!$post || $post['status'] !== 'approved') {
        echo json_encode(['success'=>false,'message'=>'Post not found']); exit;
    }
    if (Wishlist::exists($userId, $postId)) {
        echo json_encode(['success'=>false,'message'=>'Already in wishlist']); exit;
    }
    Wishlist::add($userId, $postId);
    echo json_encode(['success'=>true]);

} elseif ($action === 'remove') {
    verifyCsrf();
    $postId = (int)($_POST['post_id'] ?? 0);
    Wishlist::remove($userId, $postId);
    echo json_encode(['success'=>true]);

} elseif ($action === 'list') {
    $items = Wishlist::getByUser($userId);
    echo json_encode(['success'=>true,'items'=>$items]);

} else {
    echo json_encode(['error'=>'Unknown action']);
}
