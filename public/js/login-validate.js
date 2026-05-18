// public/js/login-validate.js
document.getElementById('loginForm').addEventListener('submit', function(e) {
  let valid = true;
  const set = (id, msg) => { document.getElementById(id).textContent = msg; if(msg) valid = false; };

  const email = document.getElementById('loginEmail').value.trim();
  const pass  = document.getElementById('loginPass').value;

  set('loginEmailErr', !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email) ? 'Enter a valid email address.' : '');
  set('loginPassErr',  !pass ? 'Password is required.' : '');

  if (!valid) {
    e.preventDefault();
    document.querySelectorAll('.field-error:not(:empty)').forEach(el => {
      el.previousElementSibling?.classList.add('is-invalid');
    });
  }
});
