<?php
/** @var \Kirby\Cms\Block $block */
$heading = $block->heading();
$text    = $block->text();
?>
<div class="feature">
  <?php if ($heading->isNotEmpty()): ?>
    <h3 class="display-s"><?= $heading->html() ?></h3>
  <?php endif ?>
  <?php if ($text->isNotEmpty()): ?>
    <p class="paragraph-italic"><?= $text->kti() ?></p>
  <?php endif ?>
</div>
