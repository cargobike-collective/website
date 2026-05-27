<?php
// One-pager section anchors. Adjust labels/targets as sections are built.
$menu = [
  'start'      => 'Start',
  'events'     => 'Events',
  'about'      => 'About',
  'newsletter' => 'Newsletter',
  'contact'    => 'Contact',
];
?>
<nav class="nav" aria-label="Hauptnavigation">
  <button class="nav__toggle" type="button" aria-expanded="false" aria-controls="nav-menu" aria-label="Menü öffnen">
    <span class="nav__toggle-bar"></span>
    <span class="nav__toggle-bar"></span>
    <span class="nav__toggle-bar"></span>
  </button>

  <div class="nav__menu" id="nav-menu" hidden>
    <div class="container nav__menu-header">
      <?php snippet('logo') ?>
      <button class="nav__close" type="button" aria-label="Menü schließen">
        <svg viewBox="0 0 32 32" width="32" height="32" aria-hidden="true" focusable="false">
          <path d="M6 6 26 26M26 6 6 26" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
        </svg>
      </button>
    </div>

    <ul class="container nav__list">
      <?php foreach ($menu as $anchor => $label): ?>
        <li class="nav__item">
          <a href="#<?= $anchor ?>" class="nav__link"><?= html($label) ?></a>
        </li>
      <?php endforeach ?>
    </ul>

    <div class="container nav__menu-footer">
      <?php snippet('language-switch') ?>
    </div>
  </div>
</nav>
