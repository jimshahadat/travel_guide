// public/js/register-validate.js
document.getElementById('registerForm').addEventListener('submit', function(e) {
  let valid = true;
  const set = (id, msg) => { document.getElementById(id).textContent = msg; if(msg) valid = false; };

  const name  = document.getElementById('regName').value.trim();
  const email = document.getElementById('regEmail').value.trim();
  const role  = document.getElementById('regRole').value;
  const pass  = document.getElementById('regPass').value;
  const pass2 = document.getElementById('regPass2').value;

  set('nameErr',  name.length < 2       ? 'Name must be at least 2 characters.' : '');
  set('emailErr', !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email) ? 'Enter a valid email address.' : '');
  set('roleErr',  !role                  ? 'Please select a role.' : '');
  set('passErr',  pass.length < 8       ? 'Password must be at least 8 characters.' : '');
  set('pass2Err', pass !== pass2         ? 'Passwords do not match.' : '');

  if (!valid) {
    e.preventDefault();
    // Shake effect on errors
    document.querySelectorAll('.field-error:not(:empty)').forEach(el => {
      el.previousElementSibling?.classList.add('is-invalid');
    });
  }
});

// Live clear errors
['regName','regEmail','regRole','regPass','regPass2'].forEach(id => {
  document.getElementById(id)?.addEventListener('input', function() {
    this.classList.remove('is-invalid');
  });
});
