// js/notify_update.js
(function () {
  // ---------------- Config ----------------
  const UPDATE_URL  = 'dashboard-update.php';
  // Robuste Pfade: funktionieren in /, /admin/, etc.
  const API_URL     = new URL('../api/version.php', location.href).toString();
  const VERSION_URL = new URL('../VERSION',        location.href).toString();

  // ---------------- Debug Controller ----------------
  const dbg = (() => {
    const enabled = (() => {
      const url = new URL(location.href);
      if (url.searchParams.get('debugNotify') === '1') return true;
      try { return (localStorage.getItem('notifyDebug') === '1'); } catch { return false; }
    })();

    const tag = '[notify]';
    const group   = (label) => { if (enabled) console.group(`${tag} ${label}`); };
    const groupEnd= ()       => { if (enabled) console.groupEnd(); };
    const log     = (...a)  => { if (enabled) console.log(tag, ...a); };
    const info    = (...a)  => { if (enabled) console.info(tag, ...a); };
    const warn    = (...a)  => { if (enabled) console.warn(tag, ...a); };
    const error   = (...a)  => { if (enabled) console.error(tag, ...a); };
    const table   = (obj)   => { if (enabled && obj) console.table(obj); };
    const time    = (label) => { if (enabled) console.time(`${tag} ${label}`); };
    const timeEnd = (label) => { if (enabled) console.timeEnd(`${tag} ${label}`); };
    const attachBadge = (el, text='DEBUG') => {
      if (!enabled || !el) return;
      let b = el.querySelector('.notify-debug-badge');
      if (!b) {
        b = document.createElement('span');
        b.className = 'notify-debug-badge absolute -bottom-2 left-1/2 -translate-x-1/2 text-[10px] px-1 py-0.5 rounded bg-amber-500 text-black';
        el.appendChild(b);
      }
      b.textContent = text;
    };
    return { enabled, group, groupEnd, log, info, warn, error, table, time, timeEnd, attachBadge };
  })();

  // ---------------- DOM helpers ----------------
  const qs  = (sel, el = document) => el.querySelector(sel);
  const qsa = (sel, el = document) => Array.from(el.querySelectorAll(sel));

  function findBellButton() {
    // 1) Heuristik: Button mit Glocken-SVG (dein Markup)
    const btns = qsa('button');
    const bySvg = btns.find(b => {
      const svg = qs('svg', b);
      // Dein Glocken-Icon: viewBox 0 0 24 24 + charakteristischer Pfad
      return svg && /viewBox="0 0 24 24"/i.test(svg.outerHTML) && svg.outerHTML.includes('A6 6 0 0 0 6 9');
    });
    if (bySvg) return bySvg;
    // 2) Fallback: sr-only mit „notif“
    const bySr = btns.find(b => {
      const sr = qs('.sr-only', b);
      return sr && /notif/i.test((sr.textContent || '').trim());
    });
    return bySr || null;
  }

  function ensureWrapper(btn) {
    if (btn.parentElement && btn.parentElement.classList.contains('notif-wrap')) return btn.parentElement;
    const wrap = document.createElement('div');
    wrap.className = 'notif-wrap relative';
    btn.parentElement.insertBefore(wrap, btn);
    wrap.appendChild(btn);
    return wrap;
  }

  function ensureMenu(wrap) {
    let menu = qs('.notif-menu', wrap);
    if (!menu) {
      menu = document.createElement('div');
      menu.className = 'notif-menu';
      menu.setAttribute('hidden', '');
      wrap.appendChild(menu);
    }
    return menu;
  }

  function forceIconSize(btn) {
    const svg = qs('svg', btn);
    if (svg) {
      svg.classList.add('w-6', 'h-6', 'shrink-0', 'block');
      svg.classList.remove('size-6'); // optional, falls Tailwind-Alias stört
    }
  }

  function addBadge(wrap) {
    if (qs('.notify-badge', wrap)) return;
    const dot = document.createElement('span');
    dot.className = 'notify-badge absolute -top-0.5 -right-0.5 inline-block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white dark:ring-black';
    wrap.appendChild(dot);
  }

  function removeBadge(wrap) {
    qs('.notify-badge', wrap)?.remove();
  }

  function escapeHtml(s) {
    return String(s)
      .replaceAll('&','&amp;')
      .replaceAll('<','&lt;')
      .replaceAll('>','&gt;')
      .replaceAll('"','&quot;')
      .replaceAll("'","&#039;");
  }

  function buildMenu(menu, hasUpdate, currentVersion = '', latestVersion = '') {
    if (!hasUpdate) {
      menu.innerHTML = `
        <div role="menu"
             class="absolute right-0 mt-2 w-64 origin-top-right rounded-md py-2 shadow-lg outline outline-gray-900/5
                    bg-white dark:bg-black dark:shadow-none dark:-outline-offset-1 dark:outline-white/10 z-50">
          <div class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
            Keine neuen Benachrichtigungen
          </div>
        </div>`;
      return;
    }
    const subtitle = (currentVersion && latestVersion)
      ? `<div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">${escapeHtml(currentVersion)} → ${escapeHtml(latestVersion)}</div>`
      : '';
    menu.innerHTML = `
      <div role="menu"
           class="absolute right-0 mt-2 w-64 origin-top-right rounded-md py-2 shadow-lg outline outline-gray-900/5
                  bg-white dark:bg-black dark:shadow-none dark:-outline-offset-1 dark:outline-white/10 z-50">
        <a href="${UPDATE_URL}"
           class="block px-4 py-3 text-sm text-gray-900 hover:bg-gray-50 dark:text-white dark:hover:bg-white/5"
           role="menuitem">
          <div class="font-medium">Update available</div>
          ${subtitle}
        </a>
      </div>`;
  }

  function toggleMenu(menu, btn) {
    const isHidden = menu.hasAttribute('hidden');
    qsa('.notif-menu').forEach(m => m.setAttribute('hidden', '')); // andere Menüs schließen
    if (isHidden) {
      menu.removeAttribute('hidden');
      btn.setAttribute('aria-expanded', 'true');
    } else {
      menu.setAttribute('hidden', '');
      btn.setAttribute('aria-expanded', 'false');
    }
  }

  function closeMenu(menu, btn) {
    if (!menu.hasAttribute('hidden')) {
      menu.setAttribute('hidden', '');
      btn.setAttribute('aria-expanded', 'false');
    }
  }

  // ---------------- Version compare (client-seitig, unabhängig vom API-Flag) ----------------
  function parseVer(v){ return String(v||'').replace(/^v/i,'').split('.').map(n=>parseInt(n,10)||0); }
  function cmpVer(a,b){
    const A=parseVer(a), B=parseVer(b), len=Math.max(A.length,B.length);
    for(let i=0;i<len;i++){ const x=A[i]||0, y=B[i]||0; if(x>y) return 1; if(x<y) return -1; }
    return 0;
  }

  // ---------------- API calls ----------------
  async function fetchVersionInfo() {
    try {
      dbg.group('API /api/version.php');
      dbg.time('api');
      dbg.log('GET', API_URL);
      const res = await fetch(API_URL, { cache: 'no-store' });
      dbg.log('HTTP', res.status);
      if (!res.ok) {
        dbg.warn('API HTTP error', res.status);
        dbg.timeEnd('api'); dbg.groupEnd();
        return null;
      }
      const json = await res.json();
      dbg.table(json);
      dbg.timeEnd('api'); dbg.groupEnd();
      return json; // enthält: new_version_number, new_version_available (kann ignoriert werden)
    } catch (e) {
      dbg.error('API fetch failed', e);
      return null;
    }
  }

  async function fetchCurrentVersion() {
    try {
      dbg.group('GET VERSION');
      dbg.time('version');
      dbg.log('GET', VERSION_URL);
      const res = await fetch(VERSION_URL, { cache: 'no-store' });
      dbg.log('HTTP', res.status);
      if (!res.ok) {
        dbg.warn('VERSION fetch error', res.status);
        dbg.timeEnd('version'); dbg.groupEnd();
        return '';
      }
      const text = (await res.text() || '').trim();
      dbg.log('currentVersion =', text);
      dbg.timeEnd('version'); dbg.groupEnd();
      return text;
    } catch (e) {
      dbg.error('VERSION fetch failed', e);
      return '';
    }
  }

  // ---------------- Init ----------------
  async function init() {
    dbg.group('init');
    dbg.info('script loaded at', new Date().toISOString());
    dbg.log('API_URL=', API_URL, 'VERSION_URL=', VERSION_URL, 'UPDATE_URL=', UPDATE_URL);

    const btn = findBellButton();
    if (!btn) { dbg.warn('bell button NOT found'); dbg.groupEnd(); return; }
    dbg.log('✅ Bell button found', btn);

    forceIconSize(btn);

    const wrap = ensureWrapper(btn);
    dbg.log('✅ Wrapper ensured', wrap);
    const menu = ensureMenu(wrap);
    dbg.log('✅ Menu ensured', menu);

    // Dropdown Events
    btn.addEventListener('click', (e) => { e.stopPropagation(); toggleMenu(menu, btn); });
    document.addEventListener('click', () => closeMenu(menu, btn));
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeMenu(menu, btn); });

    if (dbg.enabled) dbg.attachBadge(wrap, 'DBG');

    // Hole beide Versionen parallel
    const [apiData, currentVersion] = await Promise.all([
      fetchVersionInfo(),
      fetchCurrentVersion()
    ]);

    if (!apiData) {
      dbg.warn('No data from API – defaulting to no notifications');
      buildMenu(menu, false);
      removeBadge(wrap);
      dbg.groupEnd();
      return;
    }

    const latestVersion = apiData?.new_version_number ? String(apiData.new_version_number) : '';
    // Client-seitig entscheiden (ignoriert evtl. veraltetes API-Flag):
    const hasUpdate = (latestVersion && currentVersion)
      ? (cmpVer(latestVersion, currentVersion) > 0)
      : !!apiData.new_version_available;

    if (dbg.enabled && typeof apiData.new_version_available === 'boolean' && latestVersion && currentVersion) {
      const apiFlag = !!apiData.new_version_available;
      const clientFlag = (cmpVer(latestVersion, currentVersion) > 0);
      if (apiFlag !== clientFlag) {
        dbg.warn('API flag mismatch with client compare', { apiFlag, clientFlag, currentVersion, latestVersion });
      }
    }

    dbg.info('current=', currentVersion || '(unknown)', 'latest=', latestVersion || '(unknown)', 'update?', hasUpdate);

    buildMenu(menu, hasUpdate, currentVersion, latestVersion);
    if (hasUpdate) addBadge(wrap); else removeBadge(wrap);

    dbg.groupEnd();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
