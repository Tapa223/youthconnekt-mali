<?php
$a_infos = $i['date_libelle'] || $i['lieu'] || $i['participants'] || $i['format'];
$objectifs = lignes_texte($i['objectifs']);
$programme = lignes_colonnes($i['programme'], 3);
$chiffres_i = lignes_colonnes($i['chiffres'], 2);
?>
<?php partiel('partials/banniere_detail', [
    'images' => array_merge($i['image'] ? [media($i['image'])] : [], array_map(fn ($ph) => media($ph['image']), $photos)),
    'titre' => $i['titre'],
    'etiquette' => '<span class="etiquette rounded-full bg-white text-vert">' . e(ETATS_INITIATIVE[$i['etat']] ?? '') . '</span>',
    'meta' => implode(' · ', array_filter([$i['date_libelle'], $i['lieu']])),
    'retour' => url('/initiatives'),
]) ?>
<section class="conteneur grid items-center gap-5 pt-5 pb-8 sm:gap-10 sm:pt-7 sm:pb-12 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)] lg:gap-14">
  <div class="relative">
    <p class="text-[15px] leading-relaxed sm:text-lg"><?= e($i['resume']) ?></p>
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
    <?php if ($inscrits['nombre'] || ($i['date_debut'] && $i['etat'] !== 'terminee')): ?>
    <div class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-3">
      <?php if ($inscrits['nombre']): ?>
      <p class="flex items-center gap-2.5 text-sm font-bold text-vert">
        <span class="flex -space-x-2.5" aria-hidden="true">
          <?php foreach ($inscrits['visages'] as $k => $v): ?>
          <?php if ($v['photo']): ?><img src="<?= e(media($v['photo'])) ?>" alt="" class="size-8 rounded-full border-2 border-white object-cover" loading="lazy">
          <?php else: ?><span class="grid size-8 place-items-center rounded-full border-2 border-white text-[10px] font-bold text-white <?= cycle(['bg-vert', 'bg-orange', 'bg-turquoise', 'bg-bleu'], $k) ?>"><?= e($v['initiales']) ?></span><?php endif ?>
          <?php endforeach ?>
        </span>
        <span><?= $inscrits['nombre'] ?> personne<?= pluriel($inscrits['nombre']) ?> <?= $i['etat'] === 'terminee' ? 'y ont pris part' : 'déjà inscrite' . pluriel($inscrits['nombre']) ?></span>
      </p>
      <?php endif ?>
      <?php if ($i['date_debut'] && $i['etat'] !== 'terminee'): ?>
      <a href="<?= url('/initiatives/' . $i['slug'] . '/agenda') ?>" class="inline-flex min-h-10 items-center gap-2 rounded-full border-2 border-vert px-4 text-sm font-bold text-vert transition-colors hover:border-orange hover:bg-orange hover:text-white" download>
        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4M12 13v5M9.5 15.5h5"/></svg>
        Ajouter à mon agenda
      </a>
      <?php endif ?>
    </div>
    <?php endif ?>
    <?php if ($a_infos) partiel('pages/infos_initiative', ['i' => $i, 'classes' => 'mt-6 hidden lg:grid']) ?>
  </div>

  <?php /* Bloc photo moins haut sur ordinateur : le texte et la photo tiennent sous le bandeau, et la suite se devine */ ?>
  <div class="relative mx-auto w-full max-w-xl pr-3 pb-6 sm:pr-6 sm:pb-8 lg:max-w-lg lg:pb-7">
    <div class="absolute top-4 right-0 bottom-8 left-1/3 rounded-2xl bg-orange sm:top-6 sm:rounded-3xl lg:bottom-7" aria-hidden="true"></div>
    <div class="devoile survol-zoom relative aspect-[16/10] overflow-hidden rounded-2xl shadow-xl sm:aspect-[4/3] sm:rounded-3xl lg:aspect-[16/10]">
      <?php partiel('partials/photo', ['transition' => 'photo-ini-' . $i['id'], 'image' => $i['image'], 'eager' => true]) ?>
    </div>
    <?php if ($photos): $p2 = $photos[0]; ?>
    <div class="survol-penche absolute right-0 bottom-0 aspect-[4/3] w-[34%] rotate-2 overflow-hidden rounded-xl border-[3px] border-white shadow-xl sm:w-[42%] sm:rounded-2xl sm:border-4">
      <img src="<?= e(media($p2['image'])) ?>" alt="<?= e($p2['legende']) ?>" class="h-full w-full object-cover" loading="lazy">
    </div>
    <?php endif ?>
  </div>
  <?php if ($a_infos) partiel('pages/infos_initiative', ['i' => $i, 'classes' => '-mt-2 grid lg:hidden']) ?>
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
          <?php if ($r['places'] !== null): $taux = min(100, (int) round(100 * (int) $r['nb'] / max(1, (int) $r['places']))); ?>
          <span class="jauge mt-3 block h-2 overflow-hidden rounded-full bg-gris" role="img" aria-label="<?= (int) $r['nb'] ?> sur <?= (int) $r['places'] ?> places prises"><span class="block h-full rounded-full <?= $taux >= 80 ? 'bg-orange' : 'bg-turquoise' ?>" style="--taux: <?= $taux ?>%"></span></span>
          <?php endif ?>
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

<section class="bg-gris py-9 sm:py-16">
  <div class="conteneur grid gap-7 sm:gap-10 lg:grid-cols-[minmax(0,7fr)_minmax(0,5fr)] lg:gap-14">
    <div class="order-2 lg:order-1">
      <h2 class="text-[1.4rem] font-bold sm:text-3xl">L'initiative</h2>
      <div class="prose-ycm mt-3 sm:mt-4"><?= paragraphes($i['contenu']) ?></div>
      <?php if ($objectifs): ?>
      <h3 class="mt-8 text-lg font-bold sm:mt-9 sm:text-xl">Objectifs</h3>
      <ul class="mt-3 grid gap-2 sm:mt-4 sm:grid-cols-2 sm:gap-3">
        <?php foreach ($objectifs as $objectif): ?>
        <li class="flex items-start gap-2.5 rounded-lg bg-white px-3 py-2.5 text-[14px] leading-snug text-encre sm:gap-3 sm:rounded-xl sm:px-4 sm:py-3 sm:text-sm">
          <svg class="mt-px size-[18px] shrink-0 text-vert sm:size-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="12" cy="12" r="11"/><path d="m7.5 12.5 3 3 6-6" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
          <?= e($objectif) ?>
        </li>
        <?php endforeach ?>
      </ul>
      <?php endif ?>
    </div>
    <aside class="order-1 lg:order-2">
      <?php if ($chiffres_i): ?>
      <h2 class="sr-only text-xl font-bold lg:not-sr-only">En chiffres</h2>
      <ul class="grid <?= in_array(count($chiffres_i), [2, 4], true) ? 'grid-cols-2' : 'grid-cols-3' ?> gap-2 sm:gap-2.5 lg:mt-4 lg:grid-cols-1 lg:gap-3">
        <?php foreach ($chiffres_i as $k => [$val, $lib]): ?>
        <li class="flex flex-col gap-0.5 rounded-lg border-t-[3px] bg-white px-3 py-2.5 sm:rounded-xl sm:border-t-4 lg:flex-row lg:items-center lg:gap-5 lg:border-t-0 lg:border-l-4 lg:px-5 lg:py-4 <?= cycle(['border-orange', 'border-turquoise', 'border-bleu'], $k) ?>">
          <span class="text-xl font-bold text-vert sm:text-2xl lg:text-4xl"><?= e($val) ?></span><span class="text-xs leading-snug text-encre lg:text-sm"><?= e($lib) ?></span>
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

<?php if ($photos):
    // En images : une seule rangée, juste après le texte. Ordinateur : 3 photos puis « +N Voir plus » ; téléphone : 2 photos puis « +N ».
    // Chaque photo s'ouvre en grand ; la visionneuse fait défiler toutes les photos.
    $n = count($photos);
    $cols = min($n, 4);
    $cols_tel = min($n, 3);
    $plus_pc = $n > 4 ? $n - 3 : 0;
    $plus_tel = $n > 3 ? $n - 2 : 0;
?>
<section class="conteneur pt-9 sm:pt-14 <?= $programme ? '' : 'pb-9 sm:pb-14' ?>" aria-labelledby="titre-images" data-phototheque>
  <div class="flex items-end justify-between gap-4">
    <h2 id="titre-images" class="titre-section">En images</h2>
    <button type="button" class="btn-tout-voir" data-ouvrir-photo="0"><?= $n ?> photo<?= pluriel($n) ?></button>
  </div>
  <ul class="mt-4 grid <?= ['', 'grid-cols-1', 'grid-cols-2', 'grid-cols-3'][$cols_tel] ?> <?= ['', 'sm:grid-cols-1', 'sm:grid-cols-2', 'sm:grid-cols-3', 'sm:grid-cols-4'][$cols] ?> gap-2 sm:mt-6 sm:gap-3">
    <?php foreach ($photos as $k => $photo):
        $visible = ($k < 3 ? '' : 'max-sm:hidden ') . ($k < 4 ? '' : 'sm:hidden'); ?>
    <li class="group relative <?= $visible ?>">
      <button type="button" class="relative block aspect-[4/3] w-full overflow-hidden rounded-lg bg-vert sm:rounded-xl" data-agrandir="<?= $k ?>" aria-label="Agrandir la photo <?= $k + 1 ?>">
        <img src="<?= e(media($photo['image'])) ?>" alt="<?= e($photo['legende']) ?>" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy">
        <?php foreach ([[$plus_tel, 2, 'sm:hidden'], [$plus_pc, 3, 'max-sm:hidden']] as [$plus, $rang, $ecran]): if ($plus && $k === $rang): ?>
        <span class="<?= $ecran ?> absolute inset-0 grid place-items-center bg-vert/75 text-white transition-colors group-hover:bg-orange/85" aria-hidden="true">
          <span class="text-center"><span class="block text-xl font-bold sm:text-3xl">+<?= $plus ?></span><span class="block text-[11px] font-bold sm:text-sm">Voir plus</span></span>
        </span>
        <?php endif; endforeach ?>
      </button>
    </li>
    <?php endforeach ?>
  </ul>
  <dialog class="visionneuse" aria-label="Photo agrandie">
    <img src="" alt="">
    <p class="visionneuse-legende"></p>
    <button type="button" class="visionneuse-bouton top-4 right-4" data-fermer aria-label="Fermer"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg></button>
    <button type="button" class="visionneuse-bouton top-1/2 left-4 -translate-y-1/2" data-precedent aria-label="Photo précédente"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg></button>
    <button type="button" class="visionneuse-bouton top-1/2 right-4 -translate-y-1/2" data-suivant aria-label="Photo suivante"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg></button>
  </dialog>
</section>
<?php endif ?>

<?php if ($programme): ?>
<section class="conteneur py-9 sm:py-16" aria-labelledby="titre-programme">
  <h2 id="titre-programme" class="titre-section">Le programme</h2>
  <ol class="mt-5 grid gap-5 sm:mt-8 sm:gap-6 md:grid-cols-3 md:gap-8">
    <?php foreach ($programme as $k => [$etiquette, $t, $texte]): ?>
    <li class="relative grid grid-cols-[auto_minmax(0,1fr)] gap-x-4 md:block">
      <div class="row-span-3 flex items-center gap-3 md:row-span-1">
        <span class="grid size-9 shrink-0 place-items-center rounded-full text-sm font-bold text-white sm:size-11 sm:text-base <?= cycle(['bg-orange', 'bg-turquoise', 'bg-bleu'], $k) ?>"><?= $k + 1 ?></span>
        <?php if ($k < count($programme) - 1): ?><span class="hidden h-px flex-1 border-t-2 border-dashed border-ligne md:block" aria-hidden="true"></span><?php endif ?>
      </div>
      <?php if ($etiquette): ?><p class="mt-1 text-xs font-bold tracking-[0.14em] uppercase md:mt-4"><?= e($etiquette) ?></p><?php endif ?>
      <h3 class="mt-0.5 text-base font-bold sm:mt-1 sm:text-lg"><?= e($t) ?></h3>
      <?php if ($texte): ?><p class="mt-1 text-sm leading-relaxed sm:mt-2"><?= e($texte) ?></p><?php endif ?>
    </li>
    <?php endforeach ?>
  </ol>
</section>
<?php endif ?>



<?php if ($i['axe_nom']): ?>
<p class="conteneur py-6 text-sm text-texte sm:py-10">Cette initiative s'inscrit dans l'axe <a href="<?= url('/a-propos') ?>#axes" class="font-bold text-vert hover:text-orange"><?= e($i['axe_nom']) ?></a> du chapitre.</p>
<?php endif ?>

<?php if ($autres): ?>
<section class="bg-gris py-9 sm:py-16" aria-labelledby="titre-autres">
  <div class="conteneur">
    <div class="flex items-end justify-between gap-4">
      <h2 id="titre-autres" class="titre-section">Autres initiatives</h2>
      <a href="<?= url('/initiatives') ?>" class="btn btn-contour hidden sm:inline-flex">Toutes les initiatives</a>
      <a href="<?= url('/initiatives') ?>" class="btn-tout-voir sm:hidden">Tout voir</a>
    </div>
    <ul class="masquer-defilement -mx-4 mt-5 flex snap-x snap-mandatory scroll-px-4 gap-4 overflow-x-auto px-4 pb-2 sm:mx-0 sm:grid sm:grid-cols-2 sm:gap-5 sm:overflow-visible sm:px-0 lg:grid-cols-3">
      <?php foreach ($autres as $autre): ?><li class="w-[75%] max-w-72 shrink-0 snap-start sm:w-auto sm:max-w-none"><?php partiel('cartes/initiative', ['i' => $autre, 'simple' => true]) ?></li><?php endforeach ?>
    </ul>
  </div>
</section>
<?php endif ?>
