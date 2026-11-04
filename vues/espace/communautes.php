<?php ob_start(); ?>
<section class="bloc">
  <div class="flex items-center justify-between gap-3 max-lg:justify-end">
    <h1 class="text-xl font-bold max-lg:sr-only sm:text-3xl">Mes communautés</h1>
    <a href="<?= url('/communaute') ?>" class="btn-lien min-h-9 px-3.5 text-[13px]">Découvrir</a>
  </div>
  <?php if ($demandes): ?>
  <ul class="mt-3 space-y-3 lg:mt-6">
    <?php foreach ($demandes as $d): ?>
    <li class="flex flex-wrap items-center gap-x-3 gap-y-2 rounded-xl border border-ligne p-3.5 sm:p-4">
      <div class="w-full min-w-0 sm:w-auto sm:flex-1">
        <a href="<?= url('/communaute/' . $d['communaute_slug']) ?>" class="text-base font-bold text-vert hover:text-orange sm:text-lg"><?= e($d['communaute_nom']) ?></a>
        <p class="text-xs">Demande du <?= e(date_longue($d['demandee_le'])) ?></p>
      </div>
      <?php if ($d['statut'] === 'acceptee'): ?><span class="rounded-full bg-vert-clair px-3 py-1 text-xs font-bold text-vert">Membre</span>
      <?php elseif ($d['statut'] === 'en_attente'): ?><span class="rounded-full bg-orange/15 px-3 py-1 text-xs font-bold text-orange-fonce">En cours de traitement</span>
      <?php else: ?><span class="rounded-full bg-rouge/10 px-3 py-1 text-xs font-bold text-rouge">Non retenue</span><?php endif ?>
      <?php if ($d['statut'] !== 'refusee'): ?>
      <form class="ml-auto" method="post" action="<?= url('/communaute/' . $d['communaute_slug'] . '/quitter') ?>" data-confirmer="<?= $d['statut'] === 'acceptee' ? 'Quitter la communauté ' . e($d['communaute_nom']) . ' ?' : 'Annuler votre demande pour ' . e($d['communaute_nom']) . ' ?' ?>" data-confirmer-oui="<?= $d['statut'] === 'acceptee' ? 'Quitter' : 'Annuler la demande' ?>"><?= csrf() ?><input type="hidden" name="suivant" value="<?= e(adresse_actuelle()) ?>">
        <button type="submit" class="btn-discret"><?= $d['statut'] === 'acceptee' ? 'Quitter' : 'Annuler la demande' ?></button></form>
      <?php endif ?>
    </li>
    <?php endforeach ?>
  </ul>
  <?php else: ?>
  <p class="mt-3 text-sm text-encre lg:mt-5">Vous n'avez encore rejoint aucune communauté.</p>
  <?php endif ?>
</section>
<?php $espace = ob_get_clean(); partiel('espace/base', get_defined_vars());
