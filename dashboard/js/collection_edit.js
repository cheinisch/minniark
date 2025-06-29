document.addEventListener("DOMContentLoaded", () => {

    let simplemde = null;

    const normalBtnGroup = document.getElementById("normal-group");
    const editBtnGroup = document.getElementById("edit-group");

    const editBtn = document.getElementById("edit_text");
    const saveBtn = document.getElementById("save_edit");
    const cancelBtn = document.getElementById("cancel_edit");

    const viewFrame = document.getElementById("text-show-frame");
    const editFrame = document.getElementById("text-edit-frame");

    const newTitleInput = document.getElementById("album-title-edit");
    const currentAlbum = document.getElementById("album-current-title");

    editBtn.addEventListener("click", () => {
        console.log("Edit butten pressed");
        normalBtnGroup.classList.add("hidden");
        editBtnGroup.classList.remove("hidden");
        viewFrame.classList.add("hidden");
        editFrame.classList.remove("hidden");
        // Initialisiere den Editor nur bei Bedarf
        if (!simplemde) {
            simplemde = new SimpleMDE({
                element: document.getElementById("collection-description"),
                toolbar: ["bold", "italic", "heading", "|", "quote"]
            });
        }
    });

    cancelBtn.addEventListener("click", () => {
        console.log("Cancel butten pressed");
        normalBtnGroup.classList.remove("hidden");
        editBtnGroup.classList.add("hidden");
        viewFrame.classList.remove("hidden");
        editFrame.classList.add("hidden");

        // Editor entfernen & zerstören
        if (simplemde) {
            simplemde.toTextArea(); // zurückverwandeln in normales <textarea>
            simplemde = null;       // Referenz löschen
        }
    });

    saveBtn.addEventListener("click", () => {
        saveBtn.innerHTML = 'saving...';
    });
    

});