<?php /* Carte d'actualité ou d'événement. Paramètres : $a, $mode (« evenement », « actualite » ou « lire ») */ $mode = $mode ?? 'actualite'; ?>
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
      <p class="text-[11px] font-bold tracking-[0.12em] text-vert uppercase"><?= e($a['categorie_libelle']) ?>s · <?= e(date_courte($a['date_affichee'])) ?></p>
    <?php else: ?>
      <p class="text-xs text-texte"><time datetime="<?= e(date_iso($a['date_affichee'])) ?>"><?= e(date_courte($a['date_affichee'])) ?></time></p>
    <?php endif ?>
    <h3 class="mt-1.5 text-base leading-snug font-bold sm:text-lg <?= $mode === 'lire' ? 'text-encre' : 'text-vert' ?> group-hover:text-orange"><?= e($a['titre']) ?></h3>
    <?php if ($mode === 'actualite'): ?><p class="mt-2 line-clamp-2 text-sm"><?= e($a['chapeau']) ?></p><?php endif ?>
  </div>
</a>
