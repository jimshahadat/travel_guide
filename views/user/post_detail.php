<?php require_once __DIR__ . '/../../config/session.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($post['title']) ?> — Wanderlust</title>
  <link rel="stylesheet" href="<?= BASE ?>/public/css/style.css">
  <link rel="stylesheet" href="<?= BASE ?>/public/css/user.css">
</head>
<body>
<?php require __DIR__ . '/../partials/navbar.php'; ?>

<!-- Post Hero -->
<div class="post-hero">
  <div class="post-hero-overlay"></div>
  <div class="post-hero-content">
    <div class="post-meta" style="justify-content:center;margin-bottom:1rem;">
      <span class="tag tag-country">🌍 <?= e($post['country']) ?></span>
      <span class="tag tag-genre"><?= e($post['genre']) ?></span>
      <span class="tag tag-<?= $post['cost_level'] ?>"><?= ucfirst($post['cost_level']) ?></span>
    </div>
    <h1><?= e($post['title']) ?></h1>
    <p>By <?= e($post['scout_name'] ?? 'Scout') ?></p>
  </div>
</div>

<div class="page-wrapper">
  <div class="container-md">

    <?php if ($flash_success): ?>
      <div class="alert alert-success"><span class="alert-icon">✓</span><?= e($flash_success) ?></div>
    <?php endif; ?>
    <?php if ($flash_error): ?>
      <div class="alert alert-error"><span class="alert-icon">⚠</span><?= e($flash_error) ?></div>
    <?php endif; ?>

    <!-- Back link -->
    <div style="margin-bottom:1.5rem;">
      <a href="<?= BASE ?>/user/browse.php" class="btn btn-ghost btn-sm">← Back to Browse</a>
      <?php if ($_SESSION['role'] === 'user'): ?>
        <button id="wishlistBtn" class="btn btn-ghost btn-sm <?= $inWishlist?'btn-wishlisted':'' ?>"
          data-id="<?= $post['id'] ?>" style="margin-left:.5rem;">
          <?= $inWishlist ? '♥ In Wishlist' : '♡ Add to Wishlist' ?>
        </button>
      <?php endif; ?>
    </div>

    <!-- Post Content -->
    <div class="card fade-up" style="margin-bottom:1.5rem;">
      <h2 style="margin-bottom:1rem;">📖 About This Destination</h2>
      <div class="post-content">
        <?= nl2br(e($post['short_history'])) ?>
      </div>

      <div class="post-info-grid" style="margin-top:1.5rem;">
        <div class="info-item">
          <span class="info-label">🌍 Country</span>
          <span class="info-value"><?= e($post['country']) ?></span>
        </div>
        <div class="info-item">
          <span class="info-label">🏷 Genre</span>
          <span class="info-value"><?= ucfirst($post['genre']) ?></span>
        </div>
        <div class="info-item">
          <span class="info-label">💰 Cost Level</span>
          <span class="info-value"><?= ucfirst($post['cost_level']) ?></span>
        </div>
        <div class="info-item">
          <span class="info-label">🚗 Travel By</span>
          <span class="info-value"><?= e($post['travel_medium_info'] ?? 'Not specified') ?></span>
        </div>
      </div>
    </div>

    <!-- Cost Calculator (General USER only) -->
    <?php if ($_SESSION['role'] === 'user'): ?>
    <div class="card fade-up fade-up-1" style="margin-bottom:1.5rem;">
      <h2 style="margin-bottom:.5rem;">💰 Cost Estimator</h2>
      <p style="color:var(--text-muted);font-size:.88rem;margin-bottom:1.2rem;">
        Base cost: <strong>$<?= number_format($costEstimate['base_cost'] ?? ($post['cost_level']==='low'?500:($post['cost_level']==='medium'?1500:3000))) ?> <?= $costEstimate['currency']??'USD' ?></strong> per person/week
      </p>
      <div class="cost-calculator">
        <div class="calc-field">
          <label class="form-label">👥 Number of Travelers</label>
          <input type="number" id="calcTravelers" class="form-control" min="1" max="20" value="1" placeholder="1-20">
          <span class="field-error" id="err-travelers"></span>
        </div>
        <div class="calc-field">
          <label class="form-label">📅 Number of Days</label>
          <input type="number" id="calcDays" class="form-control" min="1" max="365" value="7" placeholder="1-365">
          <span class="field-error" id="err-days"></span>
        </div>
        <div class="calc-result">
          <button id="calcBtn" class="btn btn-primary" style="width:auto;padding:.7rem 2rem;">Calculate</button>
          <div id="costResult" class="cost-result-box" style="display:none;">
            <div class="cost-result-label">Estimated Total</div>
            <div class="cost-result-value" id="costResultValue">$0</div>
            <div class="cost-result-note" id="costResultNote"></div>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- Comments Section -->
    <div class="card fade-up fade-up-2">
      <div class="comments-header">
        <h2>💬 Comments <span class="badge badge-count" id="commentCount"><?= count($comments) ?></span></h2>
      </div>

      <!-- Existing Comments -->
      <div id="commentsList">
        <?php if (empty($comments)): ?>
          <div class="empty-comments" id="noComments">
            <p>No comments yet. Be the first to share your thoughts!</p>
          </div>
        <?php else: ?>
          <?php foreach ($comments as $c): ?>
            <div class="comment-item" id="comment-<?= $c['id'] ?>">
              <div class="comment-avatar"><?= strtoupper(substr($c['user_name']??'?',0,1)) ?></div>
              <div class="comment-body">
                <div class="comment-meta">
                  <strong><?= e($c['user_name'] ?? 'User') ?></strong>
                  <span><?= date('M j, Y · H:i', strtotime($c['created_at'])) ?></span>
                </div>
                <p class="comment-text"><?= e($c['content']) ?></p>
                <?php if (isLoggedIn() && ($c['user_id'] == $_SESSION['user_id'] || $_SESSION['role']==='admin')): ?>
                  <button class="btn-delete-comment" data-id="<?= $c['id'] ?>">Delete</button>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <!-- Add Comment Form (General USER only) -->
      <?php if (isLoggedIn() && $_SESSION['role']==='user' && isVerified()): ?>
        <div class="add-comment-form" style="margin-top:1.5rem;border-top:1px solid var(--ivory-dark);padding-top:1.5rem;">
          <h4 style="margin-bottom:1rem;">Add Your Comment</h4>
          <div id="commentAlert" class="alert" style="display:none;"></div>
          <div class="form-group">
            <label class="form-label">Your Name</label>
            <input type="text" id="commentName" class="form-control" value="<?= e($_SESSION['name']) ?>" readonly>
          </div>
          <div class="form-group">
            <label class="form-label">Comment *</label>
            <textarea id="commentContent" class="form-control" rows="4" maxlength="1000"
              placeholder="Share your thoughts about this destination..."></textarea>
            <span class="field-error" id="err-comment"></span>
            <small class="file-hint"><span id="charCount">0</span>/1000 characters</small>
          </div>
          <button id="submitComment" class="btn btn-primary" style="width:auto;padding:.7rem 2rem;">Post Comment</button>
        </div>
      <?php elseif (!isLoggedIn() || !isVerified()): ?>
        <div class="alert alert-warning" style="margin-top:1rem;">
          <span class="alert-icon">ℹ</span>
          You must be a verified general user to post comments.
          <a href="<?= BASE ?>/login.php">Login</a> or <a href="<?= BASE ?>/register.php">Register</a>.
        </div>
      <?php endif; ?>
    </div>

  </div>
</div>

<footer class="footer"><span>Wanderlust</span> &copy; <?= date('Y') ?></footer>

<script>
const POST_ID = <?= $post['id'] ?>;
const BASE_COST = <?= $costEstimate['base_cost'] ?? ($post['cost_level']==='low'?500:($post['cost_level']==='medium'?1500:3000)) ?>;
const CSRF = '<?= csrfToken() ?>';
const BASE_URL = '<?= BASE ?>';
const USER_ROLE = '<?= $_SESSION['role'] ?? '' ?>';
const IN_WISHLIST = <?= $inWishlist ? 'true' : 'false' ?>;
</script>
<script src="<?= BASE ?>/public/js/user.js"></script>
</body>
</html>
