<div class="ycm-entete-page"><div><h1>Recherche</h1>
<p class="ycm-sous-titre"><?= $q !== '' ? 'Résultats pour « ' . e($q) . ' »' : 'Tapez au moins deux lettres dans la barre de recherche.' ?></p></div></div>
<?php if ($q !== '' && !$groupes): ?><div class="ycm-boite ycm-boite--pad"><p class="ycm-vide">Aucun résultat.</p></div><?php endif ?>
<div class="ycm-resultats-recherche">
<?php foreach ($groupes as $g): ?>
  <section class="ycm-boite ycm-boite--pad">
    <div class="ycm-boite-tete ycm-boite-tete--nu"><h2><?= e($g['titre']) ?></h2><a class="ycm-lien" href="<?= e($g['tout']) ?>">Tout voir</a></div>
    <ul class="ycm-liste-simple"><?php foreach ($g['elements'] as $el): ?><li><a href="<?= e($el['url']) ?>"><?= e($el['texte']) ?></a></li><?php endforeach ?></ul>
  </section>
<?php endforeach ?>
</div>
