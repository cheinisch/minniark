document.querySelector('#change-password-form').addEventListener('submit', async function (e) {
  e.preventDefault();

  const formData = new FormData(e.target);

  try {
    const res = await fetch('../api/change_password.php', {
      method: 'POST',
      body: formData
    });

    const result = await res.json();

    if (res.ok && result.status === 'success') {
      showNotification('success', result.message);
    } else {
      showNotification('error', result.message || 'Es ist ein Fehler aufgetreten.');
    }
  } catch (err) {
    showNotification('error', 'Verbindung zum Server fehlgeschlagen.');
    console.error(err);
  }
});

function showNotification(type, message) {
  const successBox = document.getElementById('notification-success');
  const errorBox = document.getElementById('notification-error');

  successBox.classList.add('hidden');
  errorBox.classList.add('hidden');

  if (type === 'success') {
    successBox.querySelector('span').textContent = message;
    successBox.classList.remove('hidden');
  } else if (type === 'error') {
    errorBox.querySelector('span').textContent = message;
    errorBox.classList.remove('hidden');
  }
}

document.querySelector('#change-data-form').addEventListener('submit', async function (e) {
    e.preventDefault();
  
    const formData = new FormData(this);
  
    try {
      const res = await fetch('../api/change_userdata.php', {
        method: 'POST',
        body: formData
      });
  
      const result = await res.json();
  
      if (res.ok && result.status === 'success') {
        showUserNotification('success', result.message);
      } else {
        showUserNotification('error', result.message || 'Ein Fehler ist aufgetreten.');
      }
    } catch (err) {
      showUserNotification('error', 'Verbindung zum Server fehlgeschlagen.');
      console.error(err);
    }
  });
  
  function showUserNotification(type, message) {
    const successBox = document.getElementById('notification-success-user');
    const errorBox = document.getElementById('notification-error-user');
  
    successBox.classList.add('hidden');
    errorBox.classList.add('hidden');
  
    if (type === 'success') {
      successBox.querySelector('span').textContent = message;
      successBox.classList.remove('hidden');
    } else if (type === 'error') {
      errorBox.querySelector('span').textContent = message;
      errorBox.classList.remove('hidden');
    }
  }


  document.getElementById('change-login-type-form').addEventListener('submit', async function (e) {
    e.preventDefault();
  
    const loginType = document.getElementById('login_type').value;
  
    const payload = new FormData();
    payload.append('auth_type', loginType);
  
    try {
      const response = await fetch('../api/change_userdata.php', {
        method: 'POST',
        body: payload
      });
  
      const result = await response.json();
  
      if (result.status === 'success') {
        document.getElementById('notification-logintype-success').classList.remove('hidden');
        document.getElementById('notification-logintype-error').classList.add('hidden');
      } else {
        throw new Error(result.message);
      }
    } catch (error) {
      console.error('Fehler beim Speichern:', error);
      document.getElementById('notification-logintype-error').classList.remove('hidden');
      document.getElementById('notification-logintype-success').classList.add('hidden');
    }
  });