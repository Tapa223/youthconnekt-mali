<?php
/* Liste générique : tableau en carte, cases à cocher et actions groupées. */
partiel('admin/_entete_liste', get_defined_vars());
$avec_actions = $r['actions'] && $peut_modifier;
$id_form = 'liste-' . $r['slug'];
?>
<?php if ($lignes): ?>
<form method="post" action="<?= url('/gestion/' . $r['slug'] . '/action') ?>" id="<?= $id_form ?>" class="ycm-liste" data-liste>
  <?= csrf() ?><input type="hidden" name="suivant" value="<?= e(adresse_actuelle()) ?>">
  <?php if ($avec_actions): /* Cases à cocher masquées : « Sélectionner » les fait apparaître avec les actions groupées */ ?>
  <div class="ycm-liste-outils">
    <button type="button" class="ycm-bouton-selection" data-basculer-selection aria-pressed="false">
      <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="5" cy="12" r="1.6"/><circle cx="12" cy="12" r="1.6"/><circle cx="19" cy="12" r="1.6"/></svg>
      <span data-libelle-selection>Sélectionner</span>
    </button>
  </div>
  <div class="ycm-actions-groupees">
    <label for="action-<?= $r['slug'] ?>">Action :</label>
    <select name="action" id="action-<?= $r['slug'] ?>">
      <option value="">Choisir…</option>
      <?php foreach ($r['actions'] as $cle => [$libelle]): ?><option value="<?= e($cle) ?>"><?= e($libelle) ?></option><?php endforeach ?>
    </select>
    <button type="submit" class="ycm-bouton-vert ycm-petit">Appliquer</button>
    <span class="ycm-selection" data-compte-selection>0 sur <?= count($lignes) ?> sélectionné</span>
  </div>
  <?php endif ?>
  <div class="ycm-resultats<?= $avec_actions ? ' ycm-resultats--actions' : '' ?>">
    <table class="ycm-tableau">
      <thead>
        <tr>
          <?php if ($avec_actions): ?><th class="ycm-case"><input type="checkbox" data-tout-cocher aria-label="Tout sélectionner"></th><?php endif ?>
          <?php foreach ($r['colonnes'] as $col): ?><th class="<?= e($col['classe'] ?? '') ?>"><?= e($col[0]) ?></th><?php endforeach ?>
          <?php if (empty($r['pas_de_lien_modifier'])): ?><th class="ycm-droite">Actions</th><?php endif ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($lignes as $l):
            $url_modifier = url('/gestion/' . $r['slug'] . '/' . $l['id']);
            $voir = $r['url_voir'] ? ($r['url_voir'])($l) : null;
            if ($voir && isset($l['statut']) && $l['statut'] !== 'publie') $voir = null; ?>
        <tr>
          <?php if ($avec_actions): ?><td class="ycm-case"><input type="checkbox" name="ids[]" value="<?= (int) $l['id'] ?>" aria-label="Sélectionner"></td><?php endif ?>
          <?php foreach ($r['colonnes'] as $i_col => $col): ?>
          <td class="<?= e($col['classe'] ?? '') ?>" data-label="<?= $i_col ? e($col[0]) : '' ?>"><?php if (!empty($col['lien'])): ?><a href="<?= e($url_modifier) ?>" class="ycm-lien-ligne"><?= ($col[1])($l) ?></a><?php else: ?><?= ($col[1])($l) ?><?php endif ?></td>
          <?php endforeach ?>
          <?php if (empty($r['pas_de_lien_modifier'])): ?>
          <td class="ycm-droite ycm-td-actions"><?= liens_ligne(array_filter(['Voir' => $voir, 'Modifier' => $url_modifier])) ?></td>
          <?php endif ?>
        </tr>
        <?php endforeach ?>
      </tbody>
    </table>
  </div>
</form>
<?php partiel('admin/_pagination', get_defined_vars()) ?>
<?php if ($r['slug'] === 'newsletter'): foreach ($lignes as $l): ?>
<form id="basculer-<?= (int) $l['id'] ?>" method="post" action="<?= url('/gestion/newsletter/' . $l['id'] . '/basculer') ?>" hidden><?= csrf() ?><input type="hidden" name="suivant" value="<?= e(adresse_actuelle()) ?>"></form>
<?php endforeach; endif ?>
<?php else: ?>
<div class="ycm-boite ycm-boite--pad"><p class="ycm-vide"><?= $q !== '' || $filtres ? 'Aucun élément ne correspond à cette recherche.' : 'Aucun élément pour l\'instant.' ?></p></div>
<?php endif ?>
