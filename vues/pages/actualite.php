<?php
/*
 * Détail d'une actualité ou d'un événement :
 *  - à gauche, le titre, le chapeau puis le texte ;
 *  - les images : carrousel à flèches sur ordinateur, bande à glisser sur téléphone ;
 *  - en bas, la photothèque : texte à gauche, mosaïque à droite (une grande, quatre petites, « +N ») ; en grand, chaque photo se télécharge et se partage.
 */
$images = [];
if ($a['image']) {
    $images[] = ['src' => media($a['image']), 'legende' => ''];
}
foreach ($photos as $photo) {
    $images[] = ['src' => media($photo['image']), 'legende' => (string) $photo['legende']];
}
$meta = array_filter([$a['periode'], $a['lieu']]);
$icone_telecharger = '<svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 4v11M7 10.5l5 5 5-5M5 20h14"/></svg>';
$icone_partager = '<svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="18" cy="5" r="2.5"/><circle cx="6" cy="12" r="2.5"/><circle cx="18" cy="19" r="2.5"/><path d="M8.2 10.8l7.6-4.4M8.2 13.2l7.6 4.4"/></svg>';
?>
<?php partiel('partials/banniere_detail', [
    'images' => array_column($images, 'src'),
    'titre' => $a['titre'],
    'etiquette' => $a['categorie_libelle'] !== '' ? '<span class="etiquette rounded-full bg-white text-vert">' . e($a['categorie_libelle']) . '</span>' : '',
    'meta' => implode(' · ', $meta),
    'retour' => url('/actualites'),
]) ?>
<article class="conteneur pt-5 pb-10 sm:pt-10 sm:pb-14">
  <div class="grid gap-x-12 gap-y-6 <?= $images ? 'lg:grid-cols-[minmax(0,7fr)_minmax(0,5fr)]' : 'max-w-3xl' ?>">
    <header class="relative lg:col-start-1">
      <?php if ($a['chapeau']): ?><p class="text-lg leading-relaxed text-encre sm:text-xl"><?= e($a['chapeau']) ?></p><?php endif ?>
      <?php if ($a['est_a_venir']): ?>
      <a href="<?= url('/actualites/' . $a['slug'] . '/agenda') ?>" class="mt-5 inline-flex min-h-11 items-center gap-2 rounded-full border-2 border-vert px-5 text-sm font-bold text-vert transition-colors hover:border-orange hover:bg-orange hover:text-white" download>
        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4M12 13v5M9.5 15.5h5"/></svg>
        Ajouter à mon agenda
      </a>
      <?php endif ?>
    </header>

    <?php if ($images): ?>
    <div class="lg:col-start-2 lg:row-span-2 lg:row-start-1">
      <?php /* Téléphone : les photos côte à côte, la suivante dépasse pour inviter à glisser ; un appui l'ouvre en grand */ ?>
      <?php if (count($images) > 1): ?>
      <ul class="masquer-defilement -mx-4 flex snap-x snap-mandatory scroll-px-4 gap-2.5 overflow-x-auto px-4 lg:hidden" data-bande-auto aria-label="Images de l'actualité">
        <?php foreach ($images as $k => $img): ?>
        <li class="w-[82%] shrink-0 snap-start">
          <button type="button" class="block aspect-[4/3] w-full overflow-hidden rounded-2xl bg-vert" data-ouvrir-photo="<?= $k ?>" aria-label="Agrandir la photo <?= $k + 1 ?>">
            <img src="<?= e($img['src']) ?>" alt="<?= e($img['legende']) ?>" class="h-full w-full object-cover" <?= $k === 0 ? 'fetchpriority="high"' : 'loading="lazy"' ?>>
          </button>
        </li>
        <?php endforeach ?>
      </ul>
      <?php endif ?>
      <div class="lg:sticky lg:top-24 <?= count($images) > 1 ? 'max-lg:hidden' : '' ?>">
        <div class="carrousel relative aspect-[4/3] overflow-hidden rounded-3xl bg-vert" data-carrousel aria-roledescription="carrousel" aria-label="Images de l'actualité">
          <?php foreach ($images as $k => $img): ?>
          <figure class="carrousel-vue <?= $k === 0 ? 'actif' : '' ?>" aria-hidden="<?= $k === 0 ? 'false' : 'true' ?>">
            <img src="<?= e($img['src']) ?>" alt="<?= e($img['legende']) ?>" class="h-full w-full object-cover" <?= $k === 0 ? 'fetchpriority="high"' . ($a['image'] ? ' style="view-transition-name: photo-act-' . (int) $a['id'] . '"' : '') : 'loading="lazy"' ?>>
          </figure>
          <?php endforeach ?>
          <?php if (count($images) > 1): ?>
          <button type="button" class="carrousel-fleche left-3" data-precedent aria-label="Image précédente"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg></button>
          <button type="button" class="carrousel-fleche right-3" data-suivant aria-label="Image suivante"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg></button>
          <div class="absolute inset-x-0 bottom-3 flex justify-center gap-1.5">
            <?php foreach ($images as $k => $img): ?><button type="button" class="carrousel-point <?= $k === 0 ? 'actif' : '' ?>" data-point="<?= $k ?>" aria-label="Image <?= $k + 1 ?>"></button><?php endforeach ?>
          </div>
          <?php endif ?>
        </div>
        <?php if (count($images) > 1): ?>
        <a href="#phototheque" class="mt-3 inline-flex items-center gap-2 text-sm font-bold text-vert hover:text-orange">Voir les <?= count($images) ?> photos <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5v14M6 13l6 6 6-6"/></svg></a>
        <?php endif ?>
      </div>
    </div>
    <?php endif ?>

    <div class="lg:col-start-1">
      <div class="prose-ycm prose-article max-w-[46rem]">
        <?= corps_article($a['contenu']) ?>
      </div>
      <?php partiel('partials/partage', ['titre' => $a['titre'], 'classes' => 'mt-8 border-t border-ligne pt-5']) ?>
    </div>
  </div>
</article>

<?php if (count($images) > 1): ?>
<section id="phototheque" class="scroll-mt-20 border-t border-ligne py-10 sm:py-14" aria-labelledby="titre-phototheque" data-phototheque>
  <?php
  // Mosaïque : une grande photo à gauche, jusqu'à 4 petites à droite ; la dernière case porte « +N » s'il en reste
  $total = count($images);
  $montrees = $total > 5 ? 5 : $total;
  $reste = $total - $montrees + ($total > 5 ? 1 : 0);
  $petites = $montrees - 1;
  $place = fn (int $k) => $k === 0 ? 'col-span-2 row-span-2'
      : ($petites === 1 ? 'col-span-2 row-span-2' : ($petites === 2 || ($petites === 3 && $k === 3) ? 'col-span-2' : ''));
  ?>
  <div class="conteneur grid items-center gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(0,2.1fr)] lg:gap-14">
    <div>
      <p class="surtitre"><?= $total ?> photos</p>
      <h2 id="titre-phototheque" class="mt-1 text-2xl font-bold sm:text-3xl">Photothèque</h2>
      <p class="mt-3 max-w-sm text-[15px] leading-relaxed sm:text-base">Découvrez en intégralité les moments forts de <?= stripos($a['categorie_libelle'], 'nement') !== false ? 'cet événement' : 'cette actualité' ?> : chaque photo s'ouvre en grand, se télécharge et se partage.</p>
      <button type="button" class="btn btn-contour mt-5 min-h-11 max-lg:hidden" data-ouvrir-photo="0">Voir toutes les photos</button>
    </div>
    <ul class="grid aspect-[2/1] grid-cols-4 grid-rows-2 gap-2 sm:gap-3">
      <?php foreach ($images as $k => $img): $plus = $reste && $k === $montrees - 1; ?>
      <li class="group relative min-h-0 <?= $k >= $montrees ? 'hidden' : $place($k) ?>">
        <button type="button" class="relative block h-full w-full overflow-hidden rounded-xl bg-vert sm:rounded-2xl" data-agrandir="<?= $k ?>" aria-label="<?= $plus ? 'Voir toute la photothèque, ' . $total . ' photos' : 'Agrandir la photo ' . ($k + 1) ?>">
          <img src="<?= e($img['src']) ?>" alt="<?= e($img['legende']) ?>" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy">
          <?php if ($plus): ?>
          <span class="absolute inset-0 grid place-items-center bg-vert/75 text-white transition-colors group-hover:bg-orange/85" aria-hidden="true">
            <span class="text-center"><span class="block text-xl font-bold sm:text-3xl">+<?= $reste ?></span><span class="mt-0.5 block text-[11px] font-bold sm:text-sm">Voir plus</span></span>
          </span>
          <?php endif ?>
        </button>
      </li>
      <?php endforeach ?>
    </ul>
  </div>
  <dialog class="visionneuse" aria-label="Photo agrandie">
    <img src="" alt="">
    <div class="absolute inset-x-0 bottom-4 flex flex-col items-center gap-3">
      <div class="flex gap-2">
        <a href="" download class="inline-flex min-h-11 items-center gap-2 rounded-full bg-white px-5 text-sm font-bold text-vert transition-colors hover:bg-orange hover:text-white" data-telecharger><?= $icone_telecharger ?>Télécharger</a>
        <button type="button" class="inline-flex min-h-11 items-center gap-2 rounded-full bg-white px-5 text-sm font-bold text-vert transition-colors hover:bg-orange hover:text-white" data-partager-photo><?= $icone_partager ?>Partager</button>
      </div>
      <p class="visionneuse-legende static"></p>
    </div>
    <button type="button" class="visionneuse-bouton top-4 right-4" data-fermer aria-label="Fermer"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg></button>
    <button type="button" class="visionneuse-bouton top-1/2 left-4 -translate-y-1/2" data-precedent aria-label="Photo précédente"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg></button>
    <button type="button" class="visionneuse-bouton top-1/2 right-4 -translate-y-1/2" data-suivant aria-label="Photo suivante"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg></button>
  </dialog>
</section>
<?php endif ?>

<?php if ($autres): ?>
<section class="bg-gris py-14 sm:py-16" aria-labelledby="titre-autres">
  <div class="conteneur">
    <div class="flex items-end justify-between gap-4">
      <h2 id="titre-autres" class="titre-section">À lire aussi</h2>
      <a href="<?= url('/actualites') ?>" class="btn btn-contour hidden sm:inline-flex">Toutes les actualités</a>
      <a href="<?= url('/actualites') ?>" class="btn-tout-voir sm:hidden">Tout voir</a>
    </div>
    <ul class="masquer-defilement -mx-4 mt-7 flex snap-x snap-mandatory scroll-px-4 gap-4 overflow-x-auto px-4 pb-2 sm:mx-0 sm:grid sm:grid-cols-2 sm:gap-5 sm:overflow-visible sm:px-0 lg:grid-cols-3">
      <?php foreach ($autres as $autre): ?><li class="w-[75%] max-w-72 shrink-0 snap-start sm:w-auto sm:max-w-none"><?php partiel('cartes/actualite', ['a' => $autre, 'mode' => 'lire']) ?></li><?php endforeach ?>
    </ul>
  </div>
</section>
<?php endif ?>
