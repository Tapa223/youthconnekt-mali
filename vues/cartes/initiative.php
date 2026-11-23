<?php /* Carte d'initiative. Paramètres : $i, $simple (sans résumé), $compacte_tel (petite carte sur téléphone, deux par ligne),
   $basse (page Initiatives : photo moins haute et texte resserré, pour voir une rangée entière et deviner la suivante) */
$ct = !empty($compacte_tel);
$bas = !empty($basse); ?>
<a href="<?= e($i['url']) ?>" class="carte group flex h-full flex-col transition-shadow hover:shadow-lg">
  <div class="relative <?= $bas ? 'aspect-[16/9] sm:aspect-[2/1] lg:aspect-[5/2]' : 'aspect-[16/9]' ?> border-b-4 <?= $ct ? 'max-sm:aspect-[4/3] max-sm:border-b-[3px]' : '' ?> border-orange">
    <?php partiel('partials/photo', ['transition' => 'photo-ini-' . $i['id'], 'image' => $i['image'], 'largeur' => 720]) ?>
  </div>
  <div class="flex flex-1 flex-col <?= $bas ? 'px-5 py-4' : 'p-5' ?> <?= $ct ? 'max-sm:p-3' : '' ?>">
    <?php if ($i['date_libelle'] || $i['lieu']): ?><p class="text-xs text-texte <?= $ct ? 'max-sm:text-[11px] max-sm:leading-tight' : '' ?>"><?= e($i['date_libelle']) ?><?= $i['date_libelle'] && $i['lieu'] ? ' · ' : '' ?><?= e($i['lieu']) ?></p><?php endif ?>
    <h3 class="mt-1 <?= $bas ? 'line-clamp-2 text-[17px]' : 'text-lg' ?> leading-snug font-bold text-vert <?= $ct ? 'max-sm:line-clamp-2 max-sm:text-[14px] max-sm:leading-tight' : '' ?>"><?= e($i['titre']) ?></h3>
    <?php if (empty($simple)): ?>
    <p class="mt-1.5 <?= $bas ? 'line-clamp-2' : 'line-clamp-3' ?> text-sm leading-relaxed <?= $ct ? 'max-sm:hidden' : '' ?>"><?= e($i['resume']) ?></p>
    <?php /* Le vide pousse le bouton en bas de la carte ; le résumé garde sa hauteur pour que la coupure « … » reste nette */ ?>
    <span class="flex-1"></span>
    <span class="btn-lien <?= $bas ? 'mt-3' : 'mt-4' ?> self-start group-hover:border-orange group-hover:bg-orange group-hover:text-white <?= $ct ? 'max-sm:hidden' : '' ?>">Voir l'initiative</span>
    <?php endif ?>
  </div>
</a>
