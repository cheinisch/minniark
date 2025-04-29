document.getElementById('backup-btn').addEventListener('click', async function () {
    const button = this;
    button.disabled = true;
    button.textContent = 'Erstelle Backup...';
  
    try {
      const response = await fetch('/api/generate_backup.php', {
        method: 'POST',
      });
  
      const data = await response.json();
  
      if (data.success) {
        // Modal öffnen + Link setzen
        const modal = document.getElementById('backup-modal');
        const link = document.getElementById('backup-download-link');
        link.href = data.download_url;
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