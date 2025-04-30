document.getElementById('upload-backup-form').addEventListener('submit', function (e) {
    e.preventDefault(); // verhindert normales Formularverhalten
  
    const fileInput = document.getElementById('backup-file');
    const file = fileInput.files[0];
  
    if (!file) {
      alert("Bitte eine ZIP-Datei auswählen.");
      return;
    }
  
    const formData = new FormData();
    formData.append('backup_file', file);
  
    fetch('../api/upload_backup.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        document.getElementById('notification-success-upload').classList.remove('hidden');
        document.getElementById('notification-error-upload').classList.add('hidden');
        fileInput.value = ''; // Zurücksetzen
      } else {
        throw new Error(data.message || 'Upload fehlgeschlagen');
      }
    })
    .catch(err => {
      console.error(err);
      document.getElementById('notification-error-upload').classList.remove('hidden');
      document.getElementById('notification-success-upload').classList.add('hidden');
    });
  });