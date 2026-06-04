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
  <?= css(['assets/css/reset.css', 'assets/css/fonts.css', 'assets/css/main.css']) ?>
</head>
<body class="page-home">

  <?php snippet('header') ?>

  <main class="bleed-grid">
    <?php foreach ($page->layout()->toLayouts() as $layout): ?>
      <?php
        $columns = $layout->columns();
        $multi   = $columns->count() > 1;

        // Horizontal placement in the full-bleed grid (see main.css):
        //   full    → edge-to-edge (hero images)
        //   wide    → container width (animation, multi-column rows)
        //   content → reading width (text)
        $type  = $columns->first()->blocks()->first()?->type();
        $bleed = match (true) {
          $type === 'hero'           => 'full',
          $type === 'section-header' => 'full', // full-bleed background; .header keeps text at reading width
          $type === 'animation'      => 'full', // full-bleed background; player stays container width
          $multi                     => 'wide',
          $type === 'sticker'        => 'wide',
          default                    => 'content',
        };
      ?>
      <div class="layout-row layout-row--<?= $bleed ?><?= $multi ? ' layout-row--multi' : '' ?>">
        <?php foreach ($columns as $column): ?>
          <?php
            // Convert "1/3", "1/2" etc. into a 12-column grid span.
            [$num, $den] = array_pad(explode('/', $column->width()), 2, 1);
            $span = (int) round((int) $num * 12 / max(1, (int) $den));
          ?>
          <div class="layout-col" style="--span: <?= $span ?>">
            <?= $column->blocks() ?>
          </div>
        <?php endforeach ?>
      </div>
    <?php endforeach ?>
  </main>

  <?php snippet('footer') ?>

  <?= js('assets/js/main.js', ['defer' => true]) ?>
</body>
</html>
