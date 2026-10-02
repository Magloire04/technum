<?php /** @var Technum\Content\SiteInfo $site */ ?>
<article class="legal" aria-labelledby="titre-page">
  <div class="legal__inner">
    <div class="legal__content">
      <h1 id="titre-page">Mentions légales</h1>
      <p class="legal__updated">Dernière mise à jour&nbsp;: 2 octobre 2026</p>

      <h2>Éditeur du site</h2>
      <p>Le site bytechnum.com est édité par Elisée Magloire ATONDE, qui exploite la marque TECHNUM à <?= e($site->city) ?>.</p>
      <ul>
        <li>E-mail&nbsp;: <a href="<?= e($site->emailUrl()) ?>"><?= e($site->email) ?></a></li>
        <li>Téléphone et WhatsApp&nbsp;: <a href="<?= e($site->phoneUrl()) ?>"><?= e($site->phoneDisplay) ?></a></li>
      </ul>
      <p>Directeur de la publication&nbsp;: Elisée Magloire ATONDE.</p>

      <h2>Hébergement</h2>
      <p>Le site est hébergé par Spaceship, Inc., 4600 East Washington Street, Suite 300, Phoenix, AZ 85034, États-Unis, sur un serveur situé à Amsterdam, aux Pays-Bas. Site web&nbsp;: <a href="https://www.spaceship.com">spaceship.com</a>.</p>

      <h2>Propriété intellectuelle</h2>
      <p>Les textes, le logo TECHNUM, les visuels et la mise en page de ce site appartiennent à Elisée Magloire ATONDE. Les captures d'écran montrent des produits édités par TECHNUM. Toute reproduction, même partielle, demande une autorisation écrite préalable.</p>

      <h2>Liens</h2>
      <p>Ce site renvoie vers les produits TECHNUM, hébergés sur des sous-domaines de bytechnum.com et soumis à leurs propres conditions, ainsi que vers des sites tiers comme GitHub. TECHNUM n'est pas responsable du contenu des sites tiers.</p>

      <h2>Contact</h2>
      <p>Pour toute question sur ce site, écrivez à <a href="<?= e($site->emailUrl()) ?>"><?= e($site->email) ?></a>.</p>
    </div>
  </div>
</article>
