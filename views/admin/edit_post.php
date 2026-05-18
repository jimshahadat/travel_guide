<?php require_once __DIR__ . '/../../config/session.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Post — Admin</title>
  <link rel="stylesheet" href="<?= BASE ?>/public/css/style.css">
  <link rel="stylesheet" href="<?= BASE ?>/public/css/admin.css">
</head>
<body>
<?php require __DIR__ . '/../partials/navbar.php'; ?>
<div class="page-wrapper">
  <div class="container-md">

    <div class="admin-header fade-up">
      <div>
        <h2>✏️ Edit Post</h2>
        <p class="text-muted">Post ID #<?= $post['id'] ?> — Last updated <?= date('M j, Y', strtotime($post['updated_at'] ?? $post['created_at'])) ?></p>
      </div>
      <a href="<?= BASE ?>/admin/posts.php" class="btn btn-ghost">← Back to Posts</a>
    </div>

    <?php if ($flash_success): ?>
      <div class="alert alert-success"><span class="alert-icon">✓</span><?= e($flash_success) ?></div>
    <?php endif; ?>
    <?php if ($flash_error): ?>
      <div class="alert alert-error"><span class="alert-icon">⚠</span><?= e($flash_error) ?></div>
    <?php endif; ?>

    <div class="card fade-up fade-up-1">
      <form method="POST" action="<?= BASE ?>/admin/posts.php" id="editPostForm">
        <input type="hidden" name="action" value="update_post">
        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">

        <div class="form-group">
          <label class="form-label">Title *</label>
          <input type="text" name="title" class="form-control" required value="<?= e($post['title']) ?>">
          <span class="field-error" id="err-title"></span>
        </div>

        <div class="form-group">
          <label class="form-label">Short History / Description *</label>
          <textarea name="short_history" class="form-control" rows="5" required><?= e($post['short_history']) ?></textarea>
          <span class="field-error" id="err-history"></span>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Country *</label>
            <input type="text" name="country" class="form-control" required value="<?= e($post['country']) ?>">
          </div>
          <div class="form-group">
            <label class="form-label">Genre *</label>
            <select name="genre" class="form-control" required>
              <?php foreach (['beach','mountain','city','historical','nature','cultural','adventure','other'] as $g): ?>
                <option value="<?= $g ?>" <?= $post['genre']===$g?'selected':'' ?>><?= ucfirst($g) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Cost Level *</label>
            <select name="cost_level" class="form-control" required>
              <option value="low"    <?= $post['cost_level']==='low'   ?'selected':'' ?>>Low ($500)</option>
              <option value="medium" <?= $post['cost_level']==='medium' ?'selected':'' ?>>Medium ($1,500)</option>
              <option value="high"   <?= $post['cost_level']==='high'  ?'selected':'' ?>>High ($3,000)</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Travel Medium Info</label>
            <input type="text" name="travel_medium_info" class="form-control" value="<?= e($post['travel_medium_info'] ?? '') ?>" placeholder="e.g. Flight, Train, Bus">
          </div>
        </div>

        <div class="form-group" style="margin-top:1rem;">
          <button type="submit" class="btn btn-primary" style="width:auto;padding:.75rem 2rem;">Save Changes</button>
          <a href="<?= BASE ?>/admin/posts.php" class="btn btn-secondary" style="margin-left:.5rem;">Cancel</a>
        </div>
      </form>
    </div>

  </div>
</div>
<footer class="footer"><span>Wanderlust</span> Admin Panel &copy; <?= date('Y') ?></footer>
<script>
document.getElementById('editPostForm').addEventListener('submit', function(e) {
  let valid = true;
  document.querySelectorAll('.field-error').forEach(el => el.textContent = '');
  const title = document.querySelector('[name=title]').value.trim();
  const history = document.querySelector('[name=short_history]').value.trim();
  if (!title) { document.getElementById('err-title').textContent = 'Title is required.'; valid=false; }
  if (!history) { document.getElementById('err-history').textContent = 'Short history is required.'; valid=false; }
  if (!valid) e.preventDefault();
});
</script>
</body>
</html>
