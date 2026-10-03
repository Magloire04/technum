<?php /** @var Technum\View\View $view */ ?>
<header class="site-header">
  <div class="site-header__inner">
    <a class="site-header__logo" href="/">
      <img src="<?= e($view->asset('img/logo-technum.svg')) ?>" alt="TECHNUM, accueil" width="148" height="41">
    </a>
    <nav class="site-nav" aria-label="Navigation principale">
      <button class="site-nav__toggle" type="button" aria-expanded="false" aria-controls="menu-principal" hidden>Menu</button>
      <ul class="site-nav__list" id="menu-principal">
        <li><a href="/#produits">Produits</a></li>
        <li><a href="/#realisations">Réalisations</a></li>
        <li><a href="/#services">Services</a></li>
        <li><a href="/#methode">Méthode</a></li>
        <li class="site-nav__contact"><a href="/#contact">Parler de votre projet</a></li>
      </ul>
    </nav>
    <a class="button button--primary site-header__cta" href="/#contact">Parler de votre projet</a>
  </div>
</header>
