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