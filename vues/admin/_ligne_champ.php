<?php
/* Une question d'un rôle. Paramètres : $rk (clé du rôle), $cle, $c (question), $d (attribut disabled), $peut_modifier */
$avec_choix = in_array($c['type'], ['choix', 'cases'], true);
$n = fn ($champ) => 'champ_' . $champ . '[' . e($rk) . '][' . e($cle) . ']';
?>
<div class="ycm-pa-ligne ycm-pa-question" data-ligne>
  <input type="hidden" name="<?= $n('id') ?>" value="<?= (int) $c['id'] ?>">
  <div class="ycm-pa-champs-question">
    <label class="ycm-pa-large">Question<input type="text" name="<?= $n('libelle') ?>" value="<?= e($c['libelle']) ?>" maxlength="200" placeholder="Exemple : Quelle thématique souhaitez-vous aborder ?"<?= $d ?>></label>
    <label>Type de réponse
      <select name="<?= $n('type') ?>" data-type-champ<?= $d ?>>
        <?php foreach (TYPES_CHAMP_PARTICIPATION as $t => $lib): ?><option value="<?= $t ?>"<?= $c['type'] === $t ? ' selected' : '' ?>><?= e($lib) ?></option><?php endforeach ?>
      </select>
    </label>
    <label class="ycm-case-ligne ycm-pa-requis"><input type="checkbox" name="<?= $n('requis') ?>" value="1"<?= $c['requis'] ? ' checked' : '' ?><?= $d ?>> Réponse obligatoire</label>
    <label class="ycm-pa-large" data-options<?= $avec_choix ? '' : ' hidden' ?>>Choix proposés, un par ligne<textarea name="<?= $n('options') ?>" rows="3" placeholder="Atelier&#10;Conférence&#10;Témoignage"<?= $d ?>><?= e((string) $c['options']) ?></textarea></label>
    <label class="ycm-pa-large">Précision sous la question (facultative)<input type="text" name="<?= $n('aide') ?>" value="<?= e((string) $c['aide']) ?>" maxlength="300"<?= $d ?>></label>
  </div>
  <?php if ($peut_modifier): ?><div class="ycm-pa-boutons-ligne"><button type="button" data-monter aria-label="Monter la question">↑</button><button type="button" data-descendre aria-label="Descendre la question">↓</button><button type="button" data-retirer class="ycm-pa-retirer">Retirer</button></div><?php endif ?>
</div>
