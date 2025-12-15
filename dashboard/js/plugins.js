document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('button[role="switch"]').forEach(button => {
      const thumb = button.querySelector('span[aria-hidden="true"]');
      const pluginKey = button.dataset.plugin;
      const hiddenInput = document.getElementById(`enabled-input-${pluginKey}`);

      function updateState(isChecked) {
        button.setAttribute('aria-checked', isChecked);
        hiddenInput.value = isChecked ? 'true' : 'false';

        button.classList.toggle('bg-cyan-600', isChecked);
        button.classList.toggle('bg-gray-400', !isChecked);
        thumb.classList.toggle('translate-x-5', isChecked);
        thumb.classList.toggle('translate-x-0', !isChecked);
      }

      let initial = button.getAttribute('aria-checked') === 'true';
      updateState(initial);

      button.addEventListener('click', () => {
        updateState(!(button.getAttribute('aria-checked') === 'true'));
      });
    });
  });