<?php
// views/scout/create_request.php
$base   = '/travel_guide';
$errors = $_SESSION['form_errors'] ?? [];
$old    = $_SESSION['form_old']    ?? [];
unset($_SESSION['form_errors'], $_SESSION['form_old']);

$genres = ['beach','mountain','city','historical','nature','adventure','cultural','wildlife'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>New Request · Wanderlust Scout</title>
  <link rel="stylesheet" href="<?= $base ?>/public/css/style.css">
  <link rel="stylesheet" href="<?= $base ?>/public/css/scout.css">
</head>
<body>
<?php require __DIR__ . '/../partials/navbar.php'; ?>

<div class="page-wrapper">
  <div class="container-md">

    <div class="scout-page-header fade-up">
      <div>
        <h2>Submit New Request</h2>
        <div class="section-line"></div>
        <p style="color:var(--text-muted); font-size:0.9rem;">
          Share a new travel destination with the world
        </p>
      </div>
      <a href="<?= $base ?>/scout/requests.php" class="btn btn-secondary" style="width:auto; padding:0.6rem 1.2rem;">
        ← Back
      </a>
    </div>

    <?php if ($errors): ?>
      <div class="alert alert-error fade-up">
        <span class="alert-icon">⚠</span>
        <ul><?php foreach($errors as $e): ?><li><?= e($e) ?></li><?php endforeach; ?></ul>
      </div>
    <?php endif; ?>

    <div class="card fade-up">
      <form id="scoutCreateForm" action="<?= $base ?>/scout/create.php" method="POST"
            enctype="multipart/form-data" novalidate>
        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">

        <!-- Basic Info -->
        <div class="form-section-title">📍 Destination Info</div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Destination Title *</label>
            <input type="text" name="title" class="form-control" id="sc_title"
                   value="<?= e($old['title'] ?? '') ?>"
                   placeholder="e.g. Sundarban Mangrove Forest" required>
            <span class="field-error" id="sc_titleErr"></span>
          </div>
          <div class="form-group">
            <label class="form-label">Country *</label>
            <input type="text" name="country" class="form-control" id="sc_country"
                   value="<?= e($old['country'] ?? '') ?>"
                   placeholder="e.g. Bangladesh" required>
            <span class="field-error" id="sc_countryErr"></span>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Genre *</label>
            <select name="genre" class="form-control" id="sc_genre" required>
              <option value="">— Select Genre —</option>
              <?php foreach ($genres as $g): ?>
                <option value="<?= $g ?>" <?= ($old['genre'] ?? '')===$g?'selected':'' ?>>
                  <?= ucfirst($g) ?>
                </option>
              <?php endforeach; ?>
            </select>
            <span class="field-error" id="sc_genreErr"></span>
          </div>
          <div class="form-group">
            <label class="form-label">Cost Level *</label>
            <select name="cost_level" class="form-control" id="sc_cost" required>
              <option value="">— Select Cost —</option>
              <option value="low"    <?= ($old['cost_level']??'')==='low'   ?'selected':'' ?>>💚 Low</option>
              <option value="medium" <?= ($old['cost_level']??'')==='medium'?'selected':'' ?>>🟡 Medium</option>
              <option value="high"   <?= ($old['cost_level']??'')==='high'  ?'selected':'' ?>>🔴 High</option>
            </select>
            <span class="field-error" id="sc_costErr"></span>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Travel Medium / How to Get There *</label>
          <input type="text" name="travel_medium_info" class="form-control" id="sc_travel"
                 value="<?= e($old['travel_medium_info'] ?? '') ?>"
                 placeholder="e.g. Flight to Dhaka, then boat ride (3 hours)" required>
          <span class="field-error" id="sc_travelErr"></span>
        </div>

        <!-- History -->
        <div class="form-section-title" style="margin-top:1.5rem;">📜 Description</div>

        <div class="form-group">
          <label class="form-label">Short History / Description *</label>
          <textarea name="short_history" class="form-control" id="sc_history"
                    rows="5" placeholder="Describe the place — its history, cultural significance, what makes it special... (min 20 characters)"
                    required><?= e($old['short_history'] ?? '') ?></textarea>
          <span class="field-error" id="sc_historyErr"></span>
          <div style="font-size:0.75rem; color:var(--text-muted); margin-top:0.3rem;">
            <span id="historyCount">0</span> characters (min 20)
          </div>
        </div>

        <!-- Images -->
        <div class="form-section-title" style="margin-top:1.5rem;">🖼️ Images (Optional)</div>

        <div class="form-group">
          <label class="form-label">Upload Images (max 5, 3MB each)</label>
          <div class="file-input-wrapper">
            <input type="file" name="images[]" accept="image/*" multiple id="imageUpload">
          </div>
          <p class="file-hint">JPEG, PNG, WebP or GIF · Max 3MB per file · Up to 5 files</p>
          <div id="imagePreview" class="image-preview-grid" style="display:none;"></div>
        </div>

        <div style="display:flex; gap:1rem; justify-content:flex-end; margin-top:1.5rem;">
          <a href="<?= $base ?>/scout/requests.php" class="btn btn-secondary">Cancel</a>
          <button type="submit" class="btn btn-primary" style="width:auto; padding:0.75rem 2rem;">
            Submit Request →
          </button>
        </div>
      </form>
    </div>

  </div>
</div>

<footer class="footer">© <?= date('Y') ?> <span>Wanderlust</span> · Scout Dashboard</footer>
<script src="<?= $base ?>/public/js/scout.js"></script>
</body>
</html>
