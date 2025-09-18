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