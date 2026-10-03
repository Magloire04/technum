<?php
/**
 * @var Technum\View\View $view
 * @var Technum\Content\Product $product
 */
$image = $product->image;
?>
<article class="product" id="<?= e($product->anchor()) ?>" aria-labelledby="<?= e($product->anchor()) ?>-nom">
  <figure class="product__media product__media--<?= e($image->frame) ?>">
    <div class="product__frame">
      <img src="<?= e($view->asset($image->src)) ?>"<?php if ($image->srcSmall !== '') : ?> srcset="<?= e($view->asset($image->srcSmall)) ?> <?= intdiv($image->width, 2) ?>w, <?= e($view->asset($image->src)) ?> <?= $image->width ?>w" sizes="(min-width: 60rem) 34rem, 100vw"<?php endif ?> width="<?= $image->width ?>" height="<?= $image->height ?>" alt="<?= e($image->alt) ?>" loading="lazy" decoding="async">
    </div>
    <figcaption class="product__host"><?= e($product->host()) ?></figcaption>
  </figure>
  <div class="product__text">
    <h3 class="product__name" id="<?= e($product->anchor()) ?>-nom"><?= e($product->name) ?></h3>
    <p class="product__tagline"><?= e($product->tagline) ?></p>
    <p class="product__audience">Pour qui&nbsp;: <?= e($product->audience) ?></p>
    <?= $view->render('partials/stages', ['stage' => $product->stage]) ?>
    <dl class="product__facts">
      <div>
        <dt>Déjà en place</dt>
        <dd><?= e($product->done) ?></dd>
      </div>
      <div>
        <dt>En cours</dt>
        <dd><?= $product->isStable() ? 'Version stable, maintenue.' : e($product->next) ?></dd>
      </div>
    </dl>
    <?php if ($product->note !== '') : ?>
      <p class="product__note"><?= e($product->note) ?></p>
    <?php endif ?>
    <a class="button button--secondary product__action" href="<?= e($product->trackedUrl()) ?>">Ouvrir <?= e($product->name) ?></a>
  </div>
</article>
