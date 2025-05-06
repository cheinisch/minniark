

// Delete-Button Klick
document.getElementById('delete-button').addEventListener('click', function() {
  document.getElementById('deleteModal').classList.remove('hidden');
});

// Cancel-Button Klick
document.getElementById('cancelDelete').addEventListener('click', function() {
  document.getElementById('deleteModal').classList.add('hidden');
});

// Confirm-Button Klick (Löschen)
document.getElementById('confirmDelete').addEventListener('click', function() {
  const filename = "<?php echo htmlspecialchars($fileName); ?>";

  fetch(`/backend_api/delete.php?type=img&filename=${encodeURIComponent(filename)}`)
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Erfolgreich gelöscht → Weiterleitung
        window.location.href = 'media.php';
      } else {
        alert('Failed to delete the image.');
        document.getElementById('deleteModal').classList.add('hidden');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('An error occurred.');
      document.getElementById('deleteModal').classList.add('hidden');
    });
});