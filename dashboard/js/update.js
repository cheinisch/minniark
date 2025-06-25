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
    overlay.className = 'fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50';
    overlay.innerHTML = `
      <div class="bg-white p-6 shadow-lg text-center w-96">
        <h2 class="text-xl font-bold mb-4">Updating in progress...</h2>
        <div id="progress" class="w-full bg-gray-200 rounded-full h-4 overflow-hidden mb-4">
          <div class="bg-sky-500 h-4 transition-all duration-500" style="width: 0%"></div>
        </div>
        <p id="progress-text" class="mb-4 text-gray-600">Starting update...</p>
        <button id="toggle-log" class="text-sky-600 text-sm mb-2 hover:underline focus:outline-none">
          Show details ▼
        </button>
        <div id="log" class="text-left text-xs bg-gray-100 p-2 hidden max-h-40 overflow-y-auto"></div>
      </div>
    `;
    document.body.appendChild(overlay);

    const progressBar = overlay.querySelector('#progress div');
    const progressText = overlay.querySelector('#progress-text');
    const toggleLogButton = overlay.querySelector('#toggle-log');
    const logDiv = overlay.querySelector('#log');

    toggleLogButton.addEventListener('click', () => {
      logDiv.classList.toggle('hidden');
      toggleLogButton.textContent = logDiv.classList.contains('hidden') ? 'Show details ▼' : 'Hide details ▲';
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
        .then(response => response.text()) // ⬅ Rohtext holen statt direkt .json()
        .then(text => {
          console.log('Raw response:', text); // ⬅ zeigt dir z. B. HTML, Fehler oder leere Antwort

          try {
            const data = JSON.parse(text); // ⬅ erst jetzt parsen
            console.log('Update response:', data);

            if (data.success) {
              if (data.redirect) {
                console.log('Redirecting to:', data.redirect);
                updateProgress(30, 'Switching to update mode...');
                setTimeout(() => {
                  callUpdate(data.redirect);
                }, 500);
              } else {
                updateProgress(100, 'Update complete. Reloading...');
                setTimeout(() => location.reload(), 2000);
              }
            } else {
              updateProgress(0, 'Update error: ' + (data.message || 'Unknown error'), 'error');
            }
          } catch (e) {
            updateProgress(0, 'Update failed: invalid JSON', 'error');
            console.error('Invalid JSON:', text); // ⬅ hier siehst du genau, was kaputt war
          }
        })
        .catch(error => {
          updateProgress(0, 'Update failed: ' + error, 'error');
        });
    }


    updateProgress(10, 'Starting update...');
    callUpdate('update.php');
  }
});
