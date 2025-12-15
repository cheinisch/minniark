document.addEventListener("DOMContentLoaded", () => {
  // --- Toggle "Custom Navigation" ---
  const toggle = document.getElementById("nav_enable");
  const knob = toggle?.querySelector("span");
  const hiddenInput = document.getElementById("nav_enabled");

  if (toggle && knob && hiddenInput) {
    const enabled = toggle.getAttribute("aria-checked") === "1";
    setToggleState(enabled);

    toggle.addEventListener("click", () => {
      const isEnabled = toggle.getAttribute("aria-checked") === "1";
      setToggleState(!isEnabled);
    });

    function setToggleState(state) {
      if (state) {
        toggle.classList.remove("bg-gray-400");
        toggle.classList.add("bg-cyan-600");
        knob.classList.remove("translate-x-0");
        knob.classList.add("translate-x-5");
        toggle.setAttribute("aria-checked", "1");
        hiddenInput.value = "1";
      } else {
        toggle.classList.remove("bg-cyan-600");
        toggle.classList.add("bg-gray-400");
        knob.classList.remove("translate-x-5");
        knob.classList.add("translate-x-0");
        toggle.setAttribute("aria-checked", "0");
        hiddenInput.value = "0";
      }
    }
  }

  // --- Drag & Drop ---
  const menuList = document.getElementById('menu_list');

  Array.from(document.querySelectorAll('#available_items li')).forEach(item => {
    item.addEventListener('dragstart', e => {
      e.dataTransfer.setData('text/plain', JSON.stringify({
        label: item.dataset.label,
        link: item.dataset.link
      }));
    });
  });

  // Drop ins Menü
  menuList.addEventListener('dragover', e => e.preventDefault());
  menuList.addEventListener('drop', e => {
    e.preventDefault();
    const data = JSON.parse(e.dataTransfer.getData('text/plain'));
    addMenuItem(data.label, data.link);
  });

  // Custom-Link hinzufügen
  document.getElementById("add_custom").addEventListener("click", () => {
    const label = document.getElementById("custom_label").value.trim();
    const link = document.getElementById("custom_link").value.trim();
    if (!label || !link) return alert("Please enter both label and URL.");
    addMenuItem(label, link);
    document.getElementById("custom_label").value = "";
    document.getElementById("custom_link").value = "";
  });

  // Menüeintrag erstellen
  function addMenuItem(label, link, parentUl = menuList) {
    const li = document.createElement("li");
    li.className = "bg-white dark:bg-gray-950 p-2 shadow mb-2 cursor-move";
    li.innerHTML = `
      <div class="flex items-center justify-between dark:text-gray-200">
        <span class="truncate">${escapeHtml(label)} → <a href="${escapeAttr(link)}" class="text-cyan-600 underline break-all" target="_blank" rel="noopener">${escapeHtml(link)}</a></span>
        <button type="button" class="text-red-500 hover:text-red-700" onclick="this.closest('li').remove()">Remove</button>
      </div>
      <ul class="children border-l-2 border-gray-200 ml-2 pl-4 space-y-2 mt-2"></ul>
    `;
    parentUl.appendChild(li);
    initSortable(li.querySelector('.children'));
    enableDropTarget(li);
  }

  // Sortable aktivieren (auch verschachtelt)
  function initSortable(container = menuList) {
    if (typeof Sortable === "undefined") {
      console.error("Sortable is not defined.");
      return;
    }
    Sortable.create(container, {
      group: { name: 'menu', pull: true, put: true },
      animation: 150,
      fallbackOnBody: true,
      swapThreshold: 0.65,
      ghostClass: 'bg-yellow-100',
      onAdd: function (evt) {
        const item = evt.item;
        const childrenUl = item.querySelector("ul.children");
        if (childrenUl) initSortable(childrenUl);
      }
    });
  }
  initSortable(); // Hauptmenü initialisieren

  // Menüstruktur rekursiv in JSON umwandeln
  function parseMenu(ul) {
    const items = [];
    ul.querySelectorAll(':scope > li').forEach(li => {
      const span = li.querySelector("span");
      if (!span) return;
      const text = span.childNodes[0]?.nodeValue || ""; // "Label → "
      const parts = text.split("→");
      const label = (parts[0] || "").trim();
      const linkEl = span.querySelector("a");
      const link = linkEl ? linkEl.textContent.trim() : "";
      const childrenUl = li.querySelector("ul.children");
      const children = childrenUl ? parseMenu(childrenUl) : [];
      items.push({
        label,
        link,
        ...(children.length ? { children } : {})
      });
    });
    return items;
  }

  function enableDropTarget(li) {
    li.addEventListener('dragover', e => e.preventDefault());
    li.addEventListener('drop', e => {
      e.preventDefault();
      const data = JSON.parse(e.dataTransfer.getData('text/plain'));
      const childrenUl = li.querySelector('ul.children');
      if (childrenUl) addMenuItem(data.label, data.link, childrenUl);
    });
  }

  // --- Speichern: beim Form-Submit befüllen ---
  const saveForm = document.getElementById("save-menu-form");
  if (saveForm) {
    const menuJsonInput = saveForm.querySelector("#menu_json");
    saveForm.addEventListener("submit", (e) => {
      const data = parseMenu(menuList);
      if (menuJsonInput) {
        menuJsonInput.value = JSON.stringify(data);
      } else {
        e.preventDefault();
        console.error("#menu_json not found in #save-menu-form");
        alert("Kann nicht speichern: #menu_json fehlt im Formular.");
      }
    });
  }

  // Vorhandene Navigation einspielen
  if (window.existingNav && Array.isArray(window.existingNav)) {
    window.existingNav.forEach(item => renderMenuItem(item, document.getElementById("menu_list")));
  }

  function renderMenuItem(item, parent) {
    addMenuItem(item.label, item.link, parent);
    if (item.children && Array.isArray(item.children)) {
      const lastLi = parent.lastElementChild;
      const childUl = lastLi.querySelector("ul.children");
      item.children.forEach(child => renderMenuItem(child, childUl));
    }
  }

  // kleine Escapes
  function escapeHtml(s) {
    return s.replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));
  }
  function escapeAttr(s) {
    return s.replace(/"/g, '&quot;');
  }
});
