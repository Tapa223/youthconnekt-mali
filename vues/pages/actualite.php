<?php
$adresse = url_absolue(adresse_actuelle());
$galerie = '';
if ($photos) {
    $galerie = '<ul class="!my-8 grid grid-cols-2 gap-3 !pl-0 [&>li]:!pl-0 [&>li]:before:!hidden">';
    foreach ($photos as $photo) {
        $galerie .= '<li class="aspect-[4/3] overflow-hidden rounded-2xl"><img src="' . e(media($photo['image'])) . '" alt="' . e($photo['legende']) . '" class="h-full w-full object-cover" loading="lazy"></li>';
    }
    $galerie .= '</ul>';
}
$partages = [
    'Facebook' => 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode($adresse),
    'LinkedIn' => 'https://www.linkedin.com/sharing/share-offsite/?url=' . rawurlencode($adresse),
    'WhatsApp' => 'https://wa.me/?text=' . rawurlencode($a['titre'] . ' ' . $adresse),
];
?>
<article>
  <header class="bg-gris">
    <div class="conteneur pt-6 pb-10 sm:pt-8 sm:pb-14">
      <?php partiel('partials/retour', ['url' => url('/actualites')]) ?>
      <div class="grid items-center gap-8 <?= $a['image'] ? 'lg:grid-cols-[minmax(0,7fr)_minmax(0,5fr)] lg:gap-12' : '' ?>">
        <div class="<?= $a['image'] ? '' : 'max-w-3xl' ?>">
          <p class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm">
            <span class="etiquette rounded-full <?= $a['categorie'] === 'evenement' ? 'bg-vert text-white' : 'bg-turquoise text-encre' ?>"><?= e($a['categorie_libelle']) ?></span>
            <span><time datetime="<?= e(date_iso($a['date_affichee'])) ?>"><?= e(date_longue($a['date_affichee'])) ?></time><?= $a['lieu'] ? ' · ' . e($a['lieu']) : '' ?> · <?= $a['minutes_lecture'] ?> min de lecture</span>
          </p>
          <h1 class="mt-4 text-[2rem] leading-[1.08] font-bold sm:text-5xl lg:text-[3.4rem]"><?= e($a['titre']) ?></h1>
          <?php if ($a['chapeau']): ?><p class="mt-5 text-lg leading-relaxed text-encre sm:text-[21px]"><?= e($a['chapeau']) ?></p><?php endif ?>
          <div class="mt-6 flex items-center gap-3">
            <span class="grid size-11 place-items-center rounded-full bg-vert text-xs font-bold text-white" aria-hidden="true">YC</span>
            <p class="text-sm leading-tight"><strong class="block text-[15px] text-encre">L'équipe YouthConnekt Mali</strong>Communication du chapitre</p>
          </div>
        </div>
        <?php if ($a['image']): ?>
        <div class="aspect-[4/3] overflow-hidden rounded-3xl bg-white">
          <img src="<?= e(media($a['image'])) ?>" alt="" class="h-full w-full object-cover" fetchpriority="high">
        </div>
        <?php endif ?>
      </div>
    </div>
  </header>

  <div class="conteneur grid gap-10 py-10 sm:py-14 lg:grid-cols-[minmax(0,1fr)_200px] lg:gap-14">
    <div class="prose-ycm prose-article mx-auto w-full max-w-[46rem] lg:mx-0 lg:justify-self-center">
      <?= corps_article($a['contenu'], $galerie) ?>
    </div>
    <aside class="border-t border-ligne pt-6 lg:border-0 lg:pt-1">
      <div class="lg:sticky lg:top-28">
        <p class="text-xs font-bold tracking-[0.14em] text-vert uppercase">Partager</p>
        <ul class="mt-3 flex flex-wrap gap-2 lg:flex-col">
          <?php foreach ($partages as $nom => $lien): ?>
          <li><a href="<?= e($lien) ?>" target="_blank" rel="noopener" class="inline-flex min-h-10 w-full items-center justify-center rounded-full border-[1.5px] border-vert px-4 text-sm font-bold text-vert hover:bg-vert hover:text-white"><?= $nom ?></a></li>
          <?php endforeach ?>
          <li><button type="button" data-copier-lien class="inline-flex min-h-10 w-full items-center justify-center rounded-full border-[1.5px] border-vert px-4 text-sm font-bold text-vert hover:bg-vert hover:text-white">Copier le lien</button></li>
        </ul>
      </div>
    </aside>
  </div>
</article>

<?php if ($autres): ?>
<section class="bg-gris py-14 sm:py-16" aria-labelledby="titre-autres">
  <div class="conteneur">
    <div class="flex items-end justify-between gap-4">
      <h2 id="titre-autres" class="titre-section">À lire aussi.</h2>
      <a href="<?= url('/actualites') ?>" class="btn btn-contour hidden sm:inline-flex">Toutes les actualités</a>
      <a href="<?= url('/actualites') ?>" class="btn-tout-voir sm:hidden">Tout voir</a>
    </div>
    <ul class="masquer-defilement -mx-4 mt-7 flex snap-x snap-mandatory gap-4 overflow-x-auto px-4 pb-2 sm:mx-0 sm:grid sm:grid-cols-2 sm:gap-5 sm:overflow-visible sm:px-0 lg:grid-cols-3">
      <?php foreach ($autres as $autre): ?><li class="w-[75%] max-w-72 shrink-0 snap-start sm:w-auto sm:max-w-none"><?php partiel('cartes/actualite', ['a' => $autre, 'mode' => 'lire']) ?></li><?php endforeach ?>
    </ul>
  </div>
</section>
<?php endif ?>
