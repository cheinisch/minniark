document.addEventListener("DOMContentLoaded", () => {
  const btn = document.getElementById("edit_text");
  const cancelBtn = document.getElementById("cancel_edit");

  const titleView = document.getElementById("image-title");
  const titleInput = document.getElementById("edit-title");

  const descView = document.getElementById("editable_text");
  const descInput = document.getElementById("edit-description");

  const ratingContainer = document.getElementById("rating-stars");
  const fileName = ratingContainer?.dataset.filename || null;

  if (!btn || !cancelBtn || !titleView || !titleInput || !descView || !descInput || !fileName) {
    console.warn("Text-Editor Elemente nicht vollständig gefunden oder Dateiname fehlt.");
    return;
  }

  let editing = false;
  let originalTitle = titleInput.value;
  let originalDesc = descInput.value;

  // 🪄 Auto-resize Funktion
  const autoResize = (textarea) => {
    textarea.style.height = "auto";
    textarea.style.height = textarea.scrollHeight + "px";
  };

  // Init auto-resize
  autoResize(descInput);
  descInput.addEventListener("input", () => autoResize(descInput));

  // Klick auf "Edit" / "Save"
  btn.addEventListener("click", () => {
    if (!editing) {
      // Wechsel in Edit-Modus
      titleView.classList.add("hidden");
      descView.classList.add("hidden");
      titleInput.classList.remove("hidden");
      descInput.classList.remove("hidden");
      setTimeout(() => autoResize(descInput), 10);
      cancelBtn.classList.remove("invisible");

      btn.innerText = "Save";
    } else {
      // Speichern
      const newTitle = titleInput.value.trim();
      const newDescription = descInput.value.trim();

      fetch("/api/update_text.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          filename: fileName,
          title: newTitle,
          description: newDescription
        })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          // DOM aktualisieren
          titleView.textContent = newTitle;
          descView.innerHTML = newDescription.replace(/\n/g, "<br>");
      
          originalTitle = newTitle;
          originalDesc = newDescription;
      
          titleInput.classList.add("hidden");
          descInput.classList.add("hidden");
          titleView.classList.remove("hidden");
          descView.classList.remove("hidden");
          cancelBtn.classList.add("invisible");
      
          btn.innerText = "Edit";
          editing = false;
        } else {
          alert("Fehler beim Speichern: " + (data.error || "Unbekannter Fehler"));
        }
      })
      .catch(err => {
        console.error("Netzwerkfehler:", err);
        alert("Netzwerkfehler beim Speichern.");
      });
    }

    editing = !editing;
  });

  // Klick auf "Cancel"
  cancelBtn.addEventListener("click", () => {
    titleInput.value = originalTitle;
    descInput.value = originalDesc;

    autoResize(descInput);

    titleInput.classList.add("hidden");
    descInput.classList.add("hidden");
    titleView.classList.remove("hidden");
    descView.classList.remove("hidden");

    btn.innerText = "Edit";
    cancelBtn.classList.add("invisible");
    editing = false;
  });
});
