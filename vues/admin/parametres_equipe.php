<?php /* Paramètres : comptes de l'équipe et contenus d'exemple (affiché sous les champs). */
$super = ($admin['role_admin'] ?? '') === 'super'; $couleurs = ['orange', 'turquoise', 'bleu', 'vert']; ?>
<?php if (peut('comptes')): ?>
<section class="ycm-boite ycm-boite--pad" aria-labelledby="titre-equipe">
  <div class="ycm-boite-tete ycm-boite-tete--nu">
    <h2 id="titre-equipe">Comptes administrateurs</h2>
    <?php if ($super): ?><a href="<?= url('/gestion/comptes/ajouter') ?>" class="ycm-bouton-contour">Ajouter un administrateur</a><?php endif ?>
  </div>
  <ul class="ycm-liste-admins">
    <?php foreach ($equipe as $k => $a): $a = completer_utilisateur($a); ?>
    <li>
      <span class="ycm-personne"><span class="ycm-avatar ycm-avatar--<?= $couleurs[$k % 4] ?>" aria-hidden="true"><?= e(initiales($a['nom_affiche'])) ?></span><span><strong><?= e($a['nom_affiche']) ?></strong><small><?= e(ROLES_ADMIN[$a['role_admin']] ?? '') ?> · <?= e($a['email']) ?></small></span></span>
      <a href="<?= url('/gestion/comptes/' . $a['id']) ?>" class="ycm-lien">Modifier</a>
    </li>
    <?php endforeach ?>
  </ul>
  <p class="ycm-note" style="margin-top:12px">Rôles : <?= e(implode(' · ', ROLES_ADMIN)) ?>. Un rôle se choisit dans la fiche du compte.</p>
</section>
<?php endif ?>
<?php if ($super): ?>
<section class="ycm-boite ycm-boite--pad" aria-labelledby="titre-exemples">
  <div class="ycm-boite-tete ycm-boite-tete--nu"><h2 id="titre-exemples">Contenus d'exemple</h2><span class="ycm-note"><?= $nb_exemples ?> en ligne</span></div>
  <p class="ycm-note">Les initiatives, opportunités et actualités des maquettes, pour présenter le site rempli. Elles ne sont pas officielles : retirez-les avant la mise en ligne publique.</p>
  <div class="ycm-lecture-actions">
    <button type="submit" form="form-exemples-charger" class="ycm-bouton-contour">Charger les exemples</button>
    <?php if ($nb_exemples): ?><button type="submit" form="form-exemples-retirer" class="ycm-bouton-contour ycm-contour-rouge">Retirer les exemples</button><?php endif ?>
  </div>
</section>
<?php endif ?>
