<section class="relative overflow-hidden bg-gris">
  <svg class="pointer-events-none absolute top-0 right-0 hidden h-full w-1/2 md:block" viewBox="0 0 600 300" preserveAspectRatio="xMaxYMin meet" aria-hidden="true">
    <g stroke="#04B2B1" stroke-opacity=".35" stroke-width="1.5" fill="none"><path d="M300 210 L420 70 L560 150"/><path d="M420 70 L520 0"/><path d="M560 150 L600 20"/></g>
    <circle cx="300" cy="210" r="9" fill="#F1592A"/><circle cx="420" cy="70" r="12" fill="#04B2B1"/><circle cx="560" cy="150" r="8" fill="#0358D9"/>
  </svg>
  <div class="conteneur anim-entree relative py-14 sm:py-16">
    <p class="surtitre surtitre-trait">Sur le terrain</p>
    <h1 class="mt-3 text-5xl leading-none font-bold sm:text-7xl">Nos initiatives.</h1>
    <p class="mt-5 max-w-xl text-base sm:text-lg">Formations, rencontres, prix : ce que le chapitre organise avec et pour les jeunes, partout au Mali.</p>
    <nav class="mt-7 flex flex-wrap gap-2" aria-label="Filtrer par axe d'action">
      <a href="<?= url('/initiatives') ?>" class="survol-orange inline-flex min-h-10 items-center rounded-full border-2 px-4 text-sm font-bold transition-colors <?= !$axe_actif ? 'border-orange bg-orange text-white' : 'border-vert bg-white text-vert hover:bg-vert-clair' ?>" <?= !$axe_actif ? 'aria-current="true"' : '' ?>>Toutes</a>
      <?php foreach ($axes as $axe): $actif = $axe_actif === $axe['slug']; ?>
      <a href="<?= url('/initiatives', ['axe' => $axe['slug']]) ?>" class="survol-orange inline-flex min-h-10 items-center rounded-full border-2 px-4 text-sm font-bold transition-colors <?= $actif ? 'border-orange bg-orange text-white' : bordure($axe['couleur']) . ' bg-white text-vert hover:bg-gris' ?>" <?= $actif ? 'aria-current="true"' : '' ?>><?= e($axe['nom']) ?></a>
      <?php endforeach ?>
    </nav>
  </div>
</section>

<?php if ($une): ?>
<section class="conteneur pt-10 sm:pt-12" aria-label="Initiative à la une">
  <article class="survol-zoom grid overflow-hidden rounded-3xl bg-vert text-white lg:grid-cols-[minmax(0,7fr)_minmax(0,5fr)]">
    <div class="relative aspect-[16/10] lg:aspect-auto lg:min-h-[380px]">
      <?php partiel('partials/photo', ['image' => $une['image']]) ?>
      <span class="etiquette absolute top-4 left-4 rounded-full bg-orange text-white">À la une</span>
    </div>
    <div class="flex flex-col justify-center p-6 sm:p-9">
      <?php if ($une['axe_nom']): ?><span class="etiquette self-start rounded-full bg-white text-vert"><?= e($une['axe_nom']) ?></span><?php endif ?>
      <h2 class="mt-3 text-2xl leading-tight font-bold text-white sm:text-4xl"><?= e($une['titre']) ?></h2>
      <p class="mt-3 text-[15px] leading-relaxed text-white/90"><?= e($une['resume']) ?></p>
      <?php if ($une['date_libelle'] || $une['lieu'] || $une['participants'] || $une['format']): ?>
      <dl class="mt-5 grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
        <?php foreach (['date_libelle' => 'Date', 'lieu' => 'Lieu', 'participants' => 'Participants', 'format' => 'Format'] as $c => $l): if ($une[$c]): ?>
        <div><dt class="text-xs text-white/75"><?= $l ?></dt><dd class="font-bold"><?= e($une[$c]) ?></dd></div>
        <?php endif; endforeach ?>
      </dl>
      <?php endif ?>
      <a href="<?= e($une['url']) ?>" class="btn btn-blanc mt-6 self-start">Découvrir l'initiative</a>
    </div>
  </article>
</section>
<?php endif ?>

<section class="conteneur py-12 sm:py-16" aria-labelledby="titre-liste">
  <h2 id="titre-liste" class="text-2xl font-bold sm:text-3xl">Toutes les initiatives</h2>
  <?php if ($liste): ?>
  <ul class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
    <?php foreach ($liste as $i): ?><li><?php partiel('cartes/initiative', ['i' => $i]) ?></li><?php endforeach ?>
  </ul>
  <?php partiel('partials/pagination', ['pagination' => $pagination]) ?>
  <?php else: partiel('partials/vide', ['message' => 'Aucune initiative publiée pour le moment.', 'lien_admin' => url('/gestion/initiatives/ajouter')]); endif ?>
</section>

<section class="conteneur pb-16 sm:pb-20">
  <div class="relative overflow-hidden rounded-3xl bg-orange px-6 py-8 text-white sm:px-10 sm:py-10">
    <svg class="pointer-events-none absolute top-0 right-[22%] hidden h-full w-56 md:block" viewBox="0 0 220 140" aria-hidden="true">
      <g stroke="rgb(255 255 255 / .55)" stroke-width="1.2" fill="none"><path d="M20 130 L100 20 L190 80"/></g>
      <circle cx="100" cy="20" r="8" fill="#035E4D"/><circle cx="20" cy="130" r="6" fill="#fff"/><circle cx="190" cy="80" r="6" fill="#fff"/>
    </svg>
    <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h2 class="text-2xl font-bold text-white sm:text-3xl">Vous portez une initiative ?</h2>
        <p class="mt-1 text-white/90">Parlez-en au chapitre, nous pouvons la faire connaître.</p>
      </div>
      <a href="<?= url('/contact', ['sujet' => 'initiative']) ?>" class="btn btn-blanc self-start sm:self-auto">Nous écrire</a>
    </div>
  </div>
</section>
