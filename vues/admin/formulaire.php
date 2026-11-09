<?php
/* Formulaire générique : cartes de champs à gauche, panneau de publication et boutons à droite. */
$desactive = !$peut_modifier;
$ajout = $ligne === null;
$action = $ajout ? url('/gestion/' . $r['slug'] . '/ajouter') : url('/gestion/' . $r['slug'] . '/' . $ligne['id']);
$groupe = fn ($g) => partiel('admin/_groupe', ['g' => $g, 'valeurs' => $valeurs, 'erreurs' => $erreurs, 'ligne' => $ligne, 'desactive' => $desactive]);
?>
<div class="ycm-entete-page">
  <div>
    <h1><?= e($titre_page) ?></h1>
    <?php if ($r['fiche_unique'] && $r['description']): ?><p class="ycm-sous-titre"><?= e($r['description']) ?></p><?php endif ?>
  </div>
  <div class="ycm-entete-actions">
    <?php if ($url_voir): ?><a href="<?= e($url_voir) ?>" class="ycm-bouton-contour" target="_blank" rel="noopener">Voir sur le site</a><?php endif ?>
    <?php if ($peut_modifier): ?><p class="ycm-note">Les champs marqués d'un astérisque sont obligatoires.</p><?php endif ?>
  </div>
</div>
<?php if (!$peut_modifier): ?><p class="ycm-alerte">Votre rôle permet de consulter cette fiche, pas de la modifier.</p><?php endif ?>

<form method="post" action="<?= e($action) ?>" enctype="multipart/form-data" novalidate data-confirmer="<?= $ajout ? 'Enregistrer ce nouvel élément ?' : 'Enregistrer les modifications ?' ?>" data-confirmer-oui="Enregistrer">
  <?= csrf() ?>
  <div class="ycm-form-grille">
    <div class="ycm-form-principal">
      <?php foreach ($groupes as $g) if (empty($g['cote'])) $groupe($g) ?>
      <?php if ($r['galerie']): ?>
      <fieldset class="ycm-module">
        <legend><h2><?= e($r['galerie'][3]) ?></h2></legend>
        <?php if ($galerie): ?>
        <ul class="ycm-galerie">
          <?php foreach ($galerie as $photo): $pid = (int) $photo['id']; ?>
          <li>
            <img src="<?= e(media($photo['image'])) ?>" alt="">
            <label>Légende<input type="text" name="galerie_legende[<?= $pid ?>]" value="<?= e($photo['legende']) ?>" maxlength="200"<?= $desactive ? ' disabled' : '' ?>></label>
            <label>Ordre<input type="number" min="0" name="galerie_ordre[<?= $pid ?>]" value="<?= (int) $photo['ordre'] ?>" class="ycm-champ-nombre"<?= $desactive ? ' disabled' : '' ?>></label>
            <?php if (!$desactive): ?><label class="ycm-case-ligne ycm-case-petite"><input type="checkbox" name="galerie_supprimer[<?= $pid ?>]" value="1"> Supprimer</label><?php endif ?>
          </li>
          <?php endforeach ?>
        </ul>
        <?php elseif ($ajout): ?><p class="ycm-aide">Les photos peuvent être ajoutées dès maintenant ou plus tard.</p><?php endif ?>
        <?php if (!$desactive): ?>
        <div class="ycm-zone-image">
          <label for="f-galerie">Ajouter des photos</label>
          <input type="file" id="f-galerie" name="galerie_nouvelles[]" accept="image/jpeg,image/png,image/webp" multiple>
          <small>Plusieurs photos à la fois possible. JPG, PNG ou WebP, 3 Mo maximum chacune.</small>
        </div>
        <?php endif ?>
      </fieldset>
      <?php endif ?>
      <?php if (!empty($r['vue_formulaire_apres'])) partiel($r['vue_formulaire_apres'], get_defined_vars()) ?>
    </div>
    <aside class="ycm-form-cote">
      <?php foreach ($groupes as $g) if (!empty($g['cote'])) $groupe($g) ?>
      <?php if ($peut_modifier): ?>
      <div class="ycm-panneau-boutons">
        <?php if (!$ajout && $r['decisions']): /* Propositions : valider ou non, en gardant les corrections */ ?>
        <?php foreach ($r['decisions'] as $valeur_d => [$libelle_d, $classe_d, $question_d, $texte_d]): if (($ligne['statut'] ?? '') === $valeur_d) continue; ?>
        <button type="submit" name="_decision" value="<?= e($valeur_d) ?>" class="<?= e($classe_d) ?>" data-confirmer="<?= e($question_d) ?>" data-confirmer-texte="<?= e($texte_d) ?>" data-confirmer-oui="<?= e($libelle_d) ?>"<?= $valeur_d === 'brouillon' ? ' data-confirmer-danger' : '' ?>><?= e($libelle_d) ?></button>
        <?php endforeach ?>
        <button type="submit" name="_enregistrer" class="ycm-bouton-contour">Enregistrer les corrections</button>
        <?php else: ?>
        <button type="submit" name="_enregistrer" class="ycm-bouton-vert">Enregistrer</button>
        <?php endif ?>
        <?php if (!$r['fiche_unique']): ?><a href="<?= url('/gestion/' . $r['slug']) ?>" class="ycm-bouton-gris">Annuler</a><?php endif ?>
        <?php if (!$ajout && $r['peut_supprimer'] && !$r['fiche_unique']): ?>
        <button type="submit" form="form-supprimer" class="ycm-lien-supprimer">Supprimer cet élément</button>
        <?php endif ?>
      </div>
      <?php endif ?>
      <?php if ($r['aide_cote']): ?><p class="ycm-encadre-info"><?= e($r['aide_cote']) ?></p><?php endif ?>
    </aside>
  </div>
</form>
<?php if (!$ajout && $r['peut_supprimer'] && !$r['fiche_unique'] && $peut_modifier): ?>
<form id="form-supprimer" method="post" action="<?= url('/gestion/' . $r['slug'] . '/' . $ligne['id'] . '/supprimer') ?>" data-confirmer="Supprimer définitivement cet élément ?" data-confirmer-texte="Cette action est irréversible." data-confirmer-oui="Supprimer" data-confirmer-danger hidden><?= csrf() ?></form>
<?php endif ?>
<?php if ($r['slug'] === 'parametres' && ($admin['role_admin'] ?? '') === 'super'): ?>
<form id="form-exemples-charger" method="post" action="<?= url('/gestion/exemples') ?>" hidden data-confirmer="Charger les contenus d'exemple ?" data-confirmer-oui="Charger"><?= csrf() ?><input type="hidden" name="quoi" value="charger"></form>
<form id="form-exemples-retirer" method="post" action="<?= url('/gestion/exemples') ?>" data-confirmer="Retirer tous les contenus d'exemple ?" hidden><?= csrf() ?><input type="hidden" name="quoi" value="retirer"></form>
<?php endif ?>
