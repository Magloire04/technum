<?php
/**
 * @var Technum\View\View $view
 * @var string $content
 * @var array{title: string, description: string, path: string, robots?: string} $meta
 */
$canonical = 'https://bytechnum.com' . $meta['path'];
?>
<!doctype html>
<html lang="fr" class="no-js">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($meta['title']) ?></title>
  <meta name="description" content="<?= e($meta['description']) ?>">
<?php if (isset($meta['robots'])) : ?>
  <meta name="robots" content="<?= e($meta['robots']) ?>">
<?php else : ?>
  <link rel="canonical" href="<?= e($canonical) ?>">
<?php endif ?>
  <link rel="icon" href="/favicon.ico" sizes="48x48">
  <link rel="icon" href="<?= e($view->asset('img/favicon.svg')) ?>" type="image/svg+xml">
  <link rel="apple-touch-icon" href="/apple-touch-icon.png">
  <link rel="preload" href="/assets/fonts/montserrat-700.woff2" as="font" type="font/woff2" crossorigin>
  <link rel="preload" href="/assets/fonts/poppins-400.woff2" as="font" type="font/woff2" crossorigin>
  <link rel="stylesheet" href="<?= e($view->asset('css/site.css')) ?>">
  <script src="<?= e($view->asset('js/site.js')) ?>"></script>
</head>
<body>
  <a class="skip-link" href="#contenu">Aller au contenu</a>
  <?= $view->render('partials/header') ?>
  <main id="contenu" tabindex="-1">
<?= $content ?>
  </main>
  <?= $view->render('partials/footer') ?>
</body>
</html>
