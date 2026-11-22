<?php
/*
 * Mosaïque de photos qui défilent : une grande photo et deux petites ; la dernière petite porte « +N » s'il reste des photos.
 * Un clic sur une photo ouvre la photothèque complète (flèches, clavier). Le défilement s'arrête au survol,
 * quand la mosaïque n'est plus à l'écran, et n'a pas lieu si l'appareil demande moins d'animations.
 * Paramètres : $album (titre, nom, photos), $classes, $sur_vert (section à fond vert)
 */
$photos = array_values($album['photos']);
$n = count($photos);
$titre = trim((string) $album['titre']) !== '' ? $album['titre'] : $album['nom'];
$vert = !empty($sur_vert);
$tuiles = min($n, 3);
$reste = $n - 3;
// Une tuile : toutes les photos empilées, celle de rang $decalage visible au départ (le script fait tourner les autres)
$tuile = function (int $decalage, string $classes, bool $plus = false) use ($photos, $n, $titre, $reste): void { ?>
  <button type="button" class="group relative block overflow-hidden rounded-xl bg-vert-clair sm:rounded-2xl <?= $classes ?>" data-tuile="<?= $decalage ?>" data-album-ouvrir="<?= $decalage ?>"
    aria-label="<?= e($titre) ?> : ouvrir la photothèque (<?= $n ?> photo<?= pluriel($n) ?>)">
    <?php foreach ($photos as $k => $ph): $actif = $k === $decalage; ?>
    <img src="<?= e(media($ph['image'])) ?>" alt="<?= $actif ? e($ph['legende']) : '' ?>" class="absolute inset-0 h-full w-full object-cover transition-[opacity,transform] duration-1000 ease-out group-hover:scale-105 <?= $actif ? 'opacity-100' : 'opacity-0' ?>" data-rang="<?= $k ?>" loading="lazy" decoding="async">
    <?php endforeach ?>
    <?php if ($plus && $reste > 0): ?>
    <span class="absolute inset-0 grid place-items-center bg-encre/55 text-2xl font-bold text-white transition-colors group-hover:bg-encre/45 sm:text-3xl" aria-hidden="true">+<?= $reste ?></span>
    <?php endif ?>
  </button>
<?php };
?>
<div class="<?= e($classes ?? '') ?>" data-phototheque data-album>
  <div class="grid aspect-[16/10] gap-1.5 sm:gap-2.5 <?= $tuiles > 1 ? 'grid-cols-[minmax(0,2fr)_minmax(0,1fr)] grid-rows-2' : '' ?>"<?= $n > 1 ? ' data-diaporama' : '' ?>>
    <?php $tuile(0, $tuiles > 1 ? 'row-span-2 shadow-xl' : 'shadow-xl') ?>
    <?php if ($tuiles === 2): ?><?php $tuile(1, 'row-span-2') ?><?php endif ?>
    <?php if ($tuiles === 3): ?><?php $tuile(1, '') ?><?php $tuile(2, '', true) ?><?php endif ?>
  </div>
  <p class="mt-3 text-[15px] leading-snug font-bold sm:text-base <?= $vert ? 'text-white' : 'text-vert' ?>"><?= e($titre) ?></p>
  <?php /* La liste complète, dans l'ordre, pour la visionneuse */ ?>
  <?php foreach ($photos as $k => $ph): ?>
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
