document.addEventListener("DOMContentLoaded", () => {
  const toggle = document.getElementById("nav_enable");
  const knob = toggle.querySelector("span");
  const hiddenInput = document.getElementById("nav_enabled");

  // Initialzustand aus aria-checked übernehmen
  const enabled = toggle.getAttribute("aria-checked") === "1";
  setToggleState(enabled);

  toggle.addEventListener("click", () => {
    const isEnabled = toggle.getAttribute("aria-checked") === "1";
    setToggleState(!isEnabled);
  });

  function setToggleState(state) {
    if (state) {
      toggle.classList.remove("bg-gray-400");
      toggle.classList.add("bg-sky-600");
      knob.classList.remove("translate-x-0");
      knob.classList.add("translate-x-5");
      toggle.setAttribute("aria-checked", "1");
      hiddenInput.value = "1";
    } else {
      toggle.classList.remove("bg-sky-600");
      toggle.classList.add("bg-gray-400");
      knob.classList.remove("translate-x-5");
      knob.classList.add("translate-x-0");
      toggle.setAttribute("aria-checked", "0");
      hiddenInput.value = "0";
    }
  }
});


const menuList = document.getElementById('menu_list');

// Drag from available items (Seiten, Alben etc.)
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
    <div class="flex items-center justify-between dark:text-gray-400">
      <span>${label} → <a href="${link}" class="text-sky-600 underline" target="_blank">${link}</a></span>
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
  Sortable.create(container, {
    group: {
      name: 'menu',
      pull: true,
      put: true
    },
    animation: 150,
    fallbackOnBody: true,
    swapThreshold: 0.65,
    ghostClass: 'bg-yellow-100',
    onAdd: function (evt) {
      const item = evt.item;
      const childrenUl = item.querySelector("ul.children");
      if (childrenUl) {
        initSortable(childrenUl);
      }
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
    const [label, link] = span.innerText.split(" → ");
    const childrenUl = li.querySelector("ul.children");
    const children = childrenUl ? parseMenu(childrenUl) : [];
    items.push({
      label: label.trim(),
      link: link.trim(),
      children: children.length ? children : undefined
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
    if (childrenUl) {
      addMenuItem(data.label, data.link, childrenUl);
    }
  });
}

// Menü speichern
document.getElementById("btn_nav").addEventListener("click", () => {
    console.log("Button pressed");
    const menuData = parseMenu(menuList); // verschachteltes Menü holen

    // Formular erstellen
    const form = document.createElement("form");
    form.method = "POST";
    form.action = "backend_api/nav_change.php?save=menu";

    // JSON-Daten als verstecktes Feld anhängen
    const input = document.createElement("input");
    input.type = "hidden";
    input.name = "menu_json";
    input.value = JSON.stringify(menuData);
    form.appendChild(input);

    // Formular einfügen und absenden
    document.body.appendChild(form);
    form.submit();
});

document.addEventListener("DOMContentLoaded", () => {
  if (window.existingNav && Array.isArray(window.existingNav)) {
    window.existingNav.forEach(item => renderMenuItem(item, document.getElementById("menu_list")));
  }
});

function renderMenuItem(item, parent) {
  addMenuItem(item.label, item.link, parent);
  if (item.children && Array.isArray(item.children)) {
    const lastLi = parent.lastElementChild;
    const childUl = lastLi.querySelector("ul.children");
    item.children.forEach(child => renderMenuItem(child, childUl));
  }
}