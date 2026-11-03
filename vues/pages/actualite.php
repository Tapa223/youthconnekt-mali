<?php
/*
 * Détail d'une actualité ou d'un événement :
 *  - en haut, la présentation (titre, chapeau) et à droite un carrousel des images qui défilent ;
 *  - au milieu, le texte ;
 *  - en bas, la photothèque de l'événement (clic sur une photo pour l'agrandir).
 */
$adresse = url_absolue(adresse_actuelle());
$images = [];
if ($a['image']) {
    $images[] = ['src' => media($a['image']), 'legende' => ''];
}
foreach ($photos as $photo) {
    $images[] = ['src' => media($photo['image']), 'legende' => (string) $photo['legende']];
}
$partages = [
    'Facebook' => 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode($adresse),
    'LinkedIn' => 'https://www.linkedin.com/sharing/share-offsite/?url=' . rawurlencode($adresse),
    'WhatsApp' => 'https://wa.me/?text=' . rawurlencode($a['titre'] . ' ' . $adresse),
];
$meta = array_filter([date_longue($a['date_affichee']), $a['lieu'], $a['minutes_lecture'] . ' min de lecture']);
?>
<article>
  <header class="bg-gris">
    <div class="conteneur pt-2 pb-8 sm:pt-8 sm:pb-14">
      <?php partiel('partials/retour', ['url' => url('/actualites')]) ?>
      <div class="grid items-center gap-8 <?= $images ? 'lg:grid-cols-[minmax(0,6fr)_minmax(0,6fr)] lg:gap-12' : '' ?>">
        <div class="<?= $images ? '' : 'max-w-3xl' ?>">
          <p class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm">
            <?php if ($a['categorie_libelle'] !== ''): ?><span class="etiquette rounded-full <?= fond($a['couleur']) ?>"><?= e($a['categorie_libelle']) ?></span><?php endif ?>
            <span><?= e(implode(' · ', $meta)) ?></span>
          </p>
          <h1 class="mt-4 text-[2rem] leading-[1.08] font-bold sm:text-5xl lg:text-[3.2rem]"><?= e($a['titre']) ?></h1>
          <?php if ($a['chapeau']): ?><p class="mt-5 text-lg leading-relaxed text-encre sm:text-[21px]"><?= e($a['chapeau']) ?></p><?php endif ?>
          <div class="mt-6 flex items-center gap-3">
            <span class="grid size-11 place-items-center rounded-full bg-vert text-xs font-bold text-white" aria-hidden="true">YC</span>
            <p class="text-sm leading-tight"><strong class="block text-[15px] text-encre">L'équipe YouthConnekt Mali</strong>Communication du chapitre</p>
          </div>
        </div>

        <?php if ($images): ?>
        <div class="carrousel relative aspect-[4/3] overflow-hidden rounded-3xl bg-vert" data-carrousel aria-roledescription="carrousel" aria-label="Images de l'actualité">
          <?php foreach ($images as $k => $img): ?>
          <figure class="carrousel-vue <?= $k === 0 ? 'actif' : '' ?>" aria-hidden="<?= $k === 0 ? 'false' : 'true' ?>">
            <img src="<?= e($img['src']) ?>" alt="<?= e($img['legende']) ?>" class="h-full w-full object-cover" <?= $k === 0 ? 'fetchpriority="high"' : 'loading="lazy"' ?>>
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
        <?php endif ?>
      </div>
    </div>
  </header>

  <div class="conteneur grid gap-10 py-10 sm:py-14 lg:grid-cols-[minmax(0,1fr)_200px] lg:gap-14">
    <div class="prose-ycm prose-article mx-auto w-full max-w-[46rem] lg:mx-0 lg:justify-self-center">
      <?= corps_article($a['contenu']) ?>
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

<?php if (count($images) > 1): ?>
<section class="border-t border-ligne py-12 sm:py-16" aria-labelledby="titre-phototheque" data-phototheque>
  <div class="conteneur">
    <div class="flex items-end justify-between gap-4">
      <div>
        <p class="surtitre surtitre-trait">Photothèque</p>
        <h2 id="titre-phototheque" class="titre-section">En images</h2>
      </div>
      <p class="text-sm"><?= count($images) ?> photos</p>
    </div>
    <ul class="mt-7 grid grid-cols-2 gap-2.5 sm:gap-4 <?= count($images) === 2 ? '' : (count($images) === 3 ? 'sm:grid-cols-3' : 'sm:grid-cols-3 lg:grid-cols-4') ?>">
      <?php foreach ($images as $k => $img): ?>
      <li class="<?= $k === 0 && count($images) > 4 ? 'col-span-2 row-span-2' : '' ?>">
        <button type="button" class="group relative block aspect-square w-full overflow-hidden rounded-2xl bg-gris" data-agrandir="<?= $k ?>" aria-label="Agrandir la photo <?= $k + 1 ?>">
          <img src="<?= e($img['src']) ?>" alt="<?= e($img['legende']) ?>" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy">
        </button>
      </li>
      <?php endforeach ?>
    </ul>
  </div>
  <dialog class="visionneuse" aria-label="Photo agrandie">
    <img src="" alt="">
    <p class="visionneuse-legende"></p>
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
    <ul class="masquer-defilement -mx-4 mt-7 flex snap-x snap-mandatory gap-4 overflow-x-auto px-4 pb-2 sm:mx-0 sm:grid sm:grid-cols-2 sm:gap-5 sm:overflow-visible sm:px-0 lg:grid-cols-3">
      <?php foreach ($autres as $autre): ?><li class="w-[75%] max-w-72 shrink-0 snap-start sm:w-auto sm:max-w-none"><?php partiel('cartes/actualite', ['a' => $autre, 'mode' => 'lire']) ?></li><?php endforeach ?>
    </ul>
  </div>
</section>
<?php endif ?>
