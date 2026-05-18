/**
 * Travel Guide — General User JavaScript (Task 4)
 * Search/Filter (AJAX), Comments (AJAX), Cost Calculator, Wishlist
 */
(() => {
  // BASE_URL, CSRF, POST_ID, BASE_COST etc. injected inline from PHP

  // ── Helpers ─────────────────────────────────────────────────────────────────
  function showToast(msg, type = 'success') {
    const el = document.createElement('div');
    el.className = `alert alert-${type}`;
    el.innerHTML = `<span class="alert-icon">${type === 'success' ? '✓' : '⚠'}</span>${msg}`;
    el.style.cssText = 'position:fixed;top:80px;right:1rem;z-index:9999;max-width:340px;animation:fadeUp .3s ease;';
    document.body.appendChild(el);
    setTimeout(() => el.remove(), 3000);
  }

  function csrf() { return typeof CSRF !== 'undefined' ? CSRF : ''; }

  const genreEmoji = { beach:'🏖️', mountain:'⛰️', city:'🌆', historical:'🏛️', nature:'🌿', cultural:'🎭', adventure:'🧗', other:'🌍' };
  const costColor  = { low:'tag-low', medium:'tag-medium', high:'tag-high' };

  // ── Build Post Card HTML ──────────────────────────────────────────────────
  function buildCard(p) {
    const snippet = p.short_history ? p.short_history.substring(0, 120) + '…' : '';
    const base = typeof BASE_URL !== 'undefined' ? BASE_URL : '';
    return `
      <div class="post-card">
        <div class="post-card-img">${genreEmoji[p.genre] || '🌍'}</div>
        <div class="post-card-body">
          <div class="post-meta">
            <span class="tag tag-country">🌍 ${escHtml(p.country)}</span>
            <span class="tag tag-genre">${escHtml(p.genre)}</span>
            <span class="tag ${costColor[p.cost_level]||'tag-low'}">${cap(p.cost_level)}</span>
          </div>
          <h3 class="post-card-title">${escHtml(p.title)}</h3>
          <p class="post-snippet">${escHtml(snippet)}</p>
        </div>
        <div class="post-card-footer">
          <a href="${base}/user/post_detail.php?id=${p.id}" class="btn btn-ghost" style="flex:1;text-align:center;">Read More →</a>
        </div>
      </div>`;
  }

  function escHtml(s) {
    if (!s) return '';
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }
  function cap(s) { return s ? s.charAt(0).toUpperCase() + s.slice(1) : ''; }

  // ── Search & Filter (Browse Page) ─────────────────────────────────────────
  const grid       = document.getElementById('postsGrid');
  const noResults  = document.getElementById('noResults');
  const countEl    = document.getElementById('resultsCount');
  const searchInput  = document.getElementById('searchInput');
  const filterCountry= document.getElementById('filterCountry');
  const filterGenre  = document.getElementById('filterGenre');
  const filterCost   = document.getElementById('filterCost');
  const clearBtn     = document.getElementById('clearFilters');

  let searchTimer = null;

  function updateGrid(posts) {
    if (!grid) return;
    if (!posts || posts.length === 0) {
      grid.style.display = 'none';
      if (noResults) noResults.style.display = 'block';
      if (countEl) countEl.textContent = '0 destinations found';
    } else {
      grid.style.display = 'grid';
      if (noResults) noResults.style.display = 'none';
      grid.innerHTML = posts.map(buildCard).join('');
      if (countEl) countEl.textContent = `${posts.length} destination${posts.length!==1?'s':''} found`;
    }
  }

  async function doFilter() {
    if (!grid) return;
    const q       = searchInput ? searchInput.value.trim() : '';
    const country = filterCountry ? filterCountry.value : '';
    const genre   = filterGenre   ? filterGenre.value   : '';
    const cost    = filterCost    ? filterCost.value     : '';

    const base = typeof BASE_URL !== 'undefined' ? BASE_URL : '';
    const params = new URLSearchParams({ action:'filter' });
    if (q)       params.set('q', q);
    if (country) params.set('country', country);
    if (genre)   params.set('genre', genre);
    if (cost)    params.set('cost_level', cost);

    try {
      const res  = await fetch(`${base}/api/posts.php?${params}`);
      const data = await res.json();
      updateGrid(data);
    } catch(e) { console.error('Filter error', e); }
  }

  // Live search with debounce
  searchInput?.addEventListener('input', () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(doFilter, 350);
  });

  filterCountry?.addEventListener('change', doFilter);
  filterGenre?.addEventListener('change', doFilter);
  filterCost?.addEventListener('change', doFilter);

  clearBtn?.addEventListener('click', () => {
    if (searchInput) searchInput.value = '';
    if (filterCountry) filterCountry.value = '';
    if (filterGenre) filterGenre.value = '';
    if (filterCost) filterCost.value = '';
    doFilter();
  });

  // ── Cost Calculator (Post Detail) ─────────────────────────────────────────
  const calcBtn    = document.getElementById('calcBtn');
  const calcResult = document.getElementById('costResult');
  const resultVal  = document.getElementById('costResultValue');
  const resultNote = document.getElementById('costResultNote');

  calcBtn?.addEventListener('click', () => {
    const travelers = parseInt(document.getElementById('calcTravelers')?.value || '1', 10);
    const days      = parseInt(document.getElementById('calcDays')?.value || '7', 10);

    // JS Validation
    let valid = true;
    const errT = document.getElementById('err-travelers');
    const errD = document.getElementById('err-days');
    if (errT) errT.textContent = '';
    if (errD) errD.textContent = '';

    if (!Number.isInteger(travelers) || travelers < 1 || travelers > 20) {
      if (errT) errT.textContent = 'Enter a valid number between 1 and 20.';
      valid = false;
    }
    if (!Number.isInteger(days) || days < 1 || days > 365) {
      if (errD) errD.textContent = 'Enter a valid number between 1 and 365.';
      valid = false;
    }
    if (!valid) return;

    const baseCost = typeof BASE_COST !== 'undefined' ? BASE_COST : 500;
    const total = baseCost * travelers * (days / 7);
    const formatted = total.toLocaleString('en-US', { style:'currency', currency:'USD', maximumFractionDigits:0 });
    if (resultVal) resultVal.textContent = formatted;
    if (resultNote) resultNote.textContent = `${travelers} traveler${travelers>1?'s':''} × ${days} day${days>1?'s':''} (based on $${baseCost.toLocaleString()}/person/week)`;
    if (calcResult) calcResult.style.display = 'block';
  });

  // Allow Enter key on calculator inputs
  ['calcTravelers','calcDays'].forEach(id => {
    document.getElementById(id)?.addEventListener('keydown', e => {
      if (e.key === 'Enter') calcBtn?.click();
    });
  });

  // ── Comment Submission ────────────────────────────────────────────────────
  const commentContent = document.getElementById('commentContent');
  const submitComment  = document.getElementById('submitComment');
  const commentAlert   = document.getElementById('commentAlert');
  const commentsList   = document.getElementById('commentsList');
  const commentCountEl = document.getElementById('commentCount');
  const noComments     = document.getElementById('noComments');
  const charCountEl    = document.getElementById('charCount');

  commentContent?.addEventListener('input', () => {
    if (charCountEl) charCountEl.textContent = commentContent.value.length;
    if (commentContent.value.length > 1000) {
      commentContent.classList.add('is-invalid');
    } else {
      commentContent.classList.remove('is-invalid');
    }
  });

  function showCommentAlert(msg, type) {
    if (!commentAlert) return;
    commentAlert.className = `alert alert-${type}`;
    commentAlert.innerHTML = `<span class="alert-icon">${type==='success'?'✓':'⚠'}</span>${msg}`;
    commentAlert.style.display = 'flex';
    setTimeout(() => { commentAlert.style.display = 'none'; }, 4000);
  }

  submitComment?.addEventListener('click', async () => {
    const content = commentContent?.value.trim() || '';
    const errEl   = document.getElementById('err-comment');

    // JS Validation
    if (errEl) errEl.textContent = '';
    if (!content) {
      if (errEl) errEl.textContent = 'Comment cannot be empty.';
      return;
    }
    if (content.length > 1000) {
      if (errEl) errEl.textContent = 'Comment is too long (max 1000 characters).';
      return;
    }

    submitComment.disabled = true;
    submitComment.innerHTML = '<span class="spinner"></span> Posting…';

    const base = typeof BASE_URL !== 'undefined' ? BASE_URL : '';
    const postId = typeof POST_ID !== 'undefined' ? POST_ID : 0;
    const form = new FormData();
    form.append('action', 'add');
    form.append('post_id', postId);
    form.append('content', content);
    form.append('csrf_token', csrf());

    try {
      const res  = await fetch(`${base}/api/comments.php`, { method:'POST', body:form });
      const data = await res.json();
      if (data.success) {
        // Remove no-comments placeholder
        if (noComments) noComments.remove();
        // Append new comment
        const html = `
          <div class="comment-item" id="comment-${data.id}">
            <div class="comment-avatar">${data.user_name.charAt(0).toUpperCase()}</div>
            <div class="comment-body">
              <div class="comment-meta">
                <strong>${escHtml(data.user_name)}</strong>
                <span>Just now</span>
              </div>
              <p class="comment-text">${escHtml(data.content)}</p>
              <button class="btn-delete-comment" data-id="${data.id}">Delete</button>
            </div>
          </div>`;
        if (commentsList) commentsList.insertAdjacentHTML('beforeend', html);
        // Update count
        if (commentCountEl) commentCountEl.textContent = parseInt(commentCountEl.textContent||'0', 10) + 1;
        // Clear textarea
        if (commentContent) { commentContent.value = ''; if (charCountEl) charCountEl.textContent = '0'; }
        showCommentAlert('Comment posted!', 'success');
        // Bind delete on new comment
        const newBtn = document.querySelector(`#comment-${data.id} .btn-delete-comment`);
        if (newBtn) bindDeleteComment(newBtn);
      } else {
        showCommentAlert(data.message || 'Could not post comment.', 'error');
      }
    } catch(e) {
      showCommentAlert('Network error. Please try again.', 'error');
    }

    submitComment.disabled = false;
    submitComment.textContent = 'Post Comment';
  });

  // ── Delete Comment ────────────────────────────────────────────────────────
  function bindDeleteComment(btn) {
    btn.addEventListener('click', async () => {
      if (!confirm('Delete this comment?')) return;
      const id = btn.dataset.id;
      const base = typeof BASE_URL !== 'undefined' ? BASE_URL : '';
      const form = new FormData();
      form.append('action', 'delete');
      form.append('comment_id', id);
      form.append('csrf_token', csrf());
      try {
        const res  = await fetch(`${base}/api/comments.php`, { method:'POST', body:form });
        const data = await res.json();
        if (data.success) {
          const item = document.getElementById(`comment-${id}`);
          if (item) { item.style.opacity='0'; setTimeout(()=>item.remove(), 300); }
          if (commentCountEl) {
            const c = Math.max(0, parseInt(commentCountEl.textContent||'1',10)-1);
            commentCountEl.textContent = c;
          }
          showToast('Comment deleted.');
        } else showToast(data.message || 'Could not delete.', 'error');
      } catch(e) { showToast('Network error.', 'error'); }
    });
  }
  document.querySelectorAll('.btn-delete-comment').forEach(bindDeleteComment);

  // ── Wishlist (Post Detail Page) ───────────────────────────────────────────
  const wishlistBtn = document.getElementById('wishlistBtn');
  if (wishlistBtn) {
    let inWishlist = typeof IN_WISHLIST !== 'undefined' ? IN_WISHLIST : false;
    wishlistBtn.addEventListener('click', async () => {
      const base   = typeof BASE_URL !== 'undefined' ? BASE_URL : '';
      const postId = typeof POST_ID  !== 'undefined' ? POST_ID  : 0;
      const action = inWishlist ? 'remove' : 'add';
      const form   = new FormData();
      form.append('action', action);
      form.append('post_id', postId);
      form.append('csrf_token', csrf());
      try {
        const res  = await fetch(`${base}/api/wishlist.php`, { method:'POST', body:form });
        const data = await res.json();
        if (data.success) {
          inWishlist = !inWishlist;
          wishlistBtn.className = `btn btn-ghost btn-sm${inWishlist?' btn-wishlisted':''}`;
          wishlistBtn.textContent = inWishlist ? '♥ In Wishlist' : '♡ Add to Wishlist';
          showToast(inWishlist ? 'Added to wishlist!' : 'Removed from wishlist.');
        } else showToast(data.message || 'Error', 'error');
      } catch(e) { showToast('Network error.', 'error'); }
    });
  }

  // ── Wishlist Buttons on Browse Grid ──────────────────────────────────────
  function bindWishlistBtns() {
    document.querySelectorAll('.btn-wishlist').forEach(btn => {
      if (btn._bound) return;
      btn._bound = true;
      btn.addEventListener('click', async () => {
        const base   = typeof BASE_URL !== 'undefined' ? BASE_URL : '';
        const postId = btn.dataset.id;
        const form   = new FormData();
        form.append('action', 'add');
        form.append('post_id', postId);
        form.append('csrf_token', csrf());
        try {
          const res  = await fetch(`${base}/api/wishlist.php`, { method:'POST', body:form });
          const data = await res.json();
          if (data.success) {
            btn.textContent = '♥';
            btn.classList.add('active');
            showToast('Added to wishlist!');
          } else showToast(data.message || 'Already in wishlist', 'error');
        } catch(e) { showToast('Network error.', 'error'); }
      });
    });
  }
  bindWishlistBtns();

})();
