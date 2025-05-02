document.addEventListener("DOMContentLoaded", function () {
    console.log("Essay-Save.js geladen");
  
    const form = document.getElementById("essayForm");
    if (!form) {
      console.error("Formular mit ID 'essayForm' nicht gefunden.");
      return;
    }
  
    form.addEventListener("submit", async function (e) {
      e.preventDefault();
      console.log("Formular wurde über JS abgefangen.");
  
      const title = document.getElementById("title").value;
      const content = document.getElementById("content").value;
      const tagsRaw = document.getElementById("tags").value;
      const cover = document.getElementById("cover").value;
      const publishedAt = document.getElementById("published_at").value;
      const foldername = document.getElementById("foldername").value;
      const originalFolder = document.getElementById("original_foldername").value;
      const isPublished = document.getElementById("is_published").getAttribute("aria-checked") === "true";
  
      const formData = {
        title,
        content,
        tags: tagsRaw.split(",").map(t => t.trim()).filter(Boolean),
        cover,
        published_at: publishedAt,
        foldername,
        original_foldername: originalFolder,
        is_published: isPublished
      };
  
      try {
        const response = await fetch("./backend_api/essay_save.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify(formData)
        });
  
        const result = await response.json();
        if (result.success && result.folder) {
          window.location.href = "blog-detail.php?edit=" + encodeURIComponent(result.folder);
        } else {
          alert("Fehler: " + (result.message || "Unbekannter Fehler"));
        }
      } catch (error) {
        console.error("Fehler beim Speichern:", error);
        alert("Beim Speichern ist ein Fehler aufgetreten.");
      }
    });
  });
  