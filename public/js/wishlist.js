// public/js/wishlist.js
// AJAX POST to /api/wishlist/add.php  &  /api/wishlist/remove.php
const BASE = '/travel_guide';

// ── ADD TO WISHLIST ──────────────────────────────────────────────────────────
document.querySelectorAll('.wishlist-btn').forEach(btn => {
  btn.addEventListener('click', async function () {
    const postId = this.dataset.postId;
    const csrf   = this.dataset.csrf;
    this.disabled = true;
    this.textContent = '...';

    try {
      const res  = await fetch(BASE + '/api/wishlist.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=add&post_id=' + postId + '&csrf_token=' + encodeURIComponent(csrf)
      });
      const data = await res.json();
      if (data.success) {
        this.textContent = 'Saved';
        this.style.background  = 'rgba(201,168,76,0.2)';
        this.style.color       = 'var(--gold)';
        this.style.borderColor = 'var(--gold)';
        showToast('Added to wishlist!', 'success');
      } else {
        this.disabled = false;
        this.textContent = 'Wishlist';
        showToast(data.message, 'warn');
      }
    } catch(err) {
      this.disabled = false;
      this.textContent = 'Wishlist';
      showToast('Network error. Try again.', 'error');
    }
  });
});

// ── REMOVE FROM WISHLIST ─────────────────────────────────────────────────────
document.querySelectorAll('.remove-btn').forEach(btn => {
  btn.addEventListener('click', async function () {
    if (!confirm('Remove this destination from your wishlist?')) return;
    const postId = this.dataset.postId;
    const csrf   = this.dataset.csrf;
    const card   = document.getElementById('wishlist-item-' + postId);
    this.disabled = true;
    this.textContent = '...';

    try {
      const res  = await fetch(BASE + '/api/wishlist.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=remove&post_id=' + postId + '&csrf_token=' + encodeURIComponent(csrf)
      });
      const data = await res.json();
      if (data.success) {
        card.style.transition = 'all 0.35s ease';
        card.style.opacity    = '0';
        card.style.transform  = 'translateX(30px)';
        showToast('Removed from wishlist.', 'success');
        setTimeout(function() {
          card.remove();
          var container = document.getElementById('wishlistContainer');
          if (container && container.children.length === 0) {
            container.innerHTML = '<div class="card" style="text-align:center;padding:3rem;">'
              + '<div style="font-size:3.5rem;margin-bottom:1rem;">🗺</div>'
              + '<h3 style="font-family:Cormorant Garamond,serif;color:var(--navy);margin-bottom:.5rem;">Your wishlist is empty</h3>'
              + '<p style="color:var(--text-muted);margin-bottom:1.5rem;">Discover amazing destinations and save them here.</p>'
              + '<a href="' + BASE + '/index.php" class="btn btn-primary" style="width:auto;display:inline-flex;">Browse Destinations</a>'
              + '</div>';
          }
        }, 360);
      } else {
        this.disabled = false;
        this.textContent = 'Remove';
        showToast(data.message, 'error');
      }
    } catch(err) {
      this.disabled = false;
      this.textContent = 'Remove';
      showToast('Network error. Try again.', 'error');
    }
  });
});

// ── TOAST ────────────────────────────────────────────────────────────────────
function showToast(msg, type) {
  var colors = { success: '#1a7a4a', warn: '#9a6f00', error: '#c0392b' };
  var t = document.createElement('div');
  t.textContent = msg;
  t.style.cssText = 'position:fixed;bottom:24px;right:24px;'
    + 'background:' + (colors[type] || colors.success) + ';'
    + 'color:#fff;padding:.75rem 1.25rem;border-radius:10px;'
    + 'font-size:.88rem;font-family:DM Sans,sans-serif;font-weight:500;'
    + 'box-shadow:0 4px 20px rgba(0,0,0,.2);z-index:9999;max-width:300px;';
  document.body.appendChild(t);
  setTimeout(function() {
    t.style.transition = 'opacity .3s';
    t.style.opacity = '0';
    setTimeout(function() { t.remove(); }, 300);
  }, 3000);
}
