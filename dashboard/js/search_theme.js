document.addEventListener('DOMContentLoaded', function () {
  const searchInput = document.getElementById('theme-search-input');
  // Nur Karten im Modal-Grid auswählen
  const cards = document.querySelectorAll('#theme-search-grid [data-theme]');

  if (!searchInput || !cards.length) return;

  searchInput.addEventListener('input', function () {
    const q = this.value.trim().toLowerCase();

    cards.forEach(card => {
      const hay =
        ((card.dataset.theme || '') + ' ' +
         (card.dataset.author || '') + ' ' +
         (card.dataset.version || '')).toLowerCase();

      card.classList.toggle('hidden', q && !hay.includes(q));
    });
  });
});
