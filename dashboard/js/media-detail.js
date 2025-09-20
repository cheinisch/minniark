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