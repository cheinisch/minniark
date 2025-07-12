document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.querySelector('input[type="search"]');
    const cards = document.querySelectorAll('[data-theme]');

    if (!searchInput) return;

    searchInput.addEventListener('input', function () {
      const query = this.value.toLowerCase();

      cards.forEach(card => {
        const themeName = card.getAttribute('data-theme').toLowerCase();
        if (themeName.includes(query)) {
          card.classList.remove('hidden');
        } else {
          card.classList.add('hidden');
        }
      });
    });
  });