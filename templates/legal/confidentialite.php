<?php /** @var Technum\Content\SiteInfo $site */ ?>
<article class="legal" aria-labelledby="titre-page">
  <div class="legal__inner">
    <div class="legal__content">
      <h1 id="titre-page">Politique de confidentialité</h1>
      <p class="legal__updated">Dernière mise à jour&nbsp;: 2 octobre 2026</p>

      <h2>Responsable du traitement</h2>
      <p>Elisée Magloire ATONDE, qui exploite la marque TECHNUM à <?= e($site->city) ?>. Contact&nbsp;: <a href="<?= e($site->emailUrl()) ?>"><?= e($site->email) ?></a>.</p>

      <h2>Données reçues</h2>
      <p>Le site ne reçoit de données personnelles que par son formulaire de contact&nbsp;: votre nom, votre organisation si vous l'indiquez, votre adresse e-mail, le type de besoin et votre message.</p>
      <p>Le site ne dépose aucun cookie et n'utilise aucun outil de mesure d'audience. Comme tout serveur web, celui de l'hébergeur garde un journal technique des connexions, avec l'adresse IP, la page demandée, la date et l'heure, pour la sécurité du service.</p>

      <h2>Usage de vos données</h2>
      <p>Vos données servent uniquement à répondre à votre demande et, si vous le souhaitez, à préparer une proposition. Elles ne sont ni vendues ni cédées, et ne servent à aucune prospection.</p>

      <h2>Base légale</h2>
      <p>Le traitement repose sur votre consentement, donné en cochant la case du formulaire. Vous pouvez le retirer à tout moment en nous écrivant.</p>

      <h2>Destinataires et lieu de conservation</h2>
      <p>Le formulaire transmet votre demande par e-mail à la boîte TECHNUM, hébergée par Spacemail, le service de messagerie de Spaceship, Inc. Le serveur du site se trouve à Amsterdam, aux Pays-Bas&nbsp;: vos données sont donc traitées hors du Bénin. Le site lui-même ne garde aucune copie de votre message.</p>

      <h2>Durée de conservation</h2>
      <p>Votre demande est conservée 12 mois après notre dernier échange, puis supprimée.</p>

      <h2>Protection contre les envois abusifs</h2>
      <p>Pour limiter les envois automatiques, le serveur garde pendant une heure une empreinte de votre adresse IP, calculée avec une clé secrète. L'adresse elle-même n'est jamais enregistrée par le site.</p>

      <h2>Vos droits</h2>
      <p>Conformément à la loi n°2017-20 du 20 avril 2018 portant code du numérique en République du Bénin, vous pouvez accéder à vos données, les faire rectifier ou effacer, et vous opposer à leur traitement. Écrivez à <a href="<?= e($site->emailUrl()) ?>"><?= e($site->email) ?></a>.</p>
      <p>Si vous estimez que vos droits ne sont pas respectés, vous pouvez saisir l'Autorité de protection des données à caractère personnel, l'APDP&nbsp;: <a href="https://apdp.bj">apdp.bj</a>.</p>
    </div>
  </div>
</article>
