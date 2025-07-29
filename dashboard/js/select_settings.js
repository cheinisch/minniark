document.addEventListener('DOMContentLoaded', () => {
    const dropdownButton = document.querySelector('[aria-haspopup="listbox"]');
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

  document.addEventListener('DOMContentLoaded', function () {
    const languageButton = document.querySelector('[aria-haspopup="listbox-language"]');
    const languageList = languageButton?.nextElementSibling;
    const languageText = languageButton?.querySelector('span.truncate');
    const hiddenLanguageInput = document.getElementById('selected-language');

    if (languageButton && languageList) {
        languageList.style.display = 'none';

        languageButton.addEventListener('click', function (e) {
            e.preventDefault();
            const isOpen = languageList.style.display === 'block';
            languageList.style.display = isOpen ? 'none' : 'block';
            languageButton.setAttribute('aria-expanded', String(!isOpen));
        });

        const options = languageList.querySelectorAll('li');
        options.forEach(option => {
            option.addEventListener('click', function (e) {
                e.preventDefault();

                const clickedTextSpan = this.querySelector('span.truncate');
                const clickedCheckmarkSpan = this.querySelector('span.absolute');

                if (clickedTextSpan && clickedCheckmarkSpan) {
                    // Button-Text ändern
                    languageText.innerText = clickedTextSpan.innerText.trim();

                    // Verstecktes Input-Feld aktualisieren!
                    hiddenLanguageInput.value = clickedTextSpan.innerText.trim();

                    // Alle Häkchen verstecken
                    languageList.querySelectorAll('li span.absolute').forEach(checkmark => {
                        checkmark.classList.add('hidden');
                    });

                    // Nur aktuelles Häkchen anzeigen
                    clickedCheckmarkSpan.classList.remove('hidden');

                    // Menü schließen
                    languageList.style.display = 'none';
                    languageButton.setAttribute('aria-expanded', 'false');
                }
            });
        });

        document.addEventListener('click', function (e) {
            if (!languageButton.contains(e.target) && !languageList.contains(e.target)) {
                languageList.style.display = 'none';
                languageButton.setAttribute('aria-expanded', 'false');
            }
        });
    }
});


document.addEventListener('DOMContentLoaded', function () {
  const imageButton = document.querySelector('[aria-haspopup="listbox-image"]');
  const imageList = imageButton?.nextElementSibling;
  const imageText = imageButton?.querySelector('span.truncate');
  const hiddenImageInput = document.getElementById('image_size');

  if (imageButton && imageList) {
      // 1. Liste beim Laden verstecken
      imageList.style.display = 'none';

      // 2. Klick auf Button: öffnen/schließen
      imageButton.addEventListener('click', function (e) {
          e.preventDefault();
          const isOpen = imageList.style.display === 'block';
          imageList.style.display = isOpen ? 'none' : 'block';
          imageButton.setAttribute('aria-expanded', String(!isOpen));
      });

      // 3. Auswahl der Größe
      const options = imageList.querySelectorAll('li');
      options.forEach(option => {
          option.addEventListener('click', function (e) {
              e.preventDefault();

              const clickedTextSpan = this.querySelector('span.truncate');
              const clickedCheckmarkSpan = this.querySelector('span.absolute');

              if (clickedTextSpan && clickedCheckmarkSpan) {
                  // Button-Text aktualisieren
                  imageText.innerText = clickedTextSpan.innerText.trim();

                  // Verstecktes Input-Field aktualisieren
                  hiddenImageInput.value = clickedTextSpan.innerText.trim();

                  // Alle Häkchen verstecken
                  imageList.querySelectorAll('li span.absolute').forEach(checkmark => {
                      checkmark.classList.add('hidden');
                  });

                  // Nur aktuelles Häkchen zeigen
                  clickedCheckmarkSpan.classList.remove('hidden');

                  // Liste schließen
                  imageList.style.display = 'none';
                  imageButton.setAttribute('aria-expanded', 'false');
              }
          });
      });

      // 4. Klick außerhalb schließt Dropdown
      document.addEventListener('click', function (e) {
          if (!imageButton.contains(e.target) && !imageList.contains(e.target)) {
              imageList.style.display = 'none';
              imageButton.setAttribute('aria-expanded', 'false');
          }
      });
  }
});


document.addEventListener('DOMContentLoaded', function () {
  const recreateCacheButton = document.getElementById('recreate-cache-button'); // Du musst dem Button eine ID geben!
  const successNotification = document.getElementById('notification-success');
  const errorNotification = document.getElementById('notification-error');

  if (recreateCacheButton) {
      recreateCacheButton.addEventListener('click', async function (e) {
          e.preventDefault();

          try {
              const response = await fetch('../api/recreate_cache.php', {
                  method: 'POST'
              });

              const result = await response.json();

              if (result.success) {
                  successNotification.classList.remove('hidden');
                  errorNotification.classList.add('hidden');
              } else {
                  errorNotification.classList.remove('hidden');
                  successNotification.classList.add('hidden');
              }
          } catch (error) {
              console.error('Error:', error);
              errorNotification.classList.remove('hidden');
              successNotification.classList.add('hidden');
          }
      });
  }
});
document.addEventListener('DOMContentLoaded', function () {
  const switchIds = [
      'timline_enable',
      'timline_group',
      'map_enable',
      'sitemap_enable'
  ];

  switchIds.forEach(id => {
      const button = document.getElementById(id);
      if (!button) return;

      const ball = button.querySelector('span');
      const isChecked = button.getAttribute('aria-checked') === 'true';

      if (isChecked) {
          // Einschalten
          button.classList.remove('bg-gray-400');
          button.classList.add('bg-sky-600');
          ball.classList.remove('translate-x-0');
          ball.classList.add('translate-x-5');
      } else {
          // Ausschalten
          button.classList.remove('bg-sky-600');
          button.classList.add('bg-gray-400');
          ball.classList.remove('translate-x-5');
          ball.classList.add('translate-x-0');
      }

      // Klick-Verhalten für Umschalten
      button.addEventListener('click', function () {
          const isNowChecked = button.getAttribute('aria-checked') === 'true';
          button.setAttribute('aria-checked', String(!isNowChecked));

          if (!isNowChecked) {
              // Einschalten
              button.classList.remove('bg-gray-400');
              button.classList.add('bg-sky-600');
              ball.classList.remove('translate-x-0');
              ball.classList.add('translate-x-5');
          } else {
              // Ausschalten
              button.classList.remove('bg-sky-600');
              button.classList.add('bg-gray-400');
              ball.classList.remove('translate-x-5');
              ball.classList.add('translate-x-0');
          }
      });
  });
});

document.addEventListener('DOMContentLoaded', function () {
    const themeButton = document.querySelector('[aria-haspopup="listbox-theme"]');
    const themeList = themeButton?.nextElementSibling;
    const themeText = themeButton?.querySelector('span.truncate');
    const hiddenThemeInput = document.getElementById('theme');

    if (themeButton && themeList) {
        // 1. Liste beim Laden verstecken
        themeList.style.display = 'none';

        // 2. Klick auf Button: öffnen/schließen
        themeButton.addEventListener('click', function (e) {
            e.preventDefault();
            const isOpen = themeList.style.display === 'block';
            themeList.style.display = isOpen ? 'none' : 'block';
            themeButton.setAttribute('aria-expanded', String(!isOpen));
        });

        // 3. Auswahl des Themes
        const options = themeList.querySelectorAll('li');
        options.forEach(option => {
            option.addEventListener('click', function (e) {
                e.preventDefault();

                const clickedTextSpan = this.querySelector('span.truncate');
                const clickedCheckmarkSpan = this.querySelector('span.absolute');

                if (clickedTextSpan && clickedCheckmarkSpan) {
                    // Button-Text aktualisieren
                    themeText.innerText = clickedTextSpan.innerText.trim();

                    // Verstecktes Input-Field aktualisieren
                    hiddenThemeInput.value = clickedTextSpan.innerText.trim();

                    // Alle Häkchen verstecken
                    themeList.querySelectorAll('li span.absolute').forEach(checkmark => {
                        checkmark.classList.add('hidden');
                    });

                    // Nur aktuelles Häkchen zeigen
                    clickedCheckmarkSpan.classList.remove('hidden');

                    // Liste schließen
                    themeList.style.display = 'none';
                    themeButton.setAttribute('aria-expanded', 'false');
                }
            });
        });

        // 4. Klick außerhalb schließt Dropdown
        document.addEventListener('click', function (e) {
            if (!themeButton.contains(e.target) && !themeList.contains(e.target)) {
                themeList.style.display = 'none';
                themeButton.setAttribute('aria-expanded', 'false');
            }
        });
    }
});

document.addEventListener('DOMContentLoaded', function () {
    const button = document.querySelector('[aria-haspopup="listbox-logintype"]');
    const dropdown = button.nextElementSibling;
    const options = dropdown.querySelectorAll('[role="option"]');
    const spanText = button.querySelector('span.truncate');
    const hiddenInput = document.getElementById('login_type');
  
    // Funktion zum Anwenden der Auswahl
    function selectOptionByText(text) {
      options.forEach(option => {
        const label = option.querySelector('span.truncate');
        const checkIcon = option.querySelector('span.absolute');
  
        if (label.textContent.trim().toLowerCase() === text.toLowerCase()) {
          label.classList.add('font-semibold');
          label.classList.remove('font-normal');
          checkIcon.classList.remove('hidden');
          spanText.textContent = label.textContent;
          if (hiddenInput) hiddenInput.value = label.textContent.toLowerCase();
        } else {
          label.classList.remove('font-semibold');
          label.classList.add('font-normal');
          checkIcon.classList.add('hidden');
        }
      });
    }
  
    // Initialisierung beim Laden
    const initialValue = spanText.textContent.trim().toLowerCase();
    selectOptionByText(initialValue);
  
    // Dropdown öffnen/schließen
    button.addEventListener('click', () => {
      dropdown.classList.toggle('hidden');
    });
  
    // Klick auf Option
    options.forEach(option => {
      option.addEventListener('click', () => {
        const label = option.querySelector('span.truncate').textContent.trim();
        selectOptionByText(label);
        dropdown.classList.add('hidden');
      });
    });
  
    // Klick außerhalb: Dropdown schließen
    document.addEventListener('click', (e) => {
      if (!button.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.classList.add('hidden');
      }
    });
  });