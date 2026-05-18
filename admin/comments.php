<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Comment.php';
require_once __DIR__ . '/../controllers/AdminController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'delete_comment') AdminController::deleteComment();
    else { header('Location: ' . BASE . '/admin/comments.php'); exit; }
} else {
    AdminController::comments();
}
