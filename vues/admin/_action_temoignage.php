<?php
/* Formulaire d'une seule action sur des témoignages. Paramètres : $id_form, $ids, $action, $suivant, $confirmer (facultatif : [question, texte, bouton]) */
$attrs = !empty($confirmer) ? ' data-confirmer="' . e($confirmer[0]) . '" data-confirmer-texte="' . e($confirmer[1]) . '" data-confirmer-oui="' . e($confirmer[2]) . '"' : '';
?>
<form id="<?= e($id_form) ?>" method="post" action="<?= url('/gestion/temoignages/action') ?>" hidden<?= $attrs ?>>
  <?= csrf() ?><input type="hidden" name="suivant" value="<?= e($suivant) ?>">
  <?php foreach ((array) $ids as $i): ?><input type="hidden" name="ids[]" value="<?= (int) $i ?>"><?php endforeach ?>
  <?php if ($action !== ''): ?><input type="hidden" name="action" value="<?= e($action) ?>"><?php endif ?>
</form>
