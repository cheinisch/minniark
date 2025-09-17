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

// Profil Dropdown

(() => {
  function setupDropdown(dd){
    const btn  = dd.querySelector('button');
    const menu = dd.querySelector('el-menu');
    if (!btn || !menu) return;

    // Gemeinsame Defaults (nur inline)
    dd.style.position ||= 'relative';
    menu.style.minWidth ||= '8rem';
    menu.style.zIndex   = '1000';

    const hasPopover = menu.hasAttribute('popover') && typeof menu.showPopover === 'function';

    if (!hasPopover) {
      // Fallback: eigenes Dropdown ohne Popover-API
      menu.hidden = true;
      menu.style.position = 'absolute';
      menu.style.right = '0';

      const recalc = () => {
        const r = btn.getBoundingClientRect();
        const dy = window.scrollY || document.documentElement.scrollTop || 0;
        const top = (r.bottom + dy + 8);
        menu.style.top = top - dd.getBoundingClientRect().top - dy + 'px';
      };

      let open = false;
      const show = () => {
        if (open) return;
        open = true;
        recalc();
        menu.hidden = false;
        btn.setAttribute('aria-expanded','true');
        trap();
        window.addEventListener('resize', recalc, {passive:true});
        window.addEventListener('scroll', recalc, {passive:true, capture:true});
      };
      const hide = () => {
        if (!open) return;
        open = false;
        menu.hidden = true;
        btn.setAttribute('aria-expanded','false');
        untrap();
        window.removeEventListener('resize', recalc);
        window.removeEventListener('scroll', recalc, true);
      };

      // A11y
      btn.setAttribute('aria-haspopup','menu');
      btn.setAttribute('aria-expanded','false');
      menu.setAttribute('role','menu');
      menu.querySelectorAll('a,button,[href],[tabindex]').forEach(el=>{
        el.setAttribute('role','menuitem');
      });

      // Events
      const onDocDown = (e)=>{ if (!dd.contains(e.target)) hide(); };
      const onKey = (e)=>{
        if (e.key === 'Escape') { e.preventDefault(); hide(); return; }
        if (!open) return;
        const items = Array.from(menu.querySelectorAll('[role="menuitem"]'));
        if (!items.length) return;
        let i = items.indexOf(document.activeElement);
        if (e.key === 'ArrowDown') { e.preventDefault(); (items[i+1]||items[0])?.focus(); }
        if (e.key === 'ArrowUp')   { e.preventDefault(); (items[i-1]||items.at(-1))?.focus(); }
        if (e.key === 'Tab') {
          const first=items[0], last=items.at(-1);
          if (!e.shiftKey && document.activeElement===last){ e.preventDefault(); first.focus(); }
          if ( e.shiftKey && document.activeElement===first){ e.preventDefault(); last.focus(); }
        }
        if (e.key === 'Enter' || e.key === ' ') {
          if (document.activeElement?.closest('[role="menuitem"]')) { document.activeElement.click(); hide(); }
        }
      };
      function trap(){
        document.addEventListener('mousedown', onDocDown, true);
        document.addEventListener('touchstart', onDocDown, {passive:true, capture:true});
        document.addEventListener('keydown', onKey, true);
        (menu.querySelector('[role="menuitem"]')||menu).focus({preventScroll:true});
      }
      function untrap(){
        document.removeEventListener('mousedown', onDocDown, true);
        document.removeEventListener('touchstart', onDocDown, true);
        document.removeEventListener('keydown', onKey, true);
      }

      btn.addEventListener('click', (e)=>{ e.stopPropagation(); menu.hidden ? show() : hide(); });
      menu.addEventListener('click', (e)=>{ if (e.target.closest('[role="menuitem"]')) hide(); });
      return;
    }

    // --- Popover-API Pfad ---
    // Wir benutzen fixed-Positionierung (Top-Layer) und platzieren relativ zum Button
    menu.style.position = 'fixed';
    menu.style.inset = 'auto';
    menu.style.right = 'auto';

    const position = () => {
      const br = btn.getBoundingClientRect();
      const mr = menu.getBoundingClientRect(); // nach showPopover verfügbar
      let left = Math.max(8, Math.min(br.right - mr.width, window.innerWidth - mr.width - 8));
      let top  = Math.min(br.bottom + 8, window.innerHeight - mr.height - 8);
      menu.style.left = left + 'px';
      menu.style.top  = top  + 'px';
    };

    const show = () => {
      if (menu.matches(':popover-open')) return;
      // Erst öffnen, dann positionieren (damit Breite/Höhe messbar sind)
      menu.showPopover();
      btn.setAttribute('aria-expanded','true');
      // In der gleichen Task positionieren, um Flicker zu minimieren
      requestAnimationFrame(position);
      trap();
      window.addEventListener('resize', position, {passive:true});
      window.addEventListener('scroll', position, {passive:true, capture:true});
    };
    const hide = () => {
      if (!menu.matches(':popover-open')) return;
      menu.hidePopover();
      btn.setAttribute('aria-expanded','false');
      untrap();
      window.removeEventListener('resize', position);
      window.removeEventListener('scroll', position, true);
    };

    // A11y
    btn.setAttribute('aria-haspopup','menu');
    btn.setAttribute('aria-expanded','false');
    menu.setAttribute('role','menu');
    menu.querySelectorAll('a,button,[href],[tabindex]').forEach(el=>{
      el.setAttribute('role','menuitem');
    });

    const onDocDown = (e)=>{ if (!dd.contains(e.target) && !menu.contains(e.target)) hide(); };
    const onKey = (e)=>{
      if (e.key === 'Escape') { e.preventDefault(); hide(); return; }
      if (!menu.matches(':popover-open')) return;
      const items = Array.from(menu.querySelectorAll('[role="menuitem"]'));
      if (!items.length) return;
      let i = items.indexOf(document.activeElement);
      if (e.key === 'ArrowDown') { e.preventDefault(); (items[i+1]||items[0])?.focus(); }
      if (e.key === 'ArrowUp')   { e.preventDefault(); (items[i-1]||items.at(-1))?.focus(); }
      if (e.key === 'Tab') {
        const first=items[0], last=items.at(-1);
        if (!e.shiftKey && document.activeElement===last){ e.preventDefault(); first.focus(); }
        if ( e.shiftKey && document.activeElement===first){ e.preventDefault(); last.focus(); }
      }
      if (e.key === 'Enter' || e.key === ' ') {
        if (document.activeElement?.closest('[role="menuitem"]')) { document.activeElement.click(); hide(); }
      }
    };
    function trap(){
      document.addEventListener('mousedown', onDocDown, true);
      document.addEventListener('touchstart', onDocDown, {passive:true, capture:true});
      document.addEventListener('keydown', onKey, true);
      (menu.querySelector('[role="menuitem"]')||menu).focus({preventScroll:true});
    }
    function untrap(){
      document.removeEventListener('mousedown', onDocDown, true);
      document.removeEventListener('touchstart', onDocDown, true);
      document.removeEventListener('keydown', onKey, true);
    }

    btn.addEventListener('click', (e)=>{ e.stopPropagation(); menu.matches(':popover-open') ? hide() : show(); });
    menu.addEventListener('click', (e)=>{ if (e.target.closest('[role="menuitem"]')) hide(); });
  }

  function init(){ document.querySelectorAll('el-dropdown').forEach(setupDropdown); }
  (document.readyState === 'loading') ? document.addEventListener('DOMContentLoaded', init) : init();
})();

// Neue Sidebar

(() => {
    // --- Scopes ---------------------------------------------------------------
    const desktopSidebar = document.querySelector('div.lg\\:fixed.lg\\:inset-y-0');
    const mobileSidebar  = document.getElementById('sidebar'); // <dialog> mobil
    const imageList      = document.getElementById('image-list');

    const scopes = [desktopSidebar, mobileSidebar, imageList].filter(Boolean);

    // --- Utils ----------------------------------------------------------------
    function getPanel(btn) {
        const sel = btn.getAttribute('data-collapse-target');
        if (sel && sel !== 'next') return document.querySelector(sel);
        let n = btn.nextElementSibling;
        while (n && n.nodeType !== 1) n = n.nextElementSibling;
        return n;
    }

    function closeBtn(btn) {
        btn.setAttribute('aria-expanded', 'false');
        const p = getPanel(btn);
        if (p) p.classList.add('hidden');
        btn.querySelector('[data-chevron]')?.classList.remove('rotate-180');
    }

    function closeAll(scope) {
        scope.querySelectorAll('button[data-collapse-target][aria-expanded="true"]').forEach(closeBtn);
    }

    // Für Sidebar: nur Geschwister unter demselben <ul> schließen
    function closeSidebarSiblings(btn, scope) {
        const root = btn.closest('ul') || scope;
        root.querySelectorAll('> li > button[data-collapse-target][aria-expanded="true"]').forEach(b => {
        if (b !== btn) closeBtn(b);
        });
    }

    // Für Bilder: alle anderen Bild-Menüs schließen (global in der Liste)
    function closeOtherImageMenus(currentBtn) {
        if (!imageList) return;
        imageList.querySelectorAll('button[data-collapse-target][aria-expanded="true"]').forEach(b => {
        if (b !== currentBtn) closeBtn(b);
        });
    }

    // Herausfinden, zu welchem Scope ein Button gehört
    function findScopeFor(el) {
        return scopes.find(s => s.contains(el)) || null;
    }

    // --- Toggle per Klick (nur innerhalb unserer Scopes) ----------------------
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('button[data-collapse-target]');
        if (!btn) return;

        const scope = findScopeFor(btn);
        if (!scope) return; // Buttons außerhalb unserer Scopes ignorieren (z. B. andere Komponenten)

        e.preventDefault();
        const panel = getPanel(btn);
        if (!panel) return;

        const expanded = btn.getAttribute('aria-expanded') === 'true';

        if (scope === imageList) {
        // Bildmenüs: alle anderen schließen
        closeOtherImageMenus(btn);
        } else {
        // Sidebar: nur Geschwister schließen
        closeSidebarSiblings(btn, scope);
        }

        btn.setAttribute('aria-expanded', String(!expanded));
        panel.classList.toggle('hidden', expanded);
        btn.querySelector('[data-chevron]')?.classList.toggle('rotate-180', !expanded);
    });

    // --- Outside-Click: je Scope schließen -----------------------------------
    document.addEventListener('click', (e) => {
        scopes.forEach(scope => {
        if (!scope) return;

        const clickedInside = scope.contains(e.target);

        if (scope === imageList) {
            // In der Bildliste: wenn nicht auf einen offenen Toggle/Panel geklickt, schließen
            const openBtns = scope.querySelectorAll('button[data-collapse-target][aria-expanded="true"]');
            if (!openBtns.length) return;

            let clickHitsOpenThing = false;
            openBtns.forEach(b => {
            const p = getPanel(b);
            if (b.contains(e.target) || (p && p.contains(e.target))) clickHitsOpenThing = true;
            });

            if (!clickedInside || (clickedInside && !clickHitsOpenThing && !e.target.closest('button[data-collapse-target]'))) {
            closeAll(scope);
            }
        } else {
            // Sidebar: Klick außerhalb schließt alles
            if (!clickedInside) closeAll(scope);
        }
        });
    });

    // --- ESC: alles schließen -------------------------------------------------
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') scopes.forEach(scope => scope && closeAll(scope));
    });

    // --- „Add to Album“ in Bildmenüs: Modal öffnen + Menü schließen ----------
    // Delegation, damit es auch bei dynamischen Listen funktioniert
    if (imageList) {
        imageList.addEventListener('click', (e) => {
        const a = e.target.closest('.assign-to-album-btn');
        if (!a) return;

        e.preventDefault();
        const filename = a.getAttribute('data-filename');
        const hiddenInput = document.getElementById('assignImageFilename');
        const modal = document.getElementById('assignToAlbumModal');
        if (hiddenInput) hiddenInput.value = filename;
        if (modal) modal.classList.remove('hidden');

        // Bildmenü schließen
        closeAll(imageList);
        });
    }
})();