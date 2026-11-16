<?php /* Badges d'une organisation : vérifiée, partenaire du chapitre. Paramètres : $o, $clair (sur fond vert), $classes */
$coche = '<svg class="size-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12l5 5 9-10"/></svg>';
if (empty($o['verifiee']) && empty($o['partenaire_chapitre'])) return; ?>
<span class="flex flex-wrap gap-1.5 <?= e($classes ?? '') ?>">
  <?php if (!empty($o['verifiee'])): ?><span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px] font-bold <?= !empty($clair) ? 'bg-white text-vert' : 'bg-vert-clair text-vert' ?>"><?= $coche ?>Vérifiée</span><?php endif ?>
  <?php if (!empty($o['partenaire_chapitre'])): ?><span class="inline-flex items-center rounded-full bg-orange px-2 py-0.5 text-[11px] font-bold text-white">Partenaire du chapitre</span><?php endif ?>
</span>
