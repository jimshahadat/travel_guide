<?php
// views/scout/edit_request.php
$base   = '/travel_guide';
$errors = $_SESSION['form_errors'] ?? [];
unset($_SESSION['form_errors']);
$pd     = $req['post_data'];
$genres = ['beach','mountain','city','historical','nature','adventure','cultural','wildlife'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Edit Request · Wanderlust Scout</title>
  <link rel="stylesheet" href="<?= $base ?>/public/css/style.css">
  <link rel="stylesheet" href="<?= $base ?>/public/css/scout.css">
</head>
<body>
<?php require __DIR__ . '/../partials/navbar.php'; ?>

<div class="page-wrapper">
  <div class="container-md">

    <div class="scout-page-header fade-up">
      <div>
        <h2>Edit Request</h2>
        <div class="section-line"></div>
        <p style="color:var(--text-muted); font-size:0.9rem;">
          Update your pending destination request
        </p>
      </div>
      <a href="<?= $base ?>/scout/requests.php" class="btn btn-secondary" style="width:auto;">← Back</a>
    </div>

    <?php if ($errors): ?>
      <div class="alert alert-error fade-up">
        <span class="alert-icon">⚠</span>
        <ul><?php foreach($errors as $e): ?><li><?= e($e) ?></li><?php endforeach; ?></ul>
      </div>
    <?php endif; ?>

    <div id="editResponseMsg"></div>

    <div class="card fade-up">
      <form id="scoutEditForm" novalidate>
        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
        <input type="hidden" name="id" value="<?= (int)$req['id'] ?>">

        <div class="form-section-title">📍 Destination Info</div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Destination Title *</label>
            <input type="text" name="title" class="form-control" id="sc_title"
                   value="<?= e($pd['title'] ?? '') ?>" required>
            <span class="field-error" id="sc_titleErr"></span>
          </div>
          <div class="form-group">
            <label class="form-label">Country *</label>
            <input type="text" name="country" class="form-control" id="sc_country"
                   value="<?= e($pd['country'] ?? '') ?>" required>
            <span class="field-error" id="sc_countryErr"></span>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Genre *</label>
            <select name="genre" class="form-control" id="sc_genre" required>
              <option value="">— Select Genre —</option>
              <?php foreach ($genres as $g): ?>
                <option value="<?= $g ?>" <?= ($pd['genre'] ?? '')===$g?'selected':'' ?>>
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
              <option value="low"    <?= ($pd['cost_level']??'')==='low'   ?'selected':'' ?>>💚 Low</option>
              <option value="medium" <?= ($pd['cost_level']??'')==='medium'?'selected':'' ?>>🟡 Medium</option>
              <option value="high"   <?= ($pd['cost_level']??'')==='high'  ?'selected':'' ?>>🔴 High</option>
            </select>
            <span class="field-error" id="sc_costErr"></span>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Travel Medium *</label>
          <input type="text" name="travel_medium_info" class="form-control" id="sc_travel"
                 value="<?= e($pd['travel_medium_info'] ?? '') ?>" required>
          <span class="field-error" id="sc_travelErr"></span>
        </div>

        <div class="form-section-title" style="margin-top:1.5rem;">📜 Description</div>

        <div class="form-group">
          <label class="form-label">Short History / Description *</label>
          <textarea name="short_history" class="form-control" id="sc_history"
                    rows="5" required><?= e($pd['short_history'] ?? '') ?></textarea>
          <span class="field-error" id="sc_historyErr"></span>
          <div style="font-size:0.75rem; color:var(--text-muted); margin-top:0.3rem;">
            <span id="historyCount">0</span> characters
          </div>
        </div>

        <!-- Existing images -->
        <?php if (!empty($pd['images'])): ?>
          <div class="form-section-title" style="margin-top:1.5rem;">🖼️ Current Images</div>
          <div class="image-preview-grid" style="display:flex;">
            <?php foreach ($pd['images'] as $img): ?>
              <div class="preview-thumb">
                <img src="<?= $base ?>/public/uploads/posts/<?= e($img) ?>" alt="">
              </div>
            <?php endforeach; ?>
          </div>
          <p style="font-size:0.8rem; color:var(--text-muted); margin-top:0.5rem;">
            Upload new images below to replace these.
          </p>
        <?php endif; ?>

        <div class="form-section-title" style="margin-top:1.5rem;">🖼️ Replace Images (Optional)</div>
        <div class="form-group">
          <div class="file-input-wrapper">
            <input type="file" name="images[]" accept="image/*" multiple id="imageUpload">
          </div>
          <p class="file-hint">Leave blank to keep existing images · Max 5 files · 3MB each</p>
          <div id="imagePreview" class="image-preview-grid" style="display:none;"></div>
        </div>

        <div style="display:flex; gap:1rem; justify-content:flex-end; margin-top:1.5rem;">
          <a href="<?= $base ?>/scout/requests.php" class="btn btn-secondary">Cancel</a>
          <button type="submit" class="btn btn-primary" style="width:auto; padding:0.75rem 2rem;">
            Save Changes ✓
          </button>
        </div>
      </form>
    </div>

  </div>
</div>

<footer class="footer">© <?= date('Y') ?> <span>Wanderlust</span> · Scout Dashboard</footer>
<script src="<?= $base ?>/public/js/scout.js"></script>
<script>
// AJAX submit for edit form
document.getElementById('scoutEditForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  if (!validateScoutForm()) return;

  const btn = this.querySelector('[type=submit]');
  btn.disabled = true;
  btn.textContent = 'Saving…';

  const formData = new FormData(this);
  const resp = await fetch('/travel_guide/api/scout_update.php', {
    method: 'POST', body: formData
  });
  const data = await resp.json();
  const msgBox = document.getElementById('editResponseMsg');

  if (data.success) {
    msgBox.innerHTML = '<div class="alert alert-success"><span class="alert-icon">✓</span> ' + data.message + '</div>';
    setTimeout(() => window.location.href = '/travel_guide/scout/requests.php', 1200);
  } else {
    const errs = data.errors ? data.errors.join('<br>') : data.message;
    msgBox.innerHTML = '<div class="alert alert-error"><span class="alert-icon">⚠</span> ' + errs + '</div>';
    btn.disabled = false;
    btn.textContent = 'Save Changes ✓';
  }
});
</script>
</body>
</html>
