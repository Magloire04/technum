<?php
/**
 * Liste d'onglets des produits. Masquée sans JavaScript : les panneaux s'affichent alors les uns sous les autres.
 *
 * @var Technum\View\View $view
 * @var list<Technum\Content\Product> $products
 */
?>
<div class="product-tabs" role="tablist" aria-label="Nos produits" hidden>
  <?php foreach ($products as $index => $product) : ?>
    <button class="product-tabs__tab" type="button" role="tab" id="onglet-<?= e($product->slug) ?>" aria-controls="<?= e($product->anchor()) ?>" aria-selected="<?= $index === 0 ? 'true' : 'false' ?>" tabindex="<?= $index === 0 ? '0' : '-1' ?>">
      <img class="product-tabs__icon" src="<?= e($view->asset($product->icon)) ?>" alt="" width="28" height="28">
      <span class="product-tabs__label">
        <span class="product-tabs__name"><?= e($product->name) ?></span>
        <span class="product-tabs__stage"><?= e($product->stage->label()) ?></span>
      </span>
    </button>
  <?php endforeach ?>
</div>
