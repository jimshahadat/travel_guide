<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../models/User.php';

class ProfileController {
    public static function show(): void {
        requireLogin();
        $user = User::findById((int)$_SESSION['user_id']);
        require __DIR__ . '/../views/profile/profile.php';
    }

    public static function update(): void {
        requireLogin();
        verifyCsrf();
        $uid    = (int)$_SESSION['user_id'];
        $user   = User::findById($uid);
        $errors = [];
        $data   = [];

        $name  = trim($_POST['name']  ?? '');
        $email = trim($_POST['email'] ?? '');

        if (strlen($name) < 2)                          $errors[] = 'Name must be at least 2 characters.';
        else                                            $data['name'] = $name;

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email address.';
        elseif (User::emailExists($email, $uid))        $errors[] = 'Email already used by another account.';
        else                                            $data['email'] = $email;

        // Password change
        $curPass  = $_POST['current_password'] ?? '';
        $newPass  = $_POST['new_password']     ?? '';
        $newPass2 = $_POST['new_password2']    ?? '';
        if ($newPass !== '') {
            if (!password_verify($curPass, $user['password_hash'])) $errors[] = 'Current password is incorrect.';
            elseif (strlen($newPass) < 8)                           $errors[] = 'New password must be at least 8 characters.';
            elseif ($newPass !== $newPass2)                         $errors[] = 'New passwords do not match.';
            else $data['password_hash'] = password_hash($newPass, PASSWORD_BCRYPT);
        }

        // Profile picture
        if (!empty($_FILES['profile_picture']['name'])) {
            $file    = $_FILES['profile_picture'];
            $allowed = ['image/jpeg','image/png','image/gif','image/webp'];
            $finfo   = new finfo(FILEINFO_MIME_TYPE);
            $mime    = $finfo->file($file['tmp_name']);
            if (!in_array($mime, $allowed))    $errors[] = 'Image must be JPEG, PNG, GIF or WebP.';
            elseif ($file['size'] > 2*1024*1024) $errors[] = 'Image must be under 2MB.';
            else {
                $ext  = pathinfo($file['name'], PATHINFO_EXTENSION);
                $fn   = 'user_'.$uid.'_'.time().'.'.$ext;
                $dest = __DIR__.'/../public/uploads/'.$fn;
                if (move_uploaded_file($file['tmp_name'], $dest)) $data['profile_picture'] = $fn;
                else $errors[] = 'Failed to upload image.';
            }
        }

        if ($errors) {
            $_SESSION['form_errors'] = $errors;
            header('Location: '.BASE.'/profile.php'); exit;
        }
        User::updateProfile($uid, $data);
        if (isset($data['name']))  $_SESSION['name']  = $data['name'];
        if (isset($data['email'])) $_SESSION['email'] = $data['email'];
        setFlash('success','Profile updated successfully!');
        header('Location: '.BASE.'/profile.php'); exit;
    }
}
