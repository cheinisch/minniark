  document.addEventListener("DOMContentLoaded", function () {
    const menuButton = document.querySelector('button[aria-controls="mobile-menu"]');
    const mobileMenu = document.getElementById("mobile-menu");
    const iconOpen = menuButton.querySelector("svg.block");
    const iconClose = menuButton.querySelector("svg.hidden");

    menuButton.addEventListener("click", () => {
      const isExpanded = menuButton.getAttribute("aria-expanded") === "true";
      menuButton.setAttribute("aria-expanded", !isExpanded);

      // Toggle Menu visibility
      mobileMenu.classList.toggle("hidden");

      // Swap icons
      iconOpen.classList.toggle("hidden");
      iconClose.classList.toggle("hidden");
    });

    // Optional: Menu is hidden initially
    mobileMenu.classList.add("hidden");
  });