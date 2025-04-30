document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.delete-backup-btn').forEach(button => {
      button.addEventListener('click', function (e) {
        e.preventDefault();
  
        if (!confirm('Datei wirklich löschen?')) return;
  
        const filename = this.dataset.filename;
        const row = this.closest('tr');
  
        fetch('../api/delete_backup.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: 'filename=' + encodeURIComponent(filename)
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            row.remove(); // Zeile aus Tabelle entfernen
          } else {
            alert('Fehler beim Löschen: ' + (data.message || 'Unbekannter Fehler'));
          }
        })
        .catch(err => {
          console.error(err);
          alert('Fehler beim Löschen.');
        });
      });
    });
  });