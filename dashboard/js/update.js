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
    // Tailwind GUI Overlay erstellen
    const overlay = document.createElement('div');
    overlay.className = 'fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50';
    overlay.innerHTML = `
      <div class="bg-white p-6 rounded-lg shadow-lg text-center">
        <h2 class="text-xl font-bold mb-4">Update wird durchgeführt...</h2>
        <div id="progress" class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
          <div class="bg-sky-500 h-4 transition-all duration-500" style="width: 0%"></div>
        </div>
        <p id="progress-text" class="mt-4 text-gray-600">Starte Update...</p>
      </div>
    `;
    document.body.appendChild(overlay);

    const progressBar = overlay.querySelector('#progress div');
    const progressText = overlay.querySelector('#progress-text');

    function updateProgress(percent, text) {
      progressBar.style.width = percent + '%';
      progressText.textContent = text;
    }

    updateProgress(10, 'Starte Update...');

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
            updateProgress(0, 'Fehler beim Update: ' + (data.message || 'Unbekannter Fehler'));
          }
        })
        .catch(error => {
          updateProgress(0, 'Update fehlgeschlagen: ' + error);
        });
    }

    callUpdate('update.php');
  }
});
