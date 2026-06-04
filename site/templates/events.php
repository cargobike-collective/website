<?php
/**
 * @var Kirby\Cms\App $kirby
 * @var Kirby\Cms\Site $site
 * @var Kirby\Cms\Page $page
 */

$events   = $page->children()->listed();
$now      = time();
$upcoming = $events->filter(fn ($e) => $e->date()->toDate() >= $now)->sortBy('date', 'asc');
$past     = $events->filter(fn ($e) => $e->date()->toDate() < $now)->sortBy('date', 'desc');
?>
<!DOCTYPE html>
<html lang="<?= $kirby->multilang() ? $kirby->language()->code() : 'de' ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $page->title() ?> – <?= $site->title() ?></title>
  <meta name="description" content="Events vom Cargobike Collective in Hamburg und Stuttgart.">

  <link rel="shortcut icon" href="<?= url('assets/images/favicon.png') ?>" type="image/png">
  <link rel="apple-touch-icon" href="<?= url('assets/images/webclip.png') ?>">
  <?= css(['assets/css/reset.css', 'assets/css/fonts.css', 'assets/css/main.css']) ?>
</head>
<body class="page-events">

  <?php snippet('header') ?>

  <main class="bleed-grid events">
    <header class="events__header">
      <h1 class="display-l"><?= $page->title()->html() ?></h1>
    </header>

    <?php if ($upcoming->isNotEmpty()): ?>
      <section class="events__group">
        <h2 class="events__group-title">Kommende Events</h2>
        <div class="events__list">
          <?php foreach ($upcoming as $event): ?>
            <?php snippet('event-card', ['event' => $event]) ?>
          <?php endforeach ?>
        </div>
      </section>
    <?php endif ?>

    <?php if ($past->isNotEmpty()): ?>
      <section class="events__group">
        <h2 class="events__group-title">Vergangene Events</h2>
        <div class="events__list">
          <?php foreach ($past as $event): ?>
            <?php snippet('event-card', ['event' => $event]) ?>
          <?php endforeach ?>
        </div>
      </section>
    <?php endif ?>

    <?php if ($events->isEmpty()): ?>
      <p class="events__empty">Aktuell sind keine Events geplant.</p>
    <?php endif ?>
  </main>

  <?php snippet('footer') ?>

  <?= js('assets/js/main.js', ['defer' => true]) ?>
</body>
</html>
