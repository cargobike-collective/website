<?php
/** @var \Kirby\Cms\Block $block */
$images = $block->image()->toFiles();
if ($images->count() === 0) {
  return;
}

$height   = $block->height()->or('large')->value();
$slider   = $images->count() > 1;
$autoplay = $slider && $block->autoplay()->toBool();

// Only the first hero on the page falls back to #start (the top-of-page anchor).
// Any other hero stays without an id unless it sets its own Anker-ID, so we
// never emit a duplicate id="start".
$page        = $block->parent();
$firstHeroId = $page->layout()->toLayouts()->toBlocks()->filterBy('type', 'hero')->first()?->id();
$anchor      = $block->anchor()->or($block->id() === $firstHeroId ? 'start' : '');

// Single <img> renderer, shared by the single-image and slider markup.
$renderImage = function ($image, $eager) use ($block) {
  $alt = $image->alt()->or($block->alt());
  ?>
  <img
    class="hero__image"
    src="<?= $image->url() ?>"
    srcset="<?= $image->srcset([500, 800, 1080, 1920]) ?>"
    sizes="100vw"
    width="<?= $image->width() ?>"
    height="<?= $image->height() ?>"
    alt="<?= $alt->html() ?>"
    loading="<?= $eager ? 'eager' : 'lazy' ?>"
    <?= $eager ? 'fetchpriority="high"' : '' ?>
  >
  <?php
};
?>
<section<?= $anchor->isNotEmpty() ? ' id="' . $anchor->esc('attr') . '"' : '' ?> class="hero hero--<?= $height ?><?= $slider ? ' hero--slider' : '' ?>"<?= $autoplay ? ' data-autoplay="6000"' : '' ?>>
<?php if ($slider): ?>
  <div class="hero__track" tabindex="0" role="group" aria-roledescription="Slider" aria-label="Bildergalerie">
    <?php foreach ($images->values() as $i => $image): ?>
      <div class="hero__slide" role="group" aria-roledescription="Folie" aria-label="Bild <?= $i + 1 ?> von <?= $images->count() ?>">
        <?php $renderImage($image, $i === 0) ?>
      </div>
    <?php endforeach ?>
  </div>
  <button class="hero__nav hero__nav--prev" type="button" aria-label="Vorheriges Bild">
    <svg viewBox="0 0 24 24" width="24" height="24" fill="none" aria-hidden="true"><path d="M15 5l-7 7 7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
  </button>
  <button class="hero__nav hero__nav--next" type="button" aria-label="Nächstes Bild">
    <svg viewBox="0 0 24 24" width="24" height="24" fill="none" aria-hidden="true"><path d="M9 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
  </button>
  <div class="hero__dots" role="group" aria-label="Bildauswahl">
    <?php foreach ($images->values() as $i => $image): ?>
      <button class="hero__dot<?= $i === 0 ? ' is-active' : '' ?>" type="button" aria-label="Bild <?= $i + 1 ?>"<?= $i === 0 ? ' aria-current="true"' : '' ?>></button>
    <?php endforeach ?>
  </div>
<?php else: ?>
  <?php $renderImage($images->first(), true) ?>
<?php endif ?>
</section>
