// public/js/profile-validate.js
document.getElementById('profileForm').addEventListener('submit', function(e) {
  let valid = true;
  const set = (id, msg) => { document.getElementById(id).textContent = msg; if(msg) valid = false; };

  const name  = document.getElementById('profName').value.trim();
  const email = document.getElementById('profEmail').value.trim();
  const newP  = document.getElementById('newPass').value;
  const newP2 = document.getElementById('newPass2').value;

  set('profNameErr',  name.length < 2 ? 'Name must be at least 2 characters.' : '');
  set('profEmailErr', !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email) ? 'Enter a valid email address.' : '');

  if (newP || newP2) {
    set('newPassErr',  newP.length < 8  ? 'New password must be at least 8 characters.' : '');
    set('newPass2Err', newP !== newP2   ? 'New passwords do not match.' : '');
  } else {
    set('newPassErr', '');
    set('newPass2Err', '');
  }

  if (!valid) e.preventDefault();
});
