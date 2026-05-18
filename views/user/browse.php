<?php require_once __DIR__ . '/../../config/session.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Browse Destinations — Wanderlust</title>
  <link rel="stylesheet" href="<?= BASE ?>/public/css/style.css">
  <link rel="stylesheet" href="<?= BASE ?>/public/css/user.css">
</head>
<body>
<?php require __DIR__ . '/../partials/navbar.php'; ?>

<div class="browse-hero">
  <div class="browse-hero-content">
    <p class="hero-eyebrow">🌍 Discover the World</p>
    <h1>Explore <em>Destinations</em></h1>
    <p>Browse curated travel guides from our community scouts worldwide.</p>
  </div>
</div>

<div class="page-wrapper">
  <div class="container">

    <?php if ($flash_success): ?>
      <div class="alert alert-success"><span class="alert-icon">✓</span><?= e($flash_success) ?></div>
    <?php endif; ?>

    <!-- Search & Filter Bar -->
    <div class="filter-bar card fade-up">
      <div class="filter-search">
        <input type="text" id="searchInput" class="form-control" placeholder="🔍 Search destinations, countries..." value="">
      </div>
      <div class="filter-controls">
        <select id="filterCountry" class="form-control">
          <option value="">All Countries</option>
          <?php foreach ($countries as $c): ?>
            <option value="<?= e($c) ?>"><?= e($c) ?></option>
          <?php endforeach; ?>
        </select>
        <select id="filterGenre" class="form-control">
          <option value="">All Genres</option>
          <?php foreach (['beach','mountain','city','historical','nature','cultural','adventure','other'] as $g): ?>
            <option value="<?= $g ?>"><?= ucfirst($g) ?></option>
          <?php endforeach; ?>
        </select>
        <select id="filterCost" class="form-control">
          <option value="">All Budgets</option>
          <option value="low">💚 Budget (Low)</option>
          <option value="medium">💛 Mid-range</option>
          <option value="high">💎 Luxury</option>
        </select>
        <button id="clearFilters" class="btn btn-secondary btn-sm">Clear</button>
      </div>
    </div>

    <!-- Results count -->
    <div class="results-info fade-up fade-up-1">
      <span id="resultsCount"><?= count($posts) ?> destinations found</span>
    </div>

    <!-- Posts Grid -->
    <div class="post-grid fade-up fade-up-2" id="postsGrid">
      <?php foreach ($posts as $post): ?>
        <div class="post-card">
          <div class="post-card-img">
            <?php
            $genreEmoji = ['beach'=>'🏖️','mountain'=>'⛰️','city'=>'🌆','historical'=>'🏛️',
                           'nature'=>'🌿','cultural'=>'🎭','adventure'=>'🧗','other'=>'🌍'];
            echo $genreEmoji[$post['genre']] ?? '🌍';
            ?>
          </div>
          <div class="post-card-body">
            <div class="post-meta">
              <span class="tag tag-country">🌍 <?= e($post['country']) ?></span>
              <span class="tag tag-genre"><?= e($post['genre']) ?></span>
              <span class="tag tag-<?= $post['cost_level'] ?>"><?= ucfirst($post['cost_level']) ?></span>
            </div>
            <h3 class="post-card-title"><?= e($post['title']) ?></h3>
            <p class="post-snippet"><?= e(mb_strimwidth($post['short_history'] ?? '', 0, 120, '…')) ?></p>
          </div>
          <div class="post-card-footer">
            <a href="<?= BASE ?>/user/post_detail.php?id=<?= $post['id'] ?>" class="btn btn-ghost" style="flex:1;text-align:center;">Read More →</a>
            <?php if ($_SESSION['role'] === 'user'): ?>
              <button class="btn-wishlist" data-id="<?= $post['id'] ?>" title="Add to Wishlist">♡</button>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- No results state -->
    <div id="noResults" class="empty-card" style="display:none;">
      <div style="font-size:3rem;margin-bottom:1rem;">🔍</div>
      <h3>No destinations found</h3>
      <p>Try adjusting your search or filters.</p>
    </div>

  </div>
</div>

<footer class="footer"><span>Wanderlust</span> &copy; <?= date('Y') ?> · Explore the World</footer>
<script src="<?= BASE ?>/public/js/user.js"></script>
<script>
const CSRF = '<?= csrfToken() ?>';
const BASE_URL = '<?= BASE ?>';
</script>
</body>
</html>
