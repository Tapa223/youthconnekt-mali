<?php ob_start(); ?>
<span data-recharger-apres-envoi hidden></span>
<section class="bloc">
  <div class="flex items-center justify-between gap-3 max-lg:justify-end">
    <h1 class="text-xl font-bold max-lg:sr-only sm:text-3xl">Mes opportunités</h1>
    <button type="button" class="btn btn-orange min-h-10 px-4 text-sm" data-ouvrir-dialogue="dialogue-opportunite">Proposer<span class="hidden sm:inline">&nbsp;une opportunité</span></button>
  </div>
  <p class="mt-1 hidden text-sm lg:block">Les opportunités ouvertes dans vos communautés, de la plus proche de sa date limite à la plus lointaine.</p>
  <?php if ($opportunites): ?>
  <ul class="mt-3 grid grid-cols-2 lg:mt-5 gap-2.5 sm:gap-4 xl:grid-cols-3"><?php foreach ($opportunites as $o): ?><li><?php partiel('cartes/opportunite', ['o' => $o, 'compacte' => true, 'via' => $o['via']]) ?></li><?php endforeach ?></ul>
  <?php elseif ($a_des_communautes): ?>
  <p class="mt-3 text-sm text-encre lg:mt-5">Rien d'ouvert dans vos communautés pour l'instant.</p>
  <a href="<?= url('/opportunites') ?>" class="btn-lien mt-3 min-h-10">Voir toutes les opportunités</a>
  <?php else: ?>
  <p class="mt-3 text-sm text-encre lg:mt-5">Rejoignez une communauté pour voir ses opportunités ici.</p>
  <a href="<?= url('/communaute') ?>" class="btn-lien mt-3 min-h-10">Trouver une communauté</a>
  <?php endif ?>
</section>
<?php partiel('partials/proposer_opportunite', ['types' => $types]) ?>
<?php $espace = ob_get_clean(); partiel('espace/base', get_defined_vars());
