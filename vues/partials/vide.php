<?php /* Message quand une liste est vide. Paramètres : $message, $lien_admin (adresse d'ajout pour l'équipe) */ ?>
<div class="mt-6 rounded-2xl border border-dashed border-vert/30 bg-gris p-8 text-center">
  <p class="text-base text-encre"><?= e($message) ?></p>
  <?php if (est_admin() && !empty($lien_admin)): ?><a href="<?= e($lien_admin) ?>" class="btn btn-vert mt-4">Ajouter depuis l'administration</a><?php endif ?>
</div>
