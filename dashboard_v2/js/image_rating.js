document.addEventListener("DOMContentLoaded", () => {
    const ratingContainer = document.getElementById("rating-stars");
    if (!ratingContainer) {
      console.error("❌ #rating-stars nicht gefunden");
      return;
    }
  
    let currentRating = 3;
    const maxRating = 5;
  
    function createStarSVG(index, filled) {
      const span = document.createElement("span");
      span.innerHTML = `
        <svg class="w-5 h-5 cursor-pointer ${filled ? 'text-sky-400' : 'text-gray-300'}"
             viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
          <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.137 3.5h3.684
                   c.969 0 1.371 1.24.588 1.81l-2.984 2.17 1.138 3.5
                   c.3.921-.755 1.688-1.538 1.117L10 13.348l-2.976 2.176
                   c-.783.571-1.838-.196-1.538-1.117l1.138-3.5-2.984-2.17
                   c-.783-.57-.38-1.81.588-1.81h3.684l1.137-3.5z"/>
        </svg>
      `;
      const star = span.firstElementChild;
      star.addEventListener("click", () => {
        if (currentRating === index) {
          currentRating = 0; // deselect if clicked again
        } else {
          currentRating = index;
        }
        renderStars(currentRating);
        saveRating(currentRating);
      });
      return star;
    }
  
    function renderStars(rating) {
      ratingContainer.innerHTML = "";
      for (let i = 1; i <= maxRating; i++) {
        const star = createStarSVG(i, i <= rating);
        ratingContainer.appendChild(star);
      }
    }
  
    function saveRating(rating) {
      console.log("⭐ Bewertung gespeichert:", rating);
      // Optional: AJAX hier einfügen
    }
  
    renderStars(currentRating);
  });
  