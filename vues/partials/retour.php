<?php /* Bouton « Retour » : ramène à la page d'où l'on vient (historique du navigateur) ; $url sert si on arrive directement sur la page.
   Paramètres : $url, $clair (sur fond vert), $classes (remplace la marge par défaut),
   $bord : simple flèche ronde, posée seule dans la marge de gauche sur grand écran (le parent doit être en position relative),
           et dans la première ligne du contenu sur les écrans plus petits. */
$bord = !empty($bord);
$couleurs = !empty($clair) ? 'bg-white/15 text-white hover:bg-white hover:text-vert' : 'bg-white text-vert ring-1 ring-ligne hover:bg-orange hover:text-white hover:ring-orange';
?>
<nav aria-label="Retour" class="<?= e($classes ?? ($bord ? 'shrink-0 xl:absolute xl:top-0 xl:-left-16' : 'mb-2 sm:mb-4')) ?>">
  <?php if ($bord): ?>
  <a href="<?= e($url) ?>" data-retour class="inline-flex size-10 items-center justify-center rounded-full shadow-sm transition-colors <?= $couleurs ?>" aria-label="Retour" title="Retour">
    <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg>
  </a>
  <?php else: ?>
  <a href="<?= e($url) ?>" data-retour class="inline-flex size-10 items-center justify-center rounded-full transition-colors sm:size-auto sm:min-h-9 sm:gap-1.5 sm:pr-4 sm:pl-3 sm:text-sm sm:font-bold <?= $couleurs ?>" aria-label="Retour">
    <svg class="size-5 shrink-0 sm:size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg>
    <span class="hidden sm:inline">Retour</span>
  </a>
  <?php endif ?>
</nav>
