/*
 * Améliorations progressives de bytechnum.com.
 * La page fonctionne sans ce script : il replie le menu sur petit écran.
 */
(function () {
  'use strict';

  document.documentElement.classList.remove('no-js');
  document.documentElement.classList.add('js');

  function setUpMenu() {
    const nav = document.querySelector('.site-nav');
    const toggle = nav ? nav.querySelector('.site-nav__toggle') : null;
    const list = nav ? nav.querySelector('.site-nav__list') : null;
    if (!nav || !toggle || !list) {
      return;
    }

    const setOpen = (isOpen) => {
      nav.dataset.open = String(isOpen);
      toggle.setAttribute('aria-expanded', String(isOpen));
      toggle.textContent = isOpen ? 'Fermer' : 'Menu';
    };

    toggle.hidden = false;
    setOpen(false);

    toggle.addEventListener('click', () => setOpen(nav.dataset.open !== 'true'));

    list.addEventListener('click', (event) => {
      if (event.target instanceof Element && event.target.closest('a')) {
        setOpen(false);
      }
    });

    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && nav.dataset.open === 'true') {
        setOpen(false);
        toggle.focus();
      }
    });
  }

  function init() {
    setUpMenu();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
