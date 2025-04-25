document.addEventListener('DOMContentLoaded', () => {
  const versionApiUrl = "/api/version.php";

  fetch(versionApiUrl)
    .then((response) => response.json())
    .then((data) => {
      if (data.new_version_available) {
        const updateBtn = document.getElementById('update-btn');
        if (updateBtn) {
          updateBtn.style.display = 'inline-flex';
          updateBtn.setAttribute('data-version', data.new_version_number);
          updateBtn.setAttribute('data-download-url', data.new_version_url);

          updateBtn.addEventListener('click', () => {
            startUpdateProcess();
          });
        }
      }
    })
    .catch((error) => {
      console.error("Fehler beim Abrufen der Version:", error);
    });

  const updateBtn = document.getElementById('update-btn');
  if (updateBtn) {
    updateBtn.style.display = 'none';
  }

  function startUpdateProcess() {
    const overlay = document.createElement('div');
    overlay.className = 'fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50';
    overlay.innerHTML = `
      <div class="bg-white p-6 rounded-lg shadow-lg text-center w-96">
        <h2 class="text-xl font-bold mb-4">Update wird durchgeführt...</h2>
        <div id="progress" class="w-full bg-gray-200 rounded-full h-4 overflow-hidden mb-4">
          <div class="bg-sky-500 h-4 transition-all duration-500" style="width: 0%"></div>
        </div>
        <p id="progress-text" class="mb-4 text-gray-600">Starte Update...</p>
        <button id="toggle-log" class="text-sky-600 text-sm mb-2 hover:underline focus:outline-none">
          Details anzeigen ▼
        </button>
        <div id="log" class="text-left text-xs bg-gray-100 p-2 rounded hidden max-h-40 overflow-y-auto"></div>
      </div>
    `;
    document.body.appendChild(overlay);

    const progressBar = overlay.querySelector('#progress div');
    const progressText = overlay.querySelector('#progress-text');
    const toggleLogButton = overlay.querySelector('#toggle-log');
    const logDiv = overlay.querySelector('#log');

    toggleLogButton.addEventListener('click', () => {
      logDiv.classList.toggle('hidden');
      toggleLogButton.textContent = logDiv.classList.contains('hidden') ? 'Details anzeigen ▼' : 'Details verbergen ▲';
    });

    function appendLog(message, type = 'info') {
      const timestamp = new Date().toLocaleTimeString();
      const color = type === 'error' ? 'text-red-500' : 'text-green-600';
      logDiv.innerHTML += `<div class="${color}">[${timestamp}] ${message}</div>`;
      logDiv.scrollTop = logDiv.scrollHeight;
    }

    function updateProgress(percent, text, type = 'info') {
      progressBar.style.width = percent + '%';
      progressText.textContent = text;
      appendLog(text, type);
    }

    function callUpdate(url) {
      fetch(url, { method: 'POST' })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            if (data.redirect) {
              updateProgress(30, 'Wechsle in den Update-Modus...');
              setTimeout(() => {
                callUpdate(data.redirect);
              }, 500);
            } else {
              updateProgress(100, 'Update abgeschlossen. Lade neu...');
              setTimeout(() => location.reload(), 2000);
            }
          } else {
            updateProgress(0, 'Fehler beim Update: ' + (data.message || 'Unbekannter Fehler'), 'error');
          }
        })
        .catch(error => {
          updateProgress(0, 'Update fehlgeschlagen: ' + error, 'error');
        });
    }

    updateProgress(10, 'Starte Update...');
    callUpdate('update.php');
  }
});
