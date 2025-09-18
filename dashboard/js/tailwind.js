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

(() => {
  function setup(dd){
    const btn  = dd.querySelector('button');
    const menu = dd.querySelector('[data-menu]');
    if (!btn || !menu) return;

    // Defaults (inline, damit kein CSS angefasst werden muss)
    dd.style.position ||= 'relative';
    menu.style.minWidth ||= '8rem';
    menu.style.zIndex = '1000';

    const hasPopover = menu.hasAttribute('popover') && typeof menu.showPopover === 'function';

    const closeOthers = () => {
      document.querySelectorAll('[data-dropdown] [data-menu].is-open').forEach(m => {
        if (m === menu) return;
        if (m.matches?.(':popover-open')) m.hidePopover?.();
        m.classList.remove('is-open');
        m.hidden = true;
        m.closest('[data-dropdown]')?.querySelector('button')?.setAttribute('aria-expanded','false');
      });
    };

    const onDocDown = (e) => {
      if (!dd.contains(e.target) && !menu.contains(e.target)) hide();
    };
    const onKey = (e) => { if (e.key === 'Escape') hide(); };

    function trap(){
      document.addEventListener('mousedown', onDocDown, true);
      document.addEventListener('touchstart', onDocDown, {passive:true, capture:true});
      document.addEventListener('keydown', onKey, true);
    }
    function untrap(){
      document.removeEventListener('mousedown', onDocDown, true);
      document.removeEventListener('touchstart', onDocDown, true);
      document.removeEventListener('keydown', onKey, true);
    }

    function computePos(){
      // Fallback-Positionierung ohne Popover
      menu.style.position = 'absolute';
      menu.style.right = '0';
      const br = btn.getBoundingClientRect();
      const dy = window.scrollY || document.documentElement.scrollTop || 0;
      const top = br.bottom + dy + 8;
      const ddTop = dd.getBoundingClientRect().top + dy;
      menu.style.top = (top - ddTop) + 'px';
    }

    function positionPopover(){
      // Popover oben im Top-Layer korrekt platzieren
      menu.style.position = 'fixed';
      menu.style.inset = 'auto';
      const br = btn.getBoundingClientRect();
      const mr = menu.getBoundingClientRect();
      const left = Math.max(8, Math.min(br.right - mr.width, window.innerWidth - mr.width - 8));
      const top  = Math.min(br.bottom + 8, window.innerHeight - mr.height - 8);
      menu.style.left = left + 'px';
      menu.style.top  = top  + 'px';
    }

    function show(){
      closeOthers();
      btn.setAttribute('aria-expanded', 'true');
      if (hasPopover) {
        if (!menu.matches(':popover-open')) menu.showPopover();
        requestAnimationFrame(positionPopover);
      } else {
        computePos();
        menu.hidden = false;
      }
      menu.classList.add('is-open');
      trap();
    }

    function hide(){
      btn.setAttribute('aria-expanded', 'false');
      if (hasPopover && menu.matches(':popover-open')) {
        menu.hidePopover();
      } else {
        menu.hidden = true;
      }
      menu.classList.remove('is-open');
      untrap();
    }

    // Events
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation(); // verhindert "direkt wieder schließen"
      const open = menu.classList.contains('is-open')
                || (hasPopover && menu.matches(':popover-open'))
                || (!hasPopover && !menu.hidden);
      open ? hide() : show();
    });

    menu.addEventListener('click', (e) => {
      if (e.target.closest('[role="menuitem"]')) hide();
    });

    // Reposition bei Scroll/Resize nur wenn offen
    const onMove = () => {
      if (hasPopover) { if (menu.matches(':popover-open')) positionPopover(); }
      else { if (menu.classList.contains('is-open')) computePos(); }
    };
    window.addEventListener('resize', onMove, { passive: true });
    window.addEventListener('scroll',  onMove, { passive: true, capture: true });
  }

  function init(){ document.querySelectorAll('[data-dropdown]').forEach(setup); }
  (document.readyState === 'loading') ? document.addEventListener('DOMContentLoaded', init) : init();
})();



(() => {
  // Alle el-dropdowns aktivieren
  document.querySelectorAll('el-dropdown').forEach(dd => {
    const btn = dd.querySelector('button');
    const menu = dd.querySelector('el-menu');
    if (!btn || !menu) return;

    // Öffnen/Schließen per Klick
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      const isOpen = !menu.classList.contains('hidden');
      document.querySelectorAll('el-dropdown el-menu:not(.hidden)').forEach(m => { // andere Dropdowns schließen
        if (m !== menu) {
          m.classList.add('hidden');
          const b = m.closest('el-dropdown')?.querySelector('button');
          if (b) b.setAttribute('aria-expanded', 'false');
        }
      });
      menu.classList.toggle('hidden', isOpen);
      btn.setAttribute('aria-expanded', String(!isOpen));
    });

    // Tastatur: Escape schließt
    btn.addEventListener('keydown', (e) => {
      if (e.key === 'ArrowDown' && menu.classList.contains('hidden')) {
        e.preventDefault();
        btn.click();
        menu.querySelector('a,button')?.focus();
      }
    });
  });

  // Outside-Click schließt
  document.addEventListener('click', () => {
    document.querySelectorAll('el-dropdown el-menu:not(.hidden)').forEach(menu => {
      menu.classList.add('hidden');
      const b = menu.closest('el-dropdown')?.querySelector('button');
      if (b) b.setAttribute('aria-expanded', 'false');
    });
  });

  // Escape global
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      document.querySelectorAll('el-dropdown el-menu').forEach(menu => menu.classList.add('hidden'));
      document.querySelectorAll('el-dropdown button[aria-expanded="true"]').forEach(b => b.setAttribute('aria-expanded', 'false'));
    }
  });
})();

// Menu IMAGE LIST

(() => {
  const root = document.getElementById('image-list');
  if (!root) return;

  // Button → zugehöriges Menü (liegt direkt als nächstes Element daneben)
  const getMenu = (btn) => btn?.nextElementSibling?.tagName === 'DIV' ? btn.nextElementSibling : null;

  // Beim Init ein paar sinnvolle Attribute setzen und Wrapper markieren
  function init() {
    root.querySelectorAll('button[aria-expanded][data-collapse-target="next"]').forEach((btn) => {
      btn.setAttribute('aria-haspopup', 'menu');
      btn.setAttribute('aria-expanded', 'false');
      // markiere Button-Wrapper und Menü für Outside-Click-Erkennung
      btn.parentElement?.setAttribute('data-image-menu-wrapper', '');
      const menu = getMenu(btn);
      if (menu) menu.setAttribute('data-image-menu', '');
    });
  }

  function closeAll(exceptWrapper = null) {
    root.querySelectorAll('button[aria-expanded="true"][data-collapse-target="next"]').forEach((btn) => {
      const wrap = btn.parentElement;
      if (exceptWrapper && exceptWrapper === wrap) return;
      const m = getMenu(btn);
      if (m && !m.classList.contains('hidden')) m.classList.add('hidden');
      btn.setAttribute('aria-expanded', 'false');
    });
  }

  function toggle(btn) {
    const menu = getMenu(btn);
    if (!menu) return;
    const wrap = btn.parentElement;
    const isOpen = !menu.classList.contains('hidden');

    if (isOpen) {
      menu.classList.add('hidden');
      btn.setAttribute('aria-expanded', 'false');
    } else {
      closeAll(wrap);            // nur ein Menü gleichzeitig offen
      menu.classList.remove('hidden');
      btn.setAttribute('aria-expanded', 'true');
      // optional: Fokus auf erstes Item
      menu.querySelector('a,button')?.focus({ preventScroll: true });
    }
  }

  // Delegation: Klick auf den Drei-Punkte-Button
  root.addEventListener('click', (e) => {
    const btn = e.target.closest('button[aria-expanded][data-collapse-target="next"]');
    if (!btn || !root.contains(btn)) return;
    e.preventDefault();
    e.stopPropagation();
    toggle(btn);
  });

  // Outside-Click schließt alle offenen Menüs
  document.addEventListener('click', (e) => {
    // Klick innerhalb eines Menü-Wrappers? Dann nicht schließen.
    if (e.target.closest('[data-image-menu-wrapper]')) return;
    closeAll();
  });

  // Esc schließt
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeAll();
  });

  // Scroll/Resize → lieber schließen (vermeidet "hängende" Menüs)
  ['scroll', 'resize'].forEach(evt =>
    window.addEventListener(evt, () => closeAll(), { passive: true })
  );

  // Wenn ein Menü-Item geklickt wird (z.B. "Add to Album"), Menü direkt schließen
  root.addEventListener('click', (e) => {
    const inMenu = e.target.closest('[data-image-menu]');
    if (!inMenu) return;
    // nicht stopPropagation: andere Handler (z.B. Modal öffnen) dürfen weiterlaufen
    closeAll();
  });

  // initialisieren
  (document.readyState === 'loading')
    ? document.addEventListener('DOMContentLoaded', init, { once: true })
    : init();
})();
