<?php
/* Témoignages : les mots des personnes ne se modifient pas. L'équipe peut seulement les afficher ou les retirer,
   les mettre en avant (ils passent en premier) et les classer par profil pour le tri. */
partiel('admin/_entete_liste', get_defined_vars());
$couleurs = ['orange', 'turquoise', 'bleu', 'vert'];
$avec_actions = $r['actions'] && $peut_modifier;
$suivant = adresse_actuelle();
// Petit formulaire d'une seule action sur un témoignage (placé hors du formulaire de sélection)
$formulaire_action = function (string $id_form, int $id, string $action, string $confirmer = '', string $texte = '', string $oui = '') use ($r, $suivant): string {
    $attrs = $confirmer ? ' data-confirmer="' . e($confirmer) . '" data-confirmer-texte="' . e($texte) . '" data-confirmer-oui="' . e($oui) . '"' : '';
    return '<form id="' . $id_form . '" method="post" action="' . url('/gestion/' . $r['slug'] . '/action') . '" hidden' . $attrs . '>' . csrf()
        . '<input type="hidden" name="suivant" value="' . e($suivant) . '"><input type="hidden" name="ids[]" value="' . $id . '">'
        . ($action !== '' ? '<input type="hidden" name="action" value="' . e($action) . '">' : '') . '</form>';
};
$formulaires = '';
?>
<?php if ($lignes): ?>
<form method="post" action="<?= url('/gestion/' . $r['slug'] . '/action') ?>" class="ycm-liste" data-liste>
  <?= csrf() ?><input type="hidden" name="suivant" value="<?= e($suivant) ?>">
  <?php if ($avec_actions): ?>
  <div class="ycm-actions-groupees ycm-actions-groupees--seules">
    <label for="action-temoignages">Action :</label>
    <select name="action" id="action-temoignages">
      <option value="">Choisir…</option>
      <?php foreach ($r['actions'] as $cle => [$libelle]): ?><option value="<?= e($cle) ?>"><?= e($libelle) ?></option><?php endforeach ?>
    </select>
    <button type="submit" class="ycm-bouton-vert ycm-petit">Appliquer</button>
    <span class="ycm-selection" data-compte-selection>0 sur <?= count($lignes) ?> sélectionné</span>
    <button type="button" class="ycm-lien ycm-annuler-selection" data-basculer-selection>Annuler</button>
  </div>
  <?php endif ?>
  <div class="ycm-temoignages">
    <?php foreach ($lignes as $k => $t): $id = (int) $t['id']; $membre = !empty($t['utilisateur_id']); $une = !empty($t['a_la_une']); ?>
    <article class="ycm-temoignage<?= $une ? ' ycm-temoignage--avant' : '' ?>">
      <?php if ($avec_actions): ?><label class="ycm-case ycm-temoignage-case"><input type="checkbox" name="ids[]" value="<?= $id ?>" aria-label="Sélectionner"></label><?php endif ?>
      <div class="ycm-temoignage-etats">
        <?php if (!$t['publie'] && $membre): ?><?= badge('À valider', 'orange') ?>
        <?php elseif (!$t['publie']): ?><?= badge('Retiré du site', 'gris') ?>
        <?php else: ?><?= badge('Affiché', 'vert') ?><?php endif ?>
        <?php if ($une && $t['publie']): ?><?= badge('Mis en avant', 'bleu') ?><?php endif ?>
      </div>
      <blockquote>« <?= e($t['texte']) ?> »</blockquote>
      <div class="ycm-personne">
        <?php if ($t['photo']): ?><img src="<?= e(media($t['photo'])) ?>" alt="" class="ycm-avatar"><?php else: ?><span class="ycm-avatar ycm-avatar--<?= $couleurs[$k % 4] ?>" aria-hidden="true"><?= e(initiales($t['nom'])) ?></span><?php endif ?>
        <span><strong><?= e($t['nom']) ?></strong><small><?= e($t['role']) ?><?= $membre && !empty($t['envoye_le']) ? ' · envoyé le ' . e(date_chiffres($t['envoye_le'])) : '' ?></small></span>
      </div>
      <?php if ($avec_actions):
        $formulaires .= $t['publie']
            ? $formulaire_action("tv-$id", $id, 'retirer', 'Retirer ce témoignage du site ?', "Il n'apparaîtra plus sur le site. Vous pourrez l'afficher de nouveau à tout moment.", 'Retirer')
            : $formulaire_action("tv-$id", $id, 'afficher', 'Afficher ce témoignage sur le site ?', 'Il apparaîtra sur la page Témoignages, avec le nom et la photo de la personne.', 'Afficher');
        $formulaires .= $formulaire_action("tu-$id", $id, $une ? 'pas_une' : 'une');
        $formulaires .= $formulaire_action("tp-$id", $id, '');
      ?>
      <div class="ycm-temoignage-actions">
        <label class="ycm-temoignage-profil"><span class="ycm-sr">Profil</span>
          <select name="action" form="tp-<?= $id ?>" data-envoi-auto aria-label="Profil de la personne">
            <option value="" disabled <?= empty($t['categorie']) ? 'selected' : '' ?>>Profil…</option>
            <?php foreach (PROFILS_TEMOIGNAGE as $c => $lib): ?><option value="profil_<?= e($c) ?>" <?= ($t['categorie'] ?? '') === $c ? 'selected' : '' ?>><?= e($lib) ?></option><?php endforeach ?>
          </select>
        </label>
        <span class="ycm-temoignage-boutons">
          <?php if ($t['publie']): ?><button type="submit" form="tu-<?= $id ?>" class="ycm-bouton-contour ycm-petit"><?= $une ? 'Ne plus mettre en avant' : 'Mettre en avant' ?></button><?php endif ?>
          <button type="submit" form="tv-<?= $id ?>" class="<?= $t['publie'] ? 'ycm-bouton-gris' : 'ycm-bouton-vert' ?> ycm-petit"><?= $t['publie'] ? 'Retirer' : 'Afficher' ?></button>
        </span>
      </div>
      <?php endif ?>
    </article>
    <?php endforeach ?>
  </div>
</form>
<?= $formulaires ?>
<?php partiel('admin/_pagination', get_defined_vars()) ?>
<?php else: ?>
<div class="ycm-boite ycm-boite--pad"><p class="ycm-vide"><?= $q !== '' || get('profil') !== '' ? 'Aucun témoignage ne correspond.' : "Aucun témoignage pour l'instant. Les membres envoient le leur depuis leur espace ; il arrive ici, à valider." ?></p></div>
<?php endif ?>
