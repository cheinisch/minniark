document.addEventListener("DOMContentLoaded", function () {
    const uploadBox = document.getElementById("uploadBox");
    const fileInput = document.getElementById("fileInput");
    const progressContainer = document.getElementById("progressContainer");
    const progressBar = document.getElementById("progressBar");
    const progressText = document.getElementById("progressText");
    const uploadStatus = document.getElementById("uploadStatus");

    // Datei per Klick auswählen
    uploadBox.addEventListener("click", () => fileInput.click());

    // Datei nach Auswahl hochladen
    fileInput.addEventListener("change", function (event) {
        handleFileUpload(event.target.files[0]);
    });

    // Drag & Drop Events
    uploadBox.addEventListener("dragover", (event) => {
        event.preventDefault();
        uploadBox.classList.add("border-indigo-500", "bg-indigo-50");
    });

    uploadBox.addEventListener("dragleave", () => {
        uploadBox.classList.remove("border-indigo-500", "bg-indigo-50");
    });

    uploadBox.addEventListener("drop", (event) => {
        event.preventDefault();
        uploadBox.classList.remove("border-indigo-500", "bg-indigo-50");

        if (event.dataTransfer.files.length > 0) {
            handleFileUpload(event.dataTransfer.files[0]);
        }
    });

    // Datei hochladen
    function handleFileUpload(file) {
        if (!file) return;

        const allowedTypes = ["image/png", "image/jpeg", "image/gif"];
        const maxFileSizeMB = 50
        const maxFileSize = maxFileSizeMB * 1024 * 1024;

        if (!allowedTypes.includes(file.type)) {
            uploadStatus.textContent = "❌ Ungültiges Dateiformat. Nur PNG, JPG und GIF erlaubt.";
            uploadStatus.classList.add("text-red-500");
            return;
        }

        if (file.size > maxFileSize) {
            uploadStatus.textContent = `❌ Datei ist zu groß. Maximal erlaubt: ${maxFileSizeMB} MB`;
            uploadStatus.classList.add("text-red-500");
            return;
        }

        // Fortschrittsanzeige aktivieren
        progressContainer.classList.remove("hidden");
        progressBar.style.width = "0%";
        progressText.textContent = "0%";
        uploadStatus.textContent = "⏳ Datei wird hochgeladen...";
        uploadStatus.classList.remove("text-red-500", "text-green-500");

        const formData = new FormData();
        formData.append("file", file);

        // Datei per AJAX hochladen
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "inc/upload.php", true);

        // Fortschrittsanzeige aktualisieren
        xhr.upload.onprogress = function (event) {
            if (event.lengthComputable) {
                let percentComplete = Math.round((event.loaded / event.total) * 100);
                progressBar.style.width = percentComplete + "%";
                progressText.textContent = percentComplete + "%";
            }
        };

        // Upload abgeschlossen
        xhr.onload = function () {
            if (xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        uploadStatus.textContent = "✅ Datei erfolgreich hochgeladen!";
                        uploadStatus.classList.add("text-green-500");
                    } else {
                        uploadStatus.textContent = `❌ Fehler: ${response.error}`;
                        uploadStatus.classList.add("text-red-500");
                    }
                } catch (e) {
                    uploadStatus.textContent = "❌ Fehler beim Verarbeiten der Serverantwort.";
                    uploadStatus.classList.add("text-red-500");
                }
            } else {
                uploadStatus.textContent = "❌ Fehler beim Hochladen.";
                uploadStatus.classList.add("text-red-500");
            }

            // Fortschrittsanzeige ausblenden
            setTimeout(() => {
                progressContainer.classList.add("hidden");
            }, 2000);
        };

        xhr.onerror = function () {
            uploadStatus.textContent = "❌ Netzwerkfehler. Bitte erneut versuchen.";
            uploadStatus.classList.add("text-red-500");
        };

        xhr.send(formData);
    }
});
