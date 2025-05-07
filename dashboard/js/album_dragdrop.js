// Macht alle Album-Listenelemente zu Drop-Zonen
function setupAlbumDropzones() {
    document.querySelectorAll('li[id]').forEach(albumEl => {
      albumEl.addEventListener('dragover', e => {
        e.preventDefault();
        albumEl.classList.add('bg-gray-200');
      });
  
      albumEl.addEventListener('dragleave', () => {
        albumEl.classList.remove('bg-gray-200');
      });
  
      albumEl.addEventListener('drop', e => {
        e.preventDefault();
        albumEl.classList.remove('bg-gray-200');
  
        const imageName = e.dataTransfer.getData('text/plain');
        const albumName = albumEl.id;
  
        fetch('./backend_api/album_assign_image.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: new URLSearchParams({
            image: imageName,
            album: albumName
          })
        })
          .then(res => res.json())
          .then(response => {
            if (response.success) {
              alert('Bild erfolgreich zum Album hinzugefügt!');
            } else {
              alert('Fehler: ' + response.error);
              console.warn(response.debug);
            }
          })
          .catch(err => {
            console.error(err);
            alert('Ein Fehler ist aufgetreten.');
          });
      });
    });
  }
  
  // Macht Bilder draggable, auch wenn sie in <a> liegen
  function setupDraggableImages() {
    document.querySelectorAll('.dynamic-image-width img').forEach(img => {
      img.setAttribute('draggable', 'true');
      img.addEventListener('dragstart', e => {
        e.stopPropagation(); // 👈 ganz wichtig, verhindert Bubble auf <a>
        const filename = img.dataset.filename;
        if (!filename) {
          console.error("Fehlendes data-filename!", img);
          return;
        }
        console.log("Drag start – filename:", filename);
        e.dataTransfer.setData('text/plain', filename);
      });
    });
  }
  
  // Setup nach DOM-Load
  document.addEventListener('DOMContentLoaded', () => {
    setupAlbumDropzones();
    setupDraggableImages();
  });
  