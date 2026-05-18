<?php require_once __DIR__ . '/../../config/session.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard — Wanderlust</title>
  <link rel="stylesheet" href="<?= BASE ?>/public/css/style.css">
  <link rel="stylesheet" href="<?= BASE ?>/public/css/admin.css">
</head>
<body>
<?php require __DIR__ . '/../partials/navbar.php'; ?>
<div class="page-wrapper">
  <div class="container">

    <div class="admin-header fade-up">
      <div>
        <h2>🛡️ Admin Dashboard</h2>
        <p class="text-muted">Welcome back, <?= e($_SESSION['name']) ?>. Here's an overview of the platform.</p>
      </div>
      <div class="admin-header-actions">
        <a href="<?= BASE ?>/admin/users.php" class="btn btn-ghost">👥 Manage Users</a>
        <a href="<?= BASE ?>/admin/posts.php" class="btn btn-ghost">📝 Manage Posts</a>
        <a href="<?= BASE ?>/admin/comments.php" class="btn btn-ghost">💬 Comments</a>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid fade-up fade-up-1">
      <div class="stat-card">
        <div class="stat-icon">👥</div>
        <div class="stat-body">
          <div class="stat-value"><?= $totalUsers ?></div>
          <div class="stat-label">Total Users</div>
          <div class="stat-sub">
            <span class="role-badge admin">Admin: <?= $userCounts['admin'] ?></span>
            <span class="role-badge scout">Scout: <?= $userCounts['scout'] ?></span>
            <span class="role-badge user">Users: <?= $userCounts['user'] ?></span>
          </div>
        </div>
      </div>
      <div class="stat-card stat-warn">
        <div class="stat-icon">⏳</div>
        <div class="stat-body">
          <div class="stat-value"><?= $pendingReqs ?></div>
          <div class="stat-label">Pending Requests</div>
          <div class="stat-sub"><a href="<?= BASE ?>/admin/posts.php">Review now →</a></div>
        </div>
      </div>
      <div class="stat-card stat-success">
        <div class="stat-icon">🗺️</div>
        <div class="stat-body">
          <div class="stat-value"><?= $totalPosts ?></div>
          <div class="stat-label">Total Posts</div>
          <div class="stat-sub"><a href="<?= BASE ?>/admin/posts.php">View all →</a></div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">💬</div>
        <div class="stat-body">
          <div class="stat-value"><?= $totalComments ?></div>
          <div class="stat-label">Total Comments</div>
          <div class="stat-sub"><a href="<?= BASE ?>/admin/comments.php">Moderate →</a></div>
        </div>
      </div>
    </div>

    <div class="admin-two-col fade-up fade-up-2">
      <!-- Recent Pending Requests -->
      <div class="card">
        <div class="card-head">
          <h3>⏳ Pending Requests</h3>
          <a href="<?= BASE ?>/admin/posts.php" class="link-more">View all</a>
        </div>
        <?php if (empty($recentReqs)): ?>
          <p class="empty-state">No pending requests. 🎉</p>
        <?php else: ?>
          <div class="mini-list">
            <?php foreach ($recentReqs as $req):
              $d = is_array($req['post_data']) ? $req['post_data'] : json_decode($req['post_data'] ?? '{}', true);
            ?>
              <div class="mini-item">
                <div class="mini-icon">📋</div>
                <div class="mini-body">
                  <strong><?= e($d['title'] ?? 'Untitled') ?></strong>
                  <span>by <?= e($req['scout_name'] ?? 'Unknown') ?></span>
                </div>
                <span class="badge badge-pending">Pending</span>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <!-- Recent Users -->
      <div class="card">
        <div class="card-head">
          <h3>👥 Recent Users</h3>
          <a href="<?= BASE ?>/admin/users.php" class="link-more">View all</a>
        </div>
        <?php if (empty($recentUsers)): ?>
          <p class="empty-state">No users yet.</p>
        <?php else: ?>
          <div class="mini-list">
            <?php foreach ($recentUsers as $u): ?>
              <div class="mini-item">
                <div class="mini-icon"><?= $u['role']==='admin'?'🛡️':($u['role']==='scout'?'🔍':'👤') ?></div>
                <div class="mini-body">
                  <strong><?= e($u['name']) ?></strong>
                  <span><?= e($u['email']) ?></span>
                </div>
                <span class="badge badge-<?= $u['is_verified']?'active':'inactive' ?>"><?= $u['is_verified']?'Verified':'Pending' ?></span>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>

  </div>
</div>
<footer class="footer"><span>Wanderlust</span> Admin Panel &copy; <?= date('Y') ?></footer>
</body>
</html>
