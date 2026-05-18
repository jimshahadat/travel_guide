<?php require_once __DIR__ . '/../../config/session.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Comment Moderation — Admin</title>
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
        <h2>💬 Comment Moderation</h2>
        <p class="text-muted"><?= count($comments) ?> total comments across all posts.</p>
      </div>
      <a href="<?= BASE ?>/admin/dashboard.php" class="btn btn-ghost">← Dashboard</a>
    </div>

    <div class="card fade-up fade-up-1">
      <div class="table-search-bar">
        <input type="text" id="commentSearch" class="form-control" placeholder="🔍 Search comments..." style="max-width:350px;">
      </div>
      <?php if (empty($comments)): ?>
        <p class="empty-state">No comments yet.</p>
      <?php else: ?>
        <div class="table-responsive">
          <table class="admin-table" id="commentsTable">
            <thead>
              <tr>
                <th>#</th>
                <th>User</th>
                <th>Post</th>
                <th>Comment</th>
                <th>Date</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($comments as $c): ?>
                <tr id="comment-row-<?= $c['id'] ?>">
                  <td><?= $c['id'] ?></td>
                  <td>
                    <div class="user-name-cell">
                      <div class="user-avatar-sm"><?= strtoupper(substr($c['user_name']??'?',0,1)) ?></div>
                      <span><?= e($c['user_name'] ?? 'Unknown') ?></span>
                    </div>
                  </td>
                  <td>
                    <a href="<?= BASE ?>/user/post_detail.php?id=<?= $c['post_id'] ?>" class="link-subtle">
                      <?= e(mb_strimwidth($c['post_title'] ?? 'Unknown Post', 0, 40, '…')) ?>
                    </a>
                  </td>
                  <td class="comment-cell"><?= e(mb_strimwidth($c['content'], 0, 100, '…')) ?></td>
                  <td><?= date('M j, Y H:i', strtotime($c['created_at'])) ?></td>
                  <td>
                    <button class="btn btn-danger btn-sm delete-comment" data-id="<?= $c['id'] ?>">Delete</button>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

  </div>
</div>

<footer class="footer"><span>Wanderlust</span> Admin Panel &copy; <?= date('Y') ?></footer>
<script src="<?= BASE ?>/public/js/admin.js"></script>
<script>
// Search filter for comments table
document.getElementById('commentSearch')?.addEventListener('input', function() {
  const q = this.value.toLowerCase();
  document.querySelectorAll('#commentsTable tbody tr').forEach(row => {
    row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
  });
});
</script>
</body>
</html>
