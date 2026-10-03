/*
 * Améliorations progressives de bytechnum.com.
 * La page fonctionne sans ce script : il replie le menu sur petit écran, présente les produits
 * en onglets, compte les caractères du message et empêche un double envoi du formulaire.
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

  function setUpProductTabs() {
    const tablist = document.querySelector('.product-tabs');
    if (!tablist) {
      return;
    }

    const tabs = Array.from(tablist.querySelectorAll('[role="tab"]'));
    const panels = tabs.map((tab) =>
      document.getElementById(tab.getAttribute('aria-controls') || ''),
    );
    if (tabs.length === 0 || panels.some((panel) => !panel)) {
      return;
    }

    panels.forEach((panel, index) => {
      panel.setAttribute('role', 'tabpanel');
      panel.setAttribute('aria-labelledby', tabs[index].id);
      panel.tabIndex = 0;
    });

    const select = (index, moveFocus) => {
      tabs.forEach((tab, position) => {
        const isSelected = position === index;
        tab.setAttribute('aria-selected', String(isSelected));
        tab.tabIndex = isSelected ? 0 : -1;
        panels[position].hidden = !isSelected;
      });
      if (moveFocus) {
        tabs[index].focus();
      }
    };

    const indexOfHash = (hash) => panels.findIndex((panel) => `#${panel.id}` === hash);

    const reveal = (index) => {
      select(index, false);
      tablist.scrollIntoView({ block: 'start' });
    };

    tablist.hidden = false;
    select(0, false);

    tabs.forEach((tab, index) => {
      tab.addEventListener('click', () => select(index, false));
    });

    tablist.addEventListener('keydown', (event) => {
      const current = tabs.indexOf(document.activeElement);
      if (current === -1) {
        return;
      }
      const last = tabs.length - 1;
      const targets = {
        ArrowRight: current === last ? 0 : current + 1,
        ArrowLeft: current === 0 ? last : current - 1,
        Home: 0,
        End: last,
      };
      const target = targets[event.key];
      if (target === undefined) {
        return;
      }
      event.preventDefault();
      select(target, true);
    });

    document.addEventListener('click', (event) => {
      const link =
        event.target instanceof Element ? event.target.closest('a[href^="#produit-"]') : null;
      const index = link ? indexOfHash(link.getAttribute('href')) : -1;
      if (index === -1) {
        return;
      }
      event.preventDefault();
      window.history.pushState(null, '', link.getAttribute('href'));
      reveal(index);
      tabs[index].focus({ preventScroll: true });
    });

    window.addEventListener('hashchange', () => {
      const index = indexOfHash(window.location.hash);
      if (index !== -1) {
        reveal(index);
      }
    });

    const initial = indexOfHash(window.location.hash);
    if (initial !== -1) {
      reveal(initial);
    }
  }

  function init() {
    setUpMenu();
    setUpProductTabs();
    setUpMessageCounter();
    setUpSubmitLock();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
