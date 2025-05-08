
          document.querySelectorAll('button[data-filename]').forEach(button => {
            button.addEventListener('click', (e) => {
              const dropdown = button.parentElement.querySelector('.dropdown');
              document.querySelectorAll('.dropdown').forEach(d => {
                if (d !== dropdown) d.classList.add('hidden');
              });
              dropdown.classList.toggle('hidden');
              e.stopPropagation();
            });
          });

          document.addEventListener('click', () => {
            document.querySelectorAll('.dropdown').forEach(d => d.classList.add('hidden'));
          });
      