<?php /* Carte d'initiative. Paramètres : $i, $simple (sans résumé), $compacte_tel (petite carte sur téléphone, deux par ligne) */
$ct = !empty($compacte_tel); ?>
<a href="<?= e($i['url']) ?>" class="carte group flex h-full flex-col transition-shadow hover:shadow-lg">
  <div class="relative aspect-[16/9] border-b-4 <?= $ct ? 'max-sm:aspect-[4/3] max-sm:border-b-[3px]' : '' ?> <?= bordure($i['axe_couleur'] ?? 'vert') ?>">
    <?php partiel('partials/photo', ['image' => $i['image']]) ?>
    <?php if (!empty($i['axe_nom'])): ?><span class="etiquette absolute top-3 left-3 rounded-full <?= $ct ? 'max-sm:top-2 max-sm:left-2 max-sm:px-2 max-sm:py-0.5 max-sm:text-[10px]' : '' ?> <?= fond($i['axe_couleur']) ?>"><?= e($i['axe_nom']) ?></span><?php endif ?>
  </div>
  <div class="flex flex-1 flex-col p-5 <?= $ct ? 'max-sm:p-3' : '' ?>">
    <?php if ($i['date_libelle'] || $i['lieu']): ?><p class="text-xs text-texte <?= $ct ? 'max-sm:text-[11px] max-sm:leading-tight' : '' ?>"><?= e($i['date_libelle']) ?><?= $i['date_libelle'] && $i['lieu'] ? ' · ' : '' ?><?= e($i['lieu']) ?></p><?php endif ?>
    <h3 class="mt-1 text-lg leading-snug font-bold text-vert <?= $ct ? 'max-sm:line-clamp-2 max-sm:text-[14px] max-sm:leading-tight' : '' ?>"><?= e($i['titre']) ?></h3>
    <?php if (empty($simple)): ?>
    <p class="mt-2 flex-1 text-sm leading-relaxed <?= $ct ? 'max-sm:hidden' : '' ?>"><?= e($i['resume']) ?></p>
    <span class="btn-lien mt-4 self-start group-hover:border-orange group-hover:bg-orange group-hover:text-white <?= $ct ? 'max-sm:hidden' : '' ?>">Voir l'initiative</span>
    <?php endif ?>
  </div>
</a>
