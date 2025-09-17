document.addEventListener('DOMContentLoaded', function () {
    const mobileMenuButton = document.querySelector('[aria-controls="mobile-menu"]');
    const mobileMenu = document.getElementById('mobile-menu');

    const userMenuButton = document.getElementById('user-menu-button');
    const userDropdown = userMenuButton.parentElement.nextElementSibling;

    // Mobilmenü öffnen/schließen
    mobileMenuButton.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
        
        const icons = mobileMenuButton.querySelectorAll('svg');
        icons.forEach(icon => icon.classList.toggle('hidden'));
    });

    // User-Dropdown öffnen/schließen (Desktop)
    userMenuButton.addEventListener('click', () => {
        userDropdown.classList.toggle('hidden');
    });

    // Dropdown schließen, wenn außerhalb geklickt wird
    document.addEventListener('click', (event) => {
        if (!userMenuButton.contains(event.target) && !userDropdown.contains(event.target)) {
            userDropdown.classList.add('hidden');
        }
    });

    // Initialzustände setzen
    mobileMenu.classList.add('hidden');
    userDropdown.classList.add('hidden');
});

// Neue NAVBAR

(() => {
  // ---------- Helpers ----------
  const getTarget = (btn) => {
    const sel = btn.getAttribute('data-collapse-target');
    if (!sel || sel === 'next') {
      let n = btn.nextElementSibling;
      while (n && n.nodeType !== 1) n = n.nextElementSibling;
      return n;
    }
    return document.querySelector(sel);
  };
  const isMobile = () => window.matchMedia('(max-width: 1023.98px)').matches;

  // ---------- Mobile Sidebar öffnen/schließen ----------
  const sidebar = document.getElementById('sidebar');
  const openBtns  = document.querySelectorAll('[command="show-modal"][commandfor="sidebar"]');
  const closeBtns = document.querySelectorAll('[command="close"][commandfor="sidebar"]');

  const openSidebar = () => {
    if (!sidebar) return;
    if (typeof sidebar.showModal === 'function' && !sidebar.open) {
      sidebar.showModal();
    } else {
      sidebar.setAttribute('open', '');
    }
    document.body.classList.add('overflow-hidden');
  };

  const closeSidebar = () => {
    if (!sidebar) return;
    if (sidebar.open && typeof sidebar.close === 'function') {
      sidebar.close();
    } else {
      sidebar.removeAttribute('open');
    }
    document.body.classList.remove('overflow-hidden');
  };

  openBtns.forEach(btn => btn.addEventListener('click', (e) => {
    e.preventDefault();
    openSidebar();
  }));
  closeBtns.forEach(btn => btn.addEventListener('click', (e) => {
    e.preventDefault();
    closeSidebar();
  }));

  // Klick auf Backdrop / außerhalb des Panels schließt
  if (sidebar) {
    const backdrop = sidebar.querySelector('el-dialog-backdrop');
    const overlayContainer = sidebar.querySelector('div.fixed.inset-0.flex');
    const panel = sidebar.querySelector('el-dialog-panel');

    backdrop?.addEventListener('click', closeSidebar);
    overlayContainer?.addEventListener('click', (e) => {
      if (panel && !panel.contains(e.target)) closeSidebar();
    });

    // ESC
    sidebar.addEventListener('cancel', (e) => { e.preventDefault(); closeSidebar(); });

    // Links im mobilen Sidebar schließen die Leiste nach Navigation
    sidebar.querySelectorAll('a[href]').forEach(a => {
      a.addEventListener('click', () => { if (isMobile()) closeSidebar(); });
    });
  }

  // ---------- Submenüs (Nav) toggeln ----------
  // Achtung: NICHT die Kärtchen-Menüs in #image-list anfassen.
  document.querySelectorAll('button[data-collapse-target]').forEach(btn => {
    if (btn.closest('#image-list')) return; // Image-Karten überspringen
    btn.addEventListener('click', () => {
      const panel = getTarget(btn);
      if (!panel) return;
      const expanded = btn.getAttribute('aria-expanded') === 'true';
      btn.setAttribute('aria-expanded', String(!expanded));
      panel.classList.toggle('hidden', expanded);
      const chev = btn.querySelector('[data-chevron]');
      if (chev) chev.classList.toggle('rotate-180', !expanded);
    });
  });

  // (Optional) Beim Resize: Sidebar schließen, wenn aus Mobile rausgesprungen
  window.addEventListener('resize', () => {
    if (!isMobile()) closeSidebar();
  });
})();