document.addEventListener('DOMContentLoaded', () => {
  const updateBtn = document.getElementById('update-btn');
  if (updateBtn) {
    updateBtn.style.display = 'inline-flex';

    updateBtn.addEventListener('click', () => {
      startUpdateProcess();
    });
  }

  function startUpdateProcess() {
    const overlay = document.createElement('div');
    overlay.className = 'fixed inset-0 z-50';
    overlay.innerHTML = `
      <div class="fixed inset-0 bg-black/50 backdrop-blur-[2px] transition-opacity"></div>

      <div class="fixed inset-0 flex items-end sm:items-center justify-center p-4">
        <div class="relative w-full sm:max-w-md transform overflow-hidden rounded-lg border border-black/10 dark:border-white/10 bg-white dark:bg-black/40 shadow-xl">
          
          <!-- Close (X) -->
          <button type="button" class="absolute top-3 right-3 inline-flex items-center justify-center rounded-md p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                  aria-label="Close update dialog"
                  data-close>
            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>

          <!-- Header -->
          <div class="px-4 pt-5 pb-2 sm:px-6">
            <div class="flex items-center gap-3">
              <div class="flex size-10 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-500/10">
                <svg class="size-5 text-indigo-600 dark:text-indigo-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                  <path d="M12 6v6l4 2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
              <div>
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">System-Update</h2>
                <p class="mt-0.5 text-xs text-black/60 dark:text-gray-400">Das System wird auf die neueste Version aktualisiert.</p>
              </div>
            </div>
          </div>

          <!-- Body -->
          <div class="px-4 pb-4 sm:px-6 sm:pb-6">
            <div class="mt-3">
              <div id="progress" class="w-full rounded-full bg-black/10 dark:bg-white/10 overflow-hidden h-2">
                <div class="h-2 bg-sky-600 dark:bg-sky-500 transition-all duration-500" style="width:0%"></div>
              </div>
              <p id="progress-text" class="mt-3 text-sm text-black/80 dark:text-gray-300">Starte Update…</p>
            </div>

            <div class="mt-3">
              <button id="toggle-log"
                      class="inline-flex items-center gap-1 text-xs font-medium text-sky-700 hover:text-sky-600 dark:text-sky-400 dark:hover:text-sky-300">
                Details anzeigen ▼
              </button>
              <div id="log" class="mt-2 hidden max-h-48 overflow-y-auto rounded-md border border-black/10 dark:border-white/10 bg-gray-50 dark:bg-white/5 p-2 text-[11px] leading-5 text-gray-700 dark:text-gray-200"></div>
            </div>
          </div>

          <!-- Footer -->
          <div class="px-4 pb-4 sm:px-6 sm:pb-6 flex items-center justify-end gap-2">
            <button type="button"
                    class="inline-flex items-center rounded-md px-3 py-1.5 text-sm font-semibold bg-white text-gray-900 inset-ring-1 inset-ring-gray-300 hover:bg-gray-50 dark:bg-white/10 dark:text-white dark:inset-ring-white/10 dark:hover:bg-white/20"
                    data-close>
              Schließen
            </button>
          </div>
        </div>
      </div>
    `;
    document.body.appendChild(overlay);

    const progressBar = overlay.querySelector('#progress div');
    const progressText = overlay.querySelector('#progress-text');
    const toggleLogButton = overlay.querySelector('#toggle-log');
    const logDiv = overlay.querySelector('#log');
    const closeButtons = overlay.querySelectorAll('[data-close]');

    closeButtons.forEach(btn => btn.addEventListener('click', () => {
      overlay.remove();
    }));

    toggleLogButton.addEventListener('click', () => {
      const hidden = logDiv.classList.toggle('hidden');
      toggleLogButton.textContent = hidden ? 'Details anzeigen ▼' : 'Details verbergen ▲';
    });

    function appendLog(message, type = 'info') {
      const timestamp = new Date().toLocaleTimeString();
      const color = type === 'error' ? 'text-red-600 dark:text-red-400' : 'text-green-700 dark:text-green-300';
      logDiv.insertAdjacentHTML('beforeend', `<div class="${color}">[${timestamp}] ${message}</div>`);
      logDiv.scrollTop = logDiv.scrollHeight;
    }

    function updateProgress(percent, text, type = 'info') {
      progressBar.style.width = percent + '%';
      progressText.textContent = text;
      appendLog(text, type);
    }

    function callUpdate(url) {
      fetch(url, { method: 'POST' })
        .then(response => response.text())
        .then(text => {
          try {
            const data = JSON.parse(text);

            if (data.success) {
              if (data.redirect) {
                updateProgress(30, 'Wechsle in den Update-Modus…');
                setTimeout(() => { callUpdate(data.redirect); }, 500);
              } else {
                updateProgress(100, 'Update abgeschlossen. Seite wird neu geladen…');
                setTimeout(() => location.reload(), 2000);
              }
            } else {
              updateProgress(0, 'Update-Fehler: ' + (data.message || 'Unbekannter Fehler'), 'error');
            }
          } catch (e) {
            updateProgress(0, 'Update fehlgeschlagen: ungültige JSON-Antwort', 'error');
            console.error('Invalid JSON:', text);
          }
        })
        .catch(error => {
          updateProgress(0, 'Update fehlgeschlagen: ' + error, 'error');
        });
    }

    updateProgress(10, 'Starte Update…');
    callUpdate('update.php');
  }
});