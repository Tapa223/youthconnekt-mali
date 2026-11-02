<?php /* Bouton « Retour » discret, en haut des pages détail. Paramètres : $url, $clair (sur fond vert) */ ?>
<nav aria-label="Retour" class="mb-4">
  <a href="<?= e($url) ?>" class="inline-flex min-h-9 items-center gap-1.5 rounded-full pr-4 pl-3 text-sm font-bold transition-colors <?= !empty($clair) ? 'bg-white/15 text-white hover:bg-white hover:text-vert' : 'bg-white text-vert ring-1 ring-ligne hover:bg-orange hover:text-white hover:ring-orange' ?>">
    <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg>
    Retour
  </a>
</nav>
