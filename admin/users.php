<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../controllers/AdminController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add_user')       AdminController::addUser();
    elseif ($action === 'toggle_verify') AdminController::toggleVerify();
    elseif ($action === 'change_role')   AdminController::changeRole();
    elseif ($action === 'delete_user')   AdminController::deleteUser();
    else { header('Location: ' . BASE . '/admin/users.php'); exit; }
} else {
    AdminController::users();
}
