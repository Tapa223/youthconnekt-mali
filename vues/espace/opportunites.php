<?php ob_start(); ?>
<span data-recharger-apres-envoi hidden></span>
<section class="bloc">
  <div class="flex items-center justify-between gap-3">
    <h1 class="text-2xl font-bold sm:text-3xl">Mes opportunités</h1>
    <button type="button" class="btn btn-orange min-h-10 px-4 text-sm" data-ouvrir-dialogue="dialogue-opportunite">Proposer<span class="hidden sm:inline">&nbsp;une opportunité</span></button>
  </div>
  <p class="mt-1 text-sm">Les opportunités ouvertes dans vos communautés, de la plus proche de sa date limite à la plus lointaine.</p>
  <?php if ($opportunites): ?>
  <ul class="mt-5 grid grid-cols-2 gap-2.5 sm:gap-4 xl:grid-cols-3"><?php foreach ($opportunites as $o): ?><li><?php partiel('cartes/opportunite', ['o' => $o, 'compacte' => true, 'via' => $o['via']]) ?></li><?php endforeach ?></ul>
  <?php elseif ($a_des_communautes): ?>
  <p class="mt-5 rounded-xl bg-gris p-5">Aucune opportunité ouverte dans vos communautés pour le moment. <a href="<?= url('/opportunites') ?>" class="btn-lien mt-3 sm:mt-0 sm:ml-2">Voir toutes les opportunités</a></p>
  <?php else: ?>
  <p class="mt-5 rounded-xl bg-gris p-5">Rejoignez une communauté pour voir ici les opportunités qui vous concernent. <a href="<?= url('/communaute') ?>" class="btn-lien mt-3 sm:mt-0 sm:ml-2">Découvrir les communautés</a></p>
  <?php endif ?>
</section>
<?php partiel('partials/proposer_opportunite', ['types' => $types]) ?>
<?php $espace = ob_get_clean(); partiel('espace/base', get_defined_vars());
