<?php
/**
 * @var Kirby\Cms\App $kirby
 * @var Kirby\Cms\Site $site
 * @var Kirby\Cms\Page $page
 */
?>
<!DOCTYPE html>
<html lang="<?= $kirby->multilang() ? $kirby->language()->code() : 'de' ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $site->title() ?></title>
  <meta name="description" content="bringing people together, moving cities forward">

  <link rel="shortcut icon" href="<?= url('assets/images/favicon.png') ?>" type="image/png">
  <link rel="apple-touch-icon" href="<?= url('assets/images/webclip.png') ?>">
  <?= css('assets/css/main.css') ?>
</head>
<body class="page-home">

  <?php snippet('header') ?>

  <main>
    <?= $page->blocks()->toBlocks() ?>
  </main>

  <?= js('assets/js/main.js', ['defer' => true]) ?>
</body>
</html>
