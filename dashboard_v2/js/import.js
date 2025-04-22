document.getElementById('uploadImageButton').addEventListener('click', () => {
    document.getElementById('uploadModal').classList.remove('hidden');
  });
  document.getElementById('closeUpload').addEventListener('click', () => {
    document.getElementById('uploadModal').classList.add('hidden');
  });