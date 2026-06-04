<?php
/** @var \Kirby\Cms\Block $block */
$heading = $block->heading();
$anchor  = $block->anchor();
?>
<section class="intro"<?= $anchor->isNotEmpty() ? ' id="' . $anchor->esc('attr') . '"' : '' ?>>
  <?php if ($heading->isNotEmpty()): ?>
    <div class="intro__header">
      <h1 class="display-xl"><?= $heading->kti() ?></h1>
    </div>
  <?php endif ?>
</section>
