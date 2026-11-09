<?php /* Carte d'actualité ou d'événement. Paramètres : $a, $mode (« evenement », « actualite », « lire » ou « liste » : compacte, deux par ligne sur téléphone) */ $mode = $mode ?? 'actualite'; ?>
<?php if ($mode === 'liste'): ?>
<a href="<?= e($a['url']) ?>" class="carte group flex h-full flex-col transition-shadow hover:shadow-lg">
  <div class="relative aspect-[4/3] border-b-[3px] sm:aspect-[16/9] sm:border-b-4 lg:aspect-[16/7] <?= bordure($a['couleur']) ?>">
    <?php partiel('partials/photo', ['image' => $a['image']]) ?>
    <?php if ($a['est_a_venir']): ?><span class="etiquette absolute top-2 left-2 rounded-full bg-orange text-white max-sm:px-2 max-sm:py-0.5 max-sm:text-[10px] sm:top-3 sm:left-3">À venir</span><?php endif ?>
  </div>
  <div class="flex flex-1 flex-col p-3 sm:p-5 lg:p-4">
    <p class="text-[11px] font-bold tracking-[0.08em] uppercase sm:text-xs <?= texte_couleur($a['couleur']) ?>"><?= $a['categorie_libelle'] !== '' ? e($a['categorie_libelle']) . ' · ' : '' ?><span class="text-texte"><?= e(date_courte($a['date_affichee'])) ?></span></p>
    <h3 class="mt-1 line-clamp-3 text-[14px] leading-tight font-bold text-vert group-hover:text-orange sm:text-lg sm:leading-snug"><?= e($a['titre']) ?></h3>
    <p class="mt-2 line-clamp-2 hidden text-sm sm:block lg:mt-1.5"><?= e($a['chapeau']) ?></p>
    <?php /* Bouton en bas de carte : toujours à la même hauteur d'une carte à l'autre */ ?>
    <span class="mt-auto block pt-3 sm:pt-4 lg:pt-3"><?php partiel('partials/bouton_carte') ?></span>
  </div>
</a>
<?php return; endif ?>
<a href="<?= e($a['url']) ?>" class="carte group flex h-full flex-col transition-shadow hover:shadow-lg">
  <div class="relative aspect-[16/8] <?= $mode === 'actualite' ? 'border-b-4 border-turquoise' : '' ?>">
    <?php partiel('partials/photo', ['image' => $a['image']]) ?>
    <?php if ($mode === 'evenement'): ?>
    <time datetime="<?= e(date_iso($a['date_affichee'])) ?>" class="absolute top-0 left-0 grid min-w-13 rounded-br-xl px-2.5 py-2 text-center leading-none <?= $a['est_a_venir'] ? 'bg-orange text-white' : 'bg-white text-vert' ?>">
      <span class="text-xl font-bold"><?= e(jour($a['date_affichee'])) ?></span><span class="mt-1 text-[10px] font-bold uppercase"><?= e(mois_pastille($a['date_affichee'])) ?></span>
    </time>
    <?php endif ?>
  </div>
  <div class="flex flex-1 flex-col p-5">
    <?php if ($mode === 'evenement'): ?>
      <p class="text-[11px] font-bold tracking-[0.12em] uppercase <?= $a['est_a_venir'] ? 'text-orange' : 'text-texte' ?>"><?= $a['est_a_venir'] ? 'À venir' : 'Passé' ?><?= $a['lieu'] ? ' · ' . e($a['lieu']) : '' ?></p>
    <?php elseif ($mode === 'lire'): ?>
      <p class="text-[11px] font-bold tracking-[0.12em] text-vert uppercase"><?= $a['categorie_libelle'] !== '' ? e($a['categorie_libelle']) . ' · ' : '' ?><?= e(date_courte($a['date_affichee'])) ?></p>
    <?php else: ?>
      <p class="text-xs text-texte"><time datetime="<?= e(date_iso($a['date_affichee'])) ?>"><?= e(date_courte($a['date_affichee'])) ?></time></p>
    <?php endif ?>
    <h3 class="mt-1.5 text-base leading-snug font-bold sm:text-lg <?= $mode === 'lire' ? 'text-encre' : 'text-vert' ?> group-hover:text-orange"><?= e($a['titre']) ?></h3>
    <?php if ($mode === 'actualite'): ?><p class="mt-2 line-clamp-2 text-sm"><?= e($a['chapeau']) ?></p><?php endif ?>
  </div>
</a>
