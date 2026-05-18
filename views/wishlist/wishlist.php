<?php
$base = '/travel_guide';
$costColors = ['low'=>'tag-low','medium'=>'tag-medium','high'=>'tag-high'];
$genreEmoji = ['beach'=>'🏖️','mountain'=>'⛰️','city'=>'🏙️','historical'=>'🏛️','nature'=>'🌿','adventure'=>'🧗'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Wishlist · Wanderlust</title>
  <link rel="stylesheet" href="<?= $base ?>/public/css/style.css">
</head>
<body>
<?php require __DIR__ . '/../partials/navbar.php'; ?>

<div class="page-wrapper">
  <div class="container-md">

    <div class="section-header fade-up">
      <div>
        <h2 style="font-family:'Cormorant Garamond',serif; color:var(--navy);">
          ❤️ My Wishlist
        </h2>
        <div class="section-line"></div>
      </div>
      <?php if (!empty($items)): ?>
        <span style="font-size:0.85rem; color:var(--text-muted);">
          <?= count($items) ?> destination<?= count($items)!==1?'s':'' ?> saved
        </span>
      <?php endif; ?>
    </div>

    <?php if (empty($items)): ?>
      <div class="card fade-up" style="text-align:center; padding:3rem;">
        <div style="font-size:3.5rem; margin-bottom:1rem;">🗺️</div>
        <h3 style="font-family:'Cormorant Garamond',serif; color:var(--navy); margin-bottom:0.5rem;">
          Your wishlist is empty
        </h3>
        <p style="color:var(--text-muted); margin-bottom:1.5rem;">
          Discover amazing destinations and save them here for later.
        </p>
        <a href="<?= $base ?>/index.php" class="btn btn-primary" style="width:auto; display:inline-flex;">
          Browse Destinations →
        </a>
      </div>

    <?php else: ?>
      <div id="wishlistContainer">
        <?php foreach ($items as $i => $item):
          $emoji = $genreEmoji[strtolower($item['genre'] ?? '')] ?? '📍';
          $costClass = $costColors[$item['cost_level']] ?? '';
        ?>
          <div class="wishlist-card fade-up" style="animation-delay:<?= $i*0.07 ?>s"
               id="wishlist-item-<?= (int)$item['post_id'] ?>">
            <div class="wishlist-icon"><?= $emoji ?></div>
            <div class="wishlist-info">
              <div class="wishlist-title"><?= e($item['title']) ?></div>
              <div class="wishlist-meta">
                <span class="tag tag-country">📍 <?= e($item['country']) ?></span>
                <span class="tag tag-genre" style="margin-left:4px;"><?= e($item['genre']) ?></span>
                <span class="tag <?= $costClass ?>" style="margin-left:4px;"><?= e(ucfirst($item['cost_level'])) ?> Cost</span>
              </div>
            </div>
            <div style="display:flex; flex-direction:column; gap:0.4rem; align-items:flex-end;">
              <div class="wishlist-date">Added <?= date('M j, Y', strtotime($item['added_at'])) ?></div>
              <div style="display:flex; gap:0.5rem;">
                <a href="<?= $base ?>/user/post_detail.php?id=<?= (int)$item['post_id'] ?>" class="btn btn-ghost">View</a>
                <button class="btn btn-danger remove-btn"
                        data-post-id="<?= (int)$item['post_id'] ?>"
                        data-csrf="<?= csrfToken() ?>">
                  🗑 Remove
                </button>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  </div>
</div>

<footer class="footer">
  © <?= date('Y') ?> <span>Wanderlust</span> · Travel Guide Project
</footer>

<script src="<?= $base ?>/public/js/wishlist.js"></script>
</body>
</html>
