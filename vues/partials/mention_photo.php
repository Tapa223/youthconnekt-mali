<?php /* Mention « Photo provisoire », tant que les vraies photos du chapitre ne sont pas en place. Paramètres : $texte, $position */
if (!photos_provisoires()) return; ?>
<span class="pointer-events-none absolute <?= e($position ?? 'right-3 bottom-3') ?> z-10 rounded bg-black/65 px-2 py-0.5 text-[10px] font-medium text-white"><?= e($texte ?? 'Photo provisoire · hors Mali') ?></span>
