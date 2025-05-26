document.addEventListener("DOMContentLoaded", function () {
  const openBtn = document.getElementById("addAlbumtoCollectionBtn");
  const modal = document.getElementById("addTocollectionAlbumModal");
  const closeBtn = document.getElementById("closeAddTocollectionAlbumModal");
  const cancelBtn = document.getElementById("cancelAddTocollectionAlbum");
  const form = document.getElementById("addAlbumsForm");

  const openCoverBtn = document.getElementById("selectCollectionImageBtn");
  const modalCover = document.getElementById("addToAlbumImageModal");
  const closeCoverBtn = document.getElementById("closeAddToAlbumImageModal");
  const cancelCoverBtn = document.getElementById("cancelAddToAlbumImage");

  // Funktion zum Schließen
  function closeModal() {
    modal.classList.add("hidden");
  }

  function closeCoverModal() {
    modalCover.classList.add("hidden");
  }

  // Modal öffnen
  openBtn?.addEventListener("click", () => {
    modal.classList.remove("hidden");
  });

  openCoverBtn?.addEventListener("click", () => {
    modalCover.classList.remove("hidden");
  });

  // Modal schließen bei Klick auf X oder Cancel
  closeBtn?.addEventListener("click", closeModal);
  cancelBtn?.addEventListener("click", closeModal);

  closeCoverBtn?.addEventListener("click", closeCoverModal);
  cancelCoverBtn?.addEventListener("click", closeCoverModal);
});
