<?php
/*
 * Bandeau court en haut d'une initiative ou d'une actualité : la photo en fond, le titre,
 * et les mêmes images qui défilent en petit (une bande qui glisse en continu).
 * Paramètres : $images (adresses), $titre, $etiquette (HTML déjà échappé), $meta (texte), $retour (adresse)
 */
$images = array_values(array_unique(array_filter($images ?? [])));
$bande = count($images) > 1;
// Assez de vignettes pour remplir la bande, puis la même suite répétée pour une boucle sans à-coup
$vignettes = $images;
while ($bande && count($vignettes) < 6) {
    $vignettes = array_merge($vignettes, $images);
}
?>
<section class="relative isolate overflow-hidden bg-vert text-white" aria-label="<?= e($titre) ?>">
  <?php if ($images): ?>
  <div class="absolute inset-0 -z-20" data-diapo="banniere" aria-hidden="true">
    <?php foreach ($images as $k => $src): ?><img data-diapo-image src="<?= e($src) ?>" alt="" class="absolute inset-0 h-full w-full scale-105 object-cover blur-[2px]"<?= $k ? ' loading="lazy"' : ' fetchpriority="high"' ?>><?php endforeach ?>
  </div>
  <?php endif ?>
  <div class="absolute inset-0 -z-10 bg-linear-to-r from-vert/95 via-vert/75 to-vert/35" aria-hidden="true"></div>
  <?php partiel('partials/reseau_deco', ['opacite' => 'opacity-40']) ?>

  <div class="conteneur relative grid items-center gap-4 py-5 sm:py-7 lg:min-h-[13rem] lg:gap-10 <?= $bande ? 'lg:grid-cols-[minmax(0,1.15fr)_minmax(0,1fr)]' : '' ?>">
    <div class="min-w-0">
      <div class="flex flex-wrap items-center gap-2">
        <?php if (!empty($retour)) partiel('partials/retour', ['url' => $retour, 'clair' => true, 'bord' => true, 'classes' => 'shrink-0 xl:absolute xl:top-1/2 xl:-left-16 xl:-translate-y-1/2']) ?>
        <?= $etiquette ?? '' ?>
      </div>
      <h1 class="mt-2.5 line-clamp-3 text-[1.6rem] leading-[1.1] font-bold text-white sm:text-4xl xl:text-[2.75rem]"><?= e($titre) ?></h1>
      <?php if (!empty($meta)): ?><p class="mt-2 text-sm text-white/85 sm:text-[15px]"><?= e($meta) ?></p><?php endif ?>
    </div>
    <?php if ($bande): ?>
    <div class="bande-defile -mx-4 overflow-hidden sm:mx-0" aria-hidden="true">
      <div class="bande-piste flex w-max gap-2.5 sm:gap-3">
        <?php foreach (array_merge($vignettes, $vignettes) as $src): ?>
        <img src="<?= e($src) ?>" alt="" class="h-16 w-24 shrink-0 rounded-xl object-cover ring-2 ring-white/30 sm:h-24 sm:w-36" loading="lazy">
        <?php endforeach ?>
      </div>
    </div>
    <?php endif ?>
  </div>
</section>
