<?php require_once __DIR__ . '/../../config/session.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Management — Admin</title>
  <link rel="stylesheet" href="<?= BASE ?>/public/css/style.css">
  <link rel="stylesheet" href="<?= BASE ?>/public/css/admin.css">
</head>
<body>
<?php require __DIR__ . '/../partials/navbar.php'; ?>
<!-- Global CSRF token for AJAX calls -->
<input type="hidden" id="global-csrf" name="csrf_token" value="<?= csrfToken() ?>">
<div class="page-wrapper">
  <div class="container">

    <div class="admin-header fade-up">
      <div>
        <h2>👥 User Management</h2>
        <p class="text-muted"><?= count($users) ?> total users registered.</p>
      </div>
      <button class="btn btn-ghost" onclick="document.getElementById('addUserModal').classList.add('open')">+ Add User</button>
    </div>

    <?php if ($flash_success): ?>
      <div class="alert alert-success fade-up"><span class="alert-icon">✓</span><?= e($flash_success) ?></div>
    <?php endif; ?>
    <?php if ($flash_error): ?>
      <div class="alert alert-error fade-up"><span class="alert-icon">⚠</span><?= e($flash_error) ?></div>
    <?php endif; ?>

    <!-- Users Table -->
    <div class="card fade-up fade-up-1">
      <div class="table-search-bar">
        <input type="text" id="userSearch" class="form-control" placeholder="🔍 Search users by name or email..." style="max-width:320px;">
      </div>
      <div class="table-responsive">
        <table class="admin-table" id="usersTable">
          <thead>
            <tr>
              <th>#</th>
              <th>Name</th>
              <th>Email</th>
              <th>Role</th>
              <th>Verified</th>
              <th>Joined</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $u): ?>
              <tr data-id="<?= $u['id'] ?>" class="user-row">
                <td><?= $u['id'] ?></td>
                <td>
                  <div class="user-name-cell">
                    <div class="user-avatar-sm"><?= strtoupper(substr($u['name'],0,1)) ?></div>
                    <strong><?= e($u['name']) ?></strong>
                  </div>
                </td>
                <td><?= e($u['email']) ?></td>
                <td>
                  <span class="badge badge-role-<?= $u['role'] ?>"><?= ucfirst($u['role']) ?></span>
                </td>
                <td>
                  <?php if ($u['id'] !== (int)$_SESSION['user_id']): ?>
                    <button class="toggle-verify btn btn-sm <?= $u['is_verified']?'btn-verified':'btn-unverified' ?>"
                      data-id="<?= $u['id'] ?>" data-verified="<?= $u['is_verified'] ?>">
                      <?= $u['is_verified']?'✓ Verified':'✗ Pending' ?>
                    </button>
                  <?php else: ?>
                    <span class="badge badge-active">You</span>
                  <?php endif; ?>
                </td>
                <td><?= date('M j, Y', strtotime($u['created_at'])) ?></td>
                <td>
                  <div class="action-btns">
                    <?php if ($u['id'] !== (int)$_SESSION['user_id']): ?>
                      <select class="role-select form-control-sm" data-id="<?= $u['id'] ?>">
                        <option value="user"  <?= $u['role']==='user'  ?'selected':'' ?>>User</option>
                        <option value="scout" <?= $u['role']==='scout' ?'selected':'' ?>>Scout</option>
                        <option value="admin" <?= $u['role']==='admin' ?'selected':'' ?>>Admin</option>
                      </select>
                      <button class="btn btn-danger btn-sm delete-user" data-id="<?= $u['id'] ?>" data-name="<?= e($u['name']) ?>">Delete</button>
                    <?php else: ?>
                      <span class="text-muted" style="font-size:.8rem">Current admin</span>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Add User Modal -->
<div id="addUserModal" class="modal-overlay">
  <div class="modal-box">
    <div class="modal-head">
      <h3>Add New User</h3>
      <button class="modal-close" onclick="document.getElementById('addUserModal').classList.remove('open')">✕</button>
    </div>
    <form method="POST" action="<?= BASE ?>/admin/users.php" id="addUserForm">
      <input type="hidden" name="action" value="add_user">
      <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
      <div class="form-group">
        <label class="form-label">Name</label>
        <input type="text" name="name" class="form-control" required minlength="2" placeholder="Full name">
        <span class="field-error" id="err-name"></span>
      </div>
      <div class="form-group">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required placeholder="email@example.com">
        <span class="field-error" id="err-email"></span>
      </div>
      <div class="form-group">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required minlength="8" placeholder="Min 8 characters">
        <span class="field-error" id="err-pass"></span>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Role</label>
          <select name="role" class="form-control" required>
            <option value="user">General User</option>
            <option value="scout">Scout</option>
            <option value="admin">Admin</option>
          </select>
        </div>
        <div class="form-group" style="display:flex;align-items:flex-end;padding-bottom:.3rem;">
          <label class="form-check">
            <input type="checkbox" name="is_verified" value="1" checked>
            <span>Verify immediately</span>
          </label>
        </div>
      </div>
      <button type="submit" class="btn btn-primary">Create User</button>
    </form>
  </div>
</div>

<footer class="footer"><span>Wanderlust</span> Admin Panel &copy; <?= date('Y') ?></footer>
<script src="<?= BASE ?>/public/js/admin.js"></script>
</body>
</html>
