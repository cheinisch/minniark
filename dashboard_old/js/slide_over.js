document.addEventListener("DOMContentLoaded", function () {
    console.log("JavaScript geladen!");

    const panel = document.getElementById("slideOverPanel");
    const backdrop = document.querySelector(".fixed.inset-0.bg-gray-500\\/75");
    const imageContainer = document.getElementById("slideOverImage");
    const titleContainer = document.getElementById("slideOverTitle");
    const closeButton = document.getElementById("closePanelButton");

    function openImageDetails(event) {
        console.log("Bild geklickt!");

        let target = event.currentTarget;
        let imageSrc = target.getAttribute("data-src");
        let imageTitle = target.getAttribute("data-title");

        console.log("Bildquelle:", imageSrc);
        console.log("Bildtitel:", imageTitle);

        if (panel) {
            panel.classList.remove("hidden");
        }
        if (backdrop) {
            backdrop.classList.remove("hidden");
        }
        if (imageContainer) {
            imageContainer.src = imageSrc;
        }
        if (titleContainer) {
            titleContainer.textContent = imageTitle;
        }
    }

    function closeImageDetails() {
        console.log("Schließen des Panels...");
        if (panel) {
            panel.classList.add("hidden");
        }
        if (backdrop) {
            backdrop.classList.add("hidden");
        }
    }

    // EventListener für Bilder mit data-open-panel
    const images = document.querySelectorAll("[data-open-panel]");
    console.log("Gefundene Bilder:", images.length);

    images.forEach(image => {
        console.log("Füge EventListener hinzu für:", image);
        image.addEventListener("click", openImageDetails);
    });

    // Schließen des Panels durch Button-Klick
    if (closeButton) {
        closeButton.addEventListener("click", closeImageDetails);
    } else {
        console.error("closePanelButton nicht gefunden!");
    }
});
