<?php
/*
 * Champ de formulaire du site.
 * Paramètres : $nom, $libelle, $type (text, email, password, tel, url, select, textarea, file),
 * $valeur, $erreur, $aide, $requis (bool), $options (pour select), $attributs (tableau),
 * $mdp (bouton Afficher), $classes_bloc
 */
$id = $id ?? 'champ-' . $nom;
$type = $type ?? 'text';
$requis = $requis ?? true;
$attrs = $attributs ?? [];
$decrit = !empty($erreur) ? $id . '-erreur' : (!empty($aide) ? $id . '-aide' : '');
$commun = 'id="' . e($id) . '" name="' . e($nom) . '"'
    . (!empty($erreur) ? ' aria-invalid="true"' : '')
    . ($decrit ? ' aria-describedby="' . e($decrit) . '"' : '');
foreach ($attrs as $a => $v) {
    $commun .= ' ' . e($a) . '="' . e($v) . '"';
}
$classe_champ = !empty($mdp) ? 'champ pr-24' : 'champ';
?>
<div class="<?= e($classes_bloc ?? '') ?>">
  <label for="<?= e($id) ?>" class="libelle"><?= e($libelle) ?><?php if (!$requis): ?> <span class="font-normal text-texte">(facultatif)</span><?php endif ?></label>
  <?php if ($type === 'select'): ?>
    <select <?= $commun ?> class="champ">
      <?php foreach ($options as $cle => $texte): ?><option value="<?= e($cle) ?>"<?= (string) $cle === (string) ($valeur ?? '') ? ' selected' : '' ?>><?= e($texte) ?></option><?php endforeach ?>
    </select>
  <?php elseif ($type === 'textarea'): ?>
    <textarea <?= $commun ?> class="champ"><?= e($valeur ?? '') ?></textarea>
  <?php elseif ($type === 'tel'): [$indicatif, $national] = telephone_decoupe((string) ($valeur ?? '')); ?>
    <div class="flex gap-2">
      <select name="<?= e($nom) ?>_indicatif" class="champ w-[8.75rem] shrink-0 !pl-3 !pr-8" aria-label="Indicatif du pays" autocomplete="tel-country-code">
        <?php foreach (INDICATIFS as $code => $pays): ?><option value="<?= e($code) ?>"<?= $code === ($indicatif ?: '+223') ? ' selected' : '' ?>><?= e($pays) ?></option><?php endforeach ?>
      </select>
      <input type="tel" <?= $commun ?> class="champ min-w-0 flex-1" value="<?= e($national) ?>">
    </div>
  <?php elseif (!empty($mdp)): ?>
    <div class="relative">
      <input type="password" <?= $commun ?> class="<?= $classe_champ ?>">
      <button type="button" class="absolute top-1.5 right-1.5 min-h-9 rounded-md bg-gris px-3 text-sm font-bold text-vert hover:bg-vert-clair" data-afficher-mdp aria-controls="<?= e($id) ?>" aria-pressed="false">Afficher</button>
    </div>
  <?php else: ?>
    <input type="<?= e($type) ?>" <?= $commun ?> class="champ"<?= $type !== 'password' && $type !== 'file' ? ' value="' . e($valeur ?? '') . '"' : '' ?>>
  <?php endif ?>
  <?php if (!empty($erreur)): ?><p id="<?= e($id) ?>-erreur" class="erreur"><?= e($erreur) ?></p>
  <?php elseif (!empty($aide)): ?><p id="<?= e($id) ?>-aide" class="aide"><?= e($aide) ?></p><?php endif ?>
</div>
