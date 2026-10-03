<?php /** @var Technum\Content\SiteInfo $site */ ?>
<aside class="contact-direct" aria-labelledby="titre-contact-direct">
  <h3 class="contact-direct__title" id="titre-contact-direct">Vous préférez un échange direct&nbsp;?</h3>
  <a class="button button--secondary contact-direct__action" href="<?= e($site->whatsappUrl()) ?>">Écrire sur WhatsApp</a>
  <ul class="contact-direct__list" role="list">
    <li><span class="contact-direct__label">Téléphone</span><a href="<?= e($site->phoneUrl()) ?>"><?= e($site->phoneDisplay) ?></a></li>
    <li><span class="contact-direct__label">E-mail</span><a href="<?= e($site->emailUrl()) ?>"><?= str_replace('@', '<wbr>@', e($site->email)) ?></a></li>
    <li><span class="contact-direct__label">GitHub</span><a href="<?= e($site->githubUrl) ?>"><?= e($site->githubLabel()) ?></a></li>
    <li><span class="contact-direct__label">Adresse</span><?= e($site->city) ?></li>
  </ul>
</aside>
