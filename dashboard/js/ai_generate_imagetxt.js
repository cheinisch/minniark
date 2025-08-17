document.addEventListener("DOMContentLoaded", () => {
  const aiModal = document.getElementById("confirmAiModal");
  const openAiBtn = document.getElementById("generate_text");
  const cancelAiBtn = document.getElementById("confirmNo"); // Cancel-Button
  const confirmAiBtn = document.getElementById("confirmYes"); // Delete/Confirm-Button

  if (openAiBtn && aiModal) {
    // Modal öffnen
    openAiBtn.addEventListener("click", () => {
      aiModal.classList.remove("hidden");
    });
  }

  if (cancelAiBtn) {
    // Modal schließen (Cancel)
    cancelAiBtn.addEventListener("click", () => {
      aiModal.classList.add("hidden");
    });
  }
});
