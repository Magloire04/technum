<?php
/**
 * @var Technum\View\View $view
 * @var Technum\Content\SiteInfo $site
 * @var list<Technum\Content\Product> $products
 */
?>
<aside class="register" aria-labelledby="titre-registre">
  <h2 class="register__title" id="titre-registre">Nos produits aujourd'hui</h2>
  <ul class="register__list" role="list">
    <?php foreach ($products as $product) : ?>
      <li class="register__item">
        <a class="register__link" href="#<?= e($product->anchor()) ?>">
          <img class="register__icon" src="<?= e($view->asset($product->icon)) ?>" alt="" width="28" height="28">
          <span class="register__name"><?= e($product->name) ?></span>
          <?= $view->render('partials/track', ['stage' => $product->stage, 'animated' => true]) ?>
          <span class="register__stage"><?= e($product->stage->label()) ?></span>
        </a>
      </li>
    <?php endforeach ?>
  </ul>
  <p class="register__updated">Mis à jour le <?= e($site->updatedAtLabel()) ?></p>
</aside>
