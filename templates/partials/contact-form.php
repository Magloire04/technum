<?php
/**
 * @var Technum\Contact\ContactFormState $form
 * @var array<string, string> $needs
 */
?>
<div class="contact-form-area">
  <?php if ($form->sent) : ?>
    <p class="form-status form-status--success" role="status">Demande envoyée. Nous vous répondons à l'adresse indiquée.</p>
  <?php endif ?>
  <?php if ($form->notice !== '') : ?>
    <p class="form-status form-status--error" role="alert"><?= e($form->notice) ?></p>
  <?php endif ?>
  <form class="contact-form" method="post" action="/contact#contact" novalidate>
    <input type="hidden" name="token" value="<?= e($form->token) ?>">
    <div class="trap" aria-hidden="true">
      <label for="contact-website">Ne remplissez pas ce champ</label>
      <input id="contact-website" name="website" type="text" tabindex="-1" autocomplete="off">
    </div>

    <div class="form-field">
      <label class="form-field__label" for="contact-name">Votre nom</label>
      <input class="form-control" id="contact-name" name="name" type="text" autocomplete="name" required maxlength="100" value="<?= e($form->value('name')) ?>"<?= $form->ariaAttributes('name') ?>>
      <?php if ($form->hasError('name')) : ?>
        <p class="form-error" id="<?= e($form->errorId('name')) ?>"><?= e($form->error('name')) ?></p>
      <?php endif ?>
    </div>

    <div class="form-field">
      <label class="form-field__label" for="contact-organization">Votre organisation <span class="form-field__hint">(facultatif)</span></label>
      <input class="form-control" id="contact-organization" name="organization" type="text" autocomplete="organization" maxlength="120" value="<?= e($form->value('organization')) ?>"<?= $form->ariaAttributes('organization') ?>>
      <?php if ($form->hasError('organization')) : ?>
        <p class="form-error" id="<?= e($form->errorId('organization')) ?>"><?= e($form->error('organization')) ?></p>
      <?php endif ?>
    </div>

    <div class="form-field">
      <label class="form-field__label" for="contact-email">Votre adresse e-mail</label>
      <input class="form-control" id="contact-email" name="email" type="email" inputmode="email" autocomplete="email" required maxlength="254" value="<?= e($form->value('email')) ?>"<?= $form->ariaAttributes('email') ?>>
      <?php if ($form->hasError('email')) : ?>
        <p class="form-error" id="<?= e($form->errorId('email')) ?>"><?= e($form->error('email')) ?></p>
      <?php endif ?>
    </div>

    <div class="form-field">
      <label class="form-field__label" for="contact-need">Votre besoin</label>
      <select class="form-control" id="contact-need" name="need" required<?= $form->ariaAttributes('need') ?>>
        <option value="">Choisissez</option>
        <?php foreach ($needs as $value => $label) : ?>
          <option value="<?= e($value) ?>"<?= $form->value('need') === $value ? ' selected' : '' ?>><?= e($label) ?></option>
        <?php endforeach ?>
      </select>
      <?php if ($form->hasError('need')) : ?>
        <p class="form-error" id="<?= e($form->errorId('need')) ?>"><?= e($form->error('need')) ?></p>
      <?php endif ?>
    </div>

    <div class="form-field">
      <label class="form-field__label" for="contact-message">Votre message</label>
      <textarea class="form-control" id="contact-message" name="message" rows="6" required maxlength="3000"<?= $form->ariaAttributes('message', 'contact-message-aide') ?>><?= e($form->value('message')) ?></textarea>
      <p class="form-field__help" id="contact-message-aide">Entre 20 et 3&nbsp;000 caractères. <span class="form-count" id="contact-message-compte"></span></p>
      <?php if ($form->hasError('message')) : ?>
        <p class="form-error" id="<?= e($form->errorId('message')) ?>"><?= e($form->error('message')) ?></p>
      <?php endif ?>
    </div>

    <div class="form-consent">
      <input id="contact-consent" name="consent" type="checkbox" value="oui" required<?= $form->value('consent') === 'oui' ? ' checked' : '' ?><?= $form->ariaAttributes('consent', 'contact-consent-lien') ?>>
      <label for="contact-consent">J'accepte que TECHNUM utilise ces informations pour répondre à ma demande.</label>
      <p class="form-consent__link" id="contact-consent-lien"><a href="/confidentialite">Lire la politique de confidentialité</a></p>
      <?php if ($form->hasError('consent')) : ?>
        <p class="form-error" id="<?= e($form->errorId('consent')) ?>"><?= e($form->error('consent')) ?></p>
      <?php endif ?>
    </div>

    <button class="button button--primary contact-form__submit" type="submit">Envoyer la demande</button>
  </form>
</div>
