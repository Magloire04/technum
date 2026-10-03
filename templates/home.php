<?php
/**
 * @var Technum\View\View $view
 * @var list<Technum\Content\Product> $products
 * @var list<Technum\Content\Project> $projects
 * @var list<Technum\Content\Service> $services
 * @var Technum\Contact\ContactFormState $form
 * @var array<string, string> $needs
 */
?>
<section class="hero" aria-labelledby="titre-accueil">
  <div class="hero__inner">
    <div class="hero__text">
      <h1 class="hero__title" id="titre-accueil">Des solutions numériques conçues pour vos réalités.</h1>
      <p class="hero__lead">TECHNUM conçoit, met en ligne et maintient des applications pour les entreprises, les institutions et les porteurs de projets du Bénin. Nos propres produits sont déjà en service&nbsp;: vous pouvez les essayer dès maintenant.</p>
      <div class="hero__actions">
        <a class="button button--primary" href="#contact">Parler de votre projet</a>
        <a class="hero__more" href="#produits">Voir nos produits</a>
      </div>
    </div>
    <?= $view->render('partials/register') ?>
  </div>
</section>

<section class="section" id="produits" aria-labelledby="titre-produits">
  <div class="section__inner">
    <div class="section__aside">
      <div class="section__label">
        <h2 class="section__title" id="titre-produits">Nos produits</h2>
        <p class="section__context">Quatre outils conçus, hébergés et maintenus par TECHNUM.</p>
      </div>
    </div>
    <div class="section__body">
      <?php foreach ($products as $product) : ?>
        <?= $view->render('partials/product', ['product' => $product]) ?>
      <?php endforeach ?>
    </div>
  </div>
</section>

<section class="section" id="realisations" aria-labelledby="titre-realisations">
  <div class="section__inner">
    <div class="section__aside">
      <div class="section__label">
        <h2 class="section__title" id="titre-realisations">Autres réalisations</h2>
        <p class="section__context">Des preuves de concept, un produit en pause et un mandat client.</p>
      </div>
    </div>
    <div class="section__body">
      <ul class="projects" role="list">
        <?php foreach ($projects as $project) : ?>
          <li class="project" id="<?= e($project->anchor()) ?>">
            <div class="project__head">
              <h3 class="project__name"><?= e($project->name) ?></h3>
              <p class="project__nature"><?= e($project->nature) ?></p>
            </div>
            <p class="project__description"><?= e($project->description) ?></p>
            <?php if ($project->hasLink()) : ?>
              <p><a class="project__link" href="<?= e($project->linkUrl) ?>"><?= e($project->linkLabel) ?><span class="visually-hidden"> de <?= e($project->name) ?></span></a></p>
            <?php endif ?>
          </li>
        <?php endforeach ?>
      </ul>
    </div>
  </div>
</section>

<section class="section" id="services" aria-labelledby="titre-services">
  <div class="section__inner">
    <div class="section__aside">
      <div class="section__label">
        <h2 class="section__title" id="titre-services">Ce que nous faisons pour vous</h2>
        <p class="section__context">Chaque service renvoie à un exemple que vous pouvez ouvrir.</p>
      </div>
    </div>
    <div class="section__body">
      <ul class="services" role="list">
        <?php foreach ($services as $service) : ?>
          <?php $last = count($service->examples) - 1; ?>
          <li class="service">
            <h3 class="service__name"><?= e($service->name) ?></h3>
            <p class="service__description"><?= e($service->description) ?></p>
            <p class="service__examples"><?= e($service->examplesLabel()) ?>&nbsp;:
              <?php foreach ($service->examples as $index => $example) : ?><?= $index === 0 ? '' : ($index === $last ? ' et ' : ', ') ?><?php if ($example->hasLink()) : ?><a href="<?= e($example->href) ?>"><?= e($example->label) ?></a><?php else : ?><?= e($example->label) ?><?php endif ?><?php endforeach ?>
            </p>
          </li>
        <?php endforeach ?>
      </ul>
    </div>
  </div>
</section>

<section class="section" id="methode" aria-labelledby="titre-methode">
  <div class="section__inner">
    <div class="section__aside">
      <div class="section__label">
        <h2 class="section__title" id="titre-methode">Comment se passe un projet</h2>
        <p class="section__context">Nous vous accompagnons de la conception au déploiement, puis après la livraison.</p>
      </div>
    </div>
    <div class="section__body">
      <ol class="steps" role="list">
        <li class="step">
          <h3 class="step__title">Nous écoutons le besoin réel</h3>
          <p class="step__text">Qui utilisera l'outil, pour faire quoi, sur quels appareils et avec quelle connexion.</p>
        </li>
        <li class="step">
          <h3 class="step__title">Nous cadrons par écrit</h3>
          <p class="step__text">Une note de cadrage et des spécifications, validées avec vous avant le développement.</p>
        </li>
        <li class="step">
          <h3 class="step__title">Nous construisons par étapes</h3>
          <p class="step__text">Une version en ligne à tester à chaque étape, sans attendre la fin.</p>
        </li>
        <li class="step">
          <h3 class="step__title">Nous mettons en ligne</h3>
          <p class="step__text">Hébergement, nom de domaine, sauvegardes et conformité sont pris en charge.</p>
        </li>
        <li class="step">
          <h3 class="step__title">Nous vous accompagnons</h3>
          <p class="step__text">Guide d'utilisation, corrections et évolutions après la livraison.</p>
        </li>
      </ol>
      <p class="commitments">Vos données personnelles sont traitées selon la loi n°2017-20. Chaque modification du code passe par des tests automatiques. La documentation vous est remise à la livraison.</p>
    </div>
  </div>
</section>

<section class="section section--contact" id="contact" aria-labelledby="titre-contact">
  <div class="section__inner">
    <div class="section__aside">
      <div class="section__label">
        <h2 class="section__title" id="titre-contact">Parlons de votre projet</h2>
        <p class="section__context">Décrivez votre besoin en quelques lignes. Nous vous répondons par e-mail.</p>
      </div>
    </div>
    <div class="section__body">
      <div class="contact">
        <?= $view->render('partials/contact-form', ['form' => $form, 'needs' => $needs]) ?>
        <?= $view->render('partials/contact-direct') ?>
      </div>
    </div>
  </div>
</section>
