<?php
/*
 * Bandeau court en haut d'une initiative ou d'une actualité : la photo principale, poussée à droite et nette,
 * le côté du titre en vert légèrement flouté pour que le texte reste lisible.
 * Paramètres : $images (adresses, la première est utilisée), $titre, $etiquette (HTML déjà échappé), $meta (texte), $retour (adresse)
 */
$image = array_values(array_filter($images ?? []))[0] ?? '';
?>
<section class="relative isolate overflow-hidden bg-vert text-white" aria-label="<?= e($titre) ?>">
  <?php if ($image): ?>
  <img src="<?= e($image) ?>" alt="" class="absolute inset-y-0 right-0 -z-20 h-full w-full object-cover object-center sm:w-[78%] lg:w-[62%]" fetchpriority="high">
  <?php /* Le côté du texte : flou léger puis vert, qui s'effacent vers la droite pour laisser voir la photo */ ?>
  <div class="banniere-flou absolute inset-0 -z-10 backdrop-blur-[6px]" aria-hidden="true"></div>
  <div class="absolute inset-0 -z-10 bg-linear-to-r from-vert from-25% via-vert/80 via-55% to-vert/10 max-sm:from-vert/90 max-sm:from-20% max-sm:via-vert/60 max-sm:to-vert/15" aria-hidden="true"></div>
  <?php endif ?>
  <?php partiel('partials/reseau_deco', ['opacite' => 'opacity-30']) ?>

  <div class="conteneur relative py-6 sm:py-8 lg:flex lg:min-h-[14rem] lg:items-center">
    <div class="max-w-[38rem] min-w-0">
      <div class="flex flex-wrap items-center gap-2">
        <?php if (!empty($retour)) partiel('partials/retour', ['url' => $retour, 'clair' => true, 'bord' => true, 'classes' => 'shrink-0 xl:absolute xl:top-1/2 xl:-left-16 xl:-translate-y-1/2']) ?>
        <?= $etiquette ?? '' ?>
      </div>
      <h1 class="mt-2.5 line-clamp-3 text-[1.6rem] leading-[1.1] font-bold text-white sm:text-4xl xl:text-[2.75rem]"><?= e($titre) ?></h1>
      <?php if (!empty($meta)): ?><p class="mt-2 text-sm text-white/90 sm:text-[15px]"><?= e($meta) ?></p><?php endif ?>
    </div>
  </div>
</section>
