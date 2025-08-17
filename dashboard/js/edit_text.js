document.addEventListener("DOMContentLoaded", () => {
  // Buttons
  const editBtn = document.getElementById("edit_text");
  const saveBtn = document.getElementById("save_text");
  const cancelBtn = document.getElementById("cancel_edit");

  // Wrapper-Divs
  const editBtnDiv = document.getElementById("editBtnDiv");
  const saveBtnDiv = document.getElementById("saveBtnDiv");
  const cancelBtnDiv = document.getElementById("cancelBtnDiv");

  // Views / Inputs
  const titleView = document.getElementById("image-title");
  const titleInput = document.getElementById("edit-title");
  const descView = document.getElementById("editable_text");
  const descInput = document.getElementById("edit-description");

  const ratingContainer = document.getElementById("rating-stars");
  const fileName = ratingContainer?.dataset.filename || null;

  if (!editBtn || !saveBtn || !cancelBtn || !editBtnDiv || !saveBtnDiv || !cancelBtnDiv ||
      !titleView || !titleInput || !descView || !descInput) {
    console.warn("Text-Editor: erforderliche UI-Elemente nicht gefunden.");
    return;
  }

  let editing = false;
  let originalTitle = titleInput.value;
  let originalDesc = descInput.value;

  // Helfer
  const show = (el) => el.classList.remove("hidden");
  const hide = (el) => el.classList.add("hidden");

  const autoResize = (ta) => {
    ta.style.height = "auto";
    ta.style.height = ta.scrollHeight + "px";
  };
  autoResize(descInput);
  descInput.addEventListener("input", () => autoResize(descInput));

  const enterEditUI = () => {
    titleView.classList.add("hidden");
    descView.classList.add("hidden");
    titleInput.classList.remove("hidden");
    descInput.classList.remove("hidden");

    hide(editBtnDiv);
    show(saveBtnDiv);
    show(cancelBtnDiv);
    setTimeout(() => autoResize(descInput), 0);
  };

  const exitEditUI = () => {
    titleInput.classList.add("hidden");
    descInput.classList.add("hidden");
    titleView.classList.remove("hidden");
    descView.classList.remove("hidden");

    show(editBtnDiv);
    hide(saveBtnDiv);
    hide(cancelBtnDiv);
  };

  // --- Events ---
  editBtn.addEventListener("click", () => {
    if (editing) return;
    editing = true;
    enterEditUI();
  });

  saveBtn.addEventListener("click", () => {
    if (!editing) return;

    const newTitle = titleInput.value.trim();
    const newDescription = descInput.value.trim();

    if (!fileName) {
      console.warn("Kein Dateiname – Änderungen werden nicht gespeichert.");
      exitEditUI();
      editing = false;
      return;
    }

    fetch("/api/update_text.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ filename: fileName, title: newTitle, description: newDescription })
    })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          titleView.textContent = newTitle;
          descView.innerHTML = newDescription.replace(/\n/g, "<br>");
          originalTitle = newTitle;
          originalDesc = newDescription;
          exitEditUI();
          editing = false;
        } else {
          alert("Fehler beim Speichern: " + (data.error || "Unbekannter Fehler"));
        }
      })
      .catch(err => {
        console.error("Netzwerkfehler:", err);
        alert("Netzwerkfehler beim Speichern.");
      });
  });

  cancelBtn.addEventListener("click", () => {
    if (!editing) return;

    titleInput.value = originalTitle;
    descInput.value = originalDesc;
    autoResize(descInput);

    exitEditUI();
    editing = false;
  });
});
