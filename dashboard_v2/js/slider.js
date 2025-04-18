const rangeInput = document.querySelector('input[type="range"]');
const valueDisplay = document.getElementById('range-value');

rangeInput.addEventListener('input', () => {
  const value = rangeInput.value + 'px';
  valueDisplay.innerText = value;

  // Setzt CSS Variable für alle Bildcontainer
  document.querySelectorAll('.dynamic-image-width').forEach(el => {
    el.style.setProperty('--img-max-width', value);
  });
});