document.getElementById('uploadImageButton').addEventListener('click', () => {
    document.getElementById('uploadModal').classList.remove('hidden');
  });
  document.getElementById('closeUpload').addEventListener('click', () => {
    document.getElementById('uploadModal').classList.add('hidden');
  });


  document.addEventListener("DOMContentLoaded", function () {
    console.log("Load Fileupload");
    const uploadBox = document.getElementById("uploadBox");
    const fileInput = document.getElementById("fileInput");
    const progressContainer = document.getElementById("progressContainer");
    const progressBar = document.getElementById("progressBar");
    const messageBox = document.getElementById("messageBox");

    // Allowed file types and max size (in MB)
    const allowedTypes = ["image/jpeg", "image/png"];
    const maxSizeMB = 50;

    // Handle drag and drop
    uploadBox.addEventListener("dragover", (e) => {
        e.preventDefault();
        uploadBox.classList.add("border-sky-500");
    });

    uploadBox.addEventListener("dragleave", () => {
        uploadBox.classList.remove("border-indigo-500");
    });

    uploadBox.addEventListener("drop", (e) => {
        e.preventDefault();
        uploadBox.classList.remove("border-indigo-500");

        const files = e.dataTransfer.files;
        handleFiles(files);
    });

    // Handle click to open file dialog
    uploadBox.addEventListener("click", () => {
        console.log("Klick Fileupload");
        fileInput.click();
    });

    // Handle file selection
    fileInput.addEventListener("change", (e) => {
        const files = e.target.files;
        handleFiles(files);
    });

    function handleFiles(files) {
        for (let file of files) {
            if (!allowedTypes.includes(file.type)) {
                showMessage("Invalid file type! Only JPG, PNG allowed.", "error");
                continue;
            }

            if (file.size > maxSizeMB * 1024 * 1024) {
                showMessage(`File is too large! Max size is ${maxSizeMB}MB.`, "error");
                continue;
            }

            uploadFile(file);
        }
    }

    function uploadFile(file) {
        const formData = new FormData();
        formData.append("file", file);

        const xhr = new XMLHttpRequest();
        xhr.open("POST", `${window.location.origin}/api/upload.php`, true);

        // Track progress
        xhr.upload.onprogress = function (event) {
            if (event.lengthComputable) {
                const percent = (event.loaded / event.total) * 100;
                progressBar.style.width = percent + "%";
                progressBar.innerText = Math.round(percent) + "%";
            }
        };

        // When the upload is done
        xhr.onload = function () {
            if (xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        showMessage("Upload successful!", "success");
                    } else {
                        showMessage("" + response.error, "error");
                    }
                } catch (error) {
                    showMessage("Server error: Invalid response.", "error");
                }
            } else {
                showMessage("Upload failed. Try again.", "error");
            }

            progressBar.style.width = "0%";
            progressBar.innerText = "";
        };

        xhr.send(formData);
        showMessage("Uploading...", "info");
    }

    function showMessage(message, type) {
        messageBox.innerText = message;
        messageBox.className = `text-sm font-medium p-2 rounded-md mt-2 ${
            type === "success" ? "text-green-600 bg-green-100" :
            type === "error" ? "text-red-600 bg-red-100" :
            "text-blue-600 bg-blue-100"
        }`;
    }
});
