
/** kleine Helfer **/
async function postJSON(url, payload) {
  const resp = await fetch(url, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(payload)
  });
  const text = await resp.text();           // robust ggü. PHP-Warnungen o.ä.
  try { return JSON.parse(text); } catch {  // fallback
    console.warn("Raw response:", text);
    return { success: false, error: "Bad JSON from server", raw: text };
  }
}
function showResult(ok, okEl, errEl) {
  if (ok) { okEl?.classList.remove("hidden"); errEl?.classList.add("hidden"); }
  else    { errEl?.classList.remove("hidden"); okEl?.classList.add("hidden"); }
}
function getSwitchState(id) {
  const el = document.getElementById(id);
  if (!el) return false;
  // akzeptiere "true"/"false" und "1"/"0"
  const v = el.getAttribute("aria-checked");
  return v === "true" || v === "1";
}

/** --- Site Information (Name, Description, Language) --- **/
document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("change-sitedata-form");
  if (!form) return;

  const successEl = document.getElementById("notification-success-user");
  const errorEl   = document.getElementById("notification-error-user");

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    const fd = new FormData(form);

    // Sprache robust ermitteln (neu: select, fallback: hidden, ganz alt: Listbox-Text)
    const langSelect = document.getElementById("language-select");
    const hiddenLang = document.getElementById("selected-language");
    const listboxBtn = document.querySelector('[aria-haspopup="listbox-language"]');
    const listboxTxt = listboxBtn?.querySelector("span.truncate");

    const language =
      (langSelect && langSelect.value) ||
      (hiddenLang && hiddenLang.value) ||
      (listboxTxt && listboxTxt.textContent.trim()) ||
      "en";

    const data = {
      site_name:        fd.get("site-name") || "",
      site_description: fd.get("site-decription") || "",
      language
    };

    const result = await postJSON("../api/change_sitesettings.php", data);
    showResult(!!result.success, successEl, errorEl);
    if (result.success) {
      setTimeout(() => {
        location.reload();
      }, 5000);
    }
  });
});

/** --- Sitemap --- **/
document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("change-sitemap-form");
  if (!form) return;

  const successEl = document.getElementById("notification-sitemap-success");
  const errorEl   = document.getElementById("notification-sitemap-error");

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    const data = {
      sitemap_enable:         getSwitchState("sitemap_enable"),
      sitemap_images_enable:  getSwitchState("sitemap_images_enable")
    };
    const result = await postJSON("../api/change_sitesettings.php", data);
    showResult(!!result.success, successEl, errorEl);
  });
});

/** --- Image size --- **/
document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("change-image-size");
  if (!form) return;

  const successEl = document.getElementById("notification-success");
  const errorEl   = document.getElementById("notification-error");

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    // neu: select zuerst, fallback: hidden
    const imgSelect = document.getElementById("image-size-select");
    const imgHidden = document.getElementById("image_size");
    const image_size = (imgSelect && imgSelect.value) || (imgHidden && imgHidden.value) || "L";

    const result = await postJSON("../api/change_sitesettings.php", { image_size });
    showResult(!!result.success, successEl, errorEl);
  });
});

/** --- Timeline --- **/
document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("change-timeline-form");
  if (!form) return;

  const successEl = document.getElementById("notification-timeline-success");
  const errorEl   = document.getElementById("notification-timeline-error");

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    const data = {
      timeline_enable:        getSwitchState("timline_enable"),
      timeline_group_by_date: getSwitchState("timline_group")
    };
    const result = await postJSON("../api/change_sitesettings.php", data);
    showResult(!!result.success, successEl, errorEl);
  });
});

/** --- Map --- **/
document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("change-map-form");
  if (!form) return;

  const successEl = document.getElementById("notification-map-success");
  const errorEl   = document.getElementById("notification-map-error");

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    const data = { map_enable: getSwitchState("map_enable") };
    const result = await postJSON("../api/change_sitesettings.php", data);
    showResult(!!result.success, successEl, errorEl);
  });
});

/** --- Theme (falls vorhanden) --- **/
document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("change-theme");
  if (!form) return;

  const successEl = document.getElementById("notification-theme-success");
  const errorEl   = document.getElementById("notification-theme-error");
  const hiddenTheme = document.getElementById("theme");

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    const data = { theme: (hiddenTheme?.value || "").trim() };
    const result = await postJSON("../api/change_sitesettings.php", data);
    showResult(!!result.success, successEl, errorEl);
  });
});