<?php
/** Actualités : en haut, « À la une » (les actualités cochées dans l'administration, quatre au plus), puis toutes les autres en défilement continu. (le menu « Catégorie » a été retiré ; l'adresse ?categorie= reste comprise) Un menu « Catégorie » apparaissait dès qu'une actualité est rangée dans une catégorie (gérées dans l'administration). */
$nom_categorie = $categorie_active ? ($categories[$categorie_active]['nom'] ?? '') : '';
$principale = $une[0] ?? null;
$a_cote = array_slice($une, 1);
$titre_haut = $une ? 'À la une' : ($categorie_active ? $nom_categorie : 'Toutes les actualités');
// $suivante : adresse de la suite de la liste (défilement continu), null s'il n'y a plus rien
// Petite ligne colorée « Catégorie · date » (sans catégorie : la date seule)
$surtitre = fn ($a) => ($a['categorie_libelle'] !== '' ? e($a['categorie_libelle']) . ' · ' : '') . e(date_courte($a['date_affichee']));
?>
<section class="conteneur pt-2 pb-10 sm:pt-6 sm:pb-14 lg:pt-5">
  <div class="flex items-center justify-between gap-4">
    <h1 class="sr-only">Actualités</h1>
    <h2 class="text-lg font-bold sm:text-2xl"><?= e($titre_haut) ?></h2>
  </div>

  <?php if ($une): ?>
  <?php /* Téléphone et tablette : les actualités à la une côte à côte, on les fait glisser du doigt */ ?>
  <ul class="masquer-defilement -mx-4 mt-2 flex snap-x snap-mandatory scroll-px-4 gap-3 overflow-x-auto px-4 sm:mt-5 lg:hidden" aria-label="À la une">
    <?php foreach ($une as $a): ?>
    <li class="<?= count($une) > 1 ? 'w-[80%] max-w-sm' : 'w-full' ?> shrink-0 snap-start">
      <a href="<?= e($a['url']) ?>" class="group relative block aspect-[16/10] max-h-[52vw] overflow-hidden rounded-2xl sm:aspect-[4/3] sm:max-h-none">
        <?php partiel('partials/photo', ['image' => $a['image'], 'eager' => true]) ?>
        <span class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/25 to-transparent"></span>
        <span class="absolute inset-x-0 bottom-0 p-3.5">
          <span class="etiquette rounded-full bg-orange text-white"><?= $surtitre($a) ?></span>
          <span class="mt-1.5 line-clamp-2 block text-[16px] leading-tight font-bold text-white sm:line-clamp-3 sm:text-[17px]"><?= e($a['titre']) ?></span>
        </span>
      </a>
    </li>
    <?php endforeach ?>
  </ul>

  <?php /* Ordinateur : la première en grand, les autres à côté */ ?>
  <div class="mt-5 hidden gap-3 lg:mt-3 lg:grid <?= $a_cote ? 'lg:grid-cols-[minmax(0,7fr)_minmax(0,5fr)] lg:gap-6' : '' ?>">
    <?php if (!$a_cote): /* Une seule actualité à la une : grande carte, photo à gauche, texte à droite */ ?>
    <a href="<?= e($principale['url']) ?>" class="survol-leve group grid overflow-hidden rounded-3xl border border-ligne bg-white lg:grid-cols-[minmax(0,7fr)_minmax(0,5fr)]">
      <span class="relative block overflow-hidden lg:min-h-[260px]"><span class="absolute inset-0 transition-transform duration-500 group-hover:scale-105"><?php partiel('partials/photo', ['image' => $principale['image'], 'eager' => true]) ?></span></span>
      <span class="flex flex-col justify-center p-6 xl:p-7">
        <span class="etiquette self-start rounded-full bg-orange text-white"><?= $surtitre($principale) ?></span>
        <span class="mt-3 block text-2xl leading-tight font-bold text-vert group-hover:text-orange xl:text-[1.75rem]"><?= e($principale['titre']) ?></span>
        <span class="mt-3 line-clamp-3 block text-[15px] leading-relaxed text-encre"><?= e($principale['chapeau']) ?></span>
        <?php if (!empty($principale['lieu'])): ?><span class="mt-3 block text-sm text-texte"><?= e($principale['lieu']) ?></span><?php endif ?>
        <span class="btn btn-vert mt-5 min-h-11 self-start px-6">Lire la suite</span>
      </span>
    </a>
    <?php else: ?>
    <a href="<?= e($principale['url']) ?>" class="survol-leve group relative block aspect-[16/9] overflow-hidden rounded-2xl sm:rounded-3xl lg:aspect-auto lg:min-h-[340px]">
      <div class="absolute inset-0 transition-transform duration-500 group-hover:scale-105"><?php partiel('partials/photo', ['image' => $principale['image'], 'eager' => true]) ?></div>
      <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
      <div class="absolute inset-x-0 bottom-0 p-4 sm:p-7">
        <span class="etiquette rounded-full bg-orange text-white"><?= $surtitre($principale) ?></span>
        <h3 class="mt-2 text-lg leading-tight font-bold text-white sm:text-3xl"><?= e($principale['titre']) ?></h3>
        <p class="mt-1.5 line-clamp-2 hidden text-white/90 sm:block"><?= e($principale['chapeau']) ?></p>
      </div>
    </a>
    <?php endif ?>

    <?php if ($a_cote): ?>
    <ul class="grid gap-2.5 lg:content-start lg:gap-3" aria-label="Autres actualités à la une">
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
  <?php if ($une): ?><h2 class="mt-4 text-lg font-bold sm:mt-10 sm:text-2xl lg:mt-6">Toutes les actualités</h2><?php endif ?>
  <ul class="mt-2.5 grid grid-cols-2 gap-2.5 sm:mt-6 sm:gap-5 lg:mt-4 lg:grid-cols-3" data-liste-continue>
    <?php foreach ($liste as $a): ?><li><?php partiel('cartes/actualite', ['a' => $a, 'mode' => 'liste']) ?></li><?php endforeach ?>
  </ul>
  <?php if ($suivante): /* La suite se charge toute seule en faisant défiler ; le bouton reste en secours */ ?>
  <div class="mt-6 flex justify-center" data-suite-liste="<?= e($suivante) ?>">
    <a href="<?= e($suivante) ?>" class="btn btn-contour" data-suite-bouton>Voir plus d'actualités</a>
    <span class="hidden items-center gap-2 text-sm font-bold text-vert" data-suite-chargement><span class="size-4 animate-spin rounded-full border-2 border-vert border-t-transparent" aria-hidden="true"></span>Chargement…</span>
  </div>
  <?php endif ?>
  <?php elseif (!$une): ?>
  <div class="mt-6"><?php partiel('partials/vide', ['message' => $categorie_active ? 'Aucune actualité dans cette catégorie pour le moment.' : 'Aucune actualité publiée pour le moment.', 'lien_admin' => url('/gestion/actualites/ajouter')]) ?></div>
  <?php endif ?>
</section>
