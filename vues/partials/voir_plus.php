<?php /* Bouton « Voir plus » pour un texte replié sur téléphone. Paramètre : $cible (id du bloc data-repliable) */ ?>
<button type="button" class="voir-plus mt-3 hidden items-center gap-1.5 rounded-full bg-vert-clair px-4 py-2 text-sm font-bold text-vert transition-colors hover:bg-orange hover:text-white md:!hidden" aria-controls="<?= e($cible) ?>" aria-expanded="false" data-voir-plus>
  <span data-libelle>Voir plus</span>
  <svg class="size-4 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
</button>
