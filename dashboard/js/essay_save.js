document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");
  
    form.addEventListener("submit", async function (e) {
      e.preventDefault();
  
      const formData = {
        title: document.getElementById("title").value,
        content: document.getElementById("content").value,
        tags: document.getElementById("tags").value.split(",").map(t => t.trim()).filter(Boolean),
        cover: document.getElementById("cover").value,
        published_at: document.getElementById("published_at").value,
        foldername: document.getElementById("foldername").value,
        is_published: document.getElementById("is_published").getAttribute("aria-checked") === "true"
      };
  
      try {
        const response = await fetch("./backend_api/essay_save.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(formData)
        });
  
        const result = await response.json();
        if (result.success && result.folder) {
            window.location.href = "blog-detail.php?edit=" + encodeURIComponent(result.folder);
          } else {
            alert("Fehler: " + (result.message || 'Unbekannter Fehler'));
          }
      } catch (err) {
        alert("Fehler beim Speichern.");
        console.error(err);
      }
    });
  });
  