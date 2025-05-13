document.addEventListener('DOMContentLoaded', function () {
  const modal = document.getElementById('cover-modal');
  const openBtn = document.getElementById('open-cover-modal');
  const closeBtn = document.getElementById('close-cover-modal');
  const confirmBtn = document.getElementById('confirm-cover-selection');

  const coverInput = document.getElementById('cover-input');
  const styleInput = document.getElementById('cover-style');
  const albumSelect = document.getElementById('album-select');
  const galleryItems = document.querySelectorAll('#image-gallery div');
  const previewContainer = document.getElementById('cover-preview');

  let selectedImage = null;

  // Modal öffnen
  openBtn.addEventListener('click', () => {
    modal.classList.remove('hidden');
  });

  // Modal schließen
  closeBtn.addEventListener('click', () => {
    modal.classList.add('hidden');
  });

  // Bildauswahl
  galleryItems.forEach(div => {
    div.addEventListener('click', () => {
      galleryItems.forEach(d => d.classList.remove('ring-2', 'ring-sky-500'));
      div.classList.add('ring-2', 'ring-sky-500');

      selectedImage = div.getAttribute('data-filename');

      // Album zurücksetzen
      albumSelect.value = '';
    });
  });

  // Albumauswahl → Bildauswahl zurücksetzen
  albumSelect.addEventListener('change', () => {
    if (albumSelect.value !== '') {
      selectedImage = null;
      galleryItems.forEach(d => d.classList.remove('ring-2', 'ring-sky-500'));
    }
  });

  // Auswahl bestätigen & speichern
  confirmBtn.addEventListener('click', () => {
    console.log("Confirm pressed");
    const selectedAlbum = albumSelect.value;

    if (selectedAlbum) {
      coverInput.value = selectedAlbum;
      styleInput.value = 'album';
    } else if (selectedImage) {
      coverInput.value = selectedImage;
      styleInput.value = 'image';
    } else {
      coverInput.value = '';
      styleInput.value = '';
    }

    updateCoverPreview();
    modal.classList.add('hidden');

    // Dynamisch POST-Formular erstellen und absenden
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'backend_api/save_home_cover.php';

    const inputCover = document.createElement('input');
    inputCover.type = 'hidden';
    inputCover.name = 'cover';
    inputCover.value = coverInput.value;
    form.appendChild(inputCover);

    const inputStyle = document.createElement('input');
    inputStyle.type = 'hidden';
    inputStyle.name = 'default_image_style';
    inputStyle.value = styleInput.value;
    form.appendChild(inputStyle);

    document.body.appendChild(form);
    form.submit();
  });

  // Vorschau aktualisieren
  function updateCoverPreview() {
    if (!previewContainer) return;

    const filename = coverInput.value;
    const style = styleInput.value;

    if (style === 'cover' && filename) {
      previewContainer.innerHTML = `
        <img src="/userdata/content/images/${encodeURIComponent(filename)}" alt="Cover Preview"
             class="mt-4 w-40 rounded shadow border border-gray-300">
        <p class="text-xs text-gray-500 mt-1">${filename}</p>
      `;
    } else if (style === 'album' && filename) {
      previewContainer.innerHTML = `<p class="mt-4 text-sm text-sky-600 font-semibold">Album: ${filename}</p>`;
    } else {
      previewContainer.innerHTML = '';
    }
  }

  // Vorschau beim Laden anzeigen
  updateCoverPreview();
});
