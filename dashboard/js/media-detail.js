/*
 * --------------------------
 * COPY LAT & LOM
 * --------------------------
 */

console.log('[gps-copy] script tag parsed');

(function () {
  const restoreMs = 1500;

  function attach() {
    const btn   = document.getElementById('copy-gps');
    const latEl = document.getElementById('exif-lat');
    const lonEl = document.getElementById('exif-lon');

    if (!btn || !latEl || !lonEl) {
      console.warn('[gps-copy] missing elements:', { btn: !!btn, lat: !!latEl, lon: !!lonEl });
      // Wir nutzen unten Delegation, daher kein return nötig – aber Log hilft beim Debuggen.
    }

    // Original-HTML puffern (falls vorhanden)
    if (btn && !btn.dataset.originalHtml) btn.dataset.originalHtml = btn.innerHTML;

    // Delegation: funktioniert auch, wenn #copy-gps später ins DOM kommt
    document.addEventListener('click', async (e) => {
      const target = e.target.closest('#copy-gps');
      if (!target) return;

      console.log('[gps-copy] Button gedrückt');

      e.preventDefault();

      const latNode = document.getElementById('exif-lat');
      const lonNode = document.getElementById('exif-lon');

      const lat = latNode?.textContent?.trim();
      const lon = lonNode?.textContent?.trim();

      if (!lat || !lon) {
        setLabel(target, 'No coords');
        console.error('[gps-copy] No coords found', { lat, lon });
        return;
      }

      const text = `${lat}, ${lon}`;

      try {
        if (navigator.clipboard?.writeText) {
          await navigator.clipboard.writeText(text);
          setLabel(target, 'Copied');
          console.log('[gps-copy] Copied via Clipboard API:', text);
        } else {
          throw new Error('Clipboard API unavailable');
        }
      } catch (err) {
        try {
          const ta = document.createElement('textarea');
          ta.value = text;
          ta.setAttribute('readonly', '');
          ta.style.position = 'fixed';
          ta.style.left = '-9999px';
          document.body.appendChild(ta);
          ta.select();
          document.execCommand('copy');
          document.body.removeChild(ta);
          setLabel(target, 'Copied');
          console.log('[gps-copy] Copied via fallback:', text);
        } catch (err2) {
          console.error('[gps-copy] Copy failed', err, err2);
          setLabel(target, 'Failed');
        }
      }
    });
  }

  function setLabel(btn, html) {
    if (!btn.dataset.originalHtml) btn.dataset.originalHtml = btn.innerHTML;
    btn.innerHTML = html;
    btn.disabled = true;
    setTimeout(() => {
      btn.innerHTML = btn.dataset.originalHtml;
      btn.disabled = false;
    }, restoreMs);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
      console.log('[gps-copy] DOMContentLoaded');
      attach();
    });
  } else {
    console.log('[gps-copy] DOM already ready');
    attach();
  }
})();

/*
 * --------------------------
 * OPEN GENERATE AI TEXT MODAL
 * --------------------------
 */

(function () {
  const modal   = document.getElementById('confirmAiModal');
  const openBtn = document.getElementById('generate_text');
  const closeBtn= document.getElementById('aiTextClose');
  const cancel  = document.getElementById('confirmNo');
  if (!modal || !openBtn) return;

  // Backdrop-Element (das mit fixed inset-0)
  const backdrop = modal.querySelector('.fixed.inset-0');

  // Z-Index sicherstellen (falls im HTML nicht gesetzt)
  modal.classList.add('z-50');

  let prevActive = null;

  function lockScroll() {
    document.documentElement.classList.add('overflow-hidden');
    document.body.classList.add('overflow-hidden');
  }
  function unlockScroll() {
    document.documentElement.classList.remove('overflow-hidden');
    document.body.classList.remove('overflow-hidden');
  }

  function openModal() {
    prevActive = document.activeElement;
    modal.classList.remove('hidden');
    modal.removeAttribute('aria-hidden');
    modal.setAttribute('aria-modal', 'true');
    lockScroll();

    // Fokus in den Dialog setzen (Titel oder erster Button)
    const firstFocusable = modal.querySelector('button, [href], [tabindex]:not([tabindex="-1"])');
    (firstFocusable || modal).focus();
  }

  function closeModal() {
    modal.classList.add('hidden');
    modal.setAttribute('aria-hidden', 'true');
    modal.removeAttribute('aria-modal');
    unlockScroll();

    // Fokus zurück
    if (prevActive && typeof prevActive.focus === 'function') prevActive.focus();
  }

  // Öffnen
  openBtn.addEventListener('click', (e) => {
    e.preventDefault();
    openModal();
  });

  // Schließen: Close-Icon & Cancel-Button
  closeBtn?.addEventListener('click', (e) => { e.preventDefault(); closeModal(); });
  cancel?.addEventListener('click', (e) => { e.preventDefault(); closeModal(); });

  // Schließen: Klick auf Backdrop (außerhalb des Panels)
  modal.addEventListener('click', (e) => {
    // Wenn man direkt auf den äußeren Wrapper (modal) ODER den Backdrop klickt, schließen
    if (e.target === modal || e.target === backdrop) closeModal();
  });

  // ESC schließt
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
      e.preventDefault();
      closeModal();
    }
  });
})();

/*
 * --------------------------
 * OPEN EDIT EXIF MODAL
 * --------------------------
 */
(function () {
  const modal   = document.getElementById('editExifModal');
  if (!modal) return;

  const cancel  = modal.querySelector('#cancel_metadata');
  const backdrop= modal.querySelector('.fixed.inset-0');
  const closeX  = modal.querySelector('#editExifClose');

  function lockScroll(){ document.documentElement.classList.add('overflow-hidden'); document.body.classList.add('overflow-hidden'); }
  function unlockScroll(){ document.documentElement.classList.remove('overflow-hidden'); document.body.classList.remove('overflow-hidden'); }

  function openModal() {
    modal.classList.remove('hidden');
    lockScroll();
  }
  function closeModal() {
    modal.classList.add('hidden');
    unlockScroll();
  }

  // Falls du es noch nicht hast: Öffnen über den EXIF-"Edit"-Button
  const trigger = document.getElementById('edit-exif');
  trigger?.addEventListener('click', (e) => { e.preventDefault(); openModal(); });

  // X und Backdrop schließen
  closeX?.addEventListener('click', (e) => { e.preventDefault(); closeModal(); });
  backdrop?.addEventListener('click', (e) => { e.preventDefault(); closeModal(); });

  // 👉 Cancel schließt nur das Modal (unterbindet andere Handler)
  cancel?.addEventListener('click', (e) => {
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();
    closeModal();
  });

  // ESC schließt
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
      e.preventDefault();
      closeModal();
    }
  });
})();

/*
 * --------------------------
 * SAVE NEW EXIF DATA
 * --------------------------
 */

document.addEventListener("DOMContentLoaded", () => {
  // Modal-Handling
  const modal = document.getElementById("editExifModal");
  const backdrop = modal?.querySelector(".fixed.inset-0");

  const lockScroll = () => {
    document.documentElement.classList.add("overflow-hidden");
    document.body.classList.add("overflow-hidden");
  };
  const unlockScroll = () => {
    document.documentElement.classList.remove("overflow-hidden");
    document.body.classList.remove("overflow-hidden");
  };
  const isModalOpen = () => modal && !modal.classList.contains("hidden");
  const closeModal = () => {
    if (!modal) return;
    modal.classList.add("hidden");
    unlockScroll();
  };

  // Buttons (einige existieren nur im Modal)
  const editBtn   = document.getElementById("edit_metadata");   // kann fehlen (Inline-Flow)
  const syncBtn   = document.getElementById("update-exif");
  const saveBtn   = document.getElementById("save_metadata");
  const cancelBtn = document.getElementById("cancel_metadata");

  // Dateiname aus vorhandenem Container
  const ratingContainer = document.getElementById("rating-stars");
  const fileName = ratingContainer?.dataset.filename;

  // Guard: editBtn NICHT mehr zwingend erforderlich
  if (!syncBtn || !saveBtn || !cancelBtn || !fileName) {
    console.warn("Buttons oder Dateiname fehlen.");
    return;
  }

  let editing = false;
  const originalValues = {};

  // Nur für Inline-Edit benötigt:
  const makeInput = (text, key) => {
    const input = document.createElement("input");
    input.type = "text";
    input.value = text;
    input.dataset.key = key;
    input.className = "text-sm text-gray-700 border border-gray-300 rounded px-2 py-1 w-48";
    return input;
  };

  const toggleEditMode = (enable) => {
    if (!editBtn) return; // im Modal nicht nötig
    const wrapper = editBtn.closest(".space-y-4");
    if (!wrapper) return;
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

    document.getElementById("button_group_meta")?.classList.toggle("hidden", enable);
    document.getElementById("button_group_meta_manual")?.classList.toggle("hidden", !enable);

    cancelBtn.classList.toggle("invisible", !enable);
    editing = enable;
  };

  // Inline-Edit nur, wenn es den Button gibt
  editBtn?.addEventListener("click", () => {
    toggleEditMode(true);
  });

  // Cancel: im Modal => nur schließen; sonst (Inline) ursprüngliche Werte wiederherstellen und Edit-Ende
  cancelBtn.addEventListener("click", (e) => {
    e.preventDefault();
    e.stopPropagation();

    if (isModalOpen()) {
      closeModal();
      return;
    }

    // Inline-Flow
    const inputs = document.querySelectorAll("input[data-key]");
    inputs.forEach(input => {
      const key = input.dataset.key;
      const original = originalValues[key] || "";
      const span = input.parentElement;
      if (span) span.innerHTML = original;
    });
    toggleEditMode(false);
  });

  // SAVE: Payload bauen, Formular abschicken, Modal schließen
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

    // Modal sofort schließen (UI-Feedback), bevor die Seite evtl. navigiert
    if (isModalOpen()) closeModal();

    // Unsichtbares Formular im gleichen Fenster öffnen
    const form = document.createElement("form");
    form.method = "POST";
    form.action = `backend_api/update_image.php?type=exif`;
    form.style.display = "none";

    const input = document.createElement("input");
    input.type = "hidden";
    input.name = "data";
    input.value = JSON.stringify(payload);
    form.appendChild(input);

    document.body.appendChild(form);
    form.submit();
    form.remove();
  });

  // SYNC: unverändert; optional Modal schließen, um konsistent zu sein
  /*syncBtn.addEventListener("click", () => {
    fetch(`${window.location.origin}/backend_api/update_image.php?type=exif`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ filename: fileName })
    });
    if (isModalOpen()) closeModal();
    alert("EXIF-Sync gestartet.");
  });*/

  // Falls dein Modal separat geöffnet wird, hier noch ESC/Backdrop schließt (optional; falls nicht schon vorhanden)
  if (modal && backdrop) {
    backdrop.addEventListener("click", (e) => { e.preventDefault(); closeModal(); });
    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape" && isModalOpen()) {
        e.preventDefault();
        closeModal();
      }
    });
  }
});

/*
 * --------------------------
 * SYNC EXIF DATA
 * --------------------------
 */
document.addEventListener("DOMContentLoaded", () => {
  console.log("Add Eventlistener");

  const btn = document.getElementById("update-exif");
  if (!btn) { console.warn("sync_exifdata: #update-exif nicht gefunden."); return; }
  if (btn.dataset.bound === "true") return;
  btn.dataset.bound = "true";

  const ratingContainer = document.getElementById("rating-stars");
  const fileName = ratingContainer?.dataset.filename || null;

  // Modal & Inputs
  const modal = document.getElementById("editExifModal");
  const q = (key) => modal?.querySelector(`input[data-key="${key}"]`);

  // 🔧 Modal schließen + Scroll entsperren
  const closeModal = () => {
    if (!modal) return;
    modal.classList.add("hidden");
    document.documentElement.classList.remove("overflow-hidden");
    document.body.classList.remove("overflow-hidden");
  };

  const setBusy = (busy, labelWhenDone) => {
    if (busy) {
      btn.dataset.label = btn.dataset.label || btn.textContent.trim();
      btn.disabled = true;
      btn.textContent = "Syncing…";
    } else {
      btn.disabled = false;
      btn.textContent = labelWhenDone ?? btn.dataset.label ?? "Sync EXIF";
      if (!labelWhenDone && btn.dataset.label) btn.textContent = btn.dataset.label;
    }
  };

  const mapExifKey = (k) => {
    if (!k) return null;
    const n = k.toString().trim().toLowerCase().replace(/\s+/g, "_");
    switch (n) {
      case "camera": return "camera";
      case "lens": return "lens";
      case "aperture": return "aperture";
      case "shutter_speed": return "shutter_speed";
      case "iso": return "iso";
      case "focal_length": return "focal_length";
      case "date": return "date";
      default: return null;
    }
  };

  const fillInputsFromResponse = (data) => {
    if (data?.exif && typeof data.exif === "object") {
      Object.entries(data.exif).forEach(([k, v]) => {
        const key = mapExifKey(k);
        if (!key) return;
        const input = q(key);
        if (input) input.value = (v ?? "").toString();
      });
    }
    const lat = data?.gps?.latitude ?? data?.gps?.lat ?? data?.latitude ?? null;
    const lon = data?.gps?.longitude ?? data?.gps?.lon ?? data?.longitude ?? null;
    if (lat !== null && q("lat")) q("lat").value = lat;
    if (lon !== null && q("lon")) q("lon").value = lon;
  };

  btn.addEventListener("click", () => {
    if (!fileName) {
      alert("Kein Dateiname gefunden.");
      console.log("No File Name");
      return;
    }

    setBusy(true);

    fetch(`${window.location.origin}/api/sync_exif.php`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ filename: fileName })
    })
      .then(async (res) => {
        const text = await res.text();
        try { return JSON.parse(text); }
        catch { console.error("Kein valides JSON:", text); throw new Error("Ungültige Serverantwort"); }
      })
      .then((data) => {
        if (data?.success) {
          fillInputsFromResponse(data);
          setBusy(false, "Synced");
          //  Modal direkt schließen (oder mit kurzer Verzögerung)
          setBusy(false, "Synced");
          setTimeout(() => location.reload(), 300);
        } else {
          alert("Fehler: " + (data?.error || "Unbekannter Fehler"));
          setBusy(false);
        }
      })
      .catch((err) => {
        console.error("Netzwerkfehler:", err);
        alert("Netzwerkfehler beim Laden der EXIF-Daten.");
        setBusy(false);
      });
  });
});

/*
 * --------------------------
 * SAVE NEW TEXT
 * --------------------------
 */


document.addEventListener("DOMContentLoaded", () => {
  // Modal & UI
  const modal        = document.getElementById("editImageTextModal");
  const backdrop     = modal?.querySelector(".fixed.inset-0");
  const trigger      = document.getElementById("edit_text");
  const closeX       = document.getElementById("close_edit_image_text");
  const saveBtn      = document.getElementById("save_image_text");
  const cancelBtn    = document.getElementById("cancel_image_text");

  const titleView    = document.getElementById("title");        // sichtbarer Titel
  const descView     = document.getElementById("description");  // sichtbare Beschreibung
  const titleInput   = document.getElementById("image-title-input");
  const descInput    = document.getElementById("image-description-input");

  const ratingContainer = document.getElementById("rating-stars");
  const fileName    = ratingContainer?.dataset.filename || null;

  if (!modal || !trigger || !saveBtn || !cancelBtn || !titleInput || !descInput) {
    console.warn("Edit-Text: erforderliche UI-Elemente nicht gefunden.");
    return;
  }

  const lockScroll   = () => { document.documentElement.classList.add("overflow-hidden"); document.body.classList.add("overflow-hidden"); };
  const unlockScroll = () => { document.documentElement.classList.remove("overflow-hidden"); document.body.classList.remove("overflow-hidden"); };
  const isOpen       = () => !modal.classList.contains("hidden");
  const openModal    = () => { 
    // Felder vorbefüllen
    titleInput.value = (titleView?.textContent || "").trim();
    // description wurde serverseitig via nl2br gerendert → textContent holt reine Zeilenumbrüche
    descInput.value  = (descView?.textContent || "").trim();
    modal.classList.remove("hidden"); lockScroll();
    titleInput.focus();
    // Cursor ans Ende
    const L = titleInput.value.length; titleInput.setSelectionRange(L, L);
    autoResize(descInput);
  };
  const closeModal   = () => { modal.classList.add("hidden"); unlockScroll(); trigger?.focus?.(); };

  // Auto-Resize für Textarea
  const autoResize = (ta) => { ta.style.height = "auto"; ta.style.height = ta.scrollHeight + "px"; };
  descInput.addEventListener("input", () => autoResize(descInput));

  // Öffnen
  trigger.addEventListener("click", (e) => { e.preventDefault(); openModal(); });

  // Schließen
  closeX?.addEventListener("click", (e) => { e.preventDefault(); closeModal(); });
  backdrop?.addEventListener("click", (e) => { e.preventDefault(); closeModal(); });
  cancelBtn.addEventListener("click", (e) => { e.preventDefault(); closeModal(); });
  document.addEventListener("keydown", (e) => { if (e.key === "Escape" && isOpen()) { e.preventDefault(); closeModal(); } });

  // SAVE → wie in deinem Beispiel via fetch(JSON) an /api/update_text.php
  saveBtn.addEventListener("click", async () => {
    const newTitle = titleInput.value.trim();
    const newDescription = descInput.value.trim();

    if (!fileName) {
      console.warn("Kein Dateiname (data-filename in #rating-stars fehlt) – Abbruch.");
      closeModal();
      return;
    }

    // Button sperren gegen Doppelklick
    saveBtn.disabled = true;

    try {
      const res = await fetch("/api/update_text.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ filename: fileName, title: newTitle, description: newDescription })
      });

      const data = await res.json().catch(() => ({ success: false, error: "Invalid JSON" }));

      if (data?.success) {
        // UI aktualisieren (Description mit <br>, damit es wie nl2br aussieht)
        if (titleView) titleView.textContent = newTitle;
        if (descView)  descView.innerHTML   = newDescription.replace(/\n/g, "<br>");

        closeModal();
      } else {
        alert("Fehler beim Speichern: " + (data?.error || "Unbekannter Fehler"));
      }
    } catch (err) {
      console.error("Netzwerkfehler:", err);
      alert("Netzwerkfehler beim Speichern.");
    } finally {
      saveBtn.disabled = false;
    }
  });
});