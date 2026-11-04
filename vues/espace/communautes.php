<?php ob_start(); ?>
<section class="bloc">
  <div class="flex items-center justify-between gap-3">
    <h1 class="text-2xl font-bold sm:text-3xl">Mes communautés</h1>
    <a href="<?= url('/communaute') ?>" class="btn-lien min-h-9 px-3.5 text-[13px]">Découvrir</a>
  </div>
  <?php if ($demandes): ?>
  <ul class="mt-6 space-y-3">
    <?php foreach ($demandes as $d): ?>
    <li class="flex flex-wrap items-center gap-x-4 gap-y-2 rounded-xl border border-ligne p-3.5 sm:p-4">
      <div class="min-w-0 flex-1">
        <a href="<?= url('/communaute/' . $d['communaute_slug']) ?>" class="text-lg font-bold text-vert hover:text-orange"><?= e($d['communaute_nom']) ?></a>
        <p class="text-xs">Demande du <?= e(date_longue($d['demandee_le'])) ?></p>
      </div>
      <?php if ($d['statut'] === 'acceptee'): ?><span class="rounded-full bg-vert-clair px-3 py-1 text-xs font-bold text-vert">Membre</span>
      <?php elseif ($d['statut'] === 'en_attente'): ?><span class="rounded-full bg-orange/15 px-3 py-1 text-xs font-bold text-orange-fonce">En cours de traitement</span>
      <?php else: ?><span class="rounded-full bg-rouge/10 px-3 py-1 text-xs font-bold text-rouge">Non retenue</span><?php endif ?>
      <?php if ($d['statut'] !== 'refusee'): ?>
      <form method="post" action="<?= url('/communaute/' . $d['communaute_slug'] . '/quitter') ?>" <?= $d['statut'] === 'acceptee' ? 'data-confirmer="Quitter la communauté ' . e($d['communaute_nom']) . ' ?"' : '' ?>><?= csrf() ?><input type="hidden" name="suivant" value="<?= e(adresse_actuelle()) ?>">
        <button type="submit" class="btn-discret"><?= $d['statut'] === 'acceptee' ? 'Quitter' : 'Annuler la demande' ?></button></form>
      <?php endif ?>
    </li>
    <?php endforeach ?>
  </ul>
  <?php else: ?>
  <p class="mt-5 rounded-xl bg-gris p-4 text-sm sm:p-5 sm:text-base">Vous n'avez encore rejoint aucune communauté. Choisissez celles qui vous ressemblent pour recevoir les opportunités qui vous concernent.</p>
  <?php endif ?>
</section>
<?php $espace = ob_get_clean(); partiel('espace/base', get_defined_vars());
