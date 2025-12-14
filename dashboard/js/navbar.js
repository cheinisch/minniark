(() => {
  // --- Sidebars (Desktop + Mobile <dialog>) ---------------------------------
  const desktopSidebar = document.querySelector('.lg\\:fixed.lg\\:inset-y-0');
  const mobileSidebar  = document.getElementById('sidebar'); // <dialog>

  const sidebars = [desktopSidebar, mobileSidebar].filter(Boolean);

  // --- helpers ---------------------------------------------------------------
  function isInSidebar(el) {
    return sidebars.some(sb => sb.contains(el));
  }

  function getPanel(btn) {
    // data-collapse-target="next" => next element sibling (UL)
    let n = btn.nextElementSibling;
    while (n && n.nodeType === 1) {
      if (n.tagName === 'UL') return n;
      n = n.nextElementSibling;
    }
    return null;
  }

  function setOpen(btn, open) {
    btn.setAttribute('aria-expanded', String(open));

    const panel = getPanel(btn);
    if (panel) panel.classList.toggle('hidden', !open);

    const chev = btn.querySelector('[data-chevron]');
    if (chev) chev.classList.toggle('rotate-180', open);
  }

  function closeSiblings(btn) {
    // Nur Buttons auf derselben Ebene schließen
    const parentUl = btn.closest('li')?.parentElement; // das UL, das die LI enthält
    if (!parentUl) return;

    parentUl.querySelectorAll(':scope > li > button[data-collapse-target][aria-expanded="true"]').forEach(b => {
      if (b !== btn) setOpen(b, false);
    });
  }

  // --- Click toggle ----------------------------------------------------------
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('button[data-collapse-target]');
    if (!btn) return;
    if (!isInSidebar(btn)) return;

    const panel = getPanel(btn);
    if (!panel) {
      console.warn('[NAV] No panel (UL) found for button:', btn);
      return;
    }

    e.preventDefault();

    const isOpen = btn.getAttribute('aria-expanded') === 'true';

    closeSiblings(btn);
    setOpen(btn, !isOpen);

    // Debug (kannst du später löschen)
    console.log('[NAV] toggle', { isOpen, nowOpen: !isOpen, panelId: panel.id, panelClass: panel.className });
  }, true);

  // --- Outside click closes all ---------------------------------------------
  document.addEventListener('click', (e) => {
    sidebars.forEach(sb => {
      const inside = sb.contains(e.target);
      if (inside) return;

      sb.querySelectorAll('button[data-collapse-target][aria-expanded="true"]').forEach(b => setOpen(b, false));
    });
  }, true);

  // --- ESC closes all --------------------------------------------------------
  document.addEventListener('keydown', (e) => {
    if (e.key !== 'Escape') return;
    sidebars.forEach(sb => {
      sb.querySelectorAll('button[data-collapse-target][aria-expanded="true"]').forEach(b => setOpen(b, false));
    });
  });
})();
