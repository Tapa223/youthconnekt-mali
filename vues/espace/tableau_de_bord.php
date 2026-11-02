<?php ob_start(); $nb_com = count($mes_communautes); ?>
<section class="relative overflow-hidden rounded-2xl bg-vert p-6 text-white sm:p-8">
  <?php partiel('partials/reseau_deco', ['opacite' => 'opacity-50']) ?>
  <div class="relative flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
    <div>
      <h1 class="text-3xl font-bold text-white sm:text-4xl">Bonjour<?= $u['prenom_affiche'] ? ', ' . e($u['prenom_affiche']) : '' ?>.</h1>
      <p class="mt-1 text-white/90">Voici les nouveautés de vos communautés.</p>
    </div>
    <div class="w-full rounded-xl border border-white/20 bg-white/10 p-4 md:w-72">
      <div class="flex justify-between text-sm"><span>Profil complété</span><strong><?= $completion ?> %</strong></div>
      <div class="mt-2 h-2 rounded-full bg-white/25" role="progressbar" aria-valuenow="<?= $completion ?>" aria-valuemin="0" aria-valuemax="100" aria-label="Profil complété"><div class="h-2 rounded-full bg-orange" style="width: <?= $completion ?>%"></div></div>
      <?php if ($completion < 100): ?><a href="<?= url('/compte/mon-espace/profil') ?>" class="btn btn-blanc mt-3 min-h-9 px-4 text-sm">Compléter mon profil</a><?php endif ?>
    </div>
  </div>
</section>

<ul class="grid grid-cols-3 gap-2 sm:gap-4">
  <li class="survol-leve min-w-0 rounded-2xl border-t-4 border-vert bg-white p-3 sm:p-4"><span class="text-3xl font-bold text-vert"><?= $nb_com ?></span> <span class="block text-xs sm:inline sm:text-sm">communauté<?= pluriel($nb_com) ?> suivie<?= pluriel($nb_com) ?></span></li>
  <li class="survol-leve min-w-0 rounded-2xl border-t-4 border-orange bg-white p-3 sm:p-4"><span class="text-3xl font-bold text-vert"><?= $nb_opportunites ?></span> <span class="block text-xs sm:inline sm:text-sm">opportunité<?= pluriel($nb_opportunites) ?> pour vous</span></li>
  <li class="survol-leve min-w-0 rounded-2xl border-t-4 border-turquoise bg-white p-3 sm:p-4"><span class="text-3xl font-bold text-vert"><?= $nb_evenements ?></span> <span class="block text-xs sm:inline sm:text-sm">événement<?= pluriel($nb_evenements) ?> à venir</span></li>
</ul>

<section class="bloc" aria-labelledby="titre-opp">
  <div class="flex items-baseline justify-between gap-3">
    <h2 id="titre-opp" class="text-xl font-bold">Opportunités pour vous</h2>
    <a href="<?= url('/compte/mon-espace/opportunites') ?>" class="inline-flex min-h-10 shrink-0 items-center rounded-full border-2 border-vert px-4 text-sm font-bold text-vert transition-colors hover:border-orange hover:bg-orange hover:text-white">Toutes</a>
  </div>
  <p class="text-sm">Sélectionnées selon vos communautés.</p>
  <?php if ($opportunites): ?>
  <ul class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-3"><?php foreach ($opportunites as $o): ?><li><?php partiel('cartes/opportunite', ['o' => $o, 'compacte' => true, 'via' => $o['via']]) ?></li><?php endforeach ?></ul>
  <?php else: ?>
  <p class="mt-4 rounded-xl bg-gris p-4 text-sm"><?= $mes_communautes ? 'Aucune opportunité ouverte dans vos communautés pour le moment.' : 'Rejoignez une communauté pour recevoir ici les opportunités qui vous concernent.' ?> <a href="<?= url('/opportunites') ?>" class="btn-lien mt-3 sm:mt-0 sm:ml-2">Voir toutes les opportunités</a></p>
  <?php endif ?>
</section>

<div class="grid gap-5 xl:grid-cols-[minmax(0,7fr)_minmax(0,5fr)]">
  <section class="bloc" aria-labelledby="titre-com">
    <div class="flex items-baseline justify-between gap-3">
      <h2 id="titre-com" class="text-xl font-bold">Mes communautés</h2>
      <a href="<?= url('/communaute') ?>" class="inline-flex min-h-10 shrink-0 items-center rounded-full border-2 border-vert px-4 text-sm font-bold text-vert transition-colors hover:border-orange hover:bg-orange hover:text-white">Toutes les communautés</a>
    </div>
    <?php if ($mes_communautes): ?>
    <ul class="mt-4 space-y-3">
      <?php foreach ($mes_communautes as $k => $c): ?>
      <li><a href="<?= e($c['url']) ?>" class="survol-ligne flex items-center gap-4 rounded-xl border border-l-4 border-ligne p-3 <?= cycle(['border-l-vert', 'border-l-orange', 'border-l-bleu'], $k) ?>">
        <span class="grid size-12 shrink-0 place-items-center rounded-xl bg-vert-clair text-sm font-bold text-vert" aria-hidden="true"><?= e(mb_strtoupper(mb_substr($c['nom'], 0, 2))) ?></span>
        <span class="min-w-0 flex-1"><span class="block font-bold text-vert"><?= e($c['nom']) ?></span><span class="text-xs"><?= $c['nb_membres'] ?> membre<?= pluriel($c['nb_membres']) ?> · <?= $c['nb_opportunites'] ?> opportunité<?= pluriel($c['nb_opportunites']) ?> ouverte<?= pluriel($c['nb_opportunites']) ?></span></span>
        <span class="text-sm font-bold text-vert">Voir</span>
      </a></li>
      <?php endforeach ?>
    </ul>
    <?php else: ?><p class="mt-3 text-sm">Vous n'avez encore rejoint aucune communauté.</p><?php endif ?>

    <?php if ($demandes_en_attente): ?>
    <h3 class="mt-6 font-bold text-encre">Mes demandes d'intégration</h3>
    <ul class="mt-2 space-y-2"><?php foreach ($demandes_en_attente as $d): ?><li class="flex items-center justify-between gap-3 rounded-xl bg-gris px-4 py-3 text-sm"><span class="font-bold text-vert"><?= e($d['communaute_nom']) ?></span><span class="rounded-full bg-orange/15 px-2.5 py-0.5 text-xs font-bold text-orange-fonce">En cours de traitement</span></li><?php endforeach ?></ul>
    <?php endif ?>

    <?php if ($suggestions): ?>
    <h3 class="mt-6 font-bold text-encre">Suggestions pour vous</h3>
    <ul class="mt-2 space-y-2">
      <?php foreach ($suggestions as $c): ?>
      <li class="flex items-center gap-3 rounded-xl bg-gris p-3">
        <span class="min-w-0 flex-1"><a href="<?= e($c['url']) ?>" class="block font-bold text-vert hover:text-orange"><?= e($c['nom']) ?></a><span class="text-xs"><?= $c['nb_membres'] ? $c['nb_membres'] . ' membre' . pluriel($c['nb_membres']) : 'Soyez parmi les premiers' ?></span></span>
        <a href="<?= e($c['url']) ?>" class="btn-lien">Voir la communauté</a>
      </li>
      <?php endforeach ?>
    </ul>
    <?php endif ?>
  </section>

  <section class="bloc h-fit" aria-labelledby="titre-evt">
    <div class="flex items-baseline justify-between gap-3">
      <h2 id="titre-evt" class="text-xl font-bold">Prochains événements</h2>
      <a href="<?= url('/actualites', ['filtre' => 'evenements']) ?>" class="inline-flex min-h-10 shrink-0 items-center rounded-full border-2 border-vert px-4 text-sm font-bold text-vert transition-colors hover:border-orange hover:bg-orange hover:text-white">Agenda</a>
    </div>
    <?php if ($evenements): ?>
    <ul class="mt-4 space-y-4">
      <?php foreach ($evenements as $ev): ?>
      <li><a href="<?= e($ev['url']) ?>" class="group flex items-center gap-4">
        <time datetime="<?= e($ev['date_evenement']) ?>" class="grid min-w-13 rounded-xl bg-vert px-2 py-2 text-center leading-none text-white"><span class="text-xl font-bold"><?= e(jour($ev['date_evenement'])) ?></span><span class="mt-0.5 text-[10px] font-bold uppercase"><?= e(mois_pastille($ev['date_evenement'])) ?></span></time>
        <span><span class="block font-bold text-encre group-hover:text-orange"><?= e($ev['titre']) ?></span><?php if ($ev['lieu']): ?><span class="text-xs"><?= e($ev['lieu']) ?></span><?php endif ?></span>
      </a></li>
      <?php endforeach ?>
    </ul>
    <?php else: ?><p class="mt-3 text-sm">Aucun événement à venir pour le moment.</p><?php endif ?>
  </section>
</div>
<?php $espace = ob_get_clean(); partiel('espace/base', get_defined_vars());
