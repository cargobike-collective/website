<?php
/** @var \Kirby\Cms\Block $block */
$fields = $block->fields()->toStructure();
if ($fields->isEmpty()) {
  return;
}

$action  = $block->action()->value();
$method  = strtolower($block->method()->or('post')->value()) === 'get' ? 'get' : 'post';
$submit  = $block->submit()->or('Senden');
$anchor  = $block->anchor();
$consent = $block->consent()->toBool();
$base    = 'form-' . $block->id();

// Only text-like inputs for now.
$allowed = ['text', 'email', 'tel', 'textarea'];
?>
<form class="form"<?= $anchor->isNotEmpty() ? ' id="' . $anchor->esc('attr') . '"' : '' ?><?= $action ? ' action="' . esc($action, 'attr') . '"' : '' ?> method="<?= $method ?>">
  <div class="form__fields">
    <?php foreach ($fields as $i => $field): ?>
      <?php
        $type = $field->type()->or('text')->value();
        if (in_array($type, $allowed, true) === false) {
          $type = 'text';
        }
        $name        = $field->name()->or($field->label())->value();
        $placeholder = $field->placeholder()->value();
        $label       = $field->label()->or($field->placeholder())->or($name);
        $required    = $field->required()->toBool();
        $span        = $field->width()->value() === '1/2' ? 1 : 2;
        $id          = $base . '-' . $i;
      ?>
      <div class="form__field" style="--span: <?= $span ?>">
        <label class="form__label" for="<?= $id ?>"><?= $label->html() ?></label>
        <?php if ($type === 'textarea'): ?>
          <textarea class="form__input form__input--textarea" id="<?= $id ?>" name="<?= esc($name, 'attr') ?>" placeholder="<?= esc($placeholder, 'attr') ?>"<?= $required ? ' required' : '' ?>></textarea>
        <?php else: ?>
          <input class="form__input" type="<?= $type ?>" id="<?= $id ?>" name="<?= esc($name, 'attr') ?>" placeholder="<?= esc($placeholder, 'attr') ?>"<?= $required ? ' required' : '' ?>>
        <?php endif ?>
      </div>
    <?php endforeach ?>
  </div>

  <?php if ($consent): ?>
    <label class="form__consent">
      <input class="form__consent-check" type="checkbox" name="<?= esc($block->consentName()->or('OPT_IN')->value(), 'attr') ?>" value="1" required>
      <span class="form__consent-text"><?= $block->consentText()->kirbytextinline() ?></span>
    </label>
  <?php endif ?>

  <button class="form__submit" type="submit"><?= $submit->html() ?></button>
</form>
