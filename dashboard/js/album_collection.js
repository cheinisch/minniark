  const addAlbumModal = document.getElementById('add-album');
  const closeAlbumModal = document.getElementById('closeAlbumModal');
  const addCollectionModal = document.getElementById('add-collection');
  const closeCollectionModal = document.getElementById('closeCollectionModal');

  addAlbumModal.addEventListener('click', function (e) {
    document.getElementById('addAlbumModal').classList.remove('hidden');
  });
  
  closeAlbumModal.addEventListener('click', function (e) {
    document.getElementById('addAlbumModal').classList.add('hidden');
  });

  
  addCollectionModal.addEventListener('click', function (e) {
    document.getElementById('addCollectionModal').classList.remove('hidden');
  });
  
  closeCollectionModal.addEventListener('click', function (e) {
    document.getElementById('addCollectionModal').classList.add('hidden');
  });