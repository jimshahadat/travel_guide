<?php
// views/scout/change_request.php
$base   = '/travel_guide';
$errors = $_SESSION['form_errors'] ?? [];
unset($_SESSION['form_errors']);
$genres = ['beach','mountain','city','historical','nature','adventure','cultural','wildlife'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Request Changes · Wanderlust Scout</title>
  <link rel="stylesheet" href="<?= $base ?>/public/css/style.css">
  <link rel="stylesheet" href="<?= $base ?>/public/css/scout.css">
</head>
<body>
<?php require __DIR__ . '/../partials/navbar.php'; ?>

<div class="page-wrapper">
  <div class="container-md">

    <div class="scout-page-header fade-up">
      <div>
        <h2>Request Changes</h2>
        <div class="section-line"></div>
        <p style="color:var(--text-muted); font-size:0.9rem;">
          Suggest updated information for: <strong><?= e($post['title']) ?></strong>
        </p>
      </div>
      <a href="<?= $base ?>/scout/approved.php" class="btn btn-secondary" style="width:auto;">← Back</a>
    </div>

    <!-- Current Post Info Banner -->
    <div style="background:var(--ivory-dark); border-radius:10px; padding:1rem 1.2rem; margin-bottom:1.5rem; border-left:4px solid var(--gold);" class="fade-up">
      <p style="font-size:0.82rem; color:var(--text-muted); margin-bottom:0.3rem;">ORIGINAL POST</p>
      <strong style="color:var(--navy);"><?= e($post['title']) ?></strong>
      <span style="margin-left:1rem; font-size:0.82rem; color:var(--text-muted);">
        <?= e($post['country']) ?> · <?= e($post['genre']) ?> · <?= e(ucfirst($post['cost_level'])) ?> cost
      </span>
    </div>

    <?php if ($errors): ?>
      <div class="alert alert-error fade-up">
        <span class="alert-icon">⚠</span>
        <ul><?php foreach($errors as $e): ?><li><?= e($e) ?></li><?php endforeach; ?></ul>
      </div>
    <?php endif; ?>

    <div class="alert alert-warning fade-up">
      <span class="alert-icon">ℹ</span>
      Fill in the <strong>updated</strong> information below. Admin will review and apply the changes.
    </div>

    <div class="card fade-up">
      <form id="scoutCreateForm" action="<?= $base ?>/scout/change_request.php" method="POST"
            enctype="multipart/form-data" novalidate>
        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
        <input type="hidden" name="original_post_id" value="<?= (int)$post['id'] ?>">

        <div class="form-section-title">📍 Updated Destination Info</div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Title *</label>
            <input type="text" name="title" class="form-control" id="sc_title"
                   value="<?= e($post['title']) ?>" required>
            <span class="field-error" id="sc_titleErr"></span>
          </div>
          <div class="form-group">
            <label class="form-label">Country *</label>
            <input type="text" name="country" class="form-control" id="sc_country"
                   value="<?= e($post['country']) ?>" required>
            <span class="field-error" id="sc_countryErr"></span>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Genre *</label>
            <select name="genre" class="form-control" id="sc_genre" required>
              <?php foreach ($genres as $g): ?>
                <option value="<?= $g ?>" <?= $post['genre']===$g?'selected':'' ?>><?= ucfirst($g) ?></option>
              <?php endforeach; ?>
            </select>
            <span class="field-error" id="sc_genreErr"></span>
          </div>
          <div class="form-group">
            <label class="form-label">Cost Level *</label>
            <select name="cost_level" class="form-control" id="sc_cost" required>
              <option value="low"    <?= $post['cost_level']==='low'   ?'selected':'' ?>>💚 Low</option>
              <option value="medium" <?= $post['cost_level']==='medium'?'selected':'' ?>>🟡 Medium</option>
              <option value="high"   <?= $post['cost_level']==='high'  ?'selected':'' ?>>🔴 High</option>
            </select>
            <span class="field-error" id="sc_costErr"></span>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Travel Medium *</label>
          <input type="text" name="travel_medium_info" class="form-control" id="sc_travel"
                 value="<?= e($post['travel_medium_info'] ?? '') ?>" required>
          <span class="field-error" id="sc_travelErr"></span>
        </div>

        <div class="form-section-title" style="margin-top:1.5rem;">📜 Updated Description</div>

        <div class="form-group">
          <label class="form-label">Short History / Description *</label>
          <textarea name="short_history" class="form-control" id="sc_history"
                    rows="5" required><?= e($post['short_history'] ?? '') ?></textarea>
          <span class="field-error" id="sc_historyErr"></span>
          <div style="font-size:0.75rem; color:var(--text-muted); margin-top:0.3rem;">
            <span id="historyCount">0</span> characters
          </div>
        </div>

        <div class="form-section-title" style="margin-top:1.5rem;">🖼️ New Images (Optional)</div>
        <div class="form-group">
          <div class="file-input-wrapper">
            <input type="file" name="images[]" accept="image/*" multiple id="imageUpload">
          </div>
          <p class="file-hint">Upload new images to replace existing ones · Max 5 files · 3MB each</p>
          <div id="imagePreview" class="image-preview-grid" style="display:none;"></div>
        </div>

        <div style="display:flex; gap:1rem; justify-content:flex-end; margin-top:1.5rem;">
          <a href="<?= $base ?>/scout/approved.php" class="btn btn-secondary">Cancel</a>
          <button type="submit" class="btn btn-primary" style="width:auto; padding:0.75rem 2rem;">
            Submit Change Request →
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
