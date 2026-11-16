<?php /** Fiche publique d'une organisation du réseau. */
$infos = array_filter([TYPES_STRUCTURE[$o['type_structure']] ?? '', $o['secteur'], TAILLES_STRUCTURE[$o['taille'] ?? ''] ?? '', $o['ville'] ?: (REGIONS[$o['region']] ?? '')]); ?>
<section class="relative isolate overflow-hidden bg-vert text-white">
  <?php partiel('partials/reseau_deco', ['opacite' => 'opacity-30']) ?>
  <div class="conteneur relative flex items-start gap-3 py-6 sm:gap-4 sm:py-8">
    <?php partiel('partials/retour', ['url' => url('/organisations'), 'clair' => true, 'bord' => true, 'toujours' => true, 'classes' => 'shrink-0 xl:absolute xl:top-1/2 xl:-left-16 xl:-translate-y-1/2']) ?>
    <?php partiel('partials/logo_organisation', ['o' => $o, 'classes' => 'size-16 sm:size-20 shadow-lg']) ?>
    <div class="min-w-0">
      <h1 class="text-[1.4rem] leading-tight font-bold text-white sm:text-4xl"><?= e($o['nom_structure']) ?></h1>
      <?php if ($infos): ?><p class="mt-1 text-[13px] text-white/90 sm:text-[15px]"><?= e(implode(' · ', $infos)) ?></p><?php endif ?>
      <?php partiel('partials/badges_organisation', ['o' => $o, 'clair' => true, 'classes' => 'mt-2']) ?>
      <?php $moi = utilisateur(); if ($moi && (int) $moi['id'] === (int) $o['utilisateur_id']): ?>
      <a href="<?= url('/compte/mon-espace/fiche') ?>" class="btn btn-contour-blanc mt-3 min-h-9 px-4 text-sm">Modifier ma fiche</a>
      <?php endif ?>
    </div>
  </div>
</section>

<section class="conteneur grid gap-6 pt-6 pb-14 sm:pt-8 sm:pb-20 lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)] lg:gap-10">
  <div class="min-w-0 space-y-6">
    <?php if (trim((string) $o['presentation']) !== ''): ?>
    <div>
      <h2 class="text-lg font-bold sm:text-xl">Présentation</h2>
      <div class="prose-ycm mt-2"><?= paragraphes($o['presentation']) ?></div>
    </div>
    <?php endif ?>
    <?php if (trim((string) ($o['offre_jeunes'] ?? '')) !== ''): ?>
    <div class="rounded-2xl border-l-4 border-orange bg-orange-clair/40 p-4 sm:p-5">
      <h2 class="text-lg font-bold text-vert sm:text-xl">Ce que nous offrons aux jeunes</h2>
      <div class="prose-ycm mt-2"><?= paragraphes($o['offre_jeunes']) ?></div>
    </div>
    <?php endif ?>
    <?php if ($opportunites): ?>
    <div>
      <h2 class="text-lg font-bold sm:text-xl">Opportunités en cours</h2>
      <ul class="mt-3 grid gap-3 sm:grid-cols-2">
        <?php foreach ($opportunites as $op): ?><li><?php partiel('cartes/opportunite', ['o' => $op]) ?></li><?php endforeach ?>
      </ul>
    </div>
    <?php endif ?>
  </div>
  <aside class="min-w-0">
    <div class="rounded-2xl bg-gris p-4 sm:p-5">
      <h2 class="font-bold text-vert">Contact</h2>
      <ul class="mt-2 space-y-2 text-sm">
        <?php if ($o['site_web']): ?><li><a href="<?= e($o['site_web']) ?>" target="_blank" rel="noopener" class="font-bold break-all text-vert hover:text-orange"><?= e(preg_replace('#^https?://(www\.)?#', '', rtrim($o['site_web'], '/'))) ?></a></li><?php endif ?>
        <?php if ($o['email_contact_public']): ?><li><a href="mailto:<?= e($o['email_contact_public']) ?>" class="font-bold break-all text-vert hover:text-orange"><?= e($o['email_contact_public']) ?></a></li><?php endif ?>
        <?php foreach ($liens as $l): ?><li><a href="<?= e($l['url']) ?>" target="_blank" rel="noopener" class="font-bold text-vert hover:text-orange"><?= e($l['nom']) ?></a></li><?php endforeach ?>
        <?php if (!$o['site_web'] && !$o['email_contact_public'] && !$liens): ?><li class="text-texte">Pas encore de contact public.</li><?php endif ?>
      </ul>
      <p class="mt-3 border-t border-ligne pt-3 text-xs text-texte">Membre du réseau depuis <?= e(mois_annee($o['inscrit_le'])) ?></p>
    </div>
  </aside>
</section>
