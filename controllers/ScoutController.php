<?php
// controllers/ScoutController.php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../models/PostRequest.php';

class ScoutController {

    // Gate: must be verified scout
    private static function gate(): void {
        requireLogin();
        if ($_SESSION['role'] !== 'scout' || !isVerified()) {
            header('Location: ' . BASE . '/index.php'); exit;
        }
    }

    // ── MY REQUESTS LIST ─────────────────────────────────────────────────────
    public static function myRequests(): void {
        self::gate();
        $requests = PostRequest::getByScout((int)$_SESSION['user_id']);
        require __DIR__ . '/../views/scout/my_requests.php';
    }

    // ── CREATE FORM ───────────────────────────────────────────────────────────
    public static function createForm(): void {
        self::gate();
        require __DIR__ . '/../views/scout/create_request.php';
    }

    // ── HANDLE CREATE ─────────────────────────────────────────────────────────
    public static function handleCreate(): void {
        self::gate();
        verifyCsrf();

        $errors = [];
        $data   = self::validateFormData($_POST, $errors);

        // Handle image uploads
        $images = self::handleImageUploads($errors);
        if ($images) $data['images'] = $images;

        if ($errors) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_old']    = $_POST;
            header('Location: ' . BASE . '/scout/create.php'); exit;
        }

        PostRequest::create([
            'scout_id'  => (int)$_SESSION['user_id'],
            'post_data' => $data,
        ]);
        setFlash('success', 'Post request submitted! Waiting for admin approval.');
        header('Location: ' . BASE . '/scout/requests.php'); exit;
    }

    // ── EDIT FORM ─────────────────────────────────────────────────────────────
    public static function editForm(): void {
        self::gate();
        $id  = (int)($_GET['id'] ?? 0);
        $req = PostRequest::findById($id);

        if (!$req || $req['scout_id'] != $_SESSION['user_id'] || $req['status'] !== 'pending') {
            setFlash('error', 'Request not found or cannot be edited.');
            header('Location: ' . BASE . '/scout/requests.php'); exit;
        }
        require __DIR__ . '/../views/scout/edit_request.php';
    }

    // ── HANDLE UPDATE (AJAX + regular POST) ───────────────────────────────────
    public static function handleUpdate(): void {
        self::gate();
        header('Content-Type: application/json');
        verifyCsrf();

        $id     = (int)($_POST['id'] ?? 0);
        $errors = [];
        $data   = self::validateFormData($_POST, $errors);

        // Handle new image uploads
        $images = self::handleImageUploads($errors);
        if ($images) $data['images'] = $images;

        // Keep existing images if no new ones
        if (empty($data['images'])) {
            $req = PostRequest::findById($id);
            if ($req && !empty($req['post_data']['images']))
                $data['images'] = $req['post_data']['images'];
        }

        if ($errors) {
            echo json_encode(['success' => false, 'errors' => $errors]); exit;
        }

        $ok = PostRequest::update($id, (int)$_SESSION['user_id'], $data);
        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'Request updated successfully.' : 'Update failed — request may not be pending.',
        ]);
    }

    // ── HANDLE DELETE (AJAX) ──────────────────────────────────────────────────
    public static function handleDelete(): void {
        self::gate();
        header('Content-Type: application/json');
        verifyCsrf();

        $id  = (int)($_POST['id'] ?? 0);
        $ok  = PostRequest::delete($id, (int)$_SESSION['user_id']);
        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'Request deleted.' : 'Delete failed — request may not be pending.',
        ]);
    }

    // ── APPROVED POSTS (read-only view) ───────────────────────────────────────
    public static function approvedPosts(): void {
        self::gate();
        $posts = PostRequest::getApprovedByScout((int)$_SESSION['user_id']);
        require __DIR__ . '/../views/scout/approved_posts.php';
    }

    // ── REQUEST CHANGES for an approved post ──────────────────────────────────
    public static function changeRequestForm(): void {
        self::gate();
        $postId = (int)($_GET['post_id'] ?? 0);
        $pdo    = \getPDO();
        $stmt   = $pdo->prepare(
            "SELECT * FROM posts WHERE id=:id AND scout_id=:sid AND status='approved'"
        );
        $stmt->execute([':id' => $postId, ':sid' => $_SESSION['user_id']]);
        $post = $stmt->fetch();

        if (!$post) {
            setFlash('error', 'Post not found or not eligible for change request.');
            header('Location: ' . BASE . '/scout/approved.php'); exit;
        }
        require __DIR__ . '/../views/scout/change_request.php';
    }

    public static function handleChangeRequest(): void {
        self::gate();
        verifyCsrf();

        $postId = (int)($_POST['original_post_id'] ?? 0);
        $errors = [];
        $data   = self::validateFormData($_POST, $errors);
        $data['original_post_id'] = $postId;
        $data['is_change_request'] = true;

        $images = self::handleImageUploads($errors);
        if ($images) $data['images'] = $images;

        if ($errors) {
            $_SESSION['form_errors'] = $errors;
            header('Location: ' . BASE . '/scout/change_request.php?post_id=' . $postId); exit;
        }

        PostRequest::create([
            'scout_id'  => (int)$_SESSION['user_id'],
            'post_data' => $data,
        ]);
        setFlash('success', 'Change request submitted! Admin will review it.');
        header('Location: ' . BASE . '/scout/approved.php'); exit;
    }

    // ── HELPERS ───────────────────────────────────────────────────────────────
    private static function validateFormData(array $post, array &$errors): array {
        $data = [];
        $required = ['title','country','genre','cost_level','travel_medium_info'];
        foreach ($required as $field) {
            $val = trim($post[$field] ?? '');
            if ($val === '') {
                $errors[] = ucfirst(str_replace('_',' ',$field)) . ' is required.';
            } else {
                $data[$field] = $val;
            }
        }
        $history = trim($post['short_history'] ?? '');
        if (strlen($history) < 20) {
            $errors[] = 'Short history must be at least 20 characters.';
        } else {
            $data['short_history'] = $history;
        }
        if (!in_array($data['cost_level'] ?? '', ['low','medium','high'])) {
            $errors[] = 'Invalid cost level.';
        }
        return $data;
    }

    private static function handleImageUploads(array &$errors): array {
        $saved   = [];
        $allowed = ['image/jpeg','image/png','image/gif','image/webp'];
        $maxSize = 3 * 1024 * 1024; // 3MB per image

        if (empty($_FILES['images']['name'][0])) return $saved;

        $files = $_FILES['images'];
        $count = count($files['name']);

        if ($count > 5) {
            $errors[] = 'You can upload a maximum of 5 images.';
            return $saved;
        }

        $uploadDir = __DIR__ . '/../public/uploads/posts/';

        for ($i = 0; $i < $count; $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_NO_FILE) continue;
            if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                $errors[] = 'Upload error for file ' . ($i + 1) . '.';
                continue;
            }

            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime  = $finfo->file($files['tmp_name'][$i]);

            if (!in_array($mime, $allowed)) {
                $errors[] = 'File ' . ($i+1) . ' must be JPEG, PNG, GIF or WebP.';
                continue;
            }
            if ($files['size'][$i] > $maxSize) {
                $errors[] = 'File ' . ($i+1) . ' must be under 3MB.';
                continue;
            }

            $ext      = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
            $filename = 'post_' . time() . '_' . $i . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $dest     = $uploadDir . $filename;

            if (move_uploaded_file($files['tmp_name'][$i], $dest)) {
                $saved[] = $filename;
            } else {
                $errors[] = 'Failed to save file ' . ($i+1) . '.';
            }
        }
        return $saved;
    }
}
