<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/PostRequest.php';

header('Content-Type: application/json');
requireLogin();
if ($_SESSION['role'] !== 'scout' || !isVerified()) {
    echo json_encode(['success'=>false,'message'=>'Not authorized']); exit;
}
verifyCsrf();
$id = (int)($_POST['id'] ?? 0);
$scoutId = (int)$_SESSION['user_id'];
$req = PostRequest::findById($id);
if (!$req || $req['scout_id'] !== $scoutId) {
    echo json_encode(['success'=>false,'message'=>'Request not found']); exit;
}
if ($req['status'] !== 'pending') {
    echo json_encode(['success'=>false,'message'=>'Can only delete pending requests']); exit;
}
PostRequest::delete($id);
echo json_encode(['success'=>true]);
