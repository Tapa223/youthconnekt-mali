<?php
/* Témoignages : cartes compactes (trois par ligne), triables par profil ; sélection pour afficher, retirer ou mettre sur l'accueil. */
partiel('admin/_entete_liste', get_defined_vars());
$couleurs = ['orange', 'turquoise', 'bleu', 'vert'];
$avec_actions = $r['actions'] && $peut_modifier;
?>
<?php if ($lignes): ?>
<form method="post" action="<?= url('/gestion/' . $r['slug'] . '/action') ?>" class="ycm-liste" data-liste>
  <?= csrf() ?><input type="hidden" name="suivant" value="<?= e(adresse_actuelle()) ?>">
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
    <?php foreach ($lignes as $k => $t): $membre = !empty($t['utilisateur_id']); ?>
    <article class="ycm-temoignage">
      <?php if ($avec_actions): ?><label class="ycm-case ycm-temoignage-case"><input type="checkbox" name="ids[]" value="<?= (int) $t['id'] ?>" aria-label="Sélectionner"></label><?php endif ?>
      <div class="ycm-temoignage-etats">
        <?php if (!$t['publie'] && $membre): ?><?= badge('À valider', 'orange') ?>
        <?php elseif (!$t['publie']): ?><?= badge('Masqué', 'gris') ?>
        <?php else: ?><?= badge('Affiché', 'vert') ?><?php endif ?>
        <?php if (!empty($t['a_la_une'])): ?><?= badge("Sur l'accueil", 'bleu') ?><?php endif ?>
        <?php if (!empty($t['categorie'])): ?><span class="ycm-temoignage-profil"><?= e(PROFILS_TEMOIGNAGE[$t['categorie']] ?? '') ?></span><?php endif ?>
      </div>
      <blockquote>« <?= e($t['texte']) ?> »</blockquote>
      <div class="ycm-temoignage-pied">
        <span class="ycm-personne">
          <?php if ($t['photo']): ?><img src="<?= e(media($t['photo'])) ?>" alt="" class="ycm-avatar"><?php else: ?><span class="ycm-avatar ycm-avatar--<?= $couleurs[$k % 4] ?>" aria-hidden="true"><?= e(initiales($t['nom'])) ?></span><?php endif ?>
          <span><strong><?= e($t['nom']) ?></strong><small><?= $membre ? 'Envoyé par le membre' : e($t['role']) ?></small></span>
        </span>
        <a href="<?= url('/gestion/temoignages/' . $t['id']) ?>" class="ycm-bouton-contour ycm-petit"><?= $membre ? 'Ouvrir' : 'Modifier' ?></a>
      </div>
    </article>
    <?php endforeach ?>
  </div>
</form>
<?php partiel('admin/_pagination', get_defined_vars()) ?>
<?php else: ?>
<div class="ycm-boite ycm-boite--pad"><p class="ycm-vide"><?= $q !== '' || get('profil') !== '' ? 'Aucun témoignage ne correspond.' : "Aucun témoignage pour l'instant. La section Témoignages de l'accueil reste masquée tant qu'aucun n'est affiché." ?></p></div>
<?php endif ?>
