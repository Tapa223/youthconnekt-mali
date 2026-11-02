<section class="relative isolate overflow-hidden bg-vert text-white">
  <?php partiel('partials/reseau_deco') ?>
  <div class="conteneur anim-entree relative py-12 sm:py-16">
    <p class="surtitre surtitre-trait text-white/85">À saisir maintenant</p>
    <h1 class="mt-3 text-5xl leading-none font-bold text-white sm:text-7xl">Opportunités.</h1>
    <p class="mt-5 hidden max-w-lg text-base text-white/90 sm:block sm:text-lg">Formations, appels à candidatures, bourses, stages et événements relayés par le chapitre.</p>

    <form method="get" action="<?= url('/opportunites') ?>" class="mt-7 flex max-w-2xl flex-col gap-3 sm:flex-row" role="search">
      <?php if ($type_actif): ?><input type="hidden" name="type" value="<?= e($type_actif) ?>"><?php endif ?>
      <label for="recherche" class="sr-only">Rechercher une opportunité</label>
      <div class="relative flex-1">
        <svg class="pointer-events-none absolute top-1/2 left-4 size-5 -translate-y-1/2 text-texte" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
        <input id="recherche" name="q" type="search" value="<?= e($recherche) ?>" placeholder="Formation, bourse, stage…" maxlength="100"
               class="min-h-13 w-full rounded-xl border-0 bg-white pr-4 pl-12 text-base text-encre placeholder:text-texte/70 focus:ring-4 focus:ring-orange/40 focus:outline-none">
      </div>
      <button type="submit" class="btn btn-orange min-h-13 px-7">Rechercher</button>
    </form>

    <nav class="masquer-defilement -mx-4 mt-5 flex gap-2 overflow-x-auto px-4 pb-1 sm:mx-0 sm:flex-wrap sm:px-0" aria-label="Filtrer par type">
      <a href="<?= url('/opportunites', ['q' => $recherche]) ?>" class="survol-orange inline-flex min-h-10 shrink-0 items-center rounded-full border-2 border-white px-4 text-sm font-bold transition-colors <?= !$type_actif ? 'bg-vert text-white' : 'bg-white text-vert' ?>">Toutes</a>
      <?php foreach (TYPES_OPPORTUNITE as $valeur => $libelle): $actif = $type_actif === $valeur; ?>
      <a href="<?= url('/opportunites', ['type' => $valeur, 'q' => $recherche]) ?>" class="survol-orange inline-flex min-h-10 shrink-0 items-center rounded-full border-2 border-white px-4 text-sm font-bold transition-colors <?= $actif ? 'bg-vert text-white' : 'bg-white text-vert hover:bg-vert-clair' ?>" <?= $actif ? 'aria-current="true"' : '' ?>><?= e($libelle) ?></a>
      <?php endforeach ?>
    </nav>
  </div>
</section>

<section class="bg-gris py-10 sm:py-14" aria-labelledby="titre-resultats">
  <div class="conteneur">
    <div class="flex flex-wrap items-baseline justify-between gap-2">
      <h2 id="titre-resultats" class="text-xl font-bold sm:text-2xl"><?= $total ?> opportunité<?= pluriel($total) ?> ouverte<?= pluriel($total) ?><?= $recherche !== '' ? ' pour « ' . e($recherche) . ' »' : '' ?></h2>
      <p class="text-xs sm:text-sm">Triées par date limite · le badge « J- » indique les jours restants</p>
    </div>
    <?php if ($liste): ?>
    <ul class="mt-6 grid grid-cols-2 gap-3 sm:gap-5 lg:grid-cols-3">
      <?php foreach ($liste as $o): ?><li><?php partiel('cartes/opportunite', ['o' => $o]) ?></li><?php endforeach ?>
    </ul>
    <?php partiel('partials/pagination', ['pagination' => $pagination]) ?>
    <?php elseif ($recherche !== '' || $type_actif): ?>
      <?php partiel('partials/vide', ['message' => 'Aucune opportunité ouverte ne correspond à votre recherche.']) ?>
    <?php else: ?>
      <?php partiel('partials/vide', ['message' => 'Aucune opportunité ouverte pour le moment. Inscrivez-vous à la newsletter pour être prévenu des prochaines.', 'lien_admin' => url('/gestion/opportunites/ajouter')]) ?>
    <?php endif ?>
  </div>
</section>

<section class="conteneur grid gap-5 py-14 sm:py-16 md:grid-cols-[minmax(0,7fr)_minmax(0,5fr)]">
  <div class="relative overflow-hidden rounded-3xl bg-orange p-6 text-white sm:p-8">
    <h2 class="text-2xl font-bold text-white">Ne ratez plus une opportunité.</h2>
    <p class="mt-1 text-white/90">Recevez les nouvelles opportunités par e-mail.</p>
    <form action="<?= url('/newsletter') ?>" method="post" class="mt-5 grid gap-3" novalidate>
      <?= csrf() ?>
      <input type="hidden" name="suivant" value="<?= e(adresse_actuelle()) ?>">
      <div class="hidden" aria-hidden="true"><label>Ne pas remplir <input type="text" name="site_web" tabindex="-1" autocomplete="off"></label></div>
      <label for="newsletter-opp" class="sr-only">Votre adresse e-mail</label>
      <input id="newsletter-opp" name="email" type="email" required autocomplete="email" placeholder="Votre adresse e-mail" maxlength="190" class="min-h-12 rounded-lg border-0 bg-white px-4 text-base text-encre placeholder:text-texte/70 focus:ring-4 focus:ring-white/50 focus:outline-none">
      <button type="submit" class="btn btn-blanc min-h-12">M'abonner</button>
    </form>
  </div>
  <div class="flex flex-col justify-center rounded-3xl bg-gris p-6 sm:p-8">
    <h2 class="text-xl font-bold sm:text-2xl">Vous proposez une opportunité ?</h2>
    <p class="mt-1">Organisations membres : partagez-la avec le chapitre.</p>
    <a href="<?= url('/contact', ['sujet' => 'opportunite']) ?>" class="btn btn-contour mt-5 self-start">Nous contacter</a>
  </div>
</section>
