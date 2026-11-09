<?php
$a_infos = $i['date_libelle'] || $i['lieu'] || $i['participants'] || $i['format'];
$objectifs = lignes_texte($i['objectifs']);
$programme = lignes_colonnes($i['programme'], 3);
$chiffres_i = lignes_colonnes($i['chiffres'], 2);
?>
<section class="conteneur grid items-center gap-8 pt-2 pb-10 sm:gap-10 sm:py-14 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)] lg:gap-14">
  <div>
    <?php partiel('partials/retour', ['url' => url('/initiatives')]) ?>
    <div class="flex flex-wrap gap-2">
      <span class="etiquette rounded-full bg-gris text-vert"><?= e(ETATS_INITIATIVE[$i['etat']] ?? '') ?></span>
    </div>
    <h1 class="mt-3 text-[1.875rem] leading-[1.08] font-bold sm:text-5xl xl:text-[3.5rem]"><?= e($i['titre']) ?></h1>
    <p class="mt-4 text-base leading-relaxed sm:text-lg"><?= e($i['resume']) ?></p>
    <?php if ($ma_participation): ?>
    <div class="mt-5 flex flex-wrap items-center gap-2">
      <span class="inline-flex min-h-11 items-center rounded-full bg-vert-clair px-5 text-sm font-bold text-vert"><?= $ma_participation['statut'] === 'inscrit' ? 'Vous êtes inscrit' : 'Votre candidature' ?> comme « <?= e($ma_participation['role_libelle']) ?> »<?= $ma_participation['statut'] !== 'inscrit' ? ' · ' . e(STATUTS_PARTICIPATION[$ma_participation['statut']] ?? '') : '' ?></span>
      <?php if (billet_valable($ma_participation)): ?><a href="<?= url('/compte/mon-espace/participations/' . $ma_participation['id'] . '/billet') ?>" class="btn btn-orange min-h-11 px-5">Voir mon billet</a>
      <?php else: ?><a href="<?= url('/compte/mon-espace/participations') ?>" class="btn-lien min-h-11">Mes participations</a><?php endif ?>
    </div>
    <?php elseif ($ouvertes): ?>
    <div class="mt-5 flex flex-wrap items-center gap-x-4 gap-y-2">
      <a href="#prendre-part" class="btn btn-orange min-h-12 px-6">Prendre part à cette initiative</a>
      <?php if ($i['participations_fin']): ?><span class="text-sm text-texte">Jusqu'au <?= e(date_longue($i['participations_fin'])) ?></span><?php endif ?>
    </div>
    <?php elseif ($roles && $i['participations_ouvertes']): ?>
    <p class="mt-5 text-sm text-texte">Les participations à cette initiative sont closes.</p>
    <?php endif ?>
    <?php if ($a_infos) partiel('pages/infos_initiative', ['i' => $i, 'classes' => 'mt-6 hidden lg:grid']) ?>
  </div>

  <div class="relative mx-auto w-full max-w-xl pr-4 pb-8 sm:pr-6 sm:pb-10">
    <div class="absolute top-6 right-0 bottom-10 left-1/3 rounded-3xl bg-orange" aria-hidden="true"></div>
    <div class="devoile survol-zoom relative aspect-[4/3] overflow-hidden rounded-3xl shadow-xl">
      <?php partiel('partials/photo', ['image' => $i['image'], 'eager' => true]) ?>
    </div>
    <?php if ($photos): $p2 = $photos[0]; ?>
    <div class="survol-penche absolute right-0 bottom-0 aspect-[4/3] w-[42%] rotate-2 overflow-hidden rounded-2xl border-4 border-white shadow-xl">
      <img src="<?= e(media($p2['image'])) ?>" alt="<?= e($p2['legende']) ?>" class="h-full w-full object-cover" loading="lazy">
    </div>
    <?php endif ?>
  </div>
  <?php if ($a_infos) partiel('pages/infos_initiative', ['i' => $i, 'classes' => '-mt-4 grid lg:hidden']) ?>
</section>

<?php if ($ouvertes && !$ma_participation): /* Prendre part : les rôles proposés par l'équipe */ ?>
<section id="prendre-part" class="scroll-mt-24 bg-white pb-12 sm:pb-16" aria-labelledby="titre-prendre-part">
  <div class="conteneur">
    <div class="rounded-3xl border-2 border-orange/30 bg-orange-clair/40 p-5 sm:p-8">
      <h2 id="titre-prendre-part" class="text-2xl font-bold sm:text-3xl">Prendre part</h2>
      <p class="mt-2 max-w-2xl text-[15px] leading-relaxed text-encre sm:text-base"><?= trim((string) $i['participations_intro']) !== '' ? nl2br(e($i['participations_intro'])) : 'Choisissez comment vous souhaitez participer.' ?></p>
      <ul class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        <?php foreach ($roles as $r): ?>
        <li class="flex flex-col rounded-2xl bg-white p-4 shadow-sm sm:p-5 <?= $r['complet'] ? 'opacity-60' : '' ?>">
          <span class="text-lg font-bold text-vert"><?= e($r['nom']) ?></span>
          <?php if ($r['description'] !== ''): ?><span class="mt-1 text-sm leading-snug"><?= e($r['description']) ?></span><?php endif ?>
          <span class="mt-3 mb-3 text-xs font-bold <?= $r['complet'] ? 'text-texte' : 'text-orange-fonce' ?>"><?= $r['complet'] ? 'Complet' : ($r['mode'] === 'inscription' ? 'Inscription directe, billet aussitôt' : "Sur candidature : l'équipe choisit") . ($r['restantes'] !== null ? ' · ' . $r['restantes'] . ' place' . pluriel((int) $r['restantes']) . ' restante' . pluriel((int) $r['restantes']) : '') ?></span>
          <?php if (!$r['complet']): ?><a href="<?= url('/initiatives/' . $i['slug'] . '/participer', ['role' => $r['id']]) ?>" class="btn btn-vert mt-auto min-h-11 w-full"><?= $r['mode'] === 'inscription' ? "Je m'inscris" : 'Me proposer' ?></a><?php endif ?>
        </li>
        <?php endforeach ?>
      </ul>
      <?php if ($i['participations_fin']): ?><p class="mt-4 text-sm text-texte">Ouvert jusqu'au <?= e(date_longue($i['participations_fin'])) ?>.</p><?php endif ?>
    </div>
  </div>
</section>
<?php endif ?>

<section class="bg-gris py-14 sm:py-16">
  <div class="conteneur grid gap-10 lg:grid-cols-[minmax(0,7fr)_minmax(0,5fr)] lg:gap-14">
    <div class="order-2 lg:order-1">
      <h2 class="text-2xl font-bold sm:text-3xl">L'initiative</h2>
      <div class="mt-4 space-y-4 text-base leading-relaxed text-encre"><?= paragraphes($i['contenu']) ?></div>
      <?php if ($objectifs): ?>
      <h3 class="mt-9 text-xl font-bold">Objectifs</h3>
      <ul class="mt-4 grid grid-cols-2 gap-2.5 sm:gap-3">
        <?php foreach ($objectifs as $objectif): ?>
        <li class="flex flex-col gap-2 rounded-xl bg-white p-3.5 text-[13px] leading-snug text-encre sm:flex-row sm:gap-3 sm:p-4 sm:text-sm">
          <svg class="size-5 shrink-0 text-vert" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="12" cy="12" r="11"/><path d="m7.5 12.5 3 3 6-6" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
          <?= e($objectif) ?>
        </li>
        <?php endforeach ?>
      </ul>
      <?php endif ?>
    </div>
    <aside class="order-1 lg:order-2">
      <?php if ($chiffres_i): ?>
      <h2 class="sr-only text-xl font-bold lg:not-sr-only">En chiffres</h2>
      <ul class="grid grid-cols-3 gap-2.5 lg:mt-4 lg:grid-cols-1 lg:gap-3">
        <?php foreach ($chiffres_i as $k => [$val, $lib]): ?>
        <li class="flex flex-col gap-1 rounded-xl border-t-4 bg-white px-3 py-3 lg:flex-row lg:items-center lg:gap-5 lg:border-t-0 lg:border-l-4 lg:px-5 lg:py-4 <?= cycle(['border-orange', 'border-turquoise', 'border-bleu'], $k) ?>">
          <span class="text-2xl font-bold text-vert lg:text-4xl"><?= e($val) ?></span><span class="text-xs leading-snug text-encre lg:text-sm"><?= e($lib) ?></span>
        </li>
        <?php endforeach ?>
      </ul>
      <?php endif ?>
      <?php if (trim((string) $i['citation']) !== ''): ?>
      <figure class="mt-5 hidden rounded-2xl bg-vert p-6 text-white lg:block">
        <blockquote class="text-[15px] leading-relaxed">« <?= e($i['citation']) ?> »</blockquote>
        <?php if ($i['citation_auteur']): ?><figcaption class="mt-3 text-xs text-white/80"><?= e($i['citation_auteur']) ?></figcaption><?php endif ?>
      </figure>
      <?php endif ?>
    </aside>
  </div>
</section>

<?php if ($programme): ?>
<section class="conteneur py-14 sm:py-16" aria-labelledby="titre-programme">
  <h2 id="titre-programme" class="titre-section">Le programme</h2>
  <ol class="mt-8 grid gap-6 md:grid-cols-3 md:gap-8">
    <?php foreach ($programme as $k => [$etiquette, $t, $texte]): ?>
    <li class="relative grid grid-cols-[auto_minmax(0,1fr)] gap-x-4 md:block">
      <div class="row-span-3 flex items-center gap-3 md:row-span-1">
        <span class="grid size-11 shrink-0 place-items-center rounded-full font-bold text-white <?= cycle(['bg-orange', 'bg-turquoise', 'bg-bleu'], $k) ?>"><?= $k + 1 ?></span>
        <?php if ($k < count($programme) - 1): ?><span class="hidden h-px flex-1 border-t-2 border-dashed border-ligne md:block" aria-hidden="true"></span><?php endif ?>
      </div>
      <?php if ($etiquette): ?><p class="mt-1 text-xs font-bold tracking-[0.14em] uppercase md:mt-4"><?= e($etiquette) ?></p><?php endif ?>
      <h3 class="mt-1 text-lg font-bold"><?= e($t) ?></h3>
      <?php if ($texte): ?><p class="mt-2 text-sm leading-relaxed"><?= e($texte) ?></p><?php endif ?>
    </li>
    <?php endforeach ?>
  </ol>
</section>
<?php endif ?>

<?php if ($photos): $n = count($photos); ?>
<section class="conteneur pb-14 sm:pb-16 <?= !$programme ? 'pt-14' : '' ?>" aria-labelledby="titre-images">
  <h2 id="titre-images" class="titre-section">En images</h2>
  <ul class="mt-6 grid auto-rows-[110px] grid-cols-3 gap-2 sm:auto-rows-[180px] sm:gap-3 md:auto-rows-[240px] md:grid-cols-4">
    <?php foreach (array_slice($photos, 0, 5) as $k => $photo):
        $classe = $n === 1 ? 'col-span-3 row-span-2 md:col-span-4' : ($k === 0 ? 'row-span-2 md:col-span-2' : ($n === 2 ? 'col-span-2 row-span-2' : '')); ?>
    <li class="survol-zoom overflow-hidden rounded-xl sm:rounded-2xl <?= $classe ?>">
      <img src="<?= e(media($photo['image'])) ?>" alt="<?= e($photo['legende']) ?>" class="h-full w-full object-cover" loading="lazy">
    </li>
    <?php endforeach ?>
  </ul>
</section>
<?php endif ?>

<?php if ($i['axe_nom']): ?>
<p class="conteneur py-8 text-sm text-texte sm:py-10">Cette initiative s'inscrit dans l'axe <a href="<?= url('/a-propos') ?>#axes" class="font-bold text-vert hover:text-orange"><?= e($i['axe_nom']) ?></a> du chapitre.</p>
<?php endif ?>

<?php if ($autres): ?>
<section class="bg-gris py-14 sm:py-16" aria-labelledby="titre-autres">
  <div class="conteneur">
    <div class="flex items-end justify-between gap-4">
      <h2 id="titre-autres" class="titre-section">Autres initiatives</h2>
      <a href="<?= url('/initiatives') ?>" class="btn btn-contour hidden sm:inline-flex">Toutes les initiatives</a>
      <a href="<?= url('/initiatives') ?>" class="btn-tout-voir sm:hidden">Tout voir</a>
    </div>
    <ul class="masquer-defilement -mx-4 mt-7 flex snap-x snap-mandatory gap-4 overflow-x-auto px-4 pb-2 sm:mx-0 sm:grid sm:grid-cols-2 sm:gap-5 sm:overflow-visible sm:px-0 lg:grid-cols-3">
      <?php foreach ($autres as $autre): ?><li class="w-[75%] max-w-72 shrink-0 snap-start sm:w-auto sm:max-w-none"><?php partiel('cartes/initiative', ['i' => $autre, 'simple' => true]) ?></li><?php endforeach ?>
    </ul>
  </div>
</section>
<?php endif ?>
