document.querySelector('#change-password-form').addEventListener('submit', async function (e) {
  e.preventDefault();

  const formData = new FormData(e.target);

  try {
    const res = await fetch('backend_api/change_password.php', {
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
