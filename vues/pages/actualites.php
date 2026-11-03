<?php
/** Actualités : l'actualité à la une avec, à côté, les trois suivantes, puis toutes les autres (actualités et événements mêlés). */
$filtres = ['' => 'Tout', 'evenements' => 'Événements', 'actualites' => 'Actualités'];
?>
<section class="conteneur pt-4 pb-10 sm:pt-8 sm:pb-14">
  <div class="flex flex-wrap items-center justify-between gap-x-5 gap-y-3">
    <h1 class="text-[2.1rem] leading-none font-bold sm:text-5xl">Actualités</h1>
    <nav class="flex gap-1.5 sm:gap-2" aria-label="Filtrer">
      <?php foreach ($filtres as $valeur => $libelle): $actif = $filtre === $valeur; ?>
      <a href="<?= url('/actualites', $valeur !== '' ? ['filtre' => $valeur] : []) ?>" class="survol-orange inline-flex min-h-9 items-center rounded-full border-[1.5px] px-3.5 text-[13px] font-bold transition-colors sm:min-h-10 sm:px-4 sm:text-sm <?= $actif ? 'border-orange bg-orange text-white' : 'border-vert text-vert hover:bg-vert-clair' ?>" <?= $actif ? 'aria-current="true"' : '' ?>><?= $libelle ?></a>
      <?php endforeach ?>
    </nav>
  </div>

  <?php if ($une): ?>
  <div class="mt-5 grid gap-3 sm:mt-7 lg:grid-cols-[minmax(0,7fr)_minmax(0,5fr)] lg:gap-6">
    <a href="<?= e($une['url']) ?>" class="survol-leve group relative block aspect-[16/10] overflow-hidden rounded-2xl sm:rounded-3xl lg:aspect-auto lg:min-h-[380px]">
      <div class="absolute inset-0 transition-transform duration-500 group-hover:scale-105"><?php partiel('partials/photo', ['image' => $une['image'], 'eager' => true]) ?></div>
      <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
      <div class="absolute inset-x-0 bottom-0 p-4 sm:p-7">
        <span class="etiquette rounded-full bg-orange text-white">À la une · <?= e($une['categorie_libelle']) ?> · <?= e(date_courte($une['date_affichee'])) ?></span>
        <h2 class="mt-2 text-xl leading-tight font-bold text-white sm:text-3xl"><?= e($une['titre']) ?></h2>
        <p class="mt-1.5 line-clamp-2 hidden text-white/90 sm:block"><?= e($une['chapeau']) ?></p>
      </div>
    </a>

    <?php if ($a_cote): ?>
    <ul class="grid gap-3 lg:content-start lg:gap-4" aria-label="Autres actualités récentes">
      <?php foreach ($a_cote as $a): ?>
      <li>
        <a href="<?= e($a['url']) ?>" class="group flex gap-3 rounded-2xl border border-ligne bg-white p-2.5 transition-shadow hover:shadow-lg sm:gap-4">
          <span class="relative aspect-[4/3] w-28 shrink-0 overflow-hidden rounded-xl sm:w-36"><?php partiel('partials/photo', ['image' => $a['image']]) ?></span>
          <span class="flex min-w-0 flex-col justify-center py-0.5">
            <span class="text-[11px] font-bold tracking-[0.1em] uppercase <?= $a['categorie'] === 'evenement' ? 'text-orange' : 'text-turquoise' ?>"><?= e($a['categorie_libelle']) ?> · <?= e(date_courte($a['date_affichee'])) ?></span>
            <span class="mt-1 line-clamp-3 text-[15px] leading-snug font-bold text-vert group-hover:text-orange"><?= e($a['titre']) ?></span>
          </span>
        </a>
      </li>
      <?php endforeach ?>
    </ul>
    <?php endif ?>
  </div>
  <?php endif ?>

  <?php if ($liste): ?>
  <h2 class="<?= $une ? 'mt-9 sm:mt-12' : 'mt-6 sr-only' ?> text-xl font-bold sm:text-2xl">Toutes les actualités</h2>
  <ul class="mt-4 grid grid-cols-2 gap-2.5 sm:mt-6 sm:gap-5 lg:grid-cols-3">
    <?php foreach ($liste as $a): ?><li><?php partiel('cartes/actualite', ['a' => $a, 'mode' => 'liste']) ?></li><?php endforeach ?>
  </ul>
  <?php partiel('partials/pagination', ['pagination' => $pagination]) ?>
  <?php elseif (!$une && !$a_cote): ?>
  <div class="mt-6"><?php partiel('partials/vide', ['message' => 'Aucune actualité publiée pour le moment.', 'lien_admin' => url('/gestion/actualites/ajouter')]) ?></div>
  <?php endif ?>
</section>
