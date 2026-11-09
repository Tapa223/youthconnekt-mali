<?php
/* Un rôle et ses questions. Paramètres : $rk (clé), $r (rôle avec 'champs'), $d, $peut_modifier */
$champs = $r['champs'] ?? [];
?>
<div class="ycm-pa-role-bloc" data-ligne data-role-cle="<?= e($rk) ?>">
  <input type="hidden" name="role_id[<?= e($rk) ?>]" value="<?= (int) $r['id'] ?>">
  <div class="ycm-pa-role-haut">
    <div class="ycm-pa-champs-role">
      <label>Nom du rôle<input type="text" name="role_nom[<?= e($rk) ?>]" value="<?= e($r['nom']) ?>" maxlength="120" placeholder="Exemple : Participant, Bénévole, Intervenant"<?= $d ?>></label>
      <label>Places<input type="number" min="1" name="role_places[<?= e($rk) ?>]" value="<?= e((string) ($r['places'] ?? '')) ?>" placeholder="Sans limite"<?= $d ?>></label>
      <label class="ycm-pa-large">Comment la personne prend part
        <select name="role_mode[<?= e($rk) ?>]"<?= $d ?>>
          <?php foreach (MODES_ROLE as $m => $lib): ?><option value="<?= $m ?>"<?= ($r['mode'] ?? 'candidature') === $m ? ' selected' : '' ?>><?= e($lib) ?></option><?php endforeach ?>
        </select>
        <small class="ycm-pa-aide">Inscription : la personne reçoit son billet aussitôt. Candidature : elle répond aux questions, vous choisissez qui retenir, le billet arrive ensuite.</small>
      </label>
      <label class="ycm-pa-large">Ce que fera la personne (facultatif)<input type="text" name="role_description[<?= e($rk) ?>]" value="<?= e((string) $r['description']) ?>" maxlength="300" placeholder="Exemple : animer un atelier de 30 minutes"<?= $d ?>></label>
    </div>
    <?php if ($peut_modifier): ?><div class="ycm-pa-boutons-ligne"><button type="button" data-monter aria-label="Monter le rôle">↑</button><button type="button" data-descendre aria-label="Descendre le rôle">↓</button><button type="button" data-retirer class="ycm-pa-retirer" data-retirer-question="Retirer ce rôle et ses questions ?">Retirer</button></div><?php endif ?>
  </div>
  <details class="ycm-pa-questions"<?= $champs ? '' : ' open' ?>>
    <summary class="ycm-pa-questions-titre">Questions pour ce rôle <span>(<?= count($champs) ?>)</span> <em>Afficher ou masquer</em></summary>
    <div class="ycm-pa-lignes" data-lignes="champs">
      <?php foreach ($champs as $ck => $c): ?>
      <?php partiel('admin/_ligne_champ', ['rk' => $rk, 'cle' => 'c' . $ck, 'c' => $c, 'd' => $d, 'peut_modifier' => $peut_modifier]) ?>
      <?php endforeach ?>
    </div>
    <?php if (!$champs): ?><p class="ycm-discret" data-aucune-question>Aucune question : la personne choisit ce rôle et confirme, rien de plus.</p><?php endif ?>
    <?php if ($peut_modifier): ?><button type="button" class="ycm-bouton-contour ycm-petit" data-ajouter="champs">Ajouter une question</button><?php endif ?>
  </details>
</div>
