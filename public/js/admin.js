/**
 * Travel Guide — Admin Panel JavaScript
 * Task 3: AJAX for user verification toggle, post approve/reject, delete operations
 */
(() => {

  // ── Helpers ────────────────────────────────────────────────────────────────
  function showToast(msg, type = 'success') {
    const el = document.createElement('div');
    el.className = `alert alert-${type}`;
    el.innerHTML = `<span class="alert-icon">${type === 'success' ? '✓' : '⚠'}</span>${msg}`;
    el.style.cssText = 'position:fixed;top:80px;right:1rem;z-index:9999;max-width:360px;animation:fadeUp .3s ease;';
    document.body.appendChild(el);
    setTimeout(() => el.remove(), 3500);
  }

  function confirmAction(msg) { return confirm(msg); }

  // ── CSRF token ─────────────────────────────────────────────────────────────
  function csrf() {
    return document.getElementById('global-csrf')?.value
      || document.querySelector('input[name=csrf_token]')?.value
      || '';
  }

  // ── Toggle Verify ──────────────────────────────────────────────────────────
  document.querySelectorAll('.toggle-verify').forEach(btn => {
    btn.addEventListener('click', async () => {
      const id = btn.dataset.id;
      btn.disabled = true;
      const form = new FormData();
      form.append('action', 'toggle_verify');
      form.append('user_id', id);
      form.append('csrf_token', csrf());
      try {
        const res  = await fetch(window.location.href, { method: 'POST', body: form });
        const json = await res.json();
        if (json.success) {
          const v = json.is_verified;
          btn.className = `toggle-verify btn btn-sm ${v ? 'btn-verified' : 'btn-unverified'}`;
          btn.textContent = v ? '✓ Verified' : '✗ Pending';
          btn.dataset.verified = v;
          showToast(`User ${v ? 'verified' : 'unverified'} successfully.`);
        } else {
          showToast(json.message || 'Error', 'error');
        }
      } catch(e) { showToast('Request failed.', 'error'); }
      btn.disabled = false;
    });
  });

  // ── Change Role ────────────────────────────────────────────────────────────
  document.querySelectorAll('.role-select').forEach(sel => {
    sel.addEventListener('change', async () => {
      const id   = sel.dataset.id;
      const role = sel.value;
      if (!confirmAction(`Change this user's role to "${role}"?`)) {
        sel.value = sel.dataset.original || sel.value;
        return;
      }
      const form = new FormData();
      form.append('action', 'change_role');
      form.append('user_id', id);
      form.append('role', role);
      form.append('csrf_token', csrf());
      try {
        const res  = await fetch(window.location.href, { method: 'POST', body: form });
        const json = await res.json();
        if (json.success) showToast(`Role changed to ${role}.`);
        else showToast(json.message || 'Error', 'error');
      } catch(e) { showToast('Request failed.', 'error'); }
    });
  });

  // ── Delete User ────────────────────────────────────────────────────────────
  document.querySelectorAll('.delete-user').forEach(btn => {
    btn.addEventListener('click', async () => {
      const id   = btn.dataset.id;
      const name = btn.dataset.name;
      if (!confirmAction(`Delete user "${name}"? This will also delete all their posts, comments, and wishlist items.`)) return;
      const form = new FormData();
      form.append('action', 'delete_user');
      form.append('user_id', id);
      form.append('csrf_token', csrf());
      try {
        const res  = await fetch(window.location.href, { method: 'POST', body: form });
        const json = await res.json();
        if (json.success) {
          const row = document.querySelector(`tr[data-id="${id}"]`);
          if (row) { row.style.opacity = '0'; setTimeout(() => row.remove(), 300); }
          showToast('User deleted successfully.');
        } else showToast(json.message || 'Error', 'error');
      } catch(e) { showToast('Request failed.', 'error'); }
    });
  });

  // ── Approve Request ────────────────────────────────────────────────────────
  document.querySelectorAll('.btn-approve').forEach(btn => {
    btn.addEventListener('click', async () => {
      const id = btn.dataset.id;
      if (!confirmAction('Approve this post request? It will be published immediately.')) return;
      btn.disabled = true; btn.textContent = '⏳';
      const form = new FormData();
      form.append('action', 'approve_request');
      form.append('request_id', id);
      form.append('csrf_token', csrf());
      try {
        const res  = await fetch(window.location.href, { method: 'POST', body: form });
        const json = await res.json();
        if (json.success) {
          const card = document.getElementById(`req-${id}`);
          if (card) { card.style.opacity = '0'; card.style.transform = 'scale(.95)'; card.style.transition = 'all .3s'; setTimeout(() => card.remove(), 300); }
          showToast('Post request approved and published! 🎉');
        } else { showToast(json.message || 'Error', 'error'); btn.disabled = false; btn.textContent = '✓ Approve'; }
      } catch(e) { showToast('Request failed.', 'error'); btn.disabled = false; btn.textContent = '✓ Approve'; }
    });
  });

  // ── Reject Request ─────────────────────────────────────────────────────────
  document.querySelectorAll('.btn-reject').forEach(btn => {
    btn.addEventListener('click', async () => {
      const id = btn.dataset.id;
      if (!confirmAction('Reject this post request?')) return;
      btn.disabled = true; btn.textContent = '⏳';
      const form = new FormData();
      form.append('action', 'reject_request');
      form.append('request_id', id);
      form.append('csrf_token', csrf());
      try {
        const res  = await fetch(window.location.href, { method: 'POST', body: form });
        const json = await res.json();
        if (json.success) {
          const card = document.getElementById(`req-${id}`);
          if (card) { card.style.opacity = '0'; setTimeout(() => card.remove(), 300); }
          showToast('Request rejected.');
        } else { showToast(json.message || 'Error', 'error'); btn.disabled = false; btn.textContent = '✗ Reject'; }
      } catch(e) { showToast('Request failed.', 'error'); btn.disabled = false; btn.textContent = '✗ Reject'; }
    });
  });

  // ── Delete Post ────────────────────────────────────────────────────────────
  document.querySelectorAll('.delete-post').forEach(btn => {
    btn.addEventListener('click', async () => {
      const id    = btn.dataset.id;
      const title = btn.dataset.title;
      if (!confirmAction(`Delete post "${title}"? This will also delete all comments and wishlist entries.`)) return;
      const form = new FormData();
      form.append('action', 'delete_post');
      form.append('post_id', id);
      form.append('csrf_token', csrf());
      try {
        const res  = await fetch(window.location.href, { method: 'POST', body: form });
        const json = await res.json();
        if (json.success) {
          const row = document.getElementById(`post-row-${id}`);
          if (row) { row.style.opacity = '0'; setTimeout(() => row.remove(), 300); }
          showToast('Post deleted.');
        } else showToast(json.message || 'Error', 'error');
      } catch(e) { showToast('Request failed.', 'error'); }
    });
  });

  // ── Delete Comment ─────────────────────────────────────────────────────────
  document.querySelectorAll('.delete-comment').forEach(btn => {
    btn.addEventListener('click', async () => {
      const id = btn.dataset.id;
      if (!confirmAction('Delete this comment?')) return;
      const form = new FormData();
      form.append('action', 'delete_comment');
      form.append('comment_id', id);
      form.append('csrf_token', csrf());
      const url = window.location.href.includes('comments.php')
        ? window.location.href
        : window.location.pathname.replace(/\/[^/]+$/, '/comments.php');
      try {
        const res  = await fetch(url, { method: 'POST', body: form });
        const json = await res.json();
        if (json.success) {
          const row = document.getElementById(`comment-row-${id}`);
          if (row) { row.style.opacity = '0'; setTimeout(() => row.remove(), 300); }
          showToast('Comment deleted.');
        } else showToast(json.message || 'Error', 'error');
      } catch(e) { showToast('Request failed.', 'error'); }
    });
  });

  // ── User Table Search ──────────────────────────────────────────────────────
  document.getElementById('userSearch')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.user-row').forEach(row => {
      row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
  });

  // ── Comment Table Search ───────────────────────────────────────────────────
  document.getElementById('commentSearch')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#commentsTable tbody tr').forEach(row => {
      row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
  });

  // ── Add User Form JS Validation ────────────────────────────────────────────
  document.getElementById('addUserForm')?.addEventListener('submit', function(e) {
    let valid = true;
    ['err-name','err-email','err-pass'].forEach(id => {
      const el = document.getElementById(id);
      if (el) el.textContent = '';
    });
    const name  = this.querySelector('[name=name]').value.trim();
    const email = this.querySelector('[name=email]').value.trim();
    const pass  = this.querySelector('[name=password]').value;
    if (name.length < 2) {
      const el = document.getElementById('err-name');
      if (el) el.textContent = 'Name must be at least 2 characters.';
      valid = false;
    }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      const el = document.getElementById('err-email');
      if (el) el.textContent = 'Invalid email address.';
      valid = false;
    }
    if (pass.length < 8) {
      const el = document.getElementById('err-pass');
      if (el) el.textContent = 'Password must be at least 8 characters.';
      valid = false;
    }
    if (!valid) e.preventDefault();
  });

})();
