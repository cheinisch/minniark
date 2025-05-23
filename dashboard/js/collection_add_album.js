document.addEventListener("DOMContentLoaded", function () {
  const openBtn = document.getElementById("addAlbumtoCollectionBtn");
  const modal = document.getElementById("addTocollectionAlbumModal");
  const closeBtn = document.getElementById("closeAddTocollectionAlbumModal");
  const cancelBtn = document.getElementById("cancelAddTocollectionAlbum");
  const form = document.getElementById("addAlbumsForm");

  // Funktion zum Schließen
  function closeModal() {
    modal.classList.add("hidden");
  }

  // Modal öffnen
  openBtn?.addEventListener("click", () => {
    modal.classList.remove("hidden");
  });

  // Modal schließen bei Klick auf X oder Cancel
  closeBtn?.addEventListener("click", closeModal);
  cancelBtn?.addEventListener("click", closeModal);
});
