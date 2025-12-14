// album_collection.js
(() => {
  // Guard gegen doppeltes Laden
  if (window.__minniarkAlbumCollectionInit) return;
  window.__minniarkAlbumCollectionInit = true;

  const DEBUG = true;
  const log = (...args) => DEBUG && console.log('[ALBUM/COLLECTION]', ...args);

  const albumModal      = document.getElementById('addAlbumModal');
  const collectionModal = document.getElementById('addCollectionModal');

  log('init', {
    albumModal,
    collectionModal
  });

  function hardStop(e) {
    e.preventDefault();
    e.stopPropagation();
    if (typeof e.stopImmediatePropagation === 'function') {
      e.stopImmediatePropagation();
    }
  }

  function openModal(modal, name) {
    if (!modal) {
      log('modal not found:', name);
      return;
    }
    modal.classList.remove('hidden');
    log('open modal:', name);
  }

  function closeModal(modal, name) {
    if (!modal) {
      log('modal not found:', name);
      return;
    }
    modal.classList.add('hidden');
    log('close modal:', name);
  }

  // --- Add Album ------------------------------------------------------------
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('#add-album');
    if (!btn) return;

    hardStop(e);
    log('click add-album');

    requestAnimationFrame(() => {
      openModal(albumModal, 'album');
    });
  }, true);

  // --- Add Collection -------------------------------------------------------
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('#add-collection');
    if (!btn) return;

    hardStop(e);
    log('click add-collection');

    requestAnimationFrame(() => {
      openModal(collectionModal, 'collection');
    });
  }, true);

  // --- Close Album (X oder Cancel) ------------------------------------------
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('#closeAlbumModal, #cancelAlbumModal');
    if (!btn) return;

    hardStop(e);
    log('click close-album');

    closeModal(albumModal, 'album');
  }, true);

  // --- Close Collection (X oder Cancel) -------------------------------------
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('#closeCollectionModal, #cancelCollectionModal');
    if (!btn) return;

    hardStop(e);
    log('click close-collection');

    closeModal(collectionModal, 'collection');
  }, true);

})();
