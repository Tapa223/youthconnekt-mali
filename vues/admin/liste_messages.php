<?php partiel('admin/_entete_liste', get_defined_vars()); ?>
<?php if ($lignes && $ouvert): ?>
<div class="ycm-boite-reception">
  <ul class="ycm-liste-messages">
    <?php foreach ($lignes as $m): ?>
    <li>
      <a href="<?= e(avec_parametres(['ouvert' => $m['id']])) ?>" class="<?= !$m['traite'] ? 'non-lu' : '' ?><?= (int) $ouvert['id'] === (int) $m['id'] ? ' ouvert' : '' ?>">
        <strong><?= e($m['nom']) ?></strong>
        <time datetime="<?= e(date('c', strtotime($m['recu_le']))) ?>"><?= e(il_y_a($m['recu_le'])) ?></time>
        <span class="profil"><?= e(PROFILS_CONTACT[$m['profil']] ?? '') ?></span>
        <span class="objet"><?= e(couper($m['objet'] . ' · ' . $m['message'], 90)) ?></span>
      </a>
    </li>
    <?php endforeach ?>
  </ul>
  <div class="ycm-lecture">
    <div class="ycm-lecture-tete">
      <h2><?= e($ouvert['objet']) ?></h2>
      <?= $ouvert['traite'] ? badge('Traité', 'vert') : badge(PROFILS_CONTACT[$ouvert['profil']] ?? '', 'turquoise') ?>
    </div>
    <p class="ycm-lecture-meta"><?= e($ouvert['nom']) ?> · <a href="mailto:<?= e($ouvert['email']) ?>"><?= e($ouvert['email']) ?></a> · <?= e(date_longue($ouvert['recu_le']) . ', ' . date('H \h i', strtotime($ouvert['recu_le']))) ?></p>
    <div class="ycm-lecture-texte"><?= e($ouvert['message']) ?></div>
    <div class="ycm-lecture-actions">
      <a class="ycm-bouton-vert" href="mailto:<?= e($ouvert['email']) ?>?subject=<?= rawurlencode('Re : ' . $ouvert['objet']) ?>">Répondre par e-mail</a>
      <?php if ($peut_modifier): ?>
      <form method="post" action="<?= url('/gestion/messages/' . $ouvert['id'] . '/traiter') ?>">
        <?= csrf() ?><input type="hidden" name="suivant" value="<?= e(adresse_actuelle()) ?>"><input type="hidden" name="traite" value="<?= $ouvert['traite'] ? '0' : '1' ?>">
        <button type="submit" class="ycm-bouton-contour"><?= $ouvert['traite'] ? 'Remettre à traiter' : 'Marquer comme traité' ?></button>
      </form>
      <?php endif ?>
    </div>
  </div>
</div>
<?php partiel('admin/_pagination', get_defined_vars()) ?>
<?php else: ?>
<div class="ycm-boite ycm-boite--pad"><p class="ycm-vide">Aucun message dans cette liste.</p></div>
<?php endif ?>
