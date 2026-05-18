<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {

    public static function showRegister(): void {
        require __DIR__ . '/../views/auth/register.php';
    }

    public static function handleRegister(): void {
        verifyCsrf();
        $errors = [];
        $name  = trim($_POST['name']  ?? '');
        $email = trim($_POST['email'] ?? '');
        $pass  = $_POST['password']   ?? '';
        $pass2 = $_POST['password2']  ?? '';
        $role  = $_POST['role']       ?? '';

        if (strlen($name) < 2)                         $errors[] = 'Name must be at least 2 characters.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email address.';
        if (strlen($pass) < 8)                         $errors[] = 'Password must be at least 8 characters.';
        if ($pass !== $pass2)                          $errors[] = 'Passwords do not match.';
        if (!in_array($role,['admin','scout','user'])) $errors[] = 'Please select a valid role.';
        if (!$errors && User::emailExists($email))     $errors[] = 'This email is already registered.';

        if ($errors) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_old']    = compact('name','email','role');
            header('Location: '.BASE.'/register.php'); exit;
        }
        User::create(compact('name','email','role') + ['password'=>$pass]);
        setFlash('success','Registration successful! Please wait for admin approval.');
        header('Location: '.BASE.'/login.php'); exit;
    }

    public static function showLogin(): void {
        require __DIR__ . '/../views/auth/login.php';
    }

    public static function handleLogin(): void {
        verifyCsrf();
        $email    = trim($_POST['email']    ?? '');
        $pass     = $_POST['password']      ?? '';
        $remember = isset($_POST['remember_me']);
        $user     = User::findByEmail($email);

        if (!$user || !password_verify($pass, $user['password_hash'])) {
            setFlash('error','Invalid email or password.');
            header('Location: '.BASE.'/login.php'); exit;
        }
        session_regenerate_id(true);
        $_SESSION['user_id']    = $user['id'];
        $_SESSION['name']       = $user['name'];
        $_SESSION['role']       = $user['role'];
        $_SESSION['is_verified']= $user['is_verified'];

        if ($remember) {
            $token = bin2hex(random_bytes(32));
            User::setRememberToken($user['id'], hash('sha256', $token));
            setcookie('remember_token', $token, time()+60*60*24*30, '/', '', false, true);
        }
        header('Location: '.BASE.'/index.php'); exit;
    }

    public static function logout(): void {
        if (isLoggedIn()) User::clearRememberToken((int)$_SESSION['user_id']);
        session_destroy();
        setcookie('remember_token','',time()-3600,'/');
        header('Location: '.BASE.'/login.php'); exit;
    }
}
