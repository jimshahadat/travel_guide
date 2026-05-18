<?php
$error   = getFlash('error');
$success = getFlash('success');
$base = '/travel_guide';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login · Wanderlust</title>
  <link rel="stylesheet" href="<?= $base ?>/public/css/style.css">
</head>
<body>
<?php require __DIR__ . '/../partials/navbar.php'; ?>

<div class="auth-page">
  <div class="auth-box">
    <div class="auth-logo">
      <h1>Welcome to <strong>Wanderlust</strong></h1>
      <p>Sign in to continue your journey</p>
    </div>

    <div class="card-auth">
      <h2 class="auth-title">Sign In</h2>
      <p class="auth-subtitle">Enter your credentials to access your account</p>

      <?php if ($error): ?>
        <div class="alert alert-error">
          <span class="alert-icon">⚠</span> <?= e($error) ?>
        </div>
      <?php endif; ?>

      <?php if ($success): ?>
        <div class="alert alert-success">
          <span class="alert-icon">✓</span> <?= e($success) ?>
        </div>
      <?php endif; ?>

      <form id="loginForm" action="login.php" method="POST" novalidate>
        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">

        <div class="form-group fade-up fade-up-1">
          <label class="form-label">Email Address</label>
          <input type="email" name="email" id="loginEmail" class="form-control"
                 placeholder="you@example.com" required>
          <span class="field-error" id="loginEmailErr"></span>
        </div>

        <div class="form-group fade-up fade-up-2">
          <label class="form-label">Password</label>
          <input type="password" name="password" id="loginPass" class="form-control"
                 placeholder="Your password" required>
          <span class="field-error" id="loginPassErr"></span>
        </div>

        <div class="form-group fade-up fade-up-2">
          <label class="form-check">
            <input type="checkbox" name="remember_me" id="rememberMe">
            <label for="rememberMe">Remember me for 30 days</label>
          </label>
        </div>

        <div class="form-group fade-up fade-up-3">
          <button type="submit" class="btn btn-primary">Sign In →</button>
        </div>
      </form>

      <div class="form-divider">or</div>
      <p style="text-align:center; font-size:0.88rem; color:var(--text-muted);">
        New here? <a href="<?= $base ?>/register.php">Create an account</a>
      </p>
    </div>
  </div>
</div>

<script src="<?= $base ?>/public/js/login-validate.js"></script>
</body>
</html>
