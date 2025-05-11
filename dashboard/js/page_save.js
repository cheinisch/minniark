document.addEventListener("DOMContentLoaded", function () {
    console.log("Page-Save.js geladen");
  
    const form = document.getElementById("pageForm");
    if (!form) {
      console.error("Formular mit ID 'essayForm' nicht gefunden.");
      return;
    }
  
    form.addEventListener("submit", async function (e) {
      e.preventDefault();

      if (window.easyMDE) {
        document.getElementById("content").value = window.easyMDE.value();
      } else {
        console.warn("EasyMDE nicht gefunden Inhalte werden möglicherweise nicht gespeichert.");
      }

      console.log("Formular wurde über JS abgefangen.");
  
      const title = document.getElementById("title").value;
      const content = document.getElementById("content").value;
      const cover = document.getElementById("cover").value;
      const foldername = document.getElementById("foldername").value;
      const originalFolder = document.getElementById("original_foldername").value;
      const isPublished = document.getElementById("is_published").getAttribute("aria-checked") === "true";
  
      const formData = {
        title,
        content,
        cover,
        foldername,
        original_foldername: originalFolder,
        is_published: isPublished
      };
  
      try {
        const response = await fetch("./backend_api/page_save.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify(formData)
        });
  
        const result = await response.json();
        if (result.success && result.folder) {
          window.location.href = "page-detail.php?edit=" + encodeURIComponent(result.folder);
        } else {
          alert("Fehler: " + (result.message || "Unbekannter Fehler"));
        }
      } catch (error) {
        console.error("Fehler beim Speichern:", error);
        alert("Beim Speichern ist ein Fehler aufgetreten.");
      }
    });
  });
  