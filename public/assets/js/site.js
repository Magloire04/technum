/*
 * Améliorations progressives de bytechnum.com.
 * La page fonctionne sans ce script : il replie le menu sur petit écran,
 * compte les caractères du message et empêche un double envoi du formulaire.
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

  function setUpMessageCounter() {
    const message = document.getElementById('contact-message');
    const counter = document.getElementById('contact-message-compte');
    if (!message || !counter) {
      return;
    }

    const format = new Intl.NumberFormat('fr-FR');
    const update = () => {
      const length = message.value.length;
      counter.textContent = `${format.format(length)} ${length > 1 ? 'caractères saisis' : 'caractère saisi'}.`;
    };

    message.addEventListener('input', update);
    update();
  }

  function setUpSubmitLock() {
    const form = document.querySelector('.contact-form');
    const button = form ? form.querySelector('button[type="submit"]') : null;
    if (!form || !button) {
      return;
    }

    const label = button.textContent;

    form.addEventListener('submit', () => {
      button.disabled = true;
      button.textContent = 'Envoi en cours…';
    });

    window.addEventListener('pageshow', (event) => {
      if (event.persisted) {
        button.disabled = false;
        button.textContent = label;
      }
    });
  }

  function init() {
    setUpMenu();
    setUpMessageCounter();
    setUpSubmitLock();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
