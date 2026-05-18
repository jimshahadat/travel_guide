<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../controllers/UserController.php';

$action = $_GET['action'] ?? '';
if ($action === 'search') {
    UserController::searchPosts();
} elseif ($action === 'filter') {
    UserController::filterPosts();
} else {
    header('Content-Type: application/json');
    echo json_encode(['error'=>'Unknown action']);
}
