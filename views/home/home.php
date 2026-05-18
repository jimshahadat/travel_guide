<?php
$base = '/travel_guide';
$genreEmoji = ['beach'=>'🏖️','mountain'=>'⛰️','city'=>'🏙️','historical'=>'🏛️','nature'=>'🌿','adventure'=>'🧗'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Wanderlust · Discover the World</title>
  <link rel="stylesheet" href="<?= $base ?>/public/css/style.css">
</head>
<body>
<?php require __DIR__ . '/../partials/navbar.php'; ?>

<?php if (!isLoggedIn()): ?>
  <!-- ── NON-REGISTERED HERO ── -->
  <section class="hero">
    <div class="hero-content">
      <p class="hero-eyebrow">✦ Your Gateway to the World</p>
      <h1>Explore the World's Most <em>Beautiful</em> Destinations</h1>
      <p>Curated travel guides, hidden gems, and unforgettable experiences — hand-picked by our global network of scouts.</p>
      <div class="hero-actions">
        <a href="<?= $base ?>/register.php" class="btn-hero-primary">Start Exploring</a>
        <a href="<?= $base ?>/login.php" class="btn-hero-outline">Sign In</a>
      </div>
    </div>
  </section>

  <!-- Features strip -->
  <div style="background:var(--white); padding:3rem 2rem;">
    <div class="container">
      <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:2rem; text-align:center;">
        <?php foreach([
          ['🗺️','Curated Guides','Hand-picked destinations worldwide'],
          ['💰','Cost Estimates','Budget before you travel'],
          ['❤️','Wishlists','Save your dream destinations'],
          ['🔍','Smart Search','Filter by country, genre & budget'],
        ] as [$icon,$title,$desc]): ?>
          <div class="fade-up">
            <div style="font-size:2.2rem; margin-bottom:0.7rem;"><?= $icon ?></div>
            <h3 style="font-family:'Cormorant Garamond',serif; color:var(--navy); margin-bottom:0.3rem;"><?= $title ?></h3>
            <p style="font-size:0.85rem; color:var(--text-muted);"><?= $desc ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

<?php elseif (!isVerified()): ?>
  <!-- ── PENDING APPROVAL ── -->
  <div class="page-wrapper">
    <div class="container-md">
      <div class="pending-banner fade-up">
        <div class="icon">⏳</div>
        <h2>Account Pending Approval</h2>
        <p>Your account has been created successfully. An admin will verify your account shortly.<br>
        You'll have full access once approved.</p>
        <div style="margin-top:1.5rem;">
          <a href="<?= $base ?>/profile.php" class="btn-hero-outline">View Profile</a>
        </div>
      </div>
    </div>
  </div>

<?php else: ?>
  <!-- ── VERIFIED USER HOME ── -->
  <div class="page-wrapper">
    <div class="container">

      <?php
      $flash = getFlash('success');
      if ($flash): ?>
        <div class="alert alert-success fade-up">
          <span class="alert-icon">✓</span> <?= e($flash) ?>
        </div>
      <?php endif; ?>

      <div class="section-header fade-up">
        <div>
          <h2>Latest Destinations</h2>
          <div class="section-line"></div>
        </div>
      </div>

      <?php if (empty($latestPosts)): ?>
        <div class="card" style="text-align:center; padding:3rem; color:var(--text-muted);">
          <div style="font-size:3rem; margin-bottom:1rem;">🌍</div>
          <h3 style="font-family:'Cormorant Garamond',serif;">No destinations yet</h3>
          <p>Scouts are busy discovering amazing places. Check back soon!</p>
        </div>
      <?php else: ?>
        <div class="post-grid">
          <?php foreach ($latestPosts as $i => $post):
            $emoji = $genreEmoji[strtolower($post['genre'])] ?? '📍';
          ?>
            <div class="post-card fade-up" style="animation-delay:<?= $i*0.08 ?>s">
              <div class="post-card-img"><?= $emoji ?></div>
              <div class="post-card-body">
                <div class="post-meta">
                  <span class="tag tag-country">📍 <?= e($post['country']) ?></span>
                  <span class="tag tag-genre"><?= e($post['genre']) ?></span>
                  <span class="tag tag-<?= e($post['cost_level']) ?>"><?= e(ucfirst($post['cost_level'])) ?> Cost</span>
                </div>
                <h3 class="post-card-title"><?= e($post['title']) ?></h3>
                <p class="post-snippet"><?= e(mb_strimwidth($post['short_history'] ?? '', 0, 110, '…')) ?></p>
              </div>
              <div class="post-card-footer">
                <a href="<?= $base ?>/user/post_detail.php?id=<?= (int)$post['id'] ?>" class="btn btn-ghost">Read More</a>
                <?php if ($_SESSION['role']==='user'): ?>
                  <button class="btn btn-ghost wishlist-btn"
                          data-post-id="<?= (int)$post['id'] ?>"
                          data-csrf="<?= csrfToken() ?>">
                    ♡ Wishlist
                  </button>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
<?php endif; ?>

<footer class="footer">
  © <?= date('Y') ?> <span>Wanderlust</span> · Travel Guide Project · All rights reserved
</footer>

<script src="<?= $base ?>/public/js/wishlist.js"></script>
</body>
</html>
