document.getElementById('saveAlbum').addEventListener('click', function() {
    const albumName = document.getElementById('album-title').value;
    const description = document.getElementById('album-description').value;
    const password = document.getElementById('album-password').value;
    const images = []; // Hier die Logik ergänzen, um Bilder auszuwählen
    const headImage = ''; // Hier die Logik ergänzen, um ein Hauptbild festzulegen

    fetch('./backend_api/album_create.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            name: albumName,
            description: description,
            password: password,
            images: JSON.stringify(images),
            headImage: headImage
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            alert('Error: ' + data.error);
        } else {
            alert('Success: ' + data.success);
            location.reload();
        }
    })
    .catch(err => {
        console.error('Fetch error:', err);
        alert('An error occurred while creating the album.');
    });
});
