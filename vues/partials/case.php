<?php /* Case à cocher. Paramètres : $nom, $libelle, $coche (bool), $erreur, $classes_bloc */ $id = 'champ-' . $nom; ?>
<div class="<?= e($classes_bloc ?? '') ?>">
  <label class="flex cursor-pointer items-start gap-3 text-sm text-encre">
    <input type="checkbox" name="<?= e($nom) ?>" id="<?= e($id) ?>" value="1"<?= !empty($coche) ? ' checked' : '' ?> class="mt-0.5 size-5 shrink-0 rounded border-ligne accent-vert"<?= !empty($erreur) ? ' aria-invalid="true" aria-describedby="' . e($id) . '-erreur"' : '' ?>>
    <span><?= $libelle_html ?? e($libelle) ?></span>
  </label>
  <?php if (!empty($erreur)): ?><p id="<?= e($id) ?>-erreur" class="erreur ml-8"><?= e($erreur) ?></p><?php endif ?>
</div>
