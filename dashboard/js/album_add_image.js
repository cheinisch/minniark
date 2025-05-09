// Öffnen
document.getElementById('addImagetoAlbumBtn').addEventListener('click', () => {
    document.getElementById('addToAlbumImageModal').classList.remove('hidden');
  });
  
  // Schließen
  document.getElementById('closeAddToAlbumImageModal').addEventListener('click', () => {
    document.getElementById('addToAlbumImageModal').classList.add('hidden');
  });
  document.getElementById('cancelAddToAlbumImage').addEventListener('click', () => {
    document.getElementById('addToAlbumImageModal').classList.add('hidden');
  });
  
  // Live-Suche
  document.getElementById('imageSearchInput').addEventListener('input', function () {
    const search = this.value.toLowerCase();
    document.querySelectorAll('#imageList label').forEach(label => {
      const text = label.innerText.toLowerCase();
      label.style.display = text.includes(search) ? '' : 'none';
    });
  });
  