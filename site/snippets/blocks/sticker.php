<?php
/** @var \Kirby\Cms\Block $block */
$stickers = $block->stickers()->toStructure();
if ($stickers->count() === 0) {
  return;
}
$page   = $block->parent();
$anchor = $block->anchor();
?>
<section class="sticker-section"<?= $anchor->isNotEmpty() ? ' id="' . $anchor->esc('attr') . '"' : '' ?>>
  <div class="sticker-canvas">
    <?php $z = 0; foreach ($stickers as $sticker): ?>
      <?php
        $file = $page->image($sticker->image()->value());
        if (!$file) {
          continue;
        }
        $z++;
        $x = (float) $sticker->x()->or(40)->value();
        $y = (float) $sticker->y()->or(40)->value();
        $w = (float) $sticker->width()->or(20)->value();
        $r = (float) $sticker->rotation()->or(0)->value();
      ?>
      <div
        class="sticker"
        style="left: <?= $x ?>%; top: <?= $y ?>%; width: <?= $w ?>%; z-index: <?= $z ?>; transform: rotate(<?= $r ?>deg);"
      >
        <img class="sticker__img" src="<?= $file->url() ?>" alt="<?= $file->alt()->html() ?>" loading="lazy">
      </div>
    <?php endforeach ?>
  </div>
</section>
