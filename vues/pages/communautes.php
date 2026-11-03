<?php
/** Page Communauté : en-tête illustré, recherche, cartes des communautés (la première rangée se voit dès l'arrivée) */
$connecte = est_connecte();
?>
<section class="relative isolate overflow-hidden bg-vert text-white">
  <?php partiel('partials/reseau_deco') ?>
  <div class="conteneur relative flex items-center gap-4 py-4 sm:gap-10 sm:py-6">
    <div class="anim-entree min-w-0 flex-1">
      <h1 class="text-[1.5rem] leading-[1.1] font-bold text-white sm:text-4xl lg:text-[2.6rem]">Découvrez nos différentes communautés</h1>
      <p class="mt-2 max-w-xl text-[13px] text-white/90 sm:mt-3 sm:text-base">Des groupes de jeunes qui partagent un même domaine ou un même parcours. Rejoignez ceux qui vous ressemblent.</p>
    </div>
    <div class="relative aspect-[5/4] w-[34%] shrink-0 sm:w-[26%] lg:w-[17rem]" aria-hidden="true">
      <svg class="absolute inset-0 h-full w-full" viewBox="0 0 500 400"><g stroke="rgb(255 255 255 / .35)" stroke-width="2" fill="none"><path d="M90 80 L250 185 L430 70"/><path d="M90 80 L80 290 L250 185 L450 160"/><path d="M250 185 L260 370 L410 290"/><path d="M430 70 L450 160 L410 290"/></g></svg>
      <div class="absolute top-[46%] left-1/2 aspect-square w-[32%] -translate-x-1/2 -translate-y-1/2 overflow-hidden rounded-full border-[3px] border-orange shadow-2xl sm:border-4"><img src="<?= asset('img/communaute/cercle-centre.jpg') ?>" alt="" class="h-full w-full object-cover"></div>
      <?php
      /* Bulles thématiques autour du réseau : la photo du thème si elle a été déposée dans assets/img/communaute/
         (theme-tech.jpg, theme-leadership.jpg…), sinon la photo provisoire */
      $bulles = [
          ['tech', 'Tech et numérique', 'cercle-1.jpg', 'border-turquoise', 'anim-flotte top-[8%] left-[10%] w-[19%]'],
          ['leadership', 'Leadership', 'cercle-2.jpg', 'border-white', 'anim-flotte-2 top-[5%] right-[6%] w-[19%]'],
          ['agriculture', 'Agriculture', 'cercle-4.jpg', 'border-white', 'anim-flotte-2 bottom-[14%] left-[6%] w-[18%]'],
          ['entrepreneuriat', 'Entrepreneuriat', 'cercle-5.jpg', 'border-turquoise', 'anim-flotte right-[8%] bottom-[14%] w-[19%]'],
          ['arts', 'Arts et culture', 'cercle-6.jpg', 'border-orange', 'anim-flotte-2 bottom-[0%] left-[44%] w-[14%]'],
      ];
      foreach ($bulles as [$cle, $titre_bulle, $provisoire, $bord, $place]):
          $photo = 'img/communaute/theme-' . $cle . '.jpg';
          if (!is_file(RACINE . '/assets/' . $photo)) $photo = 'img/communaute/' . $provisoire; ?>
      <div class="<?= $place ?> absolute aspect-square overflow-hidden rounded-full border-2 sm:border-4 <?= $bord ?>" title="<?= e($titre_bulle) ?>"><img src="<?= asset($photo) ?>" alt="" class="h-full w-full object-cover"></div>
      <?php endforeach ?>
    </div>
  </div>
</section>

<?php /* Recherche : reste visibles en haut de l'écran pendant qu'on fait défiler */ ?>
<div class="sticky top-16 z-30 border-b border-ligne bg-white/95 shadow-sm backdrop-blur">
  <form method="get" action="<?= url('/communaute') ?>" class="conteneur flex gap-2 py-2.5 sm:gap-3 sm:py-3" role="search">
    <label for="recherche" class="sr-only">Rechercher une communauté</label>
    <div class="relative min-w-0 flex-1 sm:max-w-xl">
      <input id="recherche" name="q" type="search" value="<?= e($recherche) ?>" placeholder="Rechercher…" maxlength="100"
             class="min-h-12 w-full rounded-full border-2 border-vert/40 bg-gris pr-13 pl-5 text-[15px] text-encre shadow-inner placeholder:text-texte focus:border-vert focus:bg-white focus:ring-[3px] focus:ring-orange/35 focus:outline-none sm:text-base">
      <button type="submit" class="absolute top-1/2 right-1.5 grid size-9.5 -translate-y-1/2 place-items-center rounded-full bg-orange text-white transition-colors hover:bg-vert" aria-label="Rechercher">
        <svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
      </button>
    </div>
  </form>
</div>

<section class="conteneur pt-3 pb-10 sm:pt-5 sm:pb-16" aria-label="Les communautés">
  <?php if ($recherche !== ''): ?><p class="mb-3 text-sm sm:mb-5">Résultats pour « <strong class="text-vert"><?= e($recherche) ?></strong> » <a href="<?= url('/communaute') ?>" class="btn-discret ml-1">Tout afficher</a></p><?php endif ?>
  <?php if ($communautes): ?>
  <ul class="grid grid-cols-2 gap-2.5 sm:gap-5 lg:grid-cols-3">
    <?php foreach ($communautes as $k => $c): ?>
    <li>
      <a href="<?= e($c['url']) ?>" class="carte group flex h-full flex-col transition-shadow hover:shadow-lg">
        <span class="relative block aspect-[4/3] sm:aspect-[16/9] lg:aspect-[2/1]">
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
          <span class="text-[15px] leading-tight font-bold text-vert group-hover:text-orange sm:text-xl"><?= e($c['nom']) ?></span>
          <span class="mt-1 line-clamp-2 text-xs leading-snug text-texte sm:text-sm"><?= e($c['public_vise']) ?></span>
          <span class="mt-auto block pt-3 sm:pt-4"><?php partiel('partials/bouton_carte', ['texte' => 'Découvrir']) ?></span>
        </span>
      </a>
    </li>
    <?php endforeach ?>
  </ul>
  <?php elseif ($recherche !== ''): partiel('partials/vide', ['message' => 'Aucune communauté ne correspond à votre recherche.']);
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

