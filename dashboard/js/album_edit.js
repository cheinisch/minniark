document.addEventListener("DOMContentLoaded", () => {

    const editBtn = document.getElementById("edit_text");
    const saveBtn = document.getElementById("save_text");
    const cancelBtn = document.getElementById("cancel_text");

    const viewFrame = document.getElementById("text-show-frame");
    const editFrame = document.getElementById("text-edit-frame");

    editBtn.addEventListener("click", () => {
        console.log("Edit butten pressed");
        editBtn.classList.add("collapse");
    });

});