<?php
/** @var \Kirby\Cms\Block $block */
$events = site()->find('events')?->children()->listed() ?? new Kirby\Cms\Pages();

// Upcoming events first (soonest), falling back to the most recent past ones.
// Limited by the configurable count. Stays an empty collection when there are
// no events, so the section renders a "Keine Events gefunden" message instead.
$list = $events;
if ($list->isNotEmpty()) {
  $now      = time();
  $upcoming = $list->filter(fn ($e) => $e->date()->toDate() >= $now)->sortBy('date', 'asc');
  $list     = $upcoming->isNotEmpty() ? $upcoming : $list->sortBy('date', 'desc');

  $count = $block->count()->toInt();
  if ($count > 0) {
    $list = $list->limit($count);
  }
}

$anchor     = $block->anchor();
$tags       = $list->pluck('tags', ',', true);
$showFilter = $block->showFilter()->toBool() && count($tags) > 1;

$weekdays = ['So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa'];
?>
<section class="events"<?= $anchor->isNotEmpty() ? ' id="' . $anchor->esc('attr') . '"' : '' ?>>
  <?php if ($showFilter): ?>
    <div class="events__filter">
      <button class="events__tab is-active" type="button" data-filter="all">Alle</button>
      <?php foreach ($tags as $tag): ?>
        <button class="events__tab" type="button" data-filter="<?= esc($tag, 'attr') ?>"><?= esc($tag) ?></button>
      <?php endforeach ?>
    </div>
  <?php endif ?>

  <?php if ($list->isEmpty()): ?>
    <p class="events__empty">Keine Events gefunden.</p>
  <?php else: ?>
  <ul class="events__items">
    <?php foreach ($list as $event): ?>
      <?php
        $date = $event->date();
        $tag  = $event->tags()->value();
        // No own content on the detail page → link straight to the external URL.
        $external = $event->text()->isEmpty() && $event->link()->isNotEmpty();
        $href     = $external ? $event->link()->toUrl() : $event->url();
      ?>
      <li class="events__item" data-tag="<?= esc($tag, 'attr') ?>">
        <a class="events__link" href="<?= esc($href) ?>"<?= $external ? ' target="_blank" rel="noopener"' : '' ?>>
          <span class="events__info">
            <span class="events__day"><?= $weekdays[(int) $date->toDate('w')] ?></span>
            <span class="events__date"><?= $date->toDate('d.m.Y') ?></span>
            <span class="events__title"><?= $event->title()->html() ?></span>
            <span class="events__location"><?= esc($tag) ?></span>
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
  <?php endif ?>
</section>
