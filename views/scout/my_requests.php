<?php
// views/scout/my_requests.php
$base    = '/travel_guide';
$success = getFlash('success');
$error   = getFlash('error');
$statusColors = [
    'pending'  => ['bg'=>'#fff8e1','color'=>'#f57f17','icon'=>'⏳'],
    'approved' => ['bg'=>'#e8f5e9','color'=>'#2e7d32','icon'=>'✅'],
    'rejected' => ['bg'=>'#fce4ec','color'=>'#880e4f','icon'=>'❌'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>My Requests · Wanderlust Scout</title>
  <link rel="stylesheet" href="<?= $base ?>/public/css/style.css">
  <link rel="stylesheet" href="<?= $base ?>/public/css/scout.css">
</head>
<body>
<?php require __DIR__ . '/../partials/navbar.php'; ?>

<div class="page-wrapper">
  <div class="container">

    <!-- Page Header -->
    <div class="scout-page-header fade-up">
      <div>
        <h2>My Post Requests</h2>
        <div class="section-line"></div>
        <p style="color:var(--text-muted); font-size:0.9rem;">
          Manage your submitted destination requests
        </p>
      </div>
      <a href="<?= $base ?>/scout/create.php" class="btn btn-primary" style="width:auto; padding:0.7rem 1.5rem;">
        + New Request
      </a>
    </div>

    <?php if ($success): ?>
      <div class="alert alert-success fade-up"><span class="alert-icon">✓</span> <?= e($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="alert alert-error fade-up"><span class="alert-icon">⚠</span> <?= e($error) ?></div>
    <?php endif; ?>

    <?php if (empty($requests)): ?>
      <div class="card fade-up" style="text-align:center; padding:3.5rem;">
        <div style="font-size:3rem; margin-bottom:1rem;">📝</div>
        <h3 style="font-family:'Cormorant Garamond',serif; color:var(--navy);">No requests yet</h3>
        <p style="color:var(--text-muted); margin-bottom:1.5rem;">
          Start by submitting your first destination for admin review.
        </p>
        <a href="<?= $base ?>/scout/create.php" class="btn btn-primary" style="width:auto; display:inline-flex;">
          Submit First Request →
        </a>
      </div>

    <?php else: ?>
      <div class="requests-table-wrapper fade-up">
        <table class="requests-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Destination</th>
              <th>Country</th>
              <th>Genre</th>
              <th>Cost</th>
              <th>Submitted</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="requestsTableBody">
            <?php foreach ($requests as $i => $req):
              $pd     = $req['post_data'];
              $sc     = $statusColors[$req['status']] ?? $statusColors['pending'];
            ?>
            <tr id="row-<?= $req['id'] ?>" class="fade-up" style="animation-delay:<?= $i*0.05 ?>s">
              <td class="td-num"><?= $i+1 ?></td>
              <td class="td-title"><?= e($pd['title'] ?? '—') ?></td>
              <td><?= e($pd['country'] ?? '—') ?></td>
              <td><span class="tag tag-genre"><?= e($pd['genre'] ?? '—') ?></span></td>
              <td><span class="tag tag-<?= e($pd['cost_level'] ?? 'low') ?>"><?= e(ucfirst($pd['cost_level'] ?? '—')) ?></span></td>
              <td class="td-date"><?= date('M j, Y', strtotime($req['requested_at'])) ?></td>
              <td>
                <span class="status-badge" style="background:<?= $sc['bg'] ?>;color:<?= $sc['color'] ?>;">
                  <?= $sc['icon'] ?> <?= ucfirst($req['status']) ?>
                </span>
              </td>
              <td class="td-actions">
                <?php if ($req['status'] === 'pending'): ?>
                  <a href="<?= $base ?>/scout/edit.php?id=<?= $req['id'] ?>"
                     class="btn btn-ghost btn-sm">✏ Edit</a>
                  <button class="btn btn-danger btn-sm delete-btn"
                          data-id="<?= $req['id'] ?>"
                          data-csrf="<?= csrfToken() ?>">
                    🗑 Delete
                  </button>
                <?php else: ?>
                  <span style="color:var(--text-muted); font-size:0.8rem;">—</span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>

  </div>
</div>

<footer class="footer">© <?= date('Y') ?> <span>Wanderlust</span> · Scout Dashboard</footer>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal-overlay" style="display:none;">
  <div class="modal-box">
    <div style="font-size:2.5rem; margin-bottom:0.8rem;">🗑️</div>
    <h3>Delete Request?</h3>
    <p>This action cannot be undone. The request will be permanently removed.</p>
    <div style="display:flex; gap:0.8rem; justify-content:center; margin-top:1.5rem;">
      <button id="cancelDelete" class="btn btn-secondary" style="width:auto;">Cancel</button>
      <button id="confirmDelete" class="btn btn-danger" style="width:auto; padding:0.7rem 1.5rem;">
        Yes, Delete
      </button>
    </div>
  </div>
</div>

<script src="<?= $base ?>/public/js/scout.js"></script>
</body>
</html>
