<?php require_once __DIR__ . '/../../config/session.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Post Moderation — Admin</title>
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
        <h2>📝 Post Moderation</h2>
        <p class="text-muted">Review pending requests and manage published posts.</p>
      </div>
      <a href="<?= BASE ?>/admin/dashboard.php" class="btn btn-ghost">← Dashboard</a>
    </div>

    <?php if ($flash_success): ?>
      <div class="alert alert-success fade-up"><span class="alert-icon">✓</span><?= e($flash_success) ?></div>
    <?php endif; ?>
    <?php if ($flash_error): ?>
      <div class="alert alert-error fade-up"><span class="alert-icon">⚠</span><?= e($flash_error) ?></div>
    <?php endif; ?>

    <!-- Pending Requests Section -->
    <div class="section-label fade-up">
      <h3>⏳ Pending Post Requests <span class="badge badge-pending"><?= count(array_filter($requests, fn($r)=>$r['status']==='pending')) ?></span></h3>
    </div>

    <?php
    $pending = array_filter($requests, fn($r)=>$r['status']==='pending');
    if (empty($pending)):
    ?>
      <div class="empty-card fade-up">No pending requests right now. 🎉</div>
    <?php else: ?>
      <div class="requests-grid fade-up fade-up-1">
        <?php foreach ($pending as $req):
          $d = is_array($req['post_data']) ? $req['post_data'] : json_decode($req['post_data']??'{}',true);
        ?>
          <div class="request-card" id="req-<?= $req['id'] ?>">
            <div class="req-head">
              <div>
                <strong class="req-title"><?= e($d['title'] ?? 'Untitled') ?></strong>
                <span class="req-meta">by <?= e($req['scout_name'] ?? 'Unknown') ?> · <?= date('M j, Y', strtotime($req['requested_at'])) ?></span>
              </div>
              <span class="badge badge-pending">Pending</span>
            </div>
            <div class="req-details">
              <span>🌍 <?= e($d['country'] ?? '—') ?></span>
              <span>🏷 <?= e($d['genre'] ?? '—') ?></span>
              <span>💰 <?= ucfirst($d['cost_level'] ?? '—') ?></span>
              <span>🚗 <?= e($d['travel_medium_info'] ?? '—') ?></span>
            </div>
            <?php if (!empty($d['short_history'])): ?>
              <p class="req-history"><?= e(substr($d['short_history'],0,200)) ?>...</p>
            <?php endif; ?>
            <div class="req-actions">
              <button class="btn btn-approve" data-id="<?= $req['id'] ?>">✓ Approve</button>
              <button class="btn btn-reject"  data-id="<?= $req['id'] ?>">✗ Reject</button>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <!-- All Posts Section -->
    <div class="section-label fade-up" style="margin-top:2.5rem;">
      <h3>🗺️ Published Posts <span class="badge badge-active"><?= count(array_filter($posts, fn($p)=>$p['status']==='approved')) ?></span></h3>
    </div>

    <div class="card fade-up fade-up-2">
      <div class="table-responsive">
        <table class="admin-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Title</th>
              <th>Scout</th>
              <th>Country</th>
              <th>Genre</th>
              <th>Cost</th>
              <th>Status</th>
              <th>Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($posts as $post): ?>
              <tr id="post-row-<?= $post['id'] ?>">
                <td><?= $post['id'] ?></td>
                <td><strong><?= e($post['title']) ?></strong></td>
                <td><?= e($post['scout_name'] ?? '—') ?></td>
                <td><?= e($post['country']) ?></td>
                <td><span class="tag tag-genre"><?= e($post['genre']) ?></span></td>
                <td><span class="tag tag-<?= $post['cost_level'] ?>"><?= ucfirst($post['cost_level']) ?></span></td>
                <td><span class="badge badge-<?= $post['status']==='approved'?'active':'inactive' ?>"><?= ucfirst($post['status']) ?></span></td>
                <td><?= date('M j, Y', strtotime($post['created_at'])) ?></td>
                <td>
                  <div class="action-btns">
                    <a href="<?= BASE ?>/admin/edit_post.php?id=<?= $post['id'] ?>" class="btn btn-ghost btn-sm">Edit</a>
                    <button class="btn btn-danger btn-sm delete-post" data-id="<?= $post['id'] ?>" data-title="<?= e($post['title']) ?>">Delete</button>
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

<footer class="footer"><span>Wanderlust</span> Admin Panel &copy; <?= date('Y') ?></footer>
<script src="<?= BASE ?>/public/js/admin.js"></script>
</body>
</html>
