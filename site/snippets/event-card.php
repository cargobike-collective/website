<?php
/** @var \Kirby\Cms\Page $event */
$cover = $event->cover()->toFile() ?? $event->image();
?>
<a class="event-card" href="<?= $event->url() ?>">
  <span class="event-card__thumb">
    <?php if ($cover): ?>
      <img
        src="<?= $cover->crop(176, 176)->url() ?>"
        width="88"
        height="88"
        alt="<?= $cover->alt()->html() ?>"
        loading="lazy"
      >
    <?php endif ?>
  </span>
  <span class="event-card__body">
    <?php if ($event->date()->isNotEmpty()): ?>
      <span class="event-card__date"><?= $event->date()->toDate('d.m.Y, H:i') ?> Uhr</span>
    <?php endif ?>
    <span class="event-card__title"><?= $event->title()->html() ?></span>
    <?php if ($event->tags()->isNotEmpty()): ?>
      <span class="event-card__tags"><?= $event->tags()->html() ?></span>
    <?php endif ?>
  </span>
</a>
