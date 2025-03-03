document.addEventListener("DOMContentLoaded", function () {
    console.log("JavaScript geladen");
    
    const userMenuButton = document.getElementById("user-menu-button");
    const dropdownMenu = userMenuButton.nextElementSibling;
    const sidebarButton = document.querySelector("button span.sr-only").parentElement;
    const sidebar = document.querySelector(".relative.z-50");
    const sidebarCloseButton = sidebar ? sidebar.querySelector("button[type='button']") : null;

    console.log("Sidebar-Button gefunden:", sidebarButton);
    console.log("Sidebar gefunden:", sidebar);
    console.log("Sidebar Close-Button gefunden:", sidebarCloseButton);

    // Stelle sicher, dass das Menü beim Laden der Seite ausgeblendet ist
    dropdownMenu.classList.add("hidden", "opacity-0", "scale-95", "transition", "ease-out", "duration-100");
    sidebar.classList.add("-translate-x-full", "transition", "ease-in-out", "duration-300", "hidden");

    // User Dropdown Menü Steuerung
    userMenuButton.addEventListener("click", function () {
        console.log("User-Menu-Button geklickt");
        dropdownMenu.classList.toggle("hidden");
        setTimeout(() => {
            dropdownMenu.classList.toggle("opacity-0");
            dropdownMenu.classList.toggle("scale-95");
        }, 10);
    });

    document.addEventListener("click", function (event) {
        if (!userMenuButton.contains(event.target) && !dropdownMenu.contains(event.target)) {
            dropdownMenu.classList.add("opacity-0", "scale-95", "hidden");
        }
    });

    // Sidebar Steuerung
    if (sidebarButton && sidebar) {
        sidebarButton.addEventListener("click", function () {
            console.log("Sidebar-Button geklickt");
            sidebar.classList.remove("hidden");
            setTimeout(() => sidebar.classList.remove("-translate-x-full"), 10);
        });

        if (sidebarCloseButton) {
            sidebarCloseButton.addEventListener("click", function () {
                console.log("Sidebar-Close-Button geklickt");
                sidebar.classList.add("-translate-x-full");
                setTimeout(() => sidebar.classList.add("hidden"), 300);
            });
        }

        document.addEventListener("click", function (event) {
            if (!sidebar.contains(event.target) && !sidebarButton.contains(event.target)) {
                console.log("Außerhalb der Sidebar geklickt, Sidebar schließen");
                sidebar.classList.add("-translate-x-full");
                setTimeout(() => sidebar.classList.add("hidden"), 300);
            }
        });
    } else {
        console.error("Sidebar oder Sidebar-Button nicht gefunden");
    }
});