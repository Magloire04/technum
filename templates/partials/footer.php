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
        <li><a href="<?= e($site->emailUrl()) ?>"><?= e($site->email) ?></a></li>
        <li><?= e($site->city) ?></li>
      </ul>
    </div>
  </div>
  <div class="site-footer__meta">
    <p>Ce site ne dépose aucun cookie.</p>
    <p>© <?= e(date('Y')) ?> TECHNUM</p>
  </div>
</footer>
