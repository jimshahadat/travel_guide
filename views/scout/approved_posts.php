<?php
// views/scout/approved_posts.php
$base    = '/travel_guide';
$success = getFlash('success');
$error   = getFlash('error');
$genreEmoji = ['beach'=>'🏖️','mountain'=>'⛰️','city'=>'🏙️','historical'=>'🏛️','nature'=>'🌿','adventure'=>'🧗','cultural'=>'🎭','wildlife'=>'🦁'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Approved Posts · Wanderlust Scout</title>
  <link rel="stylesheet" href="<?= $base ?>/public/css/style.css">
  <link rel="stylesheet" href="<?= $base ?>/public/css/scout.css">
</head>
<body>
<?php require __DIR__ . '/../partials/navbar.php'; ?>

<div class="page-wrapper">
  <div class="container">

    <div class="scout-page-header fade-up">
      <div>
        <h2>My Approved Posts</h2>
        <div class="section-line"></div>
        <p style="color:var(--text-muted); font-size:0.9rem;">
          Destinations you've submitted that are now live
        </p>
      </div>
    </div>

    <?php if ($success): ?>
      <div class="alert alert-success fade-up"><span class="alert-icon">✓</span> <?= e($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="alert alert-error fade-up"><span class="alert-icon">⚠</span> <?= e($error) ?></div>
    <?php endif; ?>

    <?php if (empty($posts)): ?>
      <div class="card fade-up" style="text-align:center; padding:3.5rem;">
        <div style="font-size:3rem; margin-bottom:1rem;">🌐</div>
        <h3 style="font-family:'Cormorant Garamond',serif; color:var(--navy);">No approved posts yet</h3>
        <p style="color:var(--text-muted); margin-bottom:1.5rem;">
          Your submitted requests are pending admin approval.
        </p>
        <a href="<?= $base ?>/scout/requests.php" class="btn btn-primary" style="width:auto; display:inline-flex;">
          View My Requests →
        </a>
      </div>

    <?php else: ?>
      <div class="post-grid">
        <?php foreach ($posts as $i => $post):
          $emoji = $genreEmoji[strtolower($post['genre'] ?? '')] ?? '📍';
        ?>
          <div class="post-card fade-up" style="animation-delay:<?= $i*0.07 ?>s">
            <div class="post-card-img"><?= $emoji ?></div>
            <div class="post-card-body">
              <div class="post-meta">
                <span class="tag tag-country">📍 <?= e($post['country']) ?></span>
                <span class="tag tag-genre"><?= e($post['genre']) ?></span>
                <span class="tag tag-<?= e($post['cost_level']) ?>"><?= e(ucfirst($post['cost_level'])) ?> Cost</span>
                <span class="tag" style="background:#e8f5e9;color:#2e7d32;">✅ Live</span>
              </div>
              <h3 class="post-card-title"><?= e($post['title']) ?></h3>
              <p class="post-snippet"><?= e(mb_strimwidth($post['short_history'] ?? '', 0, 100, '…')) ?></p>
              <p style="font-size:0.75rem; color:var(--text-muted);">
                Published: <?= date('M j, Y', strtotime($post['created_at'])) ?>
              </p>
            </div>
            <div class="post-card-footer">
              <a href="<?= $base ?>/scout/change_request.php?post_id=<?= (int)$post['id'] ?>"
                 class="btn btn-ghost" style="font-size:0.82rem;">
                ✏ Request Changes
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  </div>
</div>

<footer class="footer">© <?= date('Y') ?> <span>Wanderlust</span> · Scout Dashboard</footer>
</body>
</html>
