<?php /* Bouton « Retour » : ramène à la page d'où l'on vient (historique du navigateur) ; $url sert si on arrive directement sur la page.
   Paramètres : $url, $clair (sur fond vert), $classes (remplace la marge par défaut),
   $toujours : avec $bord, la flèche reste aussi sur téléphone (bandeaux des pages de détail),
   $bord : simple flèche ronde, posée seule dans la marge de gauche sur grand écran (le parent doit être en position relative),
           et dans la première ligne du contenu sur les écrans plus petits. */
$bord = !empty($bord);
// Toujours bien visible : fond vert et flèche blanche (fond blanc sur les en-têtes verts), orange au survol
$couleurs = !empty($clair) ? 'bg-white text-vert hover:bg-orange hover:text-white' : 'bg-vert text-white hover:bg-orange';
?>
<?php /* Sur téléphone, le bouton Retour du navigateur suffit : la flèche ronde n'y est pas affichée */ ?>
<nav aria-label="Retour" class="<?= $bord && empty($toujours) ? 'max-md:hidden ' : '' ?><?= e($classes ?? ($bord ? 'shrink-0 xl:absolute xl:top-0 xl:-left-16' : 'mb-2 sm:mb-4')) ?>">
  <?php if ($bord): ?>
  <a href="<?= e($url) ?>" data-retour class="inline-flex size-8 items-center justify-center rounded-full shadow-sm sm:size-9 transition-colors <?= $couleurs ?>" aria-label="Retour" title="Retour">
    <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg>
  </a>
  <?php else: ?>
  <a href="<?= e($url) ?>" data-retour class="inline-flex size-10 items-center justify-center rounded-full transition-colors sm:size-auto sm:min-h-9 sm:gap-1.5 sm:pr-4 sm:pl-3 sm:text-sm sm:font-bold <?= $couleurs ?>" aria-label="Retour">
    <svg class="size-5 shrink-0 sm:size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg>
    <span class="hidden sm:inline">Retour</span>
  </a>
  <?php endif ?>
</nav>
