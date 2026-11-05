<?php partiel('admin/_entete_liste', get_defined_vars()); ?>
<?php if ($lignes): ?>
<div class="ycm-grille-cartes">
  <?php foreach ($lignes as $p): ?>
  <article class="ycm-carte-simple">
    <div class="ycm-carte-logo"><?php if ($p['logo']): ?><img src="<?= e(media($p['logo'])) ?>" alt=""><?php else: ?><span class="ycm-carte-logo-vide"><?= e(initiales($p['nom'])) ?></span><?php endif ?></div>
    <h3><?= e($p['nom']) ?></h3>
    <p class="ycm-discret"><?= ($p['categorie'] ?? '') === 'prive' ? 'Secteur privé' : 'Institutionnel ou technique' ?></p>
    <div class="ycm-carte-pied">
      <?= $p['actif'] ? badge('Affiché', 'vert') : badge('Masqué', 'gris') ?>
      <a href="<?= url('/gestion/partenaires/' . $p['id']) ?>">Modifier</a>
    </div>
  </article>
  <?php endforeach ?>
</div>
<?php else: ?>
<div class="ycm-boite ycm-boite--pad"><p class="ycm-vide">Aucun partenaire pour l'instant. Tant que la liste est vide, l'accueil montre les cases de la maquette.</p></div>
<?php endif ?>
