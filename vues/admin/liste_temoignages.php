<?php
/* Témoignages : cartes cliquables (la fiche donne les décisions). Le menu « ⋯ » > Sélectionner permet d'agir sur plusieurs à la fois. */
partiel('admin/_entete_liste', get_defined_vars());
$couleurs = ['orange', 'turquoise', 'bleu', 'vert'];
$avec_actions = $r['actions'] && $peut_modifier;
$rapides = ['afficher' => 'Afficher', 'retirer' => 'Retirer', 'une' => 'Mettre en avant', 'pas_une' => 'Ne plus mettre en avant'];
?>
<?php if ($lignes): ?>
<form method="post" action="<?= url('/gestion/' . $r['slug'] . '/action') ?>" class="ycm-liste" data-liste>
  <?= csrf() ?><input type="hidden" name="suivant" value="<?= e(adresse_actuelle()) ?>">
  <?php if ($avec_actions): ?>
  <div class="ycm-actions-groupees ycm-actions-groupees--seules">
    <span class="ycm-selection" data-compte-selection>0 sur <?= count($lignes) ?> sélectionné</span>
    <span class="ycm-actions-tem">
      <?php foreach ($rapides as $cle => $libelle): ?><button type="submit" name="action" value="<?= $cle ?>" class="<?= $cle === 'retirer' ? 'ycm-bouton-rouge-contour' : 'ycm-bouton-contour' ?> ycm-petit"><?= $libelle ?></button><?php endforeach ?>
    </span>
    <button type="button" class="ycm-lien ycm-annuler-selection" data-basculer-selection>Annuler</button>
  </div>
  <?php endif ?>
  <div class="ycm-temoignages">
    <?php foreach ($lignes as $k => $t): $id = (int) $t['id']; $membre = !empty($t['utilisateur_id']); $une = !empty($t['a_la_une']); ?>
    <article class="ycm-temoignage<?= $une && $t['publie'] ? ' ycm-temoignage--avant' : '' ?>">
      <?php if ($avec_actions): ?><label class="ycm-case ycm-temoignage-case"><input type="checkbox" name="ids[]" value="<?= $id ?>" aria-label="Sélectionner le témoignage de <?= e($t['nom']) ?>"></label><?php endif ?>
      <div class="ycm-temoignage-etats">
        <?php if (!$t['publie'] && $membre): ?><?= badge('À valider', 'orange') ?>
        <?php elseif (!$t['publie']): ?><?= badge('Retiré du site', 'gris') ?>
        <?php else: ?><?= badge('Affiché', 'vert') ?><?php endif ?>
        <?php if ($une && $t['publie']): ?><?= badge('Mis en avant', 'bleu') ?><?php endif ?>
        <?php if (!empty($t['profil'])): ?><span class="ycm-temoignage-profil"><?= e(PROFILS_TEMOIGNAGE[$t['profil']] ?? '') ?></span><?php endif ?>
      </div>
      <blockquote>« <?= e($t['texte']) ?> »</blockquote>
      <div class="ycm-personne">
        <?php if ($t['photo']): ?><img src="<?= e(media($t['photo'])) ?>" alt="" class="ycm-avatar"><?php else: ?><span class="ycm-avatar ycm-avatar--<?= $couleurs[$k % 4] ?>" aria-hidden="true"><?= e(initiales($t['nom'])) ?></span><?php endif ?>
        <span><a href="<?= url('/gestion/temoignages/' . $id) ?>" class="ycm-temoignage-lien"><strong><?= e($t['nom']) ?></strong></a><small><?= e($t['role']) ?><?= $membre && !empty($t['envoye_le']) ? ' · envoyé le ' . e(date_chiffres($t['envoye_le'])) : '' ?></small></span>
      </div>
    </article>
    <?php endforeach ?>
  </div>
</form>
<?php partiel('admin/_pagination', get_defined_vars()) ?>
<?php else: ?>
<div class="ycm-boite ycm-boite--pad"><p class="ycm-vide"><?= $q !== '' || get('profil') !== '' ? 'Aucun témoignage ne correspond.' : "Aucun témoignage pour l'instant. Les membres envoient le leur depuis leur espace ; il arrive ici, à valider." ?></p></div>
<?php endif ?>
