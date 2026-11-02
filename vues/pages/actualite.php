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
  <header class="mx-auto max-w-3xl px-4 pt-10 sm:px-6 sm:pt-12">
    <?php partiel('partials/retour', ['url' => url('/actualites'), 'libelle' => 'Retour aux actualités']) ?>
    <p class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm">
      <span class="etiquette rounded-full <?= $a['categorie'] === 'evenement' ? 'bg-vert text-white' : 'bg-turquoise text-encre' ?>"><?= e($a['categorie_libelle']) ?></span>
      <span><time datetime="<?= e(date_iso($a['date_affichee'])) ?>"><?= e(date_longue($a['date_affichee'])) ?></time><?= $a['lieu'] ? ' · ' . e($a['lieu']) : '' ?> · <?= $a['minutes_lecture'] ?> min de lecture</span>
    </p>
    <h1 class="mt-3 text-[1.75rem] leading-[1.1] font-bold sm:text-5xl"><?= e($a['titre']) ?></h1>
    <p class="mt-4 text-[17px] leading-relaxed sm:text-xl"><?= e($a['chapeau']) ?></p>
    <div class="mt-5 flex items-center gap-3">
      <span class="grid size-10 place-items-center rounded-full bg-vert text-xs font-bold text-white" aria-hidden="true">YC</span>
      <p class="text-sm leading-tight"><strong class="block text-encre">L'équipe YouthConnekt Mali</strong>Communication du chapitre</p>
    </div>
  </header>

  <?php if ($a['image']): ?>
  <div class="mx-auto mt-7 w-full max-w-6xl sm:mt-8 sm:px-6 lg:px-8">
    <div class="aspect-[16/10] overflow-hidden sm:aspect-[16/9] sm:rounded-3xl lg:aspect-[16/7]">
      <img src="<?= e(media($a['image'])) ?>" alt="" class="h-full w-full object-cover" fetchpriority="high">
    </div>
  </div>
  <?php endif ?>

  <div class="conteneur mt-8 grid gap-8 pb-14 sm:mt-10 lg:grid-cols-[180px_minmax(0,1fr)] lg:gap-12">
    <aside class="order-2 border-t border-ligne pt-6 lg:order-1 lg:border-0 lg:pt-0">
      <div class="lg:sticky lg:top-24">
        <p class="text-xs font-bold tracking-[0.14em] text-vert uppercase">Partager</p>
        <ul class="mt-3 flex flex-wrap gap-2 lg:flex-col">
          <?php foreach ($partages as $nom => $lien): ?>
          <li><a href="<?= e($lien) ?>" target="_blank" rel="noopener" class="inline-flex min-h-10 w-full items-center justify-center rounded-full border-[1.5px] border-vert px-4 text-sm font-bold text-vert hover:bg-vert-clair"><?= $nom ?></a></li>
          <?php endforeach ?>
          <li><button type="button" data-copier-lien class="inline-flex min-h-10 w-full items-center justify-center rounded-full border-[1.5px] border-vert px-4 text-sm font-bold text-vert hover:bg-vert-clair">Copier le lien</button></li>
        </ul>
      </div>
    </aside>
    <div class="prose-ycm order-1 max-w-3xl lg:order-2">
      <?= corps_article($a['contenu'], $galerie) ?>
    </div>
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
