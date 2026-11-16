<?php
/*
 * Une seule photo de couverture ; un clic ouvre toute la photothèque (flèches, clavier).
 * Paramètres : $album (titre, photos), $classes
 */
$photos = $album['photos'];
$n = count($photos);
$titre = trim((string) $album['titre']) !== '' ? $album['titre'] : $album['nom'];
?>
<div class="<?= e($classes ?? '') ?>" data-phototheque>
  <button type="button" class="group relative block aspect-[16/10] w-full overflow-hidden rounded-2xl bg-vert text-left shadow-xl sm:rounded-3xl" data-agrandir="0" aria-label="<?= e($titre) ?> : voir les <?= $n ?> photo<?= pluriel($n) ?>">
    <img src="<?= e(media($photos[0]['image'])) ?>" alt="<?= e($photos[0]['legende']) ?>" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105" loading="lazy">
    <span class="absolute inset-0 bg-[linear-gradient(180deg,rgb(3_94_77/0)_45%,rgb(3_94_77/.85)_100%)]" aria-hidden="true"></span>
    <span class="absolute inset-x-4 bottom-4 flex flex-wrap items-end justify-between gap-2 sm:inset-x-5 sm:bottom-5">
      <span class="text-[15px] leading-snug font-bold text-white [text-shadow:0_2px_10px_rgb(0_0_0/.35)] sm:text-lg"><?= e($titre) ?></span>
      <?php if ($n > 1): ?><span class="inline-flex min-h-9 items-center rounded-full bg-white px-4 text-[13px] font-bold text-vert transition-colors group-hover:bg-orange group-hover:text-white sm:text-sm">Voir les <?= $n ?> photos</span><?php endif ?>
    </span>
  </button>
  <?php foreach (array_slice($photos, 1, null, true) as $k => $ph): ?>
  <button type="button" hidden data-agrandir="<?= $k ?>"><img src="<?= e(media($ph['image'])) ?>" alt="<?= e($ph['legende']) ?>" loading="lazy"></button>
  <?php endforeach ?>
  <dialog class="visionneuse" aria-label="<?= e($titre) ?>">
    <img src="" alt="">
    <p class="visionneuse-legende"></p>
    <button type="button" class="visionneuse-bouton top-4 right-4" data-fermer aria-label="Fermer"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg></button>
    <button type="button" class="visionneuse-bouton top-1/2 left-4 -translate-y-1/2<?= $n < 2 ? ' hidden' : '' ?>" data-precedent aria-label="Photo précédente"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg></button>
    <button type="button" class="visionneuse-bouton top-1/2 right-4 -translate-y-1/2<?= $n < 2 ? ' hidden' : '' ?>" data-suivant aria-label="Photo suivante"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg></button>
  </dialog>
</div>
