<?php
/*
 * Bandeau court en haut d'une initiative ou d'une actualité : les photos (celles de la photothèque) se succèdent,
 * poussées à droite et nettes ; le côté du titre est en vert légèrement flouté pour que le texte reste lisible.
 * Paramètres : $images (adresses), $titre, $etiquette (HTML déjà échappé), $meta (texte), $retour (adresse)
 */
$images = array_values(array_unique(array_filter($images ?? [])));
?>
<section class="relative isolate overflow-hidden bg-vert text-white" aria-label="<?= e($titre) ?>">
  <?php if ($images): ?>
  <div class="banniere-photo absolute inset-y-0 right-0 -z-20 w-full sm:w-[78%] lg:w-[62%]" data-diapo="banniere" aria-hidden="true">
    <?php foreach ($images as $k => $src): ?><img data-diapo-image src="<?= e($src) ?>" alt="" class="absolute inset-0 h-full w-full object-cover"<?= $k ? ' loading="lazy"' : ' fetchpriority="high"' ?>><?php endforeach ?>
  </div>
  <?php /* Le côté du texte : flou léger puis vert, qui s'effacent vers la droite pour laisser voir la photo */ ?>
  <div class="banniere-flou absolute inset-0 -z-10 backdrop-blur-[6px]" aria-hidden="true"></div>
  <div class="absolute inset-0 -z-10 bg-linear-to-r from-vert from-25% via-vert/80 via-55% to-vert/10 max-sm:from-vert/90 max-sm:from-20% max-sm:via-vert/60 max-sm:to-vert/15" aria-hidden="true"></div>
  <?php endif ?>
  <?php partiel('partials/reseau_deco', ['opacite' => 'opacity-30']) ?>

  <div class="conteneur relative py-6 sm:py-8 lg:flex lg:min-h-[14rem] lg:items-center">
    <?php /* La flèche de retour à gauche du texte, alignée avec lui (dans la marge sur grand écran) */ ?>
    <div class="flex max-w-[38rem] min-w-0 items-start gap-3 sm:gap-4 xl:block">
      <?php if (!empty($retour)) partiel('partials/retour', ['url' => $retour, 'clair' => true, 'bord' => true, 'toujours' => true, 'classes' => 'shrink-0 xl:absolute xl:top-1/2 xl:-left-16 xl:-translate-y-1/2']) ?>
      <div class="min-w-0">
        <?= $etiquette ?? '' ?>
        <h1 class="mt-2 line-clamp-3 text-[1.3rem] leading-[1.15] font-bold text-white sm:mt-2.5 sm:text-4xl sm:leading-[1.1] xl:text-[2.75rem]"><?= e($titre) ?></h1>
        <?php if (!empty($meta)): ?><p class="mt-1.5 text-[13px] text-white/90 sm:mt-2 sm:text-[15px]"><?= e($meta) ?></p><?php endif ?>
      </div>
    </div>
  </div>
</section>
