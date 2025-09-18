
(() => {
  // Hilfsfunktionen -----------------------------------------------------------
  const focusablesSelector = 'a,button,[href],[tabindex]:not([tabindex="-1"])';

  function setup(dd){
    const btn  = dd.querySelector('[data-trigger]') || dd.querySelector('button');
    const menu = dd.querySelector('[data-menu]');
    if (!btn || !menu) return;

    // Inline-Defaults (kein CSS nötig)
    dd.style.position ||= 'relative';
    menu.style.position = 'absolute';
    menu.style.right = '0';
    menu.style.minWidth ||= '8rem';
    menu.style.zIndex = '1000';

    // Für Tailwind-Varianten in deinen Klassen:
    // Hidden-Zustand markiert mit data-closed für die "data-closed:*" Utilities
    menu.toggleAttribute('data-closed', true);

    // Positionierung unter dem Button (mit kleinem Gap)
    const GAP = 10;
    function position(){
      // relative zum Container
      const top = btn.offsetTop + btn.offsetHeight + GAP;
      menu.style.top = top + 'px';
    }

    // State
    let open = false;
    let unsubs = [];

    function on(e, t, h, opts){ t.addEventListener(e, h, opts); unsubs.push(() => t.removeEventListener(e, h, opts)); }
    function cleanup(){ unsubs.forEach(fn => fn()); unsubs = []; }

    // Animations-Helfer (kompatibel mit deinen data-enter/leave utilities)
    function playEnter(){
      menu.hidden = false;
      menu.setAttribute('data-enter','');
      menu.removeAttribute('data-closed');
      // Ein Frame warten, dann enter entfernen (damit Transition greift)
      requestAnimationFrame(() => {
        menu.removeAttribute('data-enter');
      });
    }
    function playLeave(cb){
      menu.setAttribute('data-leave','');
      const done = () => {
        menu.hidden = true;
        menu.removeAttribute('data-leave');
        menu.setAttribute('data-closed','');
        cb && cb();
      };
      // Falls keine Transition gesetzt ist, sofort schließen
      let transitioned = false;
      const onEnd = () => { if (transitioned) return; transitioned = true; menu.removeEventListener('transitionend', onEnd); done(); };
      menu.addEventListener('transitionend', onEnd, { once: true });
      // Fallback Timeout
      setTimeout(onEnd, 120);
    }

    function trapFocus(e){
      if (!open) return;
      if (e.key !== 'Tab') return;
      const items = Array.from(menu.querySelectorAll(focusablesSelector));
      if (!items.length) return;
      const first = items[0], last = items.at(-1);
      if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
      if ( e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
    }

    function keyNav(e){
      const items = Array.from(menu.querySelectorAll('[role="menuitem"]'));
      if (!items.length) return;
      let i = items.indexOf(document.activeElement);
      if (e.key === 'ArrowDown') { e.preventDefault(); (items[i+1] || items[0]).focus(); }
      if (e.key === 'ArrowUp')   { e.preventDefault(); (items[i-1] || items.at(-1)).focus(); }
      if (e.key === 'Enter' || e.key === ' ') {
        if (document.activeElement?.closest('[role="menuitem"]')) {
          document.activeElement.click();
          hide();
        }
      }
    }

    function closeOthers(){
      document.querySelectorAll('[data-dropdown] [data-menu]:not([hidden])').forEach(m => {
        if (m === menu) return;
        const wrap = m.closest('[data-dropdown]');
        wrap?.__dropdownApi?.hide?.();
      });
    }

    function show(){
      if (open) return;
      closeOthers();
      open = true;
      position();
      playEnter();
      btn.setAttribute('aria-expanded','true');

      // Focus erstes Item
      const firstItem = menu.querySelector('[role="menuitem"]') || menu;
      firstItem.focus?.({ preventScroll: true });

      // Outside-Click, ESC, Scroll/Resize, Focus-Trap & Pfeiltasten
      on('mousedown', document, (e) => { if (!dd.contains(e.target)) hide(); }, true);
      on('touchstart', document, (e) => { if (!dd.contains(e.target)) hide(); }, { passive: true, capture: true });
      on('keydown', document, (e) => { if (e.key === 'Escape') hide(); });
      on('keydown', document, trapFocus, true);
      on('keydown', document, keyNav, true);
      on('scroll',  window, position, { passive: true, capture: true });
      on('resize',  window, position, { passive: true });

      // Menü-Klick: Auswahl schließt
      on('click', menu, (e) => { if (e.target.closest('[role="menuitem"]')) hide(); });
    }

    function hide(){
      if (!open) return;
      open = false;
      btn.setAttribute('aria-expanded','false');
      playLeave(() => cleanup());
    }

    // Button-Toggle
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      open ? hide() : show();
    });

    // API für "closeOthers"
    dd.__dropdownApi = { show, hide };
  }

  function init(){ document.querySelectorAll('[data-dropdown]').forEach(setup); }
  (document.readyState === 'loading') ? document.addEventListener('DOMContentLoaded', init) : init();
})();
