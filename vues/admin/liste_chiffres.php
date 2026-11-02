<?php partiel('admin/_entete_liste', get_defined_vars()); $publies = array_values(array_filter($lignes, fn ($c) => $c['publie'])); ?>
<?php if ($lignes): ?>
<form method="post" action="<?= url('/gestion/chiffres/enregistrer') ?>">
  <?= csrf() ?>
  <div class="ycm-resultats">
    <table class="ycm-tableau">
      <thead><tr><th>Libellé</th><th>Valeur</th><th>Ordre</th><th>Publié</th><th class="ycm-droite">Actions</th></tr></thead>
      <tbody>
        <?php foreach ($lignes as $c): ?>
        <tr>
          <td><strong><?= e($c['libelle']) ?></strong></td>
          <td><input type="text" name="valeur[<?= $c['id'] ?>]" value="<?= e($c['valeur']) ?>" maxlength="30" class="ycm-champ-court" aria-label="Valeur de « <?= e($c['libelle']) ?> »"<?= $peut_modifier ? '' : ' disabled' ?>></td>
          <td><input type="number" min="0" name="ordre[<?= $c['id'] ?>]" value="<?= (int) $c['ordre'] ?>" class="ycm-champ-court ycm-champ-nombre" aria-label="Ordre"<?= $peut_modifier ? '' : ' disabled' ?>></td>
          <td><input type="checkbox" name="publie[<?= $c['id'] ?>]" value="1"<?= $c['publie'] ? ' checked' : '' ?> aria-label="Publié"<?= $peut_modifier ? '' : ' disabled' ?>></td>
          <td class="ycm-droite"><?= liens_ligne(['Modifier' => url('/gestion/chiffres/' . $c['id'])]) ?></td>
        </tr>
        <?php endforeach ?>
      </tbody>
    </table>
  </div>
  <?php if ($peut_modifier): ?><p style="margin:14px 0 0"><button type="submit" class="ycm-bouton-vert">Enregistrer les chiffres</button></p><?php endif ?>
</form>
<section class="ycm-boite ycm-boite--pad" style="margin-top:22px" aria-labelledby="titre-apercu">
  <div class="ycm-boite-tete ycm-boite-tete--nu"><h2 id="titre-apercu">Aperçu sur l'accueil</h2><span class="ycm-note">Les trois premiers chiffres publiés</span></div>
  <?php if ($publies): ?>
  <div class="ycm-apercu-chiffres">
    <?php foreach (array_slice($publies, 0, 3) as $c): ?><div><strong><?= e($c['valeur']) ?></strong><span aria-hidden="true"></span><small><?= e($c['libelle']) ?></small></div><?php endforeach ?>
  </div>
  <?php else: ?><p class="ycm-vide">Aucun chiffre publié : la ligne de chiffres est masquée sur l'accueil.</p><?php endif ?>
</section>
<?php else: ?>
<div class="ycm-boite ycm-boite--pad"><p class="ycm-vide">Aucun chiffre pour l'instant. Ajoutez des chiffres vérifiés (membres, événements, régions…) puis publiez-les.</p></div>
<?php endif ?>
