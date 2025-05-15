document.addEventListener("DOMContentLoaded", () => {
    const removeButton = document.getElementById("removeHeroImg");
    const coverInput = document.getElementById("cover");
    const coverPreview = document.getElementById("coverPreview");

    removeButton.addEventListener("click", () => {
      // Hidden Input leeren
      coverInput.value = "";

      // Platzhalter-Bild setzen
      coverPreview.src = "img/placeholder.png";
    });
  });