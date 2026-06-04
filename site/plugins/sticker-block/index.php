<?php

/**
 * Sticker block — drag-and-drop Panel editor.
 *
 * The block's blueprint (site/blueprints/blocks/sticker.yml) and frontend
 * snippet (site/snippets/blocks/sticker.php) are auto-discovered by Kirby.
 * This plugin only exists so the Panel loads index.js / index.css, which
 * register the custom `k-block-type-sticker` Vue component.
 */
Kirby::plugin('cbc/sticker-block', []);
