document.getElementById('backup-btn').addEventListener('click', async function () {
  const button = this;
  button.disabled = true;
  button.textContent = 'Create Backup...';

  try {
    // Basis aus aktuellem Pfad berechnen: z. B. /image_portfolio
    const basePath = window.location.pathname.split('/').slice(0, 2).join('/');
    const response = await fetch(`${basePath}/api/generate_backup.php`, {
      method: 'POST',
    });

    const data = await response.json();

    if (data.success) {
      const modal = document.getElementById('backup-modal');
      const link = document.getElementById('backup-download-link');
      link.href = `${basePath}/backup/${encodeURIComponent(data.filename)}`;
      link.textContent = data.filename;
      modal.classList.remove('hidden');
    } else {
      alert('Fehler beim Erstellen: ' + data.message);
    }
  } catch (err) {
    alert('Fehler beim Backup-Vorgang.');
    console.error(err);
  }

  button.disabled = false;
  button.textContent = 'Generate Backup';
});
