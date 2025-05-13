document.addEventListener('DOMContentLoaded', () => {
    const dropdownButton = document.querySelector('[aria-haspopup="listbox-style"]');
    const dropdownList = dropdownButton.nextElementSibling;
    const dropdownItems = dropdownList.querySelectorAll('[role="option"]');
    const display = dropdownButton.querySelector('span');
  
    // Aktuelle Auswahl setzen
    dropdownItems.forEach(item => {
      item.addEventListener('click', () => {
        const selectedText = item.querySelector('span').textContent;
  
        // Text anzeigen
        display.textContent = selectedText;
  
        // Visuelle Auswahl aktualisieren
        dropdownItems.forEach(i => {
          i.classList.remove('bg-sky-600', 'text-white', 'font-semibold');
          i.classList.add('text-gray-900', 'font-normal');
        });
        item.classList.add('bg-sky-600', 'text-white', 'font-semibold');
  
        // Dropdown schließen
        dropdownList.classList.add('hidden');
        dropdownButton.setAttribute('aria-expanded', 'false');
      });
    });
  
    // Öffnen/Schließen des Dropdowns
    dropdownButton.addEventListener('click', () => {
      const expanded = dropdownButton.getAttribute('aria-expanded') === 'true';
      dropdownButton.setAttribute('aria-expanded', String(!expanded));
      dropdownList.classList.toggle('hidden');
    });
  
    // Klick außerhalb → Dropdown schließen
    document.addEventListener('click', (e) => {
      if (!dropdownButton.contains(e.target) && !dropdownList.contains(e.target)) {
        dropdownList.classList.add('hidden');
        dropdownButton.setAttribute('aria-expanded', 'false');
      }
    });
  });
  