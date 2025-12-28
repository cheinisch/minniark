/* /dashboard/js/media-detail.js */
(() => {
  "use strict";

  const log = (...a) => console.log("[media-detail]", ...a);
  const warn = (...a) => console.warn("[media-detail]", ...a);

  const byId = (id) => document.getElementById(id);

  function getImageFile() {
    // from <meta name="image-file" content="...">
    const meta = document.querySelector('meta[name="image-file"]');
    return (meta?.getAttribute("content") || "").trim();
  }

  function getBase() {
    // you said dashboard lives under /dashboard
    return "/dashboard";
  }

  async function postJson(url, data) {
    const res = await fetch(url, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "Accept": "application/json",
      },
      credentials: "same-origin",
      body: JSON.stringify(data),
    });

    const text = await res.text();
    let json = null;
    try { json = JSON.parse(text); } catch (_) {}

    if (!res.ok) {
      // if server returned html error page, show part of it
      const msg = json?.error || text?.slice(0, 300) || `HTTP ${res.status}`;
      throw new Error(msg);
    }

    return json ?? { ok: true, raw: text };
  }

  function setBusy(btn, busy) {
    if (!btn) return;
    btn.disabled = !!busy;
    btn.classList.toggle("opacity-60", !!busy);
    btn.classList.toggle("cursor-not-allowed", !!busy);
  }

  function closeDialog(id) {
    const d = byId(id);
    if (d && typeof d.close === "function" && d.open) d.close();
  }

  function init() {
    const base = getBase();
    const file = getImageFile();

    log("init", { base, file, edit: !!byId("editImageTextModal"), ai: !!byId("confirmAiModal"), exif: !!byId("editExifModal") });

    if (!file) {
      warn("No image file found (meta[name=image-file] missing/empty). Saves will fail.");
    }

    // ---------- SAVE IMAGE TEXT ----------
    const btnSaveText = byId("save_image_text");
    if (btnSaveText) {
      btnSaveText.addEventListener("click", async (e) => {
        e.preventDefault();
        e.stopPropagation();

        const title = (byId("image-title-input")?.value || "").trim();
        const description = (byId("image-description-input")?.value || "").trim();

        try {
          setBusy(btnSaveText, true);

          const url = `${base}/backend_api/image_save.php`;
          const result = await postJson(url, { file, title, description });

          // update UI
          const titleEl = byId("title");
          const descEl = byId("description");
          if (titleEl) titleEl.textContent = title;
          if (descEl) descEl.innerHTML = description.replace(/\n/g, "<br>");

          log("image saved", result);

          // close dialog (optional)
          closeDialog("editImageTextModal");
        } catch (err) {
          console.error(err);
          alert("Text save failed: " + (err?.message || err));
        } finally {
          setBusy(btnSaveText, false);
        }
      });
    } else {
      warn("Button #save_image_text not found");
    }

    // ---------- SAVE EXIF ----------
    const btnSaveExif = byId("save_metadata");
    if (btnSaveExif) {
      btnSaveExif.addEventListener("click", async (e) => {
        e.preventDefault();
        e.stopPropagation();

        const payload = {
          file,
          camera: (byId("exif-camera-input")?.value || "").trim(),
          lens: (byId("exif-lens-input")?.value || "").trim(),
          aperture: (byId("exif-aperture-input")?.value || "").trim(),
          shutter_speed: (byId("exif-shutter-input")?.value || "").trim(),
          iso: (byId("exif-iso-input")?.value || "").trim(),
          focal_length: (byId("exif-focal-input")?.value || "").trim(),
          date: (byId("exif-date-input")?.value || "").trim(),
          lat: (byId("exif-lat-input")?.value || "").trim(),
          lon: (byId("exif-lon-input")?.value || "").trim(),
        };

        try {
          setBusy(btnSaveExif, true);

          const url = `${base}/backend_api/exif_save.php`;
          const result = await postJson(url, payload);

          // update sidebar view
          const setText = (id, v) => { const el = byId(id); if (el) el.textContent = v; };
          setText("exif-camera", payload.camera);
          setText("exif-lens", payload.lens);
          setText("exif-aperture", payload.aperture);
          setText("exif-shutter", payload.shutter_speed);
          setText("exif-iso", payload.iso);
          setText("exif-focal", payload.focal_length);
          setText("exif-date", payload.date);
          setText("exif-lat", payload.lat);
          setText("exif-lon", payload.lon);

          log("exif saved", result);

          // close dialog (optional)
          closeDialog("editExifModal");
        } catch (err) {
          console.error(err);
          alert("EXIF save failed: " + (err?.message || err));
        } finally {
          setBusy(btnSaveExif, false);
        }
      });
    } else {
      warn("Button #save_metadata not found");
    }

    // ---------- ESC closes any open dialog (optional, handy) ----------
    document.addEventListener("keydown", (e) => {
      if (e.key !== "Escape") return;
      // close topmost (rough)
      const dialogs = ["confirmAiModal", "editImageTextModal", "editExifModal"];
      for (const id of dialogs) {
        const d = byId(id);
        if (d && d.open) {
          e.preventDefault();
          d.close();
          break;
        }
      }
    });

    // ---------- Copy GPS ----------
    const btnCopyGps = byId("copy-gps");
    if (btnCopyGps) {
      btnCopyGps.addEventListener("click", async () => {
        const lat = (byId("exif-lat")?.textContent || "").trim();
        const lon = (byId("exif-lon")?.textContent || "").trim();
        const text = `${lat}, ${lon}`.trim();
        try {
          await navigator.clipboard.writeText(text);
        } catch (_) {
          // fallback
          const ta = document.createElement("textarea");
          ta.value = text;
          document.body.appendChild(ta);
          ta.select();
          document.execCommand("copy");
          ta.remove();
        }
      });
    }
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init, { once: true });
  } else {
    init();
  }
})();
