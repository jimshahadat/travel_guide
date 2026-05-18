<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Comment.php';
require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../controllers/UserController.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';
if ($action === 'add') {
    UserController::addComment();
} elseif ($action === 'delete') {
    UserController::deleteComment();
} else {
    header('Content-Type: application/json');
    echo json_encode(['error'=>'Unknown action']);
}
