<?php
/**
 * @var Technum\Content\ProductStage $stage
 * @var bool $animated
 */
?>
<span class="track<?= $animated ? ' track--animated' : '' ?>" aria-hidden="true">
  <?php foreach (\Technum\Content\ProductStage::ordered() as $step) : ?>
    <span class="track__bit<?= $step->position() <= $stage->position() ? ' track__bit--done' : '' ?>"></span>
  <?php endforeach ?>
</span>
