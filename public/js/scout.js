// public/js/scout.js
const BASE = '/travel_guide';

// ── FORM VALIDATION (shared for create & change request) ─────────────────────
function validateScoutForm() {
  let valid = true;
  const set = (id, msg) => {
    const el = document.getElementById(id);
    if (el) { el.textContent = msg; if (msg) valid = false; }
  };

  const title   = document.getElementById('sc_title')?.value.trim()   || '';
  const country = document.getElementById('sc_country')?.value.trim() || '';
  const genre   = document.getElementById('sc_genre')?.value          || '';
  const cost    = document.getElementById('sc_cost')?.value           || '';
  const travel  = document.getElementById('sc_travel')?.value.trim()  || '';
  const history = document.getElementById('sc_history')?.value.trim() || '';

  set('sc_titleErr',   title.length < 2   ? 'Title must be at least 2 characters.' : '');
  set('sc_countryErr', country.length < 2 ? 'Country is required.' : '');
  set('sc_genreErr',   !genre             ? 'Please select a genre.' : '');
  set('sc_costErr',    !cost              ? 'Please select a cost level.' : '');
  set('sc_travelErr',  travel.length < 3  ? 'Travel medium info is required.' : '');
  set('sc_historyErr', history.length < 20 ? 'Description must be at least 20 characters.' : '');

  return valid;
}

// ── SCOUT CREATE FORM SUBMIT VALIDATION ──────────────────────────────────────
const createForm = document.getElementById('scoutCreateForm');
if (createForm && !document.getElementById('scoutEditForm')) {
  createForm.addEventListener('submit', function(e) {
    if (!validateScoutForm()) e.preventDefault();
  });
}

// ── CHARACTER COUNTER ─────────────────────────────────────────────────────────
const historyTA = document.getElementById('sc_history');
const historyCount = document.getElementById('historyCount');
if (historyTA && historyCount) {
  const update = () => {
    const len = historyTA.value.length;
    historyCount.textContent = len;
    historyCount.style.color = len < 20 ? 'var(--error)' : 'var(--success)';
  };
  historyTA.addEventListener('input', update);
  update();
}

// ── IMAGE PREVIEW ─────────────────────────────────────────────────────────────
const imageUpload  = document.getElementById('imageUpload');
const imagePreview = document.getElementById('imagePreview');
if (imageUpload && imagePreview) {
  imageUpload.addEventListener('change', function() {
    imagePreview.innerHTML = '';
    const files = Array.from(this.files);

    if (files.length > 5) {
      alert('You can upload a maximum of 5 images.');
      this.value = '';
      imagePreview.style.display = 'none';
      return;
    }

    if (files.length > 0) imagePreview.style.display = 'flex';
    else { imagePreview.style.display = 'none'; return; }

    files.forEach(file => {
      if (!file.type.startsWith('image/')) return;
      const reader = new FileReader();
      reader.onload = e => {
        const div = document.createElement('div');
        div.className = 'preview-thumb';
        div.innerHTML = `<img src="${e.target.result}" alt="">`;
        imagePreview.appendChild(div);
      };
      reader.readAsDataURL(file);
    });
  });
}

// ── AJAX DELETE (My Requests page) ───────────────────────────────────────────
let deleteTargetId   = null;
let deleteTargetCsrf = null;

document.querySelectorAll('.delete-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    deleteTargetId   = this.dataset.id;
    deleteTargetCsrf = this.dataset.csrf;
    const modal = document.getElementById('deleteModal');
    if (modal) modal.style.display = 'flex';
  });
});

document.getElementById('cancelDelete')?.addEventListener('click', () => {
  document.getElementById('deleteModal').style.display = 'none';
  deleteTargetId = null;
});

document.getElementById('confirmDelete')?.addEventListener('click', async function() {
  if (!deleteTargetId) return;
  this.disabled = true;
  this.textContent = 'Deleting…';

  try {
    const res  = await fetch(BASE + '/api/scout_delete.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: `id=${deleteTargetId}&csrf_token=${encodeURIComponent(deleteTargetCsrf)}`
    });
    const data = await res.json();

    if (data.success) {
      const row = document.getElementById('row-' + deleteTargetId);
      if (row) {
        row.style.transition = 'all 0.3s ease';
        row.style.opacity = '0';
        row.style.transform = 'translateX(20px)';
        setTimeout(() => {
          row.remove();
          // Show empty state if no rows left
          const tbody = document.getElementById('requestsTableBody');
          if (tbody && tbody.children.length === 0) {
            location.reload();
          }
        }, 300);
      }
      document.getElementById('deleteModal').style.display = 'none';
    } else {
      alert(data.message || 'Delete failed.');
      this.disabled = false;
      this.textContent = 'Yes, Delete';
    }
  } catch {
    alert('Network error. Try again.');
    this.disabled = false;
    this.textContent = 'Yes, Delete';
  }
});

// Close modal on overlay click
document.getElementById('deleteModal')?.addEventListener('click', function(e) {
  if (e.target === this) {
    this.style.display = 'none';
    deleteTargetId = null;
  }
});
