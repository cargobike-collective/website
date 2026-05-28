<?php
/** @var \Kirby\Cms\Block $block */
$heading = $block->heading();
?>
<section class="intro">
  <div class="container">
    <?php if ($heading->isNotEmpty()): ?>
      <div class="intro__header">
        <h1 class="display-xl"><?= $heading->kti() ?></h1>
      </div>
    <?php endif ?>
  </div>
</section>
