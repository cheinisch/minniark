document.addEventListener("DOMContentLoaded", () => {
    const editBtn = document.getElementById("edit_metadata");
    const syncBtn = document.getElementById("update-exif");
    const saveBtn = document.getElementById("save_metadata");
    const cancelBtn = document.getElementById("cancel_metadata");
  
    const ratingContainer = document.getElementById("rating-stars");
    const fileName = ratingContainer?.dataset.filename;
  
    if (!editBtn || !syncBtn || !saveBtn || !cancelBtn || !fileName) {
      console.warn("Edit-Buttons oder Dateiname fehlen.");
      return;
    }
  
    let editing = false;
    const originalValues = {};
  
    const makeInput = (text, key) => {
      const input = document.createElement("input");
      input.type = "text";
      input.value = text;
      input.dataset.key = key;
      input.className = "text-sm text-gray-700 border border-gray-300 rounded px-2 py-1 w-48";
      return input;
    };
  
    const toggleEditMode = (enable) => {
      const wrapper = editBtn.closest(".space-y-4");
      const sections = wrapper.querySelectorAll("ul");
  
      sections.forEach(section => {
        section.querySelectorAll("li").forEach(li => {
          const label = li.querySelector("span:first-child");
          const value = li.querySelector("span:last-child");
  
          if (!label || !value || li.querySelector("div") || li.querySelector("#rating-stars")) return;
  
          const key = label.textContent.replace(":", "").trim().toLowerCase().replace(/\s+/g, '_');
  
          if (enable) {
            originalValues[key] = value.textContent.trim();
            const input = makeInput(value.textContent.trim(), key);
            value.innerHTML = "";
            value.appendChild(input);
          } else {
            const input = value.querySelector("input");
            value.innerHTML = input?.value || originalValues[key] || "";
          }
        });
      });
  
      // Button Sichtbarkeit umschalten
      document.getElementById("button_group_meta").classList.toggle("hidden", enable);
      document.getElementById("button_group_meta_manual").classList.toggle("hidden", !enable);
  
      cancelBtn.classList.toggle("invisible", !enable);
      editing = enable;
    };
  
    editBtn.addEventListener("click", () => {
      toggleEditMode(true);
    });
  
    cancelBtn.addEventListener("click", () => {
      const inputs = document.querySelectorAll("input[data-key]");
      inputs.forEach(input => {
        const key = input.dataset.key;
        const original = originalValues[key] || "";
        const span = input.parentElement;
        if (span) span.innerHTML = original;
      });
  
      toggleEditMode(false);
    });
  
    saveBtn.addEventListener("click", () => {
      const inputs = document.querySelectorAll("input[data-key]");
      const exifKeys = [
        "camera", "lens", "aperture", "shutter_speed",
        "iso", "focal_length", "date"
      ];
  
      const payload = {
        filename: fileName,
        exif: {},
        gps: {}
      };
  
      inputs.forEach(input => {
        const key = input.dataset.key;
        const value = input.value.trim();
  
        if (key === "lat") {
          payload.gps.latitude = value;
        } else if (key === "lon") {
          payload.gps.longitude = value;
        } else if (exifKeys.includes(key)) {
          const formattedKey = key.replace(/_/g, " ").replace(/\b\w/g, c => c.toUpperCase());
          payload.exif[formattedKey] = value;
        }
      });
  
      fetch(`${window.location.origin}/api/update_metadata.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            toggleEditMode(false);
            alert("Metadaten gespeichert.");
          } else {
            alert("Fehler: " + (data.error || "Unbekannter Fehler"));
          }
        })
        .catch(err => {
          console.error("Fehler beim Speichern:", err);
          alert("Netzwerkfehler beim Speichern.");
        });
    });
  
    syncBtn.addEventListener("click", () => {
      fetch(`${window.location.origin}/api/update_exif.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ filename: fileName })
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            alert("EXIF-Daten erfolgreich synchronisiert.");
            location.reload();
          } else {
            alert("Fehler beim Sync: " + (data.error || "Unbekannter Fehler"));
          }
        })
        .catch(err => {
          console.error("Netzwerkfehler beim Sync:", err);
          alert("Netzwerkfehler beim Sync.");
        });
    });
  });
  