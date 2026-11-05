<div class="ycm-entete-page">
  <div>
    <h1>Bonjour<?= $prenom ? ', ' . e($prenom) : '' ?></h1>
    <p class="ycm-sous-titre">Voici ce qui se passe sur YouthConnekt Mali aujourd'hui.</p>
  </div>
  <?php if ($actions): ?><a href="<?= e($actions[0]['url']) ?>" class="ycm-bouton-orange"><?= e($actions[0]['libelle']) ?></a><?php endif ?>
</div>

<?php if ($cartes): ?>
<section class="ycm-cartes-chiffres" aria-label="Le site en chiffres">
  <?php foreach ($cartes as $c): ?>
  <a class="ycm-carte-chiffre" href="<?= e($c['url']) ?>">
    <span class="ycm-carte-chiffre-tete"><?= e($c['libelle']) ?><span class="ycm-icone-ronde ycm-fond-<?= $c['couleur'] ?>" aria-hidden="true"><svg viewBox="0 0 24 24"><?= $c['icone'] ?></svg></span></span>
    <strong><?= $c['valeur'] ?></strong>
    <small><?= e($c['detail']) ?></small>
  </a>
  <?php endforeach ?>
</section>
<?php endif ?>

<?php if ($participation): ?>
<section class="ycm-boite ycm-boite--pad ycm-participation" aria-labelledby="tdb-participation">
  <div class="ycm-boite-tete ycm-boite-tete--nu"><h2 id="tdb-participation">Participation des membres</h2></div>
  <p class="ycm-sous-titre">Ce que les membres apportent eux-mêmes au chapitre.</p>
  <div class="ycm-participation-grille">
    <?php foreach ($participation as [$libelle, $nombre, $detail, $lien]): ?>
    <a href="<?= e($lien) ?>" class="ycm-participation-chiffre"><strong><?= $nombre ?></strong><span><?= e($libelle) ?></span><small><?= e($detail) ?></small></a>
    <?php endforeach ?>
  </div>
  <?php if ($contributeurs): ?>
  <h3 class="ycm-participation-titre">Les plus actifs</h3>
  <ul class="ycm-participation-liste">
    <?php foreach ($contributeurs as $k => $m): ?>
    <li><a href="<?= url('/gestion/comptes/' . $m['id']) ?>"><span class="ycm-avatar ycm-avatar--<?= ['orange', 'turquoise', 'bleu', 'vert'][$k % 4] ?>" aria-hidden="true"><?= e($m['initiales']) ?></span><span><?= e($m['nom_affiche']) ?></span><small><?= $m['nb'] ?> contribution<?= pluriel($m['nb']) ?></small></a></li>
    <?php endforeach ?>
  </ul>
  <?php endif ?>
</section>
<?php endif ?>

<div class="ycm-tdb-grille">
  <div class="ycm-tdb-principal">
    <?php if ($url_membres): ?>
    <section class="ycm-boite" aria-labelledby="tdb-inscriptions">
      <div class="ycm-boite-tete">
        <h2 id="tdb-inscriptions">Dernières inscriptions</h2>
        <a href="<?= e($url_membres) ?>" class="ycm-lien">Tous les membres</a>
      </div>
      <?php if ($inscriptions): ?>
      <div class="ycm-tableau-defile">
        <table class="ycm-tableau ycm-tableau--tdb">
          <thead><tr><th>Nom</th><th>Profil</th><th>Région</th><th>Inscription</th><th><span class="ycm-cache">Action</span></th></tr></thead>
          <tbody>
            <?php foreach ($inscriptions as $k => $i): ?>
            <tr>
              <td><span class="ycm-personne"><?= avatar($i['initiales'], $k) ?><strong><?= e($i['nom']) ?></strong></span></td>
              <td><?= e($i['profil']) ?></td>
              <td><?= e($i['region']) ?></td>
              <td><?= e(date_courte($i['date'])) ?></td>
              <td class="ycm-droite"><?= liens_ligne(['Voir' => $i['url']]) ?></td>
            </tr>
            <?php endforeach ?>
          </tbody>
        </table>
      </div>
      <?php else: ?>
      <p class="ycm-vide">Aucune inscription pour l'instant.</p>
      <?php endif ?>
    </section>
    <?php endif ?>
  </div>

  <div class="ycm-tdb-cote">
    <?php if ($bientot): ?>
    <section class="ycm-boite ycm-boite--pad" aria-labelledby="tdb-bientot">
      <h2 id="tdb-bientot">Bientôt clôturées</h2>
      <ul class="ycm-bientot">
        <?php foreach ($bientot as $o): ?>
        <li>
          <span class="ycm-jours<?= $o['jours'] <= 25 ? ' ycm-jours--urgent' : '' ?>"><small>J-</small><?= $o['jours'] ?></span>
          <a href="<?= e($o['url']) ?>"><strong><?= e($o['titre']) ?></strong><small>Clôture le <?= e(jour_mois($o['date'])) ?></small></a>
        </li>
        <?php endforeach ?>
      </ul>
    </section>
    <?php endif ?>

    <?php if ($actions): ?>
    <section class="ycm-actions-rapides" aria-labelledby="tdb-actions">
      <h2 id="tdb-actions">Actions rapides</h2>
      <ul>
        <?php foreach ($actions as $a): ?><li><a href="<?= e($a['url']) ?>"><svg viewBox="0 0 24 24" aria-hidden="true"><?= $a['icone'] ?></svg><?= e($a['libelle']) ?></a></li><?php endforeach ?>
      </ul>
    </section>
    <?php endif ?>
  </div>
</div>

<?php if ($chiffres): ?>
<section class="ycm-boite ycm-boite--pad" aria-labelledby="tdb-chiffres">
  <div class="ycm-boite-tete ycm-boite-tete--nu">
    <h2 id="tdb-chiffres">Chiffres clés affichés sur l'accueil</h2>
    <a href="<?= url('/gestion/chiffres') ?>" class="ycm-lien">Gérer les chiffres</a>
  </div>
  <form method="post" action="<?= url('/gestion/chiffres-rapides') ?>" class="ycm-chiffres-form">
    <?= csrf() ?>
    <?php foreach ($chiffres as $c): ?><label><?= e(majuscule_initiale($c['libelle'])) ?><?= $c['publie'] ? '' : ' (non publié)' ?><input type="text" name="chiffre_<?= $c['id'] ?>" value="<?= e($c['valeur']) ?>" maxlength="30"></label><?php endforeach ?>
    <button type="submit" class="ycm-bouton-vert">Enregistrer</button>
  </form>
</section>
<?php elseif (peut('chiffres', 'modifier')): ?>
<section class="ycm-boite ycm-boite--pad">
  <div class="ycm-boite-tete ycm-boite-tete--nu"><h2>Chiffres clés affichés sur l'accueil</h2></div>
  <p class="ycm-vide">Aucun chiffre saisi : la ligne de chiffres de l'accueil est masquée. <a class="ycm-lien" href="<?= url('/gestion/chiffres/ajouter') ?>">Ajouter un chiffre vérifié</a></p>
</section>
<?php endif ?>
