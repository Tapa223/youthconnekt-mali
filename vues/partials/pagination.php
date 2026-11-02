<?php /* Pagination. Paramètre : $pagination (voir paginer()) */
if (($pagination['pages'] ?? 1) <= 1) return; ?>
<nav class="mt-10 flex items-center justify-center gap-2" aria-label="Pagination">
  <?php if ($pagination['page'] > 1): ?><a href="<?= e(avec_parametres(['page' => $pagination['page'] - 1])) ?>" class="btn btn-contour">Précédent</a><?php endif ?>
  <span class="px-3 text-sm">Page <?= $pagination['page'] ?> sur <?= $pagination['pages'] ?></span>
  <?php if ($pagination['page'] < $pagination['pages']): ?><a href="<?= e(avec_parametres(['page' => $pagination['page'] + 1])) ?>" class="btn btn-contour">Suivant</a><?php endif ?>
</nav>
