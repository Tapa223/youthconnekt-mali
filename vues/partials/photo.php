<?php
/*
 * Affiche une image envoyée depuis l'administration, ou un bloc de remplacement
 * aux couleurs de la charte. La mention entre crochets n'est visible que de l'équipe.
 * Paramètres : $image, $alt, $classes, $eager (chargement prioritaire), $mention
 */
if (!empty($image)): ?>
<img src="<?= e(media($image)) ?>" alt="<?= e($alt ?? '') ?>" class="<?= e($classes ?? 'h-full w-full object-cover') ?>" <?= !empty($eager) ? 'fetchpriority="high"' : 'loading="lazy"' ?> decoding="async">
<?php else: ?>
<div class="photo-absente <?= e($classes ?? '') ?>" role="presentation">
  <?php if (est_admin()): ?><span class="m-3 rounded bg-black/55 px-2 py-1 text-[11px] font-medium text-white">[<?= e($mention ?? 'Photo à fournir') ?>]</span><?php endif ?>
</div>
<?php endif ?>
