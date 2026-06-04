<?php
/** @var \Kirby\Cms\Block $block */
$eyebrow   = $block->eyebrow();
$text      = $block->text();
$bg        = $block->background()->or('left')->value();
$color     = $block->backgroundColor()->or('#f0ff39')->value();
$sectionBg = $block->sectionBackground()->value();
$anchor    = $block->anchor();

// Switch text to light on dark backgrounds (relative luminance).
$darkBg = false;
if ($sectionBg && preg_match('/^#?([0-9a-f]{6})$/i', $sectionBg, $m)) {
  $r = hexdec(substr($m[1], 0, 2));
  $g = hexdec(substr($m[1], 2, 2));
  $b = hexdec(substr($m[1], 4, 2));
  $darkBg = (0.299 * $r + 0.587 * $g + 0.114 * $b) < 140;
}

$classes = ['section-header'];
if ($sectionBg) {
  $classes[] = 'section-header--filled';
}
if ($darkBg) {
  $classes[] = 'section-header--dark';
}
?>
<section
  class="<?= implode(' ', $classes) ?>"
  <?= $anchor->isNotEmpty() ? 'id="' . $anchor->esc('attr') . '"' : '' ?>
  <?= $sectionBg ? 'style="background-color: ' . htmlspecialchars($sectionBg, ENT_QUOTES) . '"' : '' ?>
>
  <div class="header header--bg-<?= $bg ?>">
    <div class="header-content">
      <?php if ($eyebrow->isNotEmpty()): ?>
        <h2 class="display-l"><?= $eyebrow->html() ?></h2>
      <?php endif ?>
      <?php if ($text->isNotEmpty()): ?>
        <p class="header-paragraph"><?= $text->kti() ?></p>
      <?php endif ?>
    </div>
    <?php if ($bg !== 'none'): ?>
      <div class="header__background" aria-hidden="true" style="color: <?= htmlspecialchars($color, ENT_QUOTES) ?>">
        <?= svg('assets/images/CBC-stripes-pattern.svg') ?>
      </div>
    <?php endif ?>
  </div>
</section>
