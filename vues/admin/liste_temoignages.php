<?php partiel('admin/_entete_liste', get_defined_vars()); $couleurs = ['orange', 'turquoise', 'bleu', 'vert']; ?>
<?php if ($lignes): ?>
<div class="ycm-grille-cartes ycm-grille-cartes--2">
  <?php foreach ($lignes as $k => $t): ?>
  <article class="ycm-carte-simple">
    <blockquote>« <?= e($t['texte']) ?> »</blockquote>
    <span class="ycm-personne">
      <?php if ($t['photo']): ?><img src="<?= e(media($t['photo'])) ?>" alt="" class="ycm-avatar"><?php else: ?><span class="ycm-avatar ycm-avatar--<?= $couleurs[$k % 4] ?>" aria-hidden="true"><?= e(initiales($t['nom'])) ?></span><?php endif ?>
      <span><strong><?= e($t['nom']) ?></strong><?php if ($t['role']): ?><small><?= e($t['role']) ?></small><?php endif ?></span>
    </span>
    <div class="ycm-carte-pied">
      <?= $t['publie'] ? badge("Affiché sur l'accueil", 'vert') : badge('Masqué', 'gris') ?>
      <a href="<?= url('/gestion/temoignages/' . $t['id']) ?>">Modifier</a>
    </div>
  </article>
  <?php endforeach ?>
</div>
<?php else: ?>
<div class="ycm-boite ycm-boite--pad"><p class="ycm-vide">Aucun témoignage pour l'instant. La section Témoignages de l'accueil reste masquée tant qu'aucun n'est affiché.</p></div>
<?php endif ?>
