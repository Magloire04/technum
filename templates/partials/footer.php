<?php
/**
 * @var Technum\View\View $view
 * @var Technum\Content\SiteInfo $site
 * @var list<Technum\Content\Product> $products
 */
?>
<footer class="site-footer">
  <div class="site-footer__inner">
    <div class="site-footer__brand">
      <img src="<?= e($view->asset('img/logo-technum-clair-signature.svg')) ?>" alt="TECHNUM, la technologie à votre portée" width="220" height="73">
    </div>
    <nav class="site-footer__column" aria-labelledby="pied-produits">
      <h2 class="site-footer__title" id="pied-produits">Produits</h2>
      <ul>
        <?php foreach ($products as $product) : ?>
          <li><a href="<?= e($product->trackedUrl()) ?>"><?= e($product->name) ?></a></li>
        <?php endforeach ?>
      </ul>
    </nav>
    <div class="site-footer__column">
      <h2 class="site-footer__title">Contact</h2>
      <ul>
        <li><a href="<?= e($site->whatsappUrl()) ?>">WhatsApp</a></li>
        <li><a href="<?= e($site->phoneUrl()) ?>"><?= e($site->phoneDisplay) ?></a></li>
        <li><a href="<?= e($site->emailUrl()) ?>"><?= str_replace('@', '<wbr>@', e($site->email)) ?></a></li>
        <li><?= e($site->city) ?></li>
      </ul>
    </div>
    <nav class="site-footer__column" aria-labelledby="pied-informations">
      <h2 class="site-footer__title" id="pied-informations">Informations</h2>
      <ul>
        <li><a href="/mentions-legales">Mentions légales</a></li>
        <li><a href="/confidentialite">Confidentialité</a></li>
        <li><a href="/cgu">Conditions d'utilisation</a></li>
      </ul>
    </nav>
  </div>
  <div class="site-footer__meta">
    <p>Ce site ne dépose aucun cookie.</p>
    <p>© <?= e(date('Y')) ?> TECHNUM</p>
  </div>
</footer>
