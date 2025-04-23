document.addEventListener('DOMContentLoaded', function () {
    const mobileMenuButton = document.querySelector('[aria-controls="mobile-menu"]');
    const mobileMenu = document.getElementById('mobile-menu');

    const userMenuButton = document.getElementById('user-menu-button');
    const userDropdown = userMenuButton.parentElement.nextElementSibling;

    // Mobilmenü öffnen/schließen
    mobileMenuButton.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
        
        const icons = mobileMenuButton.querySelectorAll('svg');
        icons.forEach(icon => icon.classList.toggle('hidden'));
    });

    // User-Dropdown öffnen/schließen (Desktop)
    userMenuButton.addEventListener('click', () => {
        userDropdown.classList.toggle('hidden');
    });

    // Dropdown schließen, wenn außerhalb geklickt wird
    document.addEventListener('click', (event) => {
        if (!userMenuButton.contains(event.target) && !userDropdown.contains(event.target)) {
            userDropdown.classList.add('hidden');
        }
    });

    // Initialzustände setzen
    mobileMenu.classList.add('hidden');
    userDropdown.classList.add('hidden');
});