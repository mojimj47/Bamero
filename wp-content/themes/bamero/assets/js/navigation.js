(() => {
  'use strict';

  const toggle = document.querySelector('.menu-toggle');
  const menu = document.querySelector('#primary-navigation');

  if (!toggle || !menu) return;

  const label = toggle.querySelector('.screen-reader-text');
  const closeMenu = () => {
    toggle.setAttribute('aria-expanded', 'false');
    menu.classList.remove('is-open');
    if (label) label.textContent = toggle.dataset.labelOpen || '';
  };

  toggle.addEventListener('click', () => {
    const expanded = toggle.getAttribute('aria-expanded') === 'true';
    toggle.setAttribute('aria-expanded', String(!expanded));
    menu.classList.toggle('is-open', !expanded);
    if (label) label.textContent = expanded ? (toggle.dataset.labelOpen || '') : (toggle.dataset.labelClose || '');
  });

  menu.addEventListener('click', (event) => {
    if (event.target instanceof Element && event.target.closest('a')) closeMenu();
  });

  document.addEventListener('keydown', (event) => {
    const menuIsOpen = toggle.getAttribute('aria-expanded') === 'true' && menu.classList.contains('is-open');
    const toggleIsVisible = toggle.getClientRects().length > 0;
    const focusIsInMenu = menu.contains(document.activeElement) || toggle.contains(document.activeElement);

    if (event.key === 'Escape' && menuIsOpen && toggleIsVisible && focusIsInMenu) {
      event.preventDefault();
      closeMenu();
      toggle.focus();
    }
  });

  document.addEventListener('click', (event) => {
    if (!menu.contains(event.target) && !toggle.contains(event.target)) closeMenu();
  });
})();
