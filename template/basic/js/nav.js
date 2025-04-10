const menuOpen = document.getElementById('menu-open');
const menuClose = document.getElementById('menu-close');
const mobileMenu = document.getElementById('mobile-menu');

menuOpen.addEventListener('click', () => {
  mobileMenu.classList.remove('hidden');
});

menuClose.addEventListener('click', () => {
  mobileMenu.classList.add('hidden');
});