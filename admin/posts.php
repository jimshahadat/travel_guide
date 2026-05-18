<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../models/PostRequest.php';
require_once __DIR__ . '/../controllers/AdminController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'approve_request')  AdminController::approveRequest();
    elseif ($action === 'reject_request') AdminController::rejectRequest();
    elseif ($action === 'delete_post')    AdminController::deletePost();
    elseif ($action === 'update_post')    AdminController::updatePost();
    else { header('Location: ' . BASE . '/admin/posts.php'); exit; }
} else {
    AdminController::posts();
}
