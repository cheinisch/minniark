document.addEventListener('DOMContentLoaded', function () {

    


  const defaultOption = document.getElementById('listbox-type-option-0'); // Welcome Page
  const pageOption = document.getElementById('listbox-type-option-1');     // Page
  const albumOption = document.getElementById('listbox-type-option-2');    // Album

  const sectionPage = document.getElementById('second_select_typ-page');
  const sectionAlbum = document.getElementById('second_select_typ-album');

  const welcomeTypeInput = document.getElementById('welcome_type');
  const welcomeContentInput = document.getElementById('welcome_content');

  const albumListItems = document.querySelectorAll('#second_select_typ-album li');
  const pageListItems = document.querySelectorAll('#second_select_typ-page li');

  // Initialzustand
  sectionPage.classList.add('hidden');
  sectionAlbum.classList.add('hidden');


    // Zustand beim Laden prüfen
  const currentType = welcomeTypeInput.value;
  if (currentType === 'page') {
    sectionPage.classList.remove('hidden');
    sectionAlbum.classList.add('hidden');
  } else if (currentType === 'album') {
    sectionAlbum.classList.remove('hidden');
    sectionPage.classList.add('hidden');
  } else {
    sectionPage.classList.add('hidden');
    sectionAlbum.classList.add('hidden');
  }

  // Helper: Auswahl-Status setzen
  function clearSelections(listItems) {
    listItems.forEach(item => {
      item.querySelector('span.text-sky-600')?.classList.add('hidden');
    });
  }

  // Welcome Page (default)
  defaultOption?.addEventListener('click', () => {
    sectionPage.classList.add('hidden');
    sectionAlbum.classList.add('hidden');
    welcomeTypeInput.value = 'start';
    welcomeContentInput.value = '';
    clearSelections([...albumListItems, ...pageListItems]);
  });

  // Page
  pageOption?.addEventListener('click', () => {
    sectionPage.classList.remove('hidden');
    sectionAlbum.classList.add('hidden');
    welcomeTypeInput.value = 'page';
    welcomeContentInput.value = '';
    clearSelections(albumListItems);
  });

  // Album
  albumOption?.addEventListener('click', () => {
    sectionAlbum.classList.remove('hidden');
    sectionPage.classList.add('hidden');
    welcomeTypeInput.value = 'album';
    welcomeContentInput.value = '';
    clearSelections(pageListItems);
  });

  // Auswahl Album
  albumListItems.forEach(item => {
    item.addEventListener('click', () => {
      clearSelections(albumListItems);
      item.querySelector('span.text-sky-600')?.classList.remove('hidden');
      const slug = item.getAttribute('data-value');
      welcomeContentInput.value = slug;
    });
  });

  // Auswahl Page
  pageListItems.forEach(item => {
    item.addEventListener('click', () => {
      clearSelections(pageListItems);
      item.querySelector('span.text-sky-600')?.classList.remove('hidden');
      const slug = item.getAttribute('data-value');
      welcomeContentInput.value = slug;
    });
  });
});
