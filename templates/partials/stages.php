<?php /** @var Technum\Content\ProductStage $stage */ ?>
<ol class="stages" role="list" aria-label="Étapes du produit">
  <?php foreach (\Technum\Content\ProductStage::ordered() as $step) : ?>
    <li class="stages__step<?= $step->position() <= $stage->position() ? ' stages__step--done' : '' ?>"<?= $step === $stage ? ' aria-current="step"' : '' ?>><?= e($step->label()) ?></li>
  <?php endforeach ?>
</ol>
