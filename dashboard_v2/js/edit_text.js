const buttonGroup = document.getElementById("button_group");
  const editButton = document.getElementById("edit_text");
  const container = document.getElementById("text_container");

  let isEditing = false;
  let originalText = "";

  editButton.addEventListener("click", () => {
    const p = document.getElementById("editable_text");

    if (!isEditing) {
      // Switch to edit mode
      originalText = p.textContent;
      const textarea = document.createElement("textarea");
      textarea.id = "editable_text";
      textarea.className = "w-full h-48 text-black p-2 rounded bg-white";
      textarea.value = originalText;
      container.replaceChild(textarea, p);

      // Replace Edit button with Save + Cancel
      editButton.textContent = "Speichern";
      const cancelBtn = document.createElement("button");
      cancelBtn.id = "cancel_edit";
      cancelBtn.textContent = "Abbrechen";
      cancelBtn.className = "bg-gray-400 hover:bg-gray-600 px-2 py-1 rounded-md text-white";
      buttonGroup.appendChild(cancelBtn);

      cancelBtn.addEventListener("click", () => {
        const textarea = document.getElementById("editable_text");
        const p = document.createElement("p");
        p.id = "editable_text";
        p.textContent = originalText;
        container.replaceChild(p, textarea);

        // Restore buttons
        editButton.textContent = "Edit Text";
        cancelBtn.remove();
        isEditing = false;
      });

    } else {
      // Save changes
      const textarea = document.getElementById("editable_text");
      const newText = textarea.value;
      const newP = document.createElement("p");
      newP.id = "editable_text";
      newP.textContent = newText;
      container.replaceChild(newP, textarea);

      // Restore button
      editButton.textContent = "Edit Text";
      const cancelBtn = document.getElementById("cancel_edit");
      if (cancelBtn) cancelBtn.remove();
    }

    isEditing = !isEditing;
  });