<?php
/*
 * Un champ du formulaire d'administration.
 * Paramètres : $nom, $def (définition), $valeur, $erreur, $ligne, $desactive
 */
$id = 'f-' . $nom;
$type = $def['type'];
$requis = !empty($def['requis']) && !in_array($type, ['case', 'lecture', 'cache'], true);
$desactive = $desactive || !empty($def['lecture_seule']);
$attr = ($desactive ? ' disabled' : '') . ($erreur ? ' aria-invalid="true"' : '') . (!empty($def['max']) ? ' maxlength="' . (int) $def['max'] . '"' : '');
$classe = 'ycm-ligne-champ' . (!empty($def['largeur']) ? ' ycm-' . $def['largeur'] : '') . ' ycm-type-' . $type;
if ($type === 'cache') return;
?>
<div class="<?= $classe ?>">
  <?php if ($type === 'case'): ?>
    <label class="ycm-case-ligne"><input type="checkbox" id="<?= $id ?>" name="<?= e($nom) ?>" value="1"<?= $valeur ? ' checked' : '' ?><?= $attr ?>> <?= e($def['libelle']) ?></label>
  <?php else: ?>
    <label for="<?= $id ?>"<?= $requis ? ' class="requis"' : '' ?>><?= e($def['libelle']) ?></label>
    <?php switch ($type):
      case 'zone': ?>
        <textarea id="<?= $id ?>" name="<?= e($nom) ?>" rows="<?= (int) ($def['lignes'] ?? 6) ?>"<?= $attr ?><?= !empty($def['barre']) ? ' data-barre="' . e($def['barre']) . '"' : '' ?>><?= e($valeur) ?></textarea>
      <?php break; case 'select': ?>
        <select id="<?= $id ?>" name="<?= e($nom) ?>"<?= $attr ?>>
          <?php if (!$requis && !isset($def['options'][''])): ?><option value="">—</option><?php endif ?>
          <?php foreach ($def['options'] as $cle => $libelle): ?><option value="<?= e($cle) ?>"<?= (string) $cle === (string) $valeur ? ' selected' : '' ?>><?= e($libelle) ?></option><?php endforeach ?>
        </select>
      <?php break; case 'date': ?>
        <input type="date" id="<?= $id ?>" name="<?= e($nom) ?>" value="<?= e($valeur ? substr((string) $valeur, 0, 10) : '') ?>"<?= $attr ?>>
      <?php break; case 'datetime': ?>
        <input type="datetime-local" id="<?= $id ?>" name="<?= e($nom) ?>" value="<?= e($valeur ? date('Y-m-d\TH:i', strtotime((string) $valeur)) : '') ?>"<?= $attr ?>>
      <?php break; case 'nombre': ?>
        <input type="number" min="0" id="<?= $id ?>" name="<?= e($nom) ?>" value="<?= e($valeur) ?>"<?= $attr ?>>
      <?php break; case 'motdepasse': ?>
        <input type="password" id="<?= $id ?>" name="<?= e($nom) ?>" autocomplete="new-password"<?= $attr ?>>
      <?php break; case 'lecture': ?>
        <div class="ycm-lecture-seule"><?= $ligne ? ($def['html'])($ligne) : '—' ?></div>
      <?php break; case 'image': ?>
        <div class="ycm-zone-image">
          <?php if ($valeur): ?><img src="<?= e(media($valeur)) ?>" alt="" class="ycm-apercu"><?php endif ?>
          <input type="file" id="<?= $id ?>" name="<?= e($nom) ?>" accept="image/jpeg,image/png,image/webp"<?= $attr ?>>
          <small>JPG, PNG ou WebP, 3 Mo maximum. Les très grandes images sont réduites automatiquement.</small>
          <?php if ($valeur && !$desactive): ?><label class="ycm-case-ligne ycm-case-petite"><input type="checkbox" name="supprimer_<?= e($nom) ?>" value="1"> Retirer l'image actuelle</label><?php endif ?>
        </div>
        <?php if (!empty($def['phototheque']) && !$desactive && ($photos_choix = photos_phototheque())): ?>
        <details class="ycm-phototheque">
          <summary>Ou choisir dans la photothèque (<?= count($photos_choix) ?>)</summary>
          <div class="ycm-phototheque-grille">
            <?php foreach ($photos_choix as $ph): ?>
            <label title="<?= e($ph['titre']) ?>"><input type="radio" name="phototheque_<?= e($nom) ?>" value="<?= e($ph['image']) ?>"<?= $valeur === $ph['image'] ? ' checked' : '' ?>><img src="<?= e(media($ph['image'])) ?>" alt="" loading="lazy"></label>
            <?php endforeach ?>
          </div>
        </details>
        <?php endif ?>
      <?php break; case 'communautes': ?>
        <ul class="ycm-cases">
          <?php foreach (options_communautes() as $cid => $libelle): ?>
          <li><label><input type="checkbox" name="<?= e($nom) ?>[]" value="<?= $cid ?>"<?= in_array((int) $cid, (array) $valeur, true) ? ' checked' : '' ?><?= $desactive ? ' disabled' : '' ?>> <?= e($libelle) ?></label></li>
          <?php endforeach ?>
        </ul>
      <?php break; default: ?>
        <input type="<?= $type === 'email' ? 'email' : ($type === 'url' ? 'url' : 'text') ?>" id="<?= $id ?>" name="<?= e($nom) ?>" value="<?= e($valeur) ?>"<?= $attr ?><?= $type === 'url' ? ' placeholder="https://"' : '' ?>>
    <?php endswitch ?>
  <?php endif ?>
  <?php if ($erreur): ?><p class="ycm-erreur"><?= e($erreur) ?></p><?php endif ?>
  <?php if (!empty($def['aide'])): ?><p class="ycm-aide"><?= e($def['aide']) ?></p><?php endif ?>
</div>
