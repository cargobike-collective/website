<?php
/** @var \Kirby\Cms\Block $block */
$file = $block->file()->toFile();
if (!$file) {
  return;
}
$height    = $block->height()->or(500)->toInt();
$anchor    = $block->anchor();
$sectionBg = $block->sectionBackground()->value();

$style = '--animation-height: ' . $height . 'px';
if ($sectionBg) {
  $style .= '; background-color: ' . $sectionBg;
}

// Load the dotLottie player only once per request, even with several animations.
static $playerLoaded = false;
?>
<div
  class="animation<?= $sectionBg ? ' animation--filled' : '' ?>"
  style="<?= htmlspecialchars($style, ENT_QUOTES) ?>"
  <?= $anchor->isNotEmpty() ? 'id="' . $anchor->esc('attr') . '"' : '' ?>
>
  <dotlottie-player
    src="<?= $file->url() ?>"
    <?= $block->autoplay()->toBool() ? 'autoplay' : '' ?>
    <?= $block->loop()->toBool() ? 'loop' : '' ?>
    aria-hidden="true"
  ></dotlottie-player>
</div>
<?php if (!$playerLoaded): $playerLoaded = true; ?>
  <?= js('assets/js/vendor/dotlottie-player.min.js', ['defer' => true]) ?>
<?php endif ?>
