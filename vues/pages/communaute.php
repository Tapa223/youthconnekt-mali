<?php $activites = lignes_texte($c['activites']); ?>
<section class="relative isolate overflow-hidden text-white">
  <?php if ($c['image']): ?><img src="<?= e(media($c['image'])) ?>" alt="" class="absolute inset-0 -z-10 h-full w-full object-cover">
  <?php else: ?><img src="<?= asset('img/communaute/bandeau-detail.jpg') ?>" alt="" class="absolute inset-0 -z-10 h-full w-full object-cover"><?php endif ?>
  <div class="absolute inset-0 -z-10 bg-gradient-to-t from-vert via-vert/75 to-vert/30"></div>
  <?php if (!$c['image']) partiel('partials/mention_photo', ['texte' => 'Photo provisoire', 'position' => 'right-3 top-3']) ?>
  <div class="conteneur flex flex-col gap-6 pt-24 pb-10 sm:pt-32 md:flex-row md:items-end md:justify-between">
    <div>
      <?php partiel('partials/retour', ['url' => url('/communaute'), 'clair' => true]) ?>
      <h1 class="mt-2 text-4xl leading-tight font-bold text-white sm:text-6xl"><?= e($c['nom']) ?></h1>
      <p class="mt-2 text-white/90"><?= $c['nb_membres'] ? $c['nb_membres'] . ' membre' . pluriel($c['nb_membres']) : 'Nouvelle communauté' ?></p>
    </div>
    <div class="shrink-0"><?php partiel('partials/bouton_adhesion', ['c' => $c, 'statut' => $statut, 'contexte' => 'detail', 'compte_admin' => $compte_admin]) ?></div>
  </div>
</section>

<section class="bg-gris py-10 sm:py-14">
  <div class="conteneur grid gap-5 lg:grid-cols-[minmax(0,5fr)_minmax(0,7fr)]">
    <div class="h-fit rounded-3xl bg-white p-6 sm:p-7">
      <h2 class="text-xl font-bold">La communauté</h2>
      <p class="mt-3 text-base leading-relaxed"><?= e($c['description'] ?: $c['public_vise']) ?></p>
      <p class="mt-3 text-sm text-encre"><strong>Pour :</strong> <?= e($c['public_vise']) ?></p>
      <?php if ($activites): ?>
      <h3 class="mt-6 font-bold text-encre">Ce qu'on y fait</h3>
      <ul class="mt-3 space-y-2"><?php foreach ($activites as $act): ?><li class="flex items-start gap-3 text-[15px]"><span class="mt-2 size-2 shrink-0 rounded-full bg-orange" aria-hidden="true"></span><?= e($act) ?></li><?php endforeach ?></ul>
      <?php endif ?>
      <?php if ($statut === 'acceptee'): ?>
      <form method="post" action="<?= url('/communaute/' . $c['slug'] . '/quitter') ?>" class="mt-8 border-t border-ligne pt-5" data-confirmer="Quitter la communauté <?= e($c['nom']) ?> ?"><?= csrf() ?>
        <input type="hidden" name="suivant" value="<?= e(url('/compte/mon-espace/communautes')) ?>">
        <button type="submit" class="btn-discret">Quitter la communauté</button>
      </form>
      <?php endif ?>
    </div>
    <div class="space-y-5">
      <div class="rounded-3xl bg-white p-5 sm:p-6">
        <div class="flex items-baseline justify-between gap-3">
          <h2 class="text-xl font-bold">Opportunités de la communauté</h2>
          <a href="<?= url('/opportunites') ?>" class="inline-flex min-h-10 shrink-0 items-center rounded-full border-2 border-vert px-4 text-sm font-bold text-vert transition-colors hover:border-orange hover:bg-orange hover:text-white">Toutes</a>
        </div>
        <?php if ($opportunites): ?>
        <ul class="mt-4 grid gap-4 sm:grid-cols-2"><?php foreach ($opportunites as $o): ?><li><?php partiel('cartes/opportunite', ['o' => $o, 'compacte' => true, 'via' => $c['nom']]) ?></li><?php endforeach ?></ul>
        <?php else: ?><p class="mt-3 text-sm">Aucune opportunité ouverte pour cette communauté en ce moment.</p><?php endif ?>
      </div>
      <div class="rounded-3xl bg-white p-5 sm:p-6">
        <h2 class="text-xl font-bold">Activités récentes</h2>
        <?php if ($activites_recentes): ?>
        <ul class="mt-3 divide-y divide-ligne">
          <?php foreach ($activites_recentes as $a): ?>
          <li><a href="<?= e($a['url']) ?>" class="survol-ligne group -mx-2 flex items-center gap-4 rounded-xl px-2 py-3">
            <span class="block size-14 shrink-0 overflow-hidden rounded-xl"><?php partiel('partials/photo', ['image' => $a['image']]) ?></span>
            <span><span class="block font-bold text-encre group-hover:text-orange"><?= e($a['titre']) ?></span><span class="text-xs"><?= e(date_courte($a['date_affichee'])) ?></span></span>
          </a></li>
          <?php endforeach ?>
        </ul>
        <?php else: ?><p class="mt-3 text-sm">Les prochaines activités de la communauté apparaîtront ici.</p><?php endif ?>
      </div>
    </div>
  </div>
</section>
