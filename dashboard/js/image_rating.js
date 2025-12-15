document.addEventListener("DOMContentLoaded", () => {
  const ratingContainer = document.getElementById("rating-stars");
  if (!ratingContainer) {
    console.error("#rating-stars nicht gefunden");
    return;
  }


  let currentRating = parseInt(ratingContainer.dataset.rating) || 0;
  const fileName = ratingContainer.dataset.filename;
  const maxRating = 5;

  function createStarSVG(index, filled) {
    const span = document.createElement("span");
    const fillColor = index === 0 ? '#d1d5db' : (filled ? '#38bdf8' : '#d1d5db'); // cyan-400 oder grau
  
    span.innerHTML = `
      <svg class="w-5 h-5 cursor-pointer" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
        <path fill="${fillColor}"
              d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.137 3.5h3.684
                c.969 0 1.371 1.24.588 1.81l-2.984 2.17 1.138 3.5
                c.3.921-.755 1.688-1.538 1.117L10 13.348l-2.976 2.176
                c-.783.571-1.838-.196-1.538-1.117l1.138-3.5-2.984-2.17
                c-.783-.57-.38-1.81.588-1.81h3.684l1.137-3.5z"/>
      </svg>
    `;
  
    const star = span.firstElementChild;
    star.addEventListener("click", () => {
      currentRating = index;
      renderStars(currentRating);
      saveRating(currentRating);
    });
    return star;
  }
  

  function renderStars(rating) {
    ratingContainer.innerHTML = "";

    // 0-Stern-Stern (links, immer leer)
    const zeroStar = createStarSVG(0, false); 
    ratingContainer.appendChild(zeroStar);

    // 1 bis 5 Sterne
    for (let i = 1; i <= maxRating; i++) {
      const star = createStarSVG(i, i <= rating);
      ratingContainer.appendChild(star);
    }

    // Optional: Text-Label (z. B. „Keine Bewertung“)
    const label = document.getElementById("rating-label");
    if (label) {
      label.textContent = rating > 0 ? `${rating} Sterne` : "Keine Bewertung";
    }
  }

  function saveRating(rating) {
    
    // Hier kann dein fetch/ajax rein
  }

  function saveRating(rating) {
    console.log("Bewertung gespeichert:", rating);
    fetch('/api/save_rating.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        filename: fileName,
        rating: rating
      })
    })
    .then(res => res.json())
    .then(data => {
      console.log("Gespeichert:", data);
    })
    .catch(err => console.error("Fehler beim Speichern:", err));
  }
  

  renderStars(currentRating);

});
