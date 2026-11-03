<?php /* Carte d'opportunité. Paramètres : $o, $compacte (bool), $via (nom de la communauté) */ $compacte = !empty($compacte); ?>
<a href="<?= e($o['url']) ?>" class="carte group flex h-full flex-col transition-shadow hover:shadow-lg">
  <div class="relative aspect-[16/7.5] border-b-4 <?= bordure($o['couleur']) ?>">
    <?php partiel('partials/photo', ['image' => $o['image']]) ?>
    <?php if (!$compacte): ?><span class="etiquette absolute top-3 left-3 hidden rounded-full sm:inline-block <?= fond($o['couleur']) ?>"><?= e($o['type_libelle']) ?></span><?php endif ?>
    <?php if ($o['jours_restants'] !== null && $o['jours_restants'] >= 0): ?>
    <span class="absolute top-2 right-2 rounded-full px-2.5 py-1 text-sm leading-none font-bold whitespace-nowrap shadow sm:top-3 sm:right-3 sm:px-3 sm:py-1.5 sm:text-base <?= $o['jours_restants'] <= 25 ? 'bg-orange text-white' : 'bg-white text-vert' ?>" title="<?= $o['jours_restants'] ?> jour<?= pluriel($o['jours_restants']) ?> avant la date limite">J-<?= $o['jours_restants'] ?></span>
    <?php endif ?>
  </div>
  <div class="flex flex-1 flex-col p-3.5 sm:p-5">
    <?php if (!empty($via)): ?><span class="etiquette self-start bg-vert-clair text-[11px] text-vert">Via <?= e($via) ?></span><?php endif ?>
    <p class="text-[11px] font-bold tracking-[0.12em] uppercase <?= texte_couleur($o['couleur']) ?> <?= !empty($via) ? 'mt-2' : '' ?> <?= !$compacte ? 'sm:hidden' : '' ?>"><?= e($o['type_libelle']) ?></p>
    <h3 class="mt-1 <?= !$compacte ? 'sm:mt-0' : '' ?> text-[15px] font-bold text-encre group-hover:text-orange sm:text-lg sm:leading-snug"><?= e($o['titre']) ?></h3>
    <?php if (!$compacte && ($o['organisme'] || $o['lieu'])): ?><p class="mt-1 hidden text-xs text-texte sm:block"><?= e($o['organisme']) ?><?= $o['organisme'] && $o['lieu'] ? ' · ' : '' ?><?= e($o['lieu']) ?></p><?php endif ?>
    <?php if ($compacte): ?>
      <p class="mt-auto pt-2 text-xs text-texte"><?php if ($o['date_limite']): ?>Date limite <strong class="text-rouge"><?= e(date_longue($o['date_limite'])) ?></strong><?php else: ?>Sans date limite<?php endif ?></p>
    <?php else: ?>
      <div class="mt-auto pt-2 text-xs sm:mt-4 sm:flex sm:items-center sm:justify-between sm:gap-3 sm:border-t sm:border-ligne sm:pt-3 sm:text-sm">
        <span class="text-texte"><span class="sm:hidden"><?= $o['date_limite'] ? 'Avant le ' : '' ?></span><span class="hidden sm:inline">Date limite</span></span>
        <strong class="text-rouge"><?= $o['date_limite'] ? e(date_courte($o['date_limite'])) : '<span class="font-medium text-texte">Sans date limite</span>' ?></strong>
      </div>
    <?php endif ?>
    <?php partiel('partials/bouton_carte', ['classes' => 'mt-3']) ?>
  </div>
</a>
