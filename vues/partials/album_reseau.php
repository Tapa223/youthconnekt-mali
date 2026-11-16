<?php
/*
 * Une seule photo de couverture ; un clic ouvre toute la photothèque (flèches, clavier).
 * Rien sur la photo : la phrase et le bouton sont dessous. Paramètres : $album (titre, photos), $classes, $sur_vert (section à fond vert)
 */
$photos = $album['photos'];
$n = count($photos);
$titre = trim((string) $album['titre']) !== '' ? $album['titre'] : $album['nom'];
$vert = !empty($sur_vert);
?>
<div class="<?= e($classes ?? '') ?>" data-phototheque>
  <button type="button" class="group block w-full text-left" data-agrandir="0" aria-label="<?= e($titre) ?> : voir les <?= $n ?> photo<?= pluriel($n) ?>">
    <span class="block aspect-[16/10] overflow-hidden rounded-2xl shadow-xl sm:rounded-3xl">
      <img src="<?= e(media($photos[0]['image'])) ?>" alt="<?= e($photos[0]['legende']) ?>" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105" loading="lazy">
    </span>
    <span class="mt-3 flex flex-wrap items-center justify-between gap-x-4 gap-y-2">
      <span class="text-[15px] leading-snug font-bold sm:text-base <?= $vert ? 'text-white' : 'text-vert' ?>"><?= e($titre) ?></span>
      <?php if ($n > 1): ?><span class="inline-flex min-h-9 items-center rounded-full border-[1.5px] px-4 text-[13px] font-bold transition-colors sm:text-sm <?= $vert ? 'border-white text-white group-hover:bg-white group-hover:text-vert' : 'border-vert text-vert group-hover:bg-vert group-hover:text-white' ?>">Voir les <?= $n ?> photos</span><?php endif ?>
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
