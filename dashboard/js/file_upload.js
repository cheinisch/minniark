const uploadBox = document.getElementById("uploadBox");
    const fileInput = document.getElementById("fileInput");
    const fileInfo = document.getElementById("fileInfo");
    const fileNameSpan = document.getElementById("fileName");
    const uploadStatus = document.getElementById("uploadStatus");

    uploadBox.addEventListener("click", () => fileInput.click());
    fileInput.addEventListener("change", handleFile);
    
    function handleFile() {
        if (fileInput.files.length > 0) {
            fileNameSpan.textContent = fileInput.files[0].name;
            fileInfo.classList.remove("hidden");
        }
    }

    function resetUpload() {
        fileInput.value = "";
        fileInfo.classList.add("hidden");
        uploadStatus.textContent = "";
    }

    function uploadFile() {
        const formData = new FormData();
        formData.append("file", fileInput.files[0]);

        fetch("inc/upload.php", { // Pfad zur Upload-Datei in /inc/
            method: "POST",
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            uploadStatus.textContent = data;
            if (data.includes("erfolgreich")) {
                uploadStatus.classList.add("text-green-600");
            } else {
                uploadStatus.classList.add("text-red-600");
            }
        })
        .catch(error => {
            uploadStatus.textContent = "Fehler beim Hochladen.";
            uploadStatus.classList.add("text-red-600");
        });
    }