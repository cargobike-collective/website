<?php
/** @var \Kirby\Cms\Block $block */
$eyebrow = $block->eyebrow();
$text    = $block->text();
$bg      = $block->background()->or('left')->value();
$color   = $block->backgroundColor()->or('#f0ff39')->value();
$anchor  = $block->anchor();
?>
<section
  class="section-header"
  <?= $anchor->isNotEmpty() ? 'id="' . $anchor->esc('attr') . '"' : '' ?>
>
  <div class="container">
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
  </div>
</section>
