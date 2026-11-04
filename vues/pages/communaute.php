<?php
/*
 * Détail d'une communauté :
 *  - en haut, bandeau compact : retour, nom, nombre de membres, bouton pour l'intégrer ;
 *  - à gauche, la présentation (description, public, ce qu'on y fait) puis les membres au profil visible ;
 *  - à droite, les opportunités et les activités de la communauté.
 */
$activites = lignes_texte($c['activites']);
$paragraphes = array_values(array_filter(array_map('trim', preg_split('/\R{2,}/u', (string) $c['description']))));
$nb = (int) $c['nb_membres'];
?>
<section class="relative isolate overflow-hidden text-white">
  <img src="<?= $c['image'] ? e(media($c['image'])) : asset('img/communaute/bandeau-detail.jpg') ?>" alt="" class="absolute inset-0 -z-10 h-full w-full object-cover">
  <div class="absolute inset-0 -z-10 bg-gradient-to-t from-vert via-vert/85 to-vert/60 md:bg-gradient-to-r md:via-vert/80 md:to-vert/35"></div>
  <div class="conteneur pt-3 pb-6 sm:pt-5 sm:pb-9">
    <?php partiel('partials/retour', ['url' => url('/communaute'), 'clair' => true]) ?>
    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
      <div>
        <h1 class="text-[2rem] leading-tight font-bold text-white sm:text-5xl"><?= e($c['nom']) ?></h1>
        <p class="mt-2 inline-flex items-center gap-2 text-white/95 sm:text-lg">
          <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c.6-3.6 3.2-5.5 6.5-5.5s5.9 1.9 6.5 5.5M16 4.8a3.5 3.5 0 0 1 0 6.4M18.5 14.8c1.7.8 2.7 2.6 3 5.2"/></svg>
          <strong><?= $nb ?></strong> membre<?= pluriel($nb) ?>
        </p>
      </div>
      <div class="shrink-0"><?php partiel('partials/bouton_adhesion', ['c' => $c, 'statut' => $statut, 'contexte' => 'detail', 'compte_admin' => $compte_admin]) ?></div>
    </div>
  </div>
</section>

<section class="bg-gris py-6 sm:py-10">
  <div class="conteneur grid gap-5 lg:grid-cols-[minmax(0,7fr)_minmax(0,5fr)]">
    <div class="space-y-5">
      <article class="rounded-3xl bg-white p-5 sm:p-8">
        <h2 class="text-xl font-bold sm:text-2xl">À propos de la communauté</h2>
        <?php if ($paragraphes): ?>
        <div class="mt-3 space-y-3 text-[15px] leading-relaxed text-encre sm:text-[17px]">
          <?php foreach ($paragraphes as $para): ?><p><?= nl2br(e($para)) ?></p><?php endforeach ?>
        </div>
        <?php endif ?>

        <div class="mt-5 flex items-start gap-3 rounded-2xl bg-vert-clair p-4">
          <span class="grid size-10 shrink-0 place-items-center rounded-full bg-vert text-white" aria-hidden="true"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="8.5"/><circle cx="12" cy="12" r="3.5"/></svg></span>
          <p class="text-[15px] leading-snug"><strong class="block text-vert">Pour qui</strong><?= e($c['public_vise']) ?></p>
        </div>

        <?php if ($activites): ?>
        <h3 class="mt-6 text-lg font-bold text-vert">Ce qu'on y fait</h3>
        <ul class="mt-3 grid gap-2.5 sm:grid-cols-2">
          <?php foreach ($activites as $act): ?>
          <li class="flex items-start gap-2.5 text-[15px]"><svg class="mt-0.5 size-5 shrink-0 text-orange" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7"/></svg><?= e($act) ?></li>
          <?php endforeach ?>
        </ul>
        <?php endif ?>
      </article>

      <section class="rounded-3xl bg-white p-5 sm:p-8" aria-labelledby="titre-membres">
        <div class="flex items-baseline justify-between gap-3">
          <h2 id="titre-membres" class="text-xl font-bold sm:text-2xl">Les membres</h2>
          <?php if ($membres): ?><span class="text-sm"><?= count($membres) ?> profil<?= pluriel(count($membres)) ?> visible<?= pluriel(count($membres)) ?></span><?php endif ?>
        </div>
        <?php if ($membres): ?>
        <ul class="mt-4 grid grid-cols-2 gap-2.5 sm:grid-cols-3 sm:gap-3">
          <?php foreach ($membres as $k => $m): ?>
          <li>
            <a href="<?= e($m['url']) ?>" class="group flex h-full flex-col items-center rounded-2xl border border-ligne p-3 text-center transition-shadow hover:shadow-lg sm:p-4">
              <?php if ($m['photo']): ?><img src="<?= e(media($m['photo'])) ?>" alt="" class="size-16 rounded-full object-cover sm:size-20" loading="lazy">
              <?php else: ?><span class="grid size-16 place-items-center rounded-full text-lg font-bold text-white sm:size-20 sm:text-xl <?= ['bg-orange', 'bg-turquoise', 'bg-bleu', 'bg-vert'][$k % 4] ?>" aria-hidden="true"><?= e($m['initiales']) ?></span><?php endif ?>
              <span class="mt-2.5 line-clamp-2 text-[15px] leading-tight font-bold text-vert group-hover:text-orange"><?= e($m['nom']) ?></span>
              <?php if ($m['detail'] !== ''): ?><span class="mt-1 line-clamp-2 text-xs text-texte"><?= e($m['detail']) ?></span><?php endif ?>
              <span class="mt-auto block w-full pt-3"><?php partiel('partials/bouton_carte', ['texte' => 'Voir le profil']) ?></span>
            </a>
          </li>
          <?php endforeach ?>
        </ul>
        <?php else: ?>
        <p class="mt-3 text-sm"><?= $nb ? "Les membres de cette communauté n'ont pas encore rendu leur profil visible." : 'Aucun membre pour le moment. Soyez parmi les premiers à rejoindre cette communauté.' ?></p>
        <?php endif ?>
      </section>

      <?php if ($statut === 'acceptee'): ?>
      <form method="post" action="<?= url('/communaute/' . $c['slug'] . '/quitter') ?>" class="px-1" data-confirmer="Quitter la communauté <?= e($c['nom']) ?> ?"><?= csrf() ?>
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
        <ul class="mt-4 grid grid-cols-2 gap-2.5 sm:gap-4"><?php foreach ($opportunites as $o): ?><li><?php partiel('cartes/opportunite', ['o' => $o, 'compacte' => true]) ?></li><?php endforeach ?></ul>
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
