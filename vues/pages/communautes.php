<?php
/** Page Communauté : en-tête illustré, recherche et tri par type (types gérés dans l'administration), cartes des communautés */
$nom_type = $type_actif ? ($types[$type_actif]['nom'] ?? '') : '';
$connecte = est_connecte();
?>
<section class="relative isolate overflow-hidden bg-vert text-white">
  <?php partiel('partials/reseau_deco') ?>
  <div class="conteneur relative flex items-center gap-4 pt-5 pb-5 sm:gap-10 sm:pt-8 sm:pb-8">
    <div class="anim-entree min-w-0 flex-1">
      <h1 class="text-[1.65rem] leading-[1.1] font-bold text-white sm:text-4xl lg:text-[3.1rem]">Découvrez nos différentes communautés</h1>
      <p class="mt-2.5 max-w-xl text-sm text-white/90 sm:mt-4 sm:text-lg">Des groupes de jeunes qui partagent un même domaine ou un même parcours. Rejoignez ceux qui vous ressemblent.</p>
    </div>
    <div class="relative aspect-[5/4] w-[38%] shrink-0 sm:w-[34%] lg:w-[30%] lg:max-w-sm" aria-hidden="true">
      <svg class="absolute inset-0 h-full w-full" viewBox="0 0 500 400"><g stroke="rgb(255 255 255 / .35)" stroke-width="2" fill="none"><path d="M90 80 L250 185 L430 70"/><path d="M90 80 L80 290 L250 185 L450 160"/><path d="M250 185 L260 370 L410 290"/><path d="M430 70 L450 160 L410 290"/></g></svg>
      <div class="absolute top-[46%] left-1/2 aspect-square w-[32%] -translate-x-1/2 -translate-y-1/2 overflow-hidden rounded-full border-[3px] border-orange shadow-2xl sm:border-4"><img src="<?= asset('img/communaute/cercle-centre.jpg') ?>" alt="" class="h-full w-full object-cover"></div>
      <div class="anim-flotte absolute top-[8%] left-[10%] aspect-square w-[19%] overflow-hidden rounded-full border-2 border-turquoise sm:border-4"><img src="<?= asset('img/communaute/cercle-1.jpg') ?>" alt="" class="h-full w-full object-cover"></div>
      <div class="anim-flotte-2 absolute top-[5%] right-[6%] aspect-square w-[19%] overflow-hidden rounded-full border-2 border-white sm:border-4"><img src="<?= asset('img/communaute/cercle-2.jpg') ?>" alt="" class="h-full w-full object-cover"></div>
      <div class="anim-flotte-2 absolute bottom-[14%] left-[6%] aspect-square w-[18%] overflow-hidden rounded-full border-2 border-white sm:border-4"><img src="<?= asset('img/communaute/cercle-4.jpg') ?>" alt="" class="h-full w-full object-cover"></div>
      <div class="anim-flotte absolute right-[8%] bottom-[14%] aspect-square w-[19%] overflow-hidden rounded-full border-2 border-turquoise sm:border-4"><img src="<?= asset('img/communaute/cercle-5.jpg') ?>" alt="" class="h-full w-full object-cover"></div>
      <div class="anim-flotte-2 absolute bottom-[0%] left-[44%] aspect-square w-[14%] overflow-hidden rounded-full border-2 border-orange sm:border-4"><img src="<?= asset('img/communaute/cercle-6.jpg') ?>" alt="" class="h-full w-full object-cover"></div>
    </div>
  </div>
</section>

<?php /* Recherche et type : restent visibles en haut de l'écran pendant qu'on fait défiler */ ?>
<div class="sticky top-16 z-30 border-b border-ligne bg-white/95 shadow-sm backdrop-blur">
  <form method="get" action="<?= url('/communaute') ?>" class="conteneur flex gap-2 py-3 sm:gap-3 sm:py-4" role="search">
    <?php if ($type_actif): ?><input type="hidden" name="type" value="<?= e($type_actif) ?>"><?php endif ?>
    <label for="recherche" class="sr-only">Rechercher une communauté</label>
    <div class="relative min-w-0 flex-1 sm:max-w-xl">
      <input id="recherche" name="q" type="search" value="<?= e($recherche) ?>" placeholder="Rechercher…" maxlength="100"
             class="min-h-12 w-full rounded-full border-2 border-vert/40 bg-gris pr-13 pl-5 text-[15px] text-encre shadow-inner placeholder:text-texte focus:border-vert focus:bg-white focus:ring-[3px] focus:ring-orange/35 focus:outline-none sm:text-base">
      <button type="submit" class="absolute top-1/2 right-1.5 grid size-9.5 -translate-y-1/2 place-items-center rounded-full bg-orange text-white transition-colors hover:bg-vert" aria-label="Rechercher">
        <svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
      </button>
    </div>
    <?php if ($nombres): ?>
    <details class="choix-type relative shrink-0" data-choix-type>
      <summary class="flex min-h-12 cursor-pointer list-none items-center gap-2 rounded-full border-2 px-4 text-sm font-bold transition-colors [&::-webkit-details-marker]:hidden <?= $type_actif ? 'border-orange bg-orange text-white' : 'border-vert bg-white text-vert hover:bg-vert-clair' ?>">
        <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M4 6h16M7 12h10M10 18h4"/></svg>
        <span class="max-w-[9rem] truncate sm:max-w-none"><?= $type_actif ? e($nom_type) : 'Catégorie' ?></span>
        <svg class="choix-fleche size-4 shrink-0 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
      </summary>
      <ul class="absolute right-0 z-40 mt-2 max-h-[60vh] w-64 overflow-y-auto rounded-2xl border border-ligne bg-white p-1.5 shadow-2xl shadow-vert/15">
        <li><a href="<?= url('/communaute', ['q' => $recherche]) ?>" class="flex min-h-11 items-center justify-between gap-3 rounded-xl px-3 text-[15px] font-bold <?= !$type_actif ? 'bg-orange text-white' : 'text-encre hover:bg-gris' ?>">Toutes les communautés <span class="text-sm opacity-75"><?= $total ?></span></a></li>
        <?php foreach ($types as $slug_type => $t): if (empty($nombres[$slug_type])) continue; $actif = $type_actif === $slug_type; ?>
        <li><a href="<?= url('/communaute', ['type' => $slug_type, 'q' => $recherche]) ?>" class="flex min-h-11 items-center justify-between gap-3 rounded-xl px-3 text-[15px] font-bold <?= $actif ? 'bg-orange text-white' : 'text-encre hover:bg-gris' ?>">
          <span class="flex items-center gap-2.5"><span class="size-2.5 shrink-0 rounded-full <?= $actif ? 'bg-white' : fond($t['couleur']) ?>" aria-hidden="true"></span><?= e($t['nom']) ?></span>
          <span class="text-sm opacity-75"><?= $nombres[$slug_type] ?></span>
        </a></li>
        <?php endforeach ?>
      </ul>
    </details>
    <?php endif ?>
  </form>
</div>

<section class="conteneur pt-4 pb-10 sm:pt-6 sm:pb-16" aria-label="Les communautés">
  <?php if ($recherche !== '' || $type_actif): ?><p class="mb-3 text-sm sm:mb-5">Résultats<?= $type_actif ? ' · <strong class="text-vert">' . e($nom_type) . '</strong>' : '' ?><?= $recherche !== '' ? ' pour « <strong class="text-vert">' . e($recherche) . '</strong> »' : '' ?> <a href="<?= url('/communaute') ?>" class="btn-discret ml-1">Tout afficher</a></p><?php endif ?>
  <?php if ($communautes): ?>
  <ul class="grid grid-cols-2 gap-2.5 sm:gap-5 lg:grid-cols-3">
    <?php foreach ($communautes as $k => $c): ?>
    <li>
      <a href="<?= e($c['url']) ?>" class="carte group flex h-full flex-col transition-shadow hover:shadow-lg">
        <span class="relative block aspect-[4/3] sm:aspect-[16/9]">
          <?php if ($c['image']): ?><img src="<?= e(media($c['image'])) ?>" alt="" class="absolute inset-0 h-full w-full object-cover" loading="lazy">
          <?php else: ?><img src="<?= asset('img/communaute/carte-' . ($k % 6 + 1) . '.jpg') ?>" alt="" class="absolute inset-0 h-full w-full object-cover" loading="lazy"><?php endif ?>
          <span class="absolute inset-0 bg-gradient-to-t from-vert/85 via-vert/15 to-transparent"></span>
          <span class="absolute bottom-2 left-2.5 leading-none text-white sm:bottom-3 sm:left-4">
            <?php if ($c['nb_membres']): ?><span class="text-lg font-bold sm:text-2xl"><?= $c['nb_membres'] ?></span> <span class="text-[11px] sm:text-sm">membre<?= pluriel($c['nb_membres']) ?></span>
            <?php else: ?><span class="text-[11px] font-bold sm:text-sm">Nouvelle communauté</span><?php endif ?>
          </span>
          <?php if ($c['statut_membre'] === 'acceptee'): ?><span class="etiquette absolute top-2 left-2 rounded-full bg-white text-[10px] text-vert sm:top-3 sm:left-3 sm:text-xs">Membre</span>
          <?php elseif ($c['statut_membre'] === 'en_attente'): ?><span class="etiquette absolute top-2 left-2 rounded-full bg-orange text-[10px] text-white sm:top-3 sm:left-3 sm:text-xs">Demande en cours</span><?php endif ?>
        </span>
        <span class="flex flex-1 flex-col p-3 sm:p-5">
          <?php if ($c['type_libelle'] !== ''): ?><span class="mb-1 text-[10px] font-bold tracking-[0.1em] uppercase sm:text-xs <?= texte_couleur($c['couleur']) ?>"><?= e($c['type_libelle']) ?></span><?php endif ?>
          <span class="text-[15px] leading-tight font-bold text-vert group-hover:text-orange sm:text-xl"><?= e($c['nom']) ?></span>
          <span class="mt-1 line-clamp-2 text-xs leading-snug text-texte sm:text-sm"><?= e($c['public_vise']) ?></span>
          <span class="mt-auto block pt-3 sm:pt-4"><?php partiel('partials/bouton_carte', ['texte' => 'Découvrir']) ?></span>
        </span>
      </a>
    </li>
    <?php endforeach ?>
  </ul>
  <?php elseif ($recherche !== '' || $type_actif): partiel('partials/vide', ['message' => 'Aucune communauté ne correspond à votre recherche.']);
  else: partiel('partials/vide', ['message' => 'Les communautés seront bientôt ouvertes.', 'lien_admin' => url('/gestion/communautes/ajouter')]); endif ?>
</section>

<section class="bg-gris py-14 sm:py-20" aria-labelledby="titre-mode-emploi">
  <div class="conteneur">
    <div class="flex flex-wrap items-end justify-between gap-5">
      <div>
        <p class="surtitre surtitre-trait">Mode d'emploi</p>
        <h2 id="titre-mode-emploi" class="titre-section">Comment intégrer une communauté ?</h2>
      </div>
      <?php if (!$connecte): ?><a href="<?= url('/compte/inscription') ?>" class="btn btn-vert hidden sm:inline-flex">Rejoindre le chapitre</a><?php endif ?>
    </div>
    <?php $etapes = [
        ['orange', 'text-white', 'Rejoignez le chapitre', 'Créez votre compte : vous devenez membre du chapitre YouthConnekt Mali.'],
        ['turquoise', 'text-encre', 'Choisissez une communauté', 'Parcourez les communautés et repérez celles qui vous ressemblent.'],
        ['bleu', 'text-white', "Demandez à l'intégrer", 'Depuis votre compte, envoyez votre demande en un clic.'],
        ['vert', 'text-white', 'Suivez votre demande', 'Retrouvez vos communautés et vos demandes dans votre espace membre.'],
    ]; ?>
    <ol class="mt-8 grid gap-3.5 sm:grid-cols-2 sm:gap-[18px] lg:grid-cols-4">
      <?php foreach ($etapes as $k => [$couleur, $texte_pastille, $t, $texte]): ?>
      <li class="flex gap-4 rounded-2xl border-t-[5px] border-<?= $couleur ?> bg-white p-5 sm:flex-col sm:gap-2.5 sm:p-6">
        <span class="grid size-10 shrink-0 place-items-center rounded-full bg-<?= $couleur ?> text-lg font-bold <?= $texte_pastille ?> sm:size-[46px] sm:text-xl" aria-hidden="true"><?= $k + 1 ?></span>
        <span class="flex flex-col gap-1.5"><strong class="text-[17px] leading-snug text-vert sm:text-[19px]"><?= e($t) ?></strong><span class="text-sm leading-relaxed text-texte sm:text-[15px]"><?= e($texte) ?></span></span>
      </li>
      <?php endforeach ?>
    </ol>
    <p class="mt-6 text-[15px] text-texte"><strong class="text-encre">Créer son compte, c'est rejoindre le chapitre.</strong> L'intégration à une communauté se demande ensuite, communauté par communauté.</p>
    <?php if (!$connecte): ?><a href="<?= url('/compte/inscription') ?>" class="btn btn-vert mt-6 w-full sm:hidden">Rejoindre le chapitre</a><?php endif ?>
  </div>
</section>

