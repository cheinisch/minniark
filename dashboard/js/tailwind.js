document.addEventListener("DOMContentLoaded", function () {
    const userMenuButton = document.getElementById("user-menu-button");
    const dropdownMenu = userMenuButton.nextElementSibling;
    const sidebarButton = document.getElementById("sidebar-toggle");
    const sidebar = document.querySelector(".relative.z-50");
    const sidebarCloseButton = sidebar ? sidebar.querySelector("button[type='button']") : null;
    const sidebarDropdowns = document.querySelectorAll(".sidebar-menu-dropdown");

    // Stelle sicher, dass das Menü beim Laden der Seite ausgeblendet ist
    dropdownMenu.classList.add("hidden", "opacity-0", "scale-95", "transition", "ease-out", "duration-200");
    sidebar.classList.add("-translate-x-full", "transition", "ease-in-out", "duration-300", "hidden");

    // User Dropdown Menü Steuerung
    userMenuButton.addEventListener("click", function () {
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
            sidebar.classList.remove("hidden");
            sidebar.classList.remove("-translate-x-full");
        });

        if (sidebarCloseButton) {
            sidebarCloseButton.addEventListener("click", function () {
                sidebar.classList.add("-translate-x-full");
                setTimeout(() => sidebar.classList.add("hidden"), 300);
            });
        }

        document.addEventListener("click", function (event) {
            if (!sidebar.contains(event.target) && !sidebarButton.contains(event.target)) {
                sidebar.classList.add("-translate-x-full");
                setTimeout(() => sidebar.classList.add("hidden"), 300);
            }
        });
    }

    // Sidebar-Menü-Dropdowns Steuerung
    sidebarDropdowns.forEach(menu => {
        const button = menu.querySelector(".dropdown-button");
        const dropdownContent = menu.querySelector(".dropdown-content");
        
        if (button && dropdownContent) {
            button.addEventListener("click", function () {
                dropdownContent.classList.toggle("hidden");
                setTimeout(() => {
                    dropdownContent.classList.toggle("opacity-100");
                    dropdownContent.classList.toggle("scale-95");
                }, 10);
            });
        }
    });
});