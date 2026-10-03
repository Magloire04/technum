<?php
/**
 * @var Technum\View\View $view
 * @var list<Technum\Content\Product> $products
 */
?>
<div class="product-strip">
  <ul class="product-strip__list" role="list">
    <?php foreach ($products as $product) : ?>
      <?php
      $image = $product->image;
      $small = $image->srcSmall !== '';
      ?>
      <li>
        <a class="product-tile" href="#<?= e($product->anchor()) ?>">
          <span class="product-tile__shot product-tile__shot--<?= e($image->frame) ?>">
            <img src="<?= e($view->asset($small ? $image->srcSmall : $image->src)) ?>" alt="" width="<?= $small ? intdiv($image->width, 2) : $image->width ?>" height="<?= $small ? intdiv($image->height, 2) : $image->height ?>" loading="lazy" decoding="async">
          </span>
          <span class="product-tile__body">
            <span class="product-tile__head">
              <span class="product-tile__name"><?= e($product->name) ?></span>
              <span class="chip chip--<?= e($product->stage->value) ?>"><?= e($product->stage->label()) ?></span>
            </span>
            <span class="product-tile__summary"><?= e($product->summary) ?></span>
          </span>
        </a>
      </li>
    <?php endforeach ?>
  </ul>
</div>
