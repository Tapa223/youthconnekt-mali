<?php /* Carte d'initiative. Paramètres : $i, $simple (sans résumé) */ ?>
<a href="<?= e($i['url']) ?>" class="carte group flex h-full flex-col transition-shadow hover:shadow-lg">
  <div class="relative aspect-[16/9] border-b-4 <?= bordure($i['axe_couleur'] ?? 'vert') ?>">
    <?php partiel('partials/photo', ['image' => $i['image']]) ?>
    <?php if (!empty($i['axe_nom'])): ?><span class="etiquette absolute top-3 left-3 rounded-full <?= fond($i['axe_couleur']) ?>"><?= e($i['axe_nom']) ?></span><?php endif ?>
  </div>
  <div class="flex flex-1 flex-col p-5">
    <?php if ($i['date_libelle'] || $i['lieu']): ?><p class="text-xs text-texte"><?= e($i['date_libelle']) ?><?= $i['date_libelle'] && $i['lieu'] ? ' · ' : '' ?><?= e($i['lieu']) ?></p><?php endif ?>
    <h3 class="mt-1 text-lg leading-snug font-bold text-vert"><?= e($i['titre']) ?></h3>
    <?php if (empty($simple)): ?>
    <p class="mt-2 flex-1 text-sm leading-relaxed"><?= e($i['resume']) ?></p>
    <span class="btn-lien mt-4 self-start group-hover:border-orange group-hover:bg-orange group-hover:text-white">Voir l'initiative</span>
    <?php endif ?>
  </div>
</a>
