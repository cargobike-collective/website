<?php
/** @var \Kirby\Cms\Block $block */
$image  = $block->image()->toFile();
$anchor = $block->anchor()->or('start');
$height = $block->height()->or('large')->value();
?>
<?php if ($image): ?>
<section id="<?= $anchor->html() ?>" class="hero hero--<?= $height ?>">
  <img
    class="hero__image"
    src="<?= $image->url() ?>"
    srcset="<?= $image->srcset([500, 800, 1080, 1920]) ?>"
    sizes="100vw"
    width="<?= $image->width() ?>"
    height="<?= $image->height() ?>"
    alt="<?= $block->alt()->or($image->alt())->html() ?>"
    fetchpriority="high"
  >
</section>
<?php endif ?>
