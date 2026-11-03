<?php
/** Actualités : l'actualité à la une avec, à côté, les trois suivantes, puis toutes les autres. Un menu « Catégorie » apparaît dès qu'une actualité est rangée dans une catégorie (gérées dans l'administration). */
$nom_categorie = $categorie_active ? ($categories[$categorie_active]['nom'] ?? '') : '';
$a_cote = $une ? array_splice($liste, 0, 3) : [];
// Petite ligne colorée « Catégorie · date » (sans catégorie : la date seule)
$surtitre = fn ($a) => ($a['categorie_libelle'] !== '' ? e($a['categorie_libelle']) . ' · ' : '') . e(date_courte($a['date_affichee']));
?>
<section class="conteneur pt-4 pb-10 sm:pt-7 sm:pb-14">
  <div class="flex items-center justify-between gap-4">
    <h1 class="text-[2.1rem] leading-none font-bold sm:text-5xl">Actualités</h1>
    <?php if ($nombres): ?>
    <details class="choix-type relative shrink-0" data-choix-type>
      <summary class="flex min-h-11 cursor-pointer list-none items-center gap-2 rounded-full border-2 px-4 text-sm font-bold transition-colors [&::-webkit-details-marker]:hidden <?= $categorie_active ? 'border-orange bg-orange text-white' : 'border-vert bg-white text-vert hover:bg-vert-clair' ?>">
        <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M4 6h16M7 12h10M10 18h4"/></svg>
        <span class="max-w-[9rem] truncate sm:max-w-none"><?= $categorie_active ? e($nom_categorie) : 'Catégorie' ?></span>
        <svg class="choix-fleche size-4 shrink-0 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
      </summary>
      <ul class="absolute right-0 z-40 mt-2 max-h-[60vh] w-64 overflow-y-auto rounded-2xl border border-ligne bg-white p-1.5 shadow-2xl shadow-vert/15">
        <li><a href="<?= url('/actualites') ?>" class="flex min-h-11 items-center justify-between gap-3 rounded-xl px-3 text-[15px] font-bold <?= !$categorie_active ? 'bg-orange text-white' : 'text-encre hover:bg-gris' ?>">Toutes les actualités <span class="text-sm opacity-75"><?= $total ?></span></a></li>
        <?php foreach ($categories as $slug_cat => $c): if (empty($nombres[$slug_cat])) continue; $actif = $categorie_active === $slug_cat; ?>
        <li><a href="<?= url('/actualites', ['categorie' => $slug_cat]) ?>" class="flex min-h-11 items-center justify-between gap-3 rounded-xl px-3 text-[15px] font-bold <?= $actif ? 'bg-orange text-white' : 'text-encre hover:bg-gris' ?>">
          <span class="flex items-center gap-2.5"><span class="size-2.5 shrink-0 rounded-full <?= $actif ? 'bg-white' : fond($c['couleur']) ?>" aria-hidden="true"></span><?= e($c['nom']) ?></span>
          <span class="text-sm opacity-75"><?= $nombres[$slug_cat] ?></span>
        </a></li>
        <?php endforeach ?>
      </ul>
    </details>
    <?php endif ?>
  </div>

  <?php if ($categorie_active): ?>
  <p class="mt-3 text-sm">Catégorie · <strong class="text-vert"><?= e($nom_categorie) ?></strong> <a href="<?= url('/actualites') ?>" class="btn-discret ml-1">Tout afficher</a></p>
  <?php endif ?>

  <?php if ($une): ?>
  <div class="mt-4 grid gap-3 sm:mt-6 lg:grid-cols-[minmax(0,7fr)_minmax(0,5fr)] lg:gap-6">
    <a href="<?= e($une['url']) ?>" class="survol-leve group relative block aspect-[16/9] overflow-hidden rounded-2xl sm:rounded-3xl lg:aspect-auto lg:min-h-[340px]">
      <div class="absolute inset-0 transition-transform duration-500 group-hover:scale-105"><?php partiel('partials/photo', ['image' => $une['image'], 'eager' => true]) ?></div>
      <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
      <div class="absolute inset-x-0 bottom-0 p-4 sm:p-7">
        <span class="etiquette rounded-full bg-orange text-white">À la une · <?= $surtitre($une) ?></span>
        <h2 class="mt-2 text-lg leading-tight font-bold text-white sm:text-3xl"><?= e($une['titre']) ?></h2>
        <p class="mt-1.5 line-clamp-2 hidden text-white/90 sm:block"><?= e($une['chapeau']) ?></p>
      </div>
    </a>

    <?php if ($a_cote): ?>
    <ul class="grid gap-2.5 lg:content-start lg:gap-3" aria-label="Autres actualités récentes">
      <?php foreach ($a_cote as $a): ?>
      <li>
        <a href="<?= e($a['url']) ?>" class="group flex gap-3 rounded-2xl border border-ligne bg-white p-2 transition-shadow hover:shadow-lg sm:gap-4 sm:p-2.5">
          <span class="relative aspect-[4/3] w-24 shrink-0 overflow-hidden rounded-xl sm:w-32"><?php partiel('partials/photo', ['image' => $a['image']]) ?></span>
          <span class="flex min-w-0 flex-col justify-center py-0.5">
            <span class="text-[11px] font-bold tracking-[0.1em] uppercase <?= texte_couleur($a['couleur']) ?>"><?= $surtitre($a) ?></span>
            <span class="mt-1 line-clamp-2 text-[15px] leading-snug font-bold text-vert group-hover:text-orange"><?= e($a['titre']) ?></span>
          </span>
        </a>
      </li>
      <?php endforeach ?>
    </ul>
    <?php endif ?>
  </div>
  <?php endif ?>

  <?php if ($liste): ?>
  <h2 class="<?= $une ? 'mt-8 sm:mt-12' : 'sr-only' ?> text-xl font-bold sm:text-2xl">Toutes les actualités</h2>
  <ul class="mt-4 grid grid-cols-2 gap-2.5 sm:mt-6 sm:gap-5 lg:grid-cols-3">
    <?php foreach ($liste as $a): ?><li><?php partiel('cartes/actualite', ['a' => $a, 'mode' => 'liste']) ?></li><?php endforeach ?>
  </ul>
  <?php partiel('partials/pagination', ['pagination' => $pagination]) ?>
  <?php elseif (!$une && !$a_cote): ?>
  <div class="mt-6"><?php partiel('partials/vide', ['message' => $categorie_active ? 'Aucune actualité dans cette catégorie pour le moment.' : 'Aucune actualité publiée pour le moment.', 'lien_admin' => url('/gestion/actualites/ajouter')]) ?></div>
  <?php endif ?>
</section>
