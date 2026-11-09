<?php
/* Fiche d'un témoignage : on le lit et on décide (afficher, retirer, mettre en avant, profil). Le texte ne se modifie pas. */
$t = $ligne;
$id = (int) $t['id'];
$membre = !empty($t['utilisateur_id']);
$une = !empty($t['a_la_une']);
$ici = url('/gestion/temoignages/' . $id);
?>
<div class="ycm-entete-page">
  <div>
    <h1><?= e($t['nom']) ?></h1>
    <p class="ycm-sous-titre"><?= $membre ? 'Témoignage envoyé par le membre' . (!empty($t['envoye_le']) ? ' le ' . e(date_longue($t['envoye_le'])) : '') : "Témoignage ajouté par l'équipe" ?></p>
  </div>
</div>
<div class="ycm-fiche-temoignage">
  <article class="ycm-boite ycm-boite--pad">
    <div class="ycm-temoignage-etats">
      <?php if (!$t['publie'] && $membre): ?><?= badge('À valider', 'orange') ?>
      <?php elseif (!$t['publie']): ?><?= badge('Retiré du site', 'gris') ?>
      <?php else: ?><?= badge('Affiché sur le site', 'vert') ?><?php endif ?>
      <?php if ($une && $t['publie']): ?><?= badge('Mis en avant', 'bleu') ?><?php endif ?>
    </div>
    <blockquote class="ycm-fiche-temoignage-texte">« <?= e($t['texte']) ?> »</blockquote>
    <div class="ycm-personne">
      <?php if ($t['photo']): ?><img src="<?= e(media($t['photo'])) ?>" alt="" class="ycm-avatar ycm-avatar--grand"><?php else: ?><span class="ycm-avatar ycm-avatar--grand ycm-avatar--orange" aria-hidden="true"><?= e(initiales($t['nom'])) ?></span><?php endif ?>
      <span><strong><?= e($t['nom']) ?></strong><small><?= e($t['role']) ?></small></span>
    </div>
    <?php if ($membre): ?><p class="ycm-discret">Le texte est celui du membre : il ne se modifie pas. <a class="ycm-lien" href="<?= url('/gestion/comptes/' . (int) $t['utilisateur_id']) ?>">Voir son compte</a></p><?php endif ?>
  </article>

  <?php if ($peut_modifier): ?>
  <aside class="ycm-boite ycm-boite--pad ycm-fiche-temoignage-actions">
    <h2>Décision</h2>
    <?php if ($t['publie']): ?>
    <button type="submit" form="f-une" class="<?= $une ? 'ycm-bouton-contour' : 'ycm-bouton-bleu' ?>"><?= $une ? 'Ne plus mettre en avant' : 'Mettre en avant' ?></button>
    <p class="ycm-discret"><?= $une ? 'Il apparaît en premier sur l\'accueil et sur la page Témoignages.' : 'Il passera en premier sur l\'accueil et sur la page Témoignages.' ?></p>
    <button type="submit" form="f-visible" class="ycm-bouton-rouge-contour">Retirer du site</button>
    <?php else: ?>
    <button type="submit" form="f-visible" class="ycm-bouton-vert">Afficher sur le site</button>
    <?php endif ?>
    <label class="ycm-fiche-temoignage-profil">
      <span>Profil de la personne</span>
      <select name="action" form="f-profil" data-envoi-auto>
        <option value="" disabled <?= empty($t['categorie']) ? 'selected' : '' ?>>Choisir…</option>
        <?php foreach (PROFILS_TEMOIGNAGE as $c => $lib): ?><option value="profil_<?= e($c) ?>" <?= ($t['categorie'] ?? '') === $c ? 'selected' : '' ?>><?= e($lib) ?></option><?php endforeach ?>
      </select>
      <small>Sert seulement au tri des témoignages.</small>
    </label>
  </aside>
  <?php
  partiel('admin/_action_temoignage', ['id_form' => 'f-visible', 'ids' => [$id], 'suivant' => $ici, 'action' => $t['publie'] ? 'retirer' : 'afficher',
      'confirmer' => $t['publie'] ? ['Retirer ce témoignage du site ?', "Il n'apparaîtra plus sur le site. Vous pourrez l'afficher de nouveau à tout moment.", 'Retirer'] : ['Afficher ce témoignage sur le site ?', 'Il apparaîtra sur la page Témoignages, avec le nom et la photo de la personne.', 'Afficher']]);
  partiel('admin/_action_temoignage', ['id_form' => 'f-une', 'ids' => [$id], 'suivant' => $ici, 'action' => $une ? 'pas_une' : 'une']);
  partiel('admin/_action_temoignage', ['id_form' => 'f-profil', 'ids' => [$id], 'suivant' => $ici, 'action' => '']);
  endif ?>
</div>
