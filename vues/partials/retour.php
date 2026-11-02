<?php /* Bouton de retour vers la liste, en haut des pages détail. Paramètres : $url, $libelle, $clair (fond vert) */ ?>
<nav aria-label="Retour" class="mb-5">
  <a href="<?= e($url) ?>" class="inline-flex min-h-11 items-center gap-2 rounded-full border-2 px-5 text-sm font-bold transition-colors <?= !empty($clair) ? 'border-white/70 bg-white/10 text-white hover:border-white hover:bg-white hover:text-vert' : 'border-vert bg-white text-vert hover:border-orange hover:bg-orange hover:text-white' ?>">
    <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg>
    <?= e($libelle) ?>
  </a>
</nav>
