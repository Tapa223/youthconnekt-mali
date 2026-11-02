<?php $filtres = ['' => 'Tout', 'evenements' => 'Événements', 'actualites' => 'Actualités']; ?>
<section class="conteneur anim-entree pt-10 sm:pt-14">
  <div class="flex flex-wrap items-end justify-between gap-5">
    <div>
      <p class="surtitre surtitre-trait">La vie du chapitre</p>
      <h1 class="mt-3 text-5xl leading-none font-bold sm:text-7xl">Actualités.</h1>
    </div>
    <nav class="flex gap-2" aria-label="Filtrer">
      <?php foreach ($filtres as $valeur => $libelle): $actif = $filtre === $valeur; ?>
      <a href="<?= url('/actualites', ['filtre' => $valeur]) ?>" class="survol-orange inline-flex min-h-10 items-center rounded-full border-[1.5px] border-vert px-4 text-sm font-bold transition-colors <?= $actif ? 'bg-vert text-white' : 'text-vert hover:bg-vert-clair' ?>" <?= $actif ? 'aria-current="true"' : '' ?>><?= $libelle ?></a>
      <?php endforeach ?>
    </nav>
  </div>

  <?php if ($une): ?>
  <div class="mt-7 grid gap-4 lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)]">
    <a href="<?= e($une['url']) ?>" class="survol-leve group relative block aspect-[4/3.3] overflow-hidden rounded-3xl sm:aspect-[16/10] lg:aspect-auto lg:min-h-[420px]">
      <div class="absolute inset-0 transition-transform duration-500 group-hover:scale-105"><?php partiel('partials/photo', ['image' => $une['image'], 'eager' => true]) ?></div>
      <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/25 to-transparent"></div>
      <time datetime="<?= e(date_iso($une['date_affichee'])) ?>" class="absolute top-0 left-0 grid min-w-15 rounded-br-2xl bg-orange px-3 py-2.5 text-center leading-none text-white"><span class="text-3xl font-bold"><?= e(jour($une['date_affichee'])) ?></span><span class="mt-1 text-xs font-bold uppercase"><?= e(mois_pastille($une['date_affichee'])) ?></span></time>
      <div class="absolute inset-x-0 bottom-0 p-5 sm:p-8">
        <span class="etiquette rounded-full bg-white tracking-wide text-vert uppercase">À la une · <?= e($une['categorie_libelle']) ?></span>
        <h2 class="mt-3 text-2xl leading-tight font-bold text-white sm:text-4xl"><?= e($une['titre']) ?></h2>
        <p class="mt-2 line-clamp-2 text-sm text-white/90 sm:text-base"><?= e($une['chapeau']) ?></p>
      </div>
    </a>

    <aside class="relative overflow-hidden rounded-3xl bg-vert p-5 text-white sm:p-6" aria-labelledby="titre-agenda">
      <h2 id="titre-agenda" class="flex items-center gap-2.5 text-xl font-bold text-white"><span class="size-2.5 rounded-full bg-orange" aria-hidden="true"></span>Agenda</h2>
      <?php if ($agenda): ?>
      <ul class="mt-4 space-y-3">
        <?php foreach ($agenda as $ev): ?>
        <li>
          <a href="<?= e($ev['url']) ?>" class="flex items-center gap-3 rounded-xl border border-white/15 bg-white/10 p-3 transition-colors hover:bg-white/20">
            <time datetime="<?= e($ev['date_evenement']) ?>" class="grid min-w-12 rounded-lg bg-white px-2 py-1.5 text-center leading-none text-vert"><span class="text-lg font-bold"><?= e(jour($ev['date_evenement'])) ?></span><span class="mt-0.5 text-[10px] font-bold uppercase"><?= e(mois_pastille($ev['date_evenement'])) ?></span></time>
            <span class="min-w-0"><span class="block text-sm leading-snug font-bold"><?= e($ev['titre']) ?></span><?php if ($ev['lieu']): ?><span class="block text-xs text-white/75"><?= e($ev['lieu']) ?></span><?php endif ?></span>
          </a>
        </li>
        <?php endforeach ?>
      </ul>
      <?php else: ?>
      <p class="mt-4 text-sm text-white/85">Aucun événement à venir pour le moment.</p>
      <?php endif ?>
      <svg class="pointer-events-none absolute right-0 bottom-0 h-24 w-40" viewBox="0 0 160 96" aria-hidden="true"><g stroke="rgb(255 255 255 / .35)" fill="none"><path d="M30 96 L95 20 L160 60"/></g><circle cx="95" cy="20" r="7" fill="#04B2B1"/><circle cx="30" cy="96" r="5" fill="#F1592A"/></svg>
    </aside>
  </div>
  <?php endif ?>
</section>

<?php if ($evenements !== null): ?>
<section class="mt-12 bg-gris py-14 sm:py-16" aria-labelledby="titre-evenements">
  <div class="conteneur">
    <p class="surtitre surtitre-trait">À venir et passés</p>
    <h2 id="titre-evenements" class="titre-section">Événements.</h2>
    <?php if ($evenements): ?>
    <ul class="mt-7 grid gap-4 sm:grid-cols-2 sm:gap-5 lg:grid-cols-3">
      <?php foreach ($evenements as $a): ?><li><?php partiel('cartes/actualite', ['a' => $a, 'mode' => 'evenement']) ?></li><?php endforeach ?>
    </ul>
    <?php else: partiel('partials/vide', ['message' => 'Aucun événement publié pour le moment.', 'lien_admin' => url('/gestion/actualites/ajouter')]); endif ?>
  </div>
</section>
<?php endif ?>

<?php if ($liste !== null): ?>
<section class="conteneur py-14 sm:py-16" aria-labelledby="titre-actus">
  <p class="surtitre surtitre-trait">La vie du chapitre</p>
  <h2 id="titre-actus" class="titre-section">Actualités.</h2>
  <?php if ($liste): ?>
  <ul class="mt-7 grid gap-4 sm:grid-cols-2 sm:gap-5 lg:grid-cols-3">
    <?php foreach ($liste as $a): ?><li><?php partiel('cartes/actualite', ['a' => $a, 'mode' => 'actualite']) ?></li><?php endforeach ?>
  </ul>
  <?php partiel('partials/pagination', ['pagination' => $pagination]) ?>
  <?php else: partiel('partials/vide', ['message' => 'Aucune actualité publiée pour le moment.', 'lien_admin' => url('/gestion/actualites/ajouter')]); endif ?>
</section>
<?php endif ?>
