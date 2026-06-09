<?php
/** @var \Kirby\Cms\Block $block */
$events = site()->find('events')?->children()->listed();
if (!$events || $events->count() === 0) {
  return;
}

// Upcoming events first (soonest), falling back to the most recent past ones
// so the block never shows up empty. Limited by the configurable count.
$now      = time();
$upcoming = $events->filter(fn ($e) => $e->date()->toDate() >= $now)->sortBy('date', 'asc');
$list     = $upcoming->isNotEmpty() ? $upcoming : $events->sortBy('date', 'desc');

$count = $block->count()->toInt();
if ($count > 0) {
  $list = $list->limit($count);
}

$anchor     = $block->anchor();
$cities     = $list->pluck('city', ',', true);
$showFilter = $block->showFilter()->toBool() && count($cities) > 1;

$weekdays = ['So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa'];
?>
<section class="events"<?= $anchor->isNotEmpty() ? ' id="' . $anchor->esc('attr') . '"' : '' ?>>
  <?php if ($showFilter): ?>
    <div class="events__filter">
      <button class="events__tab is-active" type="button" data-filter="all">Alle</button>
      <?php foreach ($cities as $city): ?>
        <button class="events__tab" type="button" data-filter="<?= esc($city, 'attr') ?>"><?= esc($city) ?></button>
      <?php endforeach ?>
    </div>
  <?php endif ?>

  <ul class="events__items">
    <?php foreach ($list as $event): ?>
      <?php $date = $event->date(); $city = $event->city()->value(); ?>
      <li class="events__item" data-city="<?= esc($city, 'attr') ?>">
        <a class="events__link" href="<?= $event->url() ?>">
          <span class="events__info">
            <span class="events__day"><?= $weekdays[(int) $date->toDate('w')] ?></span>
            <span class="events__date"><?= $date->toDate('d.m.Y') ?></span>
            <span class="events__title"><?= $event->title()->html() ?></span>
            <span class="events__location"><?= esc($city) ?></span>
          </span>
          <span class="events__cta">
            <span class="events__cta-text">Event Details</span>
            <svg class="events__cta-icon" viewBox="0 0 20 21" width="20" height="21" fill="none" aria-hidden="true">
              <path d="M4 16.07 16 4.07M16 4.07H8M16 4.07V12.07" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </span>
        </a>
      </li>
    <?php endforeach ?>
  </ul>
</section>
