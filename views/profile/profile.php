<?php
$errors  = $_SESSION['form_errors'] ?? [];
$success = getFlash('success');
unset($_SESSION['form_errors']);
$base = '/travel_guide';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Profile · Wanderlust</title>
  <link rel="stylesheet" href="<?= $base ?>/public/css/style.css">
</head>
<body>
<?php require __DIR__ . '/../partials/navbar.php'; ?>

<div class="page-wrapper">
  <div class="container-md">

    <h2 class="fade-up" style="font-family:'Cormorant Garamond',serif; color:var(--navy); margin-bottom:0.3rem;">
      My Profile
    </h2>
    <div class="section-line fade-up"></div>

    <?php if ($errors): ?>
      <div class="alert alert-error fade-up">
        <span class="alert-icon">⚠</span>
        <ul><?php foreach($errors as $e): ?><li><?= e($e) ?></li><?php endforeach; ?></ul>
      </div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="alert alert-success fade-up">
        <span class="alert-icon">✓</span> <?= e($success) ?>
      </div>
    <?php endif; ?>

    <div class="card fade-up">
      <!-- Profile Header -->
      <div class="profile-header">
        <?php if (!empty($user['profile_picture'])): ?>
          <img src="<?= $base ?>/public/uploads/<?= e($user['profile_picture']) ?>"
               alt="Profile" class="profile-avatar">
        <?php else: ?>
          <div class="profile-avatar-placeholder">👤</div>
        <?php endif; ?>
        <div>
          <div class="profile-name"><?= e($user['name']) ?></div>
          <div class="profile-email"><?= e($user['email']) ?></div>
          <div style="margin-top:0.4rem;">
            <span class="badge-role"><?= e($user['role']) ?></span>
            <?php if ($user['is_verified']): ?>
              <span class="tag tag-low" style="margin-left:4px;">✓ Verified</span>
            <?php else: ?>
              <span class="tag" style="background:#fef3cd; color:#856404; margin-left:4px;">⏳ Pending</span>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <form id="profileForm" action="profile.php" method="POST"
            enctype="multipart/form-data" novalidate>
        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" id="profName" class="form-control"
                   value="<?= e($user['name']) ?>" required>
            <span class="field-error" id="profNameErr"></span>
          </div>
          <div class="form-group">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" id="profEmail" class="form-control"
                   value="<?= e($user['email']) ?>" required>
            <span class="field-error" id="profEmailErr"></span>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Profile Picture</label>
          <div class="file-input-wrapper">
            <input type="file" name="profile_picture" accept="image/*">
          </div>
          <p class="file-hint">JPEG, PNG, WebP or GIF · Max 2MB</p>
        </div>

        <!-- Password Section -->
        <div style="background:var(--ivory-dark); border-radius:10px; padding:1.2rem; margin:1.2rem 0;">
          <h3 style="font-size:1rem; color:var(--navy); margin-bottom:1rem;">
            🔒 Change Password <small style="font-weight:400; color:var(--text-muted); font-size:0.8rem;">(leave blank to keep current)</small>
          </h3>
          <div class="form-group">
            <label class="form-label">Current Password</label>
            <input type="password" name="current_password" id="curPass" class="form-control"
                   placeholder="Enter current password">
          </div>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">New Password</label>
              <input type="password" name="new_password" id="newPass" class="form-control"
                     placeholder="Min. 8 characters">
              <span class="field-error" id="newPassErr"></span>
            </div>
            <div class="form-group">
              <label class="form-label">Confirm New Password</label>
              <input type="password" name="new_password2" id="newPass2" class="form-control"
                     placeholder="Repeat new password">
              <span class="field-error" id="newPass2Err"></span>
            </div>
          </div>
        </div>

        <div style="display:flex; gap:1rem; justify-content:flex-end;">
          <a href="<?= $base ?>/index.php" class="btn btn-secondary">Cancel</a>
          <button type="submit" class="btn btn-primary" style="width:auto; padding:0.7rem 2rem;">
            Save Changes ✓
          </button>
        </div>
      </form>
    </div>

  </div>
</div>

<footer class="footer">
  © <?= date('Y') ?> <span>Wanderlust</span> · Travel Guide Project
</footer>

<script src="<?= $base ?>/public/js/profile-validate.js"></script>
</body>
</html>
