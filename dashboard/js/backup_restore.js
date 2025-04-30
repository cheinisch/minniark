document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.restore-backup-btn').forEach(button => {
      button.addEventListener('click', function (e) {
        e.preventDefault();
  
        if (!confirm('Dieses Backup wirklich wiederherstellen?')) return;
  
        const filename = this.dataset.filename;
  
        fetch('../api/backup_restore.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: 'filename=' + encodeURIComponent(filename)
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            alert('Backup erfolgreich wiederhergestellt.');
          } else {
            alert('Fehler: ' + (data.message || 'Wiederherstellung fehlgeschlagen.'));
          }
        })
        .catch(err => {
          console.error(err);
          alert('Netzwerkfehler bei der Wiederherstellung.');
        });
      });
    });
  });