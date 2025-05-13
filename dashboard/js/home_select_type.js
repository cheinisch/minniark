document.addEventListener('DOMContentLoaded', function () {
  const defaultOption = document.getElementById('listbox-type-option-0'); // Page  
  const pageOption = document.getElementById('listbox-type-option-1'); // Page
  const albumOption = document.getElementById('listbox-type-option-2'); // Album

  const sectionPage = document.getElementById('second_select_typ-page');
  const sectionAlbum = document.getElementById('second_select_typ-album');

  // Initialzustand
  sectionPage.classList.add('hidden');
  sectionAlbum.classList.add('hidden');

  // Event-Handler für Page
  pageOption?.addEventListener('click', () => {
    sectionPage.classList.remove('hidden');
    sectionAlbum.classList.add('hidden');
  });

  // Event-Handler für Album
  albumOption?.addEventListener('click', () => {
    sectionAlbum.classList.remove('hidden');
    sectionPage.classList.add('hidden');
  });

  // Default
  defaultOption?.addEventListener('click', () => {
    sectionPage.classList.add('hidden');
    sectionAlbum.classList.add('hidden');
  });
});
