<?php
/** @var \Kirby\Cms\Block $block */
$heading   = $block->heading();
$animation = $block->animation()->toBool();

// Load the dotLottie player only once per request, even with several animations.
static $playerLoaded = false;
?>
<section class="intro">
  <div class="container">
    <?php if ($heading->isNotEmpty()): ?>
      <div class="intro__header">
        <h1 class="display-xl"><?= $heading->kti() ?></h1>
      </div>
    <?php endif ?>

    <?php if ($animation): ?>
      <div class="intro__animation">
        <dotlottie-player
          src="<?= url('assets/animations/01_CBC_Animation_LOOP_Lottie.lottie') ?>"
          autoplay
          loop
          aria-hidden="true"
        ></dotlottie-player>
      </div>
      <?php if (!$playerLoaded): $playerLoaded = true; ?>
        <?= js('assets/js/vendor/dotlottie-player.min.js', ['defer' => true]) ?>
      <?php endif ?>
    <?php endif ?>
  </div>
</section>
