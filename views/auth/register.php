<?php
$errors  = $_SESSION['form_errors'] ?? [];
$old     = $_SESSION['form_old']    ?? [];
$success = getFlash('success');
unset($_SESSION['form_errors'], $_SESSION['form_old']);
$base = '/travel_guide';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register · Wanderlust</title>
  <link rel="stylesheet" href="<?= $base ?>/public/css/style.css">
</head>
<body>
<?php require __DIR__ . '/../partials/navbar.php'; ?>

<div class="auth-page">
  <div class="auth-box">
    <div class="auth-logo">
      <h1>Join <strong>Wanderlust</strong></h1>
      <p>Create your account and start exploring</p>
    </div>

    <div class="card-auth">
      <h2 class="auth-title">Create Account</h2>
      <p class="auth-subtitle">Fill in your details to get started</p>

      <?php if ($errors): ?>
        <div class="alert alert-error">
          <span class="alert-icon">⚠</span>
          <ul><?php foreach($errors as $e): ?><li><?= e($e) ?></li><?php endforeach; ?></ul>
        </div>
      <?php endif; ?>

      <?php if ($success): ?>
        <div class="alert alert-success">
          <span class="alert-icon">✓</span> <?= e($success) ?>
        </div>
      <?php endif; ?>

      <form id="registerForm" action="register.php" method="POST" novalidate>
        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">

        <div class="form-group fade-up fade-up-1">
          <label class="form-label">Full Name</label>
          <input type="text" name="name" id="regName" class="form-control"
                 value="<?= e($old['name'] ?? '') ?>" placeholder="e.g. John Doe" required>
          <span class="field-error" id="nameErr"></span>
        </div>

        <div class="form-group fade-up fade-up-1">
          <label class="form-label">Email Address</label>
          <input type="email" name="email" id="regEmail" class="form-control"
                 value="<?= e($old['email'] ?? '') ?>" placeholder="you@example.com" required>
          <span class="field-error" id="emailErr"></span>
        </div>

        <div class="form-group fade-up fade-up-2">
          <label class="form-label">Role</label>
          <select name="role" id="regRole" class="form-control" required>
            <option value="">— Select your role —</option>
            <option value="user"  <?= ($old['role']??'')==='user'  ?'selected':'' ?>>General User</option>
            <option value="scout" <?= ($old['role']??'')==='scout' ?'selected':'' ?>>Scout</option>
            <option value="admin" <?= ($old['role']??'')==='admin' ?'selected':'' ?>>Admin</option>
          </select>
          <span class="field-error" id="roleErr"></span>
        </div>

        <div class="form-row fade-up fade-up-2">
          <div class="form-group">
            <label class="form-label">Password</label>
            <input type="password" name="password" id="regPass" class="form-control"
                   placeholder="Min. 8 characters" required>
            <span class="field-error" id="passErr"></span>
          </div>
          <div class="form-group">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="password2" id="regPass2" class="form-control"
                   placeholder="Repeat password" required>
            <span class="field-error" id="pass2Err"></span>
          </div>
        </div>

        <div class="form-group fade-up fade-up-3">
          <button type="submit" class="btn btn-primary">Create Account →</button>
        </div>
      </form>

      <div class="form-divider">or</div>
      <p style="text-align:center; font-size:0.88rem; color:var(--text-muted);">
        Already have an account? <a href="<?= $base ?>/login.php">Sign in</a>
      </p>
    </div>
  </div>
</div>

<script src="<?= $base ?>/public/js/register-validate.js"></script>
</body>
</html>
