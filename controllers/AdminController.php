<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../models/PostRequest.php';
require_once __DIR__ . '/../models/Comment.php';

class AdminController {

    private static function gate(): void {
        requireLogin();
        if ($_SESSION['role'] !== 'admin') {
            header('Location: ' . BASE . '/index.php'); exit;
        }
    }

    // ── DASHBOARD ──────────────────────────────────────────────────────────────
    public static function dashboard(): void {
        self::gate();
        $userCounts    = User::countByRole();
        $totalUsers    = array_sum($userCounts);
        $pendingReqs   = PostRequest::count('pending');
        $totalPosts    = Post::count();
        $totalComments = Comment::count();
        $recentUsers   = array_slice(User::getAll(), 0, 5);
        $recentReqs    = array_slice(PostRequest::getAll('pending'), 0, 5);
        require __DIR__ . '/../views/admin/dashboard.php';
    }

    // ── USER MANAGEMENT ────────────────────────────────────────────────────────
    public static function users(): void {
        self::gate();
        $users = User::getAll();
        $flash_success = getFlash('success');
        $flash_error   = getFlash('error');
        require __DIR__ . '/../views/admin/users.php';
    }

    public static function addUser(): void {
        self::gate();
        verifyCsrf();
        $errors = [];
        $name  = trim($_POST['name']  ?? '');
        $email = trim($_POST['email'] ?? '');
        $pass  = $_POST['password']   ?? '';
        $role  = $_POST['role']       ?? '';
        $verified = isset($_POST['is_verified']) ? 1 : 0;

        if (strlen($name) < 2)                         $errors[] = 'Name must be at least 2 characters.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email address.';
        if (strlen($pass) < 8)                         $errors[] = 'Password must be at least 8 characters.';
        if (!in_array($role, ['admin','scout','user'])) $errors[] = 'Invalid role.';
        if (!$errors && User::emailExists($email))      $errors[] = 'Email already registered.';

        if ($errors) {
            setFlash('error', implode(' | ', $errors));
            header('Location: ' . BASE . '/admin/users.php'); exit;
        }

        $data = ['name'=>$name,'email'=>$email,'password'=>$pass,'role'=>$role];
        if ($verified) {
            User::createVerified($data);
        } else {
            User::create($data);
        }
        setFlash('success', 'User added successfully.');
        header('Location: ' . BASE . '/admin/users.php'); exit;
    }

    public static function toggleVerify(): void {
        self::gate();
        header('Content-Type: application/json');
        verifyCsrf();
        $id = (int)($_POST['user_id'] ?? 0);
        $user = User::findById($id);
        if (!$user) { echo json_encode(['success'=>false,'message'=>'User not found']); exit; }
        $newStatus = $user['is_verified'] ? 0 : 1;
        User::setVerified($id, $newStatus);
        echo json_encode(['success'=>true,'is_verified'=>$newStatus]);
    }

    public static function changeRole(): void {
        self::gate();
        header('Content-Type: application/json');
        verifyCsrf();
        $id   = (int)($_POST['user_id'] ?? 0);
        $role = $_POST['role'] ?? '';
        if (!in_array($role, ['admin','scout','user'])) {
            echo json_encode(['success'=>false,'message'=>'Invalid role']); exit;
        }
        if ($id === (int)$_SESSION['user_id']) {
            echo json_encode(['success'=>false,'message'=>'Cannot change your own role']); exit;
        }
        User::setRole($id, $role);
        echo json_encode(['success'=>true,'role'=>$role]);
    }

    public static function deleteUser(): void {
        self::gate();
        header('Content-Type: application/json');
        verifyCsrf();
        $id = (int)($_POST['user_id'] ?? 0);
        if ($id === (int)$_SESSION['user_id']) {
            echo json_encode(['success'=>false,'message'=>'Cannot delete your own account']); exit;
        }
        User::deleteUser($id);
        echo json_encode(['success'=>true]);
    }

    // ── POST MODERATION ────────────────────────────────────────────────────────
    public static function posts(): void {
        self::gate();
        $requests = PostRequest::getAll();
        $posts    = Post::getAll();
        $flash_success = getFlash('success');
        $flash_error   = getFlash('error');
        require __DIR__ . '/../views/admin/posts.php';
    }

    public static function approveRequest(): void {
        self::gate();
        header('Content-Type: application/json');
        verifyCsrf();
        $id = (int)($_POST['request_id'] ?? 0);
        try {
            $postId = PostRequest::approve($id);
            if ($postId) {
                echo json_encode(['success'=>true,'post_id'=>$postId]);
            } else {
                echo json_encode(['success'=>false,'message'=>'Request not found or already processed']);
            }
        } catch (Throwable $e) {
            echo json_encode(['success'=>false,'message'=>'Server error: ' . $e->getMessage()]);
        }
    }

    public static function rejectRequest(): void {
        self::gate();
        header('Content-Type: application/json');
        verifyCsrf();
        $id = (int)($_POST['request_id'] ?? 0);
        PostRequest::reject($id);
        echo json_encode(['success'=>true]);
    }

    public static function editPost(): void {
        self::gate();
        $id   = (int)($_GET['id'] ?? 0);
        $post = Post::findById($id);
        if (!$post) { header('Location: ' . BASE . '/admin/posts.php'); exit; }
        $flash_success = getFlash('success');
        $flash_error   = getFlash('error');
        require __DIR__ . '/../views/admin/edit_post.php';
    }

    public static function updatePost(): void {
        self::gate();
        verifyCsrf();
        $id     = (int)($_POST['post_id'] ?? 0);
        $errors = [];
        $title  = trim($_POST['title']  ?? '');
        $history= trim($_POST['short_history'] ?? '');
        $country= trim($_POST['country'] ?? '');
        $genre  = trim($_POST['genre']  ?? '');
        $cost   = $_POST['cost_level']  ?? '';
        $travel = trim($_POST['travel_medium_info'] ?? '');

        if (!$title)   $errors[] = 'Title is required.';
        if (!$country) $errors[] = 'Country is required.';
        if (!$history) $errors[] = 'Short history is required.';
        if (!in_array($genre, ['beach','mountain','city','historical','nature','cultural','adventure','other'])) $errors[] = 'Invalid genre.';
        if (!in_array($cost,  ['low','medium','high'])) $errors[] = 'Invalid cost level.';

        if ($errors) {
            setFlash('error', implode(' | ', $errors));
            header('Location: ' . BASE . '/admin/edit_post.php?id=' . $id); exit;
        }

        Post::update($id, compact('title','short_history','country','genre','cost_level','travel_medium_info') + [
            'short_history' => $history,
            'cost_level'    => $cost,
            'travel_medium_info' => $travel,
        ]);
        setFlash('success', 'Post updated successfully.');
        header('Location: ' . BASE . '/admin/posts.php'); exit;
    }

    public static function deletePost(): void {
        self::gate();
        header('Content-Type: application/json');
        verifyCsrf();
        $id = (int)($_POST['post_id'] ?? 0);
        Post::delete($id);
        echo json_encode(['success'=>true]);
    }

    // ── COMMENT MODERATION ─────────────────────────────────────────────────────
    public static function comments(): void {
        self::gate();
        $comments = Comment::getAll();
        require __DIR__ . '/../views/admin/comments.php';
    }

    public static function deleteComment(): void {
        self::gate();
        header('Content-Type: application/json');
        verifyCsrf();
        $id = (int)($_POST['comment_id'] ?? 0);
        Comment::delete($id);
        echo json_encode(['success'=>true]);
    }
}
