<?php
/*
 * Affiche une image envoyée depuis l'administration, ou un bloc de remplacement
 * aux couleurs de la charte. La mention entre crochets n'est visible que de l'équipe.
 * Paramètres : $image, $alt, $classes, $eager (chargement prioritaire), $mention,
 * $largeur (largeur maximale servie, en pixels : 640 pour une carte), $transition (nom commun à la carte et à la page de détail : la photo glisse de l'une à l'autre)
 */
if (!empty($image)): ?>
<img data-l="<?= (int) ($largeur ?? 1200) ?>" src="<?= e(media($image)) ?>" alt="<?= e($alt ?? '') ?>" class="photo-attente <?= e($classes ?? 'h-full w-full object-cover') ?>" <?= !empty($eager) ? 'fetchpriority="high"' : 'loading="lazy"' ?> decoding="async"<?= !empty($transition) ? ' style="view-transition-name: ' . e($transition) . '"' : '' ?>>
<?php else: ?>
<div class="photo-absente <?= e($classes ?? '') ?>" role="presentation">
  <?php if (est_admin()): ?><span class="m-3 rounded bg-black/55 px-2 py-1 text-[11px] font-medium text-white">[<?= e($mention ?? 'Photo à fournir') ?>]</span><?php endif ?>
</div>
<?php endif ?>
