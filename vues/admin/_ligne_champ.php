<?php
/* Une question du formulaire de participation. Paramètres : $cle, $c (question), $d (attribut disabled), $peut_modifier */
$avec_choix = in_array($c['type'], ['choix', 'cases'], true);
?>
<div class="ycm-pa-ligne" data-ligne>
  <input type="hidden" name="champ_id[<?= e($cle) ?>]" value="<?= (int) $c['id'] ?>">
  <div class="ycm-pa-champs-question">
    <label class="ycm-pa-large">Question<input type="text" name="champ_libelle[<?= e($cle) ?>]" value="<?= e($c['libelle']) ?>" maxlength="200" placeholder="Exemple : Quelles sont vos disponibilités ?"<?= $d ?>></label>
    <label>Type de réponse
      <select name="champ_type[<?= e($cle) ?>]" data-type-champ<?= $d ?>>
        <?php foreach (TYPES_CHAMP_PARTICIPATION as $t => $lib): ?><option value="<?= $t ?>"<?= $c['type'] === $t ? ' selected' : '' ?>><?= e($lib) ?></option><?php endforeach ?>
      </select>
    </label>
    <label class="ycm-case-ligne ycm-pa-requis"><input type="checkbox" name="champ_requis[<?= e($cle) ?>]" value="1"<?= $c['requis'] ? ' checked' : '' ?><?= $d ?>> Réponse obligatoire</label>
    <label class="ycm-pa-large" data-options<?= $avec_choix ? '' : ' hidden' ?>>Choix proposés, un par ligne<textarea name="champ_options[<?= e($cle) ?>]" rows="3" placeholder="Matin&#10;Après-midi&#10;Week-end"<?= $d ?>><?= e((string) $c['options']) ?></textarea></label>
    <label class="ycm-pa-large">Précision sous la question (facultative)<input type="text" name="champ_aide[<?= e($cle) ?>]" value="<?= e($c['aide']) ?>" maxlength="300"<?= $d ?>></label>
  </div>
  <?php if ($peut_modifier): ?><div class="ycm-pa-boutons-ligne"><button type="button" data-monter aria-label="Monter">↑</button><button type="button" data-descendre aria-label="Descendre">↓</button><button type="button" data-retirer class="ycm-pa-retirer">Retirer</button></div><?php endif ?>
</div>
