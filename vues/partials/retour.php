<?php /* Bouton « Retour » : ramène à la page d'où l'on vient (historique du navigateur) ; $url sert si on arrive directement sur la page.
   Une simple flèche sur téléphone, flèche et texte sur ordinateur. Paramètres : $url, $clair (sur fond vert), $classes (remplace la marge par défaut) */ ?>
<nav aria-label="Retour" class="<?= e($classes ?? 'mb-2 sm:mb-4') ?>">
  <a href="<?= e($url) ?>" data-retour class="inline-flex size-10 items-center justify-center rounded-full transition-colors sm:size-auto sm:min-h-9 sm:gap-1.5 sm:pr-4 sm:pl-3 sm:text-sm sm:font-bold <?= !empty($clair) ? 'bg-white/15 text-white hover:bg-white hover:text-vert' : 'bg-white text-vert ring-1 ring-ligne hover:bg-orange hover:text-white hover:ring-orange' ?>" aria-label="Retour">
    <svg class="size-5 shrink-0 sm:size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg>
    <span class="hidden sm:inline">Retour</span>
  </a>
</nav>
