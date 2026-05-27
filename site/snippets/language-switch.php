<?php if ($kirby->multilang()): ?>
  <div class="lang-switch">
    <?php foreach ($kirby->languages() as $i => $language): ?>
      <?php if ($i > 0): ?><span class="lang-switch__divider">/</span><?php endif ?>
      <a
        href="<?= $page->url($language->code()) ?>"
        hreflang="<?= $language->code() ?>"
        class="lang-switch__link<?= $language->code() === $kirby->language()->code() ? ' is-active' : '' ?>"
      ><?= html(strtoupper($language->code())) ?></a>
    <?php endforeach ?>
  </div>
<?php endif ?>
