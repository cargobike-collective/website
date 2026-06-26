<?php
/**
 * @var Kirby\Cms\App $kirby
 * @var Kirby\Cms\Site $site
 * @var Kirby\Cms\Page $page
 */

$date  = $page->date();
$tag   = $page->tags();
$link  = $page->link();
$text  = $page->text();
$cover = $page->cover()->toFile();

// German date parts for the Luma-style date block.
$weekdays   = ['Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag'];
$months     = [1 => 'Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'];
$monthsAbbr = [1 => 'Jan', 'Feb', 'Mär', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dez'];

// Address for the location block: street as the title, "zip city" beneath it.
// Uses the address-specific city, falling back to the categorization tag.
$addrCity    = $page->addressCity()->or($tag)->value();
$street      = $page->street()->value() ?? '';
$zipCity     = trim($page->zip()->value() . ' ' . $addrCity);
$locTitle    = $street !== '' ? $street : $zipCity;
$locSub      = $street !== '' ? $zipCity : '';
$hasLocation = $locTitle !== '';

// Google Maps search link built from the full address.
$mapsQuery = trim(implode(', ', array_filter([$street, $zipCity])));
$mapsUrl   = $mapsQuery !== '' ? 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($mapsQuery) : '';
?>
<!DOCTYPE html>
<html lang="<?= $kirby->multilang() ? $kirby->language()->code() : 'de' ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $page->title() ?> – <?= $site->title() ?></title>
  <meta name="description" content="<?= $text->or($page->title())->excerpt(160) ?>">

  <link rel="shortcut icon" href="<?= url('assets/images/favicon.png') ?>" type="image/png">
  <link rel="apple-touch-icon" href="<?= url('assets/images/webclip.png') ?>">
  <?= css(['assets/css/reset.css', 'assets/css/fonts.css', 'assets/css/main.css']) ?>
</head>
<body class="page-event">

  <?php snippet('header') ?>

  <main class="event bleed-grid">
    <a class="event__back" href="<?= url('events') ?>">← Events</a>

    <div class="event__layout">
        <?php if ($cover): ?>
          <aside class="event__aside">
            <figure class="event__hero">
              <img
                src="<?= $cover->crop(640, 640)->url() ?>"
                srcset="<?= $cover->crop(320, 320)->url() ?> 320w, <?= $cover->crop(640, 640)->url() ?> 640w, <?= $cover->crop(900, 900)->url() ?> 900w"
                sizes="(max-width: 760px) 100vw, 320px"
                width="640"
                height="640"
                alt="<?= $cover->alt()->html() ?>"
              >
            </figure>
          </aside>
        <?php endif ?>

        <div class="event__main">
          <h1 class="event__title display-l"><?= $page->title()->html() ?></h1>

          <div class="event__facts">
            <?php if ($date->isNotEmpty()): ?>
              <?php $n = (int) $date->toDate('n'); ?>
              <a class="event__fact event__fact--link" href="<?= $page->url() ?>.ics" download title="Zum Kalender hinzufügen">
                <span class="event__cal" aria-hidden="true">
                  <span class="event__cal-month"><?= $monthsAbbr[$n] ?></span>
                  <span class="event__cal-day"><?= $date->toDate('j') ?></span>
                </span>
                <span class="event__fact-text">
                  <span class="event__fact-title">
                    <time datetime="<?= $date->toDate('c') ?>">
                      <?= $weekdays[(int) $date->toDate('w')] ?>, <?= $date->toDate('j') ?>. <?= $months[$n] ?> <?= $date->toDate('Y') ?>
                    </time>
                    <svg class="event__fact-ext" viewBox="0 0 16 16" width="13" height="13" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                      <rect x="2" y="3" width="12" height="11" rx="1.5"/>
                      <path d="M2 6.5h12M5 1.5v2.5M11 1.5v2.5M8 8.5v3M6.5 10h3"/>
                    </svg>
                  </span>
                  <?php if ($date->toDate('H:i') !== '00:00'): ?>
                    <span class="event__fact-sub"><?= $date->toDate('H:i') ?> Uhr</span>
                  <?php endif ?>
                </span>
              </a>
            <?php endif ?>

            <?php if ($hasLocation): ?>
              <a class="event__fact event__fact--link" href="<?= esc($mapsUrl) ?>" target="_blank" rel="noopener" title="Auf Google Maps öffnen">
                <span class="event__fact-icon" aria-hidden="true">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                    <circle cx="12" cy="10" r="3"/>
                  </svg>
                </span>
                <span class="event__fact-text">
                  <span class="event__fact-title">
                    <?= esc($locTitle) ?>
                    <svg class="event__fact-ext" viewBox="0 0 16 16" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                      <path d="M5 11 11 5M6 5h5v5"/>
                    </svg>
                  </span>
                  <?php if ($locSub !== ''): ?>
                    <span class="event__fact-sub"><?= esc($locSub) ?></span>
                  <?php endif ?>
                </span>
              </a>
            <?php endif ?>
          </div>

          <?php if ($text->isNotEmpty()): ?>
            <div class="event__text"><?= $text->kt() ?></div>
          <?php endif ?>

          <?php if ($link->isNotEmpty()): ?>
            <p class="event__cta">
              <a class="btn btn--signal" href="<?= esc($link->toUrl()) ?>" target="_blank" rel="noopener">
                <?= $page->buttonCaption()->or('Zur Veranstaltung')->html() ?>
              </a>
            </p>
          <?php endif ?>
        </div>
      </div>
  </main>

  <?php snippet('footer') ?>

  <?= js('assets/js/main.js', ['defer' => true]) ?>
</body>
</html>
