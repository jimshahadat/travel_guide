<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../models/Comment.php';
require_once __DIR__ . '/../models/Wishlist.php';

class UserController {

    private static function gateVerified(): void {
        requireLogin();
        if (!isVerified()) {
            header('Location: ' . BASE . '/index.php'); exit;
        }
    }

    // ── BROWSE ALL POSTS ───────────────────────────────────────────────────────
    public static function browse(): void {
        self::gateVerified();
        $posts     = Post::getAll('approved');
        $countries = Post::getDistinctCountries();
        $flash_success = getFlash('success');
        require __DIR__ . '/../views/user/browse.php';
    }

    // ── POST DETAIL ────────────────────────────────────────────────────────────
    public static function postDetail(): void {
        self::gateVerified();
        $id   = (int)($_GET['id'] ?? 0);
        $post = Post::findById($id);
        if (!$post || $post['status'] !== 'approved') {
            header('Location: ' . BASE . '/user/browse.php'); exit;
        }
        $comments = Comment::getByPost($id);

        // Cost estimate
        $pdo = getPDO();
        $ce = $pdo->prepare("SELECT * FROM cost_estimates WHERE post_id=?");
        $ce->execute([$id]);
        $costEstimate = $ce->fetch();

        // Wishlist status
        $inWishlist = false;
        if (isLoggedIn() && $_SESSION['role'] === 'user' && isVerified()) {
            $inWishlist = Wishlist::exists((int)$_SESSION['user_id'], $id);
        }

        $flash_success = getFlash('success');
        $flash_error   = getFlash('error');
        require __DIR__ . '/../views/user/post_detail.php';
    }

    // ── ADD COMMENT ────────────────────────────────────────────────────────────
    public static function addComment(): void {
        requireLogin();
        if (!isVerified() || $_SESSION['role'] !== 'user') {
            header('Content-Type: application/json');
            echo json_encode(['success'=>false,'message'=>'Not authorized']); exit;
        }
        header('Content-Type: application/json');
        verifyCsrf();
        $postId  = (int)($_POST['post_id'] ?? 0);
        $content = trim($_POST['content']  ?? '');

        if (!$content)                    { echo json_encode(['success'=>false,'message'=>'Comment cannot be empty']); exit; }
        if (strlen($content) > 1000)      { echo json_encode(['success'=>false,'message'=>'Comment too long (max 1000 chars)']); exit; }

        $post = Post::findById($postId);
        if (!$post || $post['status'] !== 'approved') {
            echo json_encode(['success'=>false,'message'=>'Post not found']); exit;
        }

        $id = Comment::create($postId, (int)$_SESSION['user_id'], htmlspecialchars($content, ENT_QUOTES, 'UTF-8'));
        echo json_encode([
            'success'    => true,
            'id'         => $id,
            'user_name'  => $_SESSION['name'],
            'content'    => htmlspecialchars($content, ENT_QUOTES, 'UTF-8'),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    // ── DELETE COMMENT ─────────────────────────────────────────────────────────
    public static function deleteComment(): void {
        requireLogin();
        header('Content-Type: application/json');
        verifyCsrf();
        $id      = (int)($_POST['comment_id'] ?? 0);
        $comment = Comment::findById($id);
        if (!$comment) { echo json_encode(['success'=>false,'message'=>'Comment not found']); exit; }
        // Only owner or admin can delete
        if ($comment['user_id'] !== (int)$_SESSION['user_id'] && $_SESSION['role'] !== 'admin') {
            echo json_encode(['success'=>false,'message'=>'Not authorized']); exit;
        }
        Comment::delete($id);
        echo json_encode(['success'=>true]);
    }

    // ── SEARCH (AJAX) ──────────────────────────────────────────────────────────
    public static function searchPosts(): void {
        requireLogin();
        if (!isVerified()) { http_response_code(403); exit; }
        header('Content-Type: application/json');
        $q = trim($_GET['q'] ?? '');
        if (strlen($q) < 1) { echo json_encode([]); exit; }
        echo json_encode(Post::search($q));
    }

    // ── FILTER (AJAX) ──────────────────────────────────────────────────────────
    public static function filterPosts(): void {
        requireLogin();
        if (!isVerified()) { http_response_code(403); exit; }
        header('Content-Type: application/json');
        $filters = [
            'country'    => trim($_GET['country']    ?? ''),
            'genre'      => trim($_GET['genre']      ?? ''),
            'cost_level' => trim($_GET['cost_level'] ?? ''),
            'q'          => trim($_GET['q']          ?? ''),
        ];
        echo json_encode(Post::filter($filters));
    }
}
