<?php
/** Résultats de la recherche sur tout le site, regroupés par rubrique */
$loupe = '<svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>';
?>
<section class="relative isolate overflow-hidden bg-vert text-white">
  <?php partiel('partials/reseau_deco') ?>
  <div class="conteneur anim-entree relative pt-4 pb-6 sm:pt-8 sm:pb-8">
    <h1 class="text-[1.75rem] leading-none font-bold text-white sm:text-5xl">Rechercher</h1>
    <form method="get" action="<?= url('/recherche') ?>" class="mt-4 max-w-2xl" role="search">
      <label for="q-page" class="sr-only">Rechercher sur le site</label>
      <div class="relative">
        <input id="q-page" name="q" type="search" value="<?= e($q) ?>" placeholder="Une formation, une initiative, une communauté…" maxlength="100" autocomplete="off"
               class="min-h-13 w-full rounded-full border-0 bg-white pr-14 pl-5 text-base text-encre placeholder:text-texte/70 focus:ring-[3px] focus:ring-orange/60 focus:outline-none"<?= $q === '' ? ' autofocus' : '' ?>>
        <button type="submit" class="absolute top-1/2 right-1.5 grid size-10 -translate-y-1/2 place-items-center rounded-full bg-orange text-white transition-colors hover:bg-vert" aria-label="Rechercher"><?= $loupe ?></button>
      </div>
    </form>
    <?php if ($q !== ''): ?><p class="mt-3 text-sm text-white/90"><?= $total ? $total . ' résultat' . pluriel($total) . ' pour « ' . e($q) . ' »' : 'Aucun résultat pour « ' . e($q) . ' »' ?></p><?php endif ?>
  </div>
</section>

<section class="conteneur pt-6 pb-14 sm:pt-10 sm:pb-20">
  <?php if ($q === ''): ?>
  <p class="text-base">Tapez un ou plusieurs mots : le titre, un lieu, un organisme, un domaine… Les accents et les majuscules n'ont pas d'importance.</p>
  <?php elseif (!$groupes): ?>
  <div class="max-w-xl">
    <p class="text-lg font-bold text-vert">Rien ne correspond pour l'instant</p>
    <p class="mt-2">Essayez un mot plus court ou plus général (par exemple « formation » au lieu de « formation en marketing digital avancé »), ou parcourez les rubriques :</p>
    <div class="mt-4 flex flex-wrap gap-2">
      <a href="<?= url('/opportunites') ?>" class="btn btn-contour min-h-11">Opportunités</a>
      <a href="<?= url('/initiatives') ?>" class="btn btn-contour min-h-11">Initiatives</a>
      <a href="<?= url('/actualites') ?>" class="btn btn-contour min-h-11">Actualités</a>
      <a href="<?= url('/communaute') ?>" class="btn btn-contour min-h-11">Communautés</a>
    </div>
  </div>
  <?php else: ?>
  <div class="space-y-10">
    <?php foreach ($groupes as [$titre, $tout, $elements]): ?>
    <section aria-label="<?= e($titre) ?>">
      <div class="flex items-center justify-between gap-3 border-b border-ligne pb-2">
        <h2 class="text-xl font-bold sm:text-2xl"><?= e($titre) ?> <span class="text-base font-normal text-texte">(<?= count($elements) ?>)</span></h2>
      </div>
      <ul class="mt-3 grid gap-2.5 md:grid-cols-2">
        <?php foreach ($elements as $x): ?>
        <li>
          <a href="<?= e($x['url']) ?>" class="group flex min-h-20 items-center gap-3.5 rounded-2xl border border-ligne bg-white p-3 transition-[border-color,box-shadow] hover:border-orange hover:shadow-md sm:p-3.5">
            <?php if ($x['image']): ?><img src="<?= e(media($x['image'])) ?>" alt="" class="photo-attente size-16 shrink-0 rounded-xl object-cover sm:size-18" loading="lazy">
            <?php else: ?><span class="grid size-16 shrink-0 place-items-center rounded-xl bg-vert-clair text-vert sm:size-18" aria-hidden="true"><svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="6" cy="7" r="2.5"/><circle cx="18" cy="6" r="2.5"/><circle cx="13" cy="18" r="2.5"/><path d="M8.3 7.6 15.6 6.4M7.4 9.2l4.3 6.9M17 8.4l-2.9 7.5"/></svg></span><?php endif ?>
            <span class="min-w-0">
              <span class="line-clamp-2 font-bold leading-snug text-vert group-hover:text-orange"><?= surligner($x['titre'], $q) ?></span>
              <?php if ($x['detail'] !== ''): ?><span class="mt-0.5 block text-xs font-bold text-texte"><?= e($x['detail']) ?></span><?php endif ?>
              <?php if (($x['texte'] ?? '') !== ''): ?><span class="mt-0.5 line-clamp-1 block text-sm"><?= surligner(couper((string) $x['texte'], 140), $q) ?></span><?php endif ?>
            </span>
          </a>
        </li>
        <?php endforeach ?>
      </ul>
    </section>
    <?php endforeach ?>
  </div>
  <?php endif ?>
</section>
