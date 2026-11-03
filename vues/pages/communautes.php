<?php $connecte = est_connecte(); ?>
<section class="relative isolate overflow-hidden bg-vert text-white">
  <?php partiel('partials/reseau_deco') ?>
  <div class="conteneur relative grid items-center gap-10 py-12 sm:py-16 lg:grid-cols-2">
    <div class="anim-entree">
      <h1 class="text-5xl leading-none font-bold text-white sm:text-7xl">Communauté</h1>
      <p class="mt-5 max-w-md text-base text-white/90 sm:text-lg">Chaque membre rejoint les communautés qui lui ressemblent : on s'y retrouve entre passionnés, on y partage, on y avance ensemble.</p>
      <?php if (!$connecte): ?>
      <div class="mt-7 flex flex-wrap gap-3">
        <a href="<?= url('/compte/inscription') ?>" class="btn btn-orange">Rejoindre la communauté</a>
        <a href="<?= url('/compte/connexion') ?>" class="btn btn-blanc">Se connecter</a>
      </div>
      <?php endif ?>
    </div>
    <div class="relative mx-auto aspect-[5/4] w-full max-w-md" aria-hidden="true">
      <svg class="absolute inset-0 h-full w-full" viewBox="0 0 500 400"><g stroke="rgb(255 255 255 / .35)" stroke-width="1.3" fill="none"><path d="M90 80 L250 185 L430 70"/><path d="M90 80 L80 290 L250 185 L450 160"/><path d="M250 185 L260 370 L410 290"/><path d="M430 70 L450 160 L410 290"/></g></svg>
      <div class="absolute top-[46%] left-1/2 aspect-square w-[30%] -translate-x-1/2 -translate-y-1/2 overflow-hidden rounded-full border-4 border-orange shadow-2xl"><img src="<?= asset('img/communaute/cercle-centre.jpg') ?>" alt="" class="h-full w-full object-cover"></div>
      <div class="anim-flotte absolute top-[8%] left-[10%] aspect-square w-[17%] overflow-hidden rounded-full border-4 border-turquoise"><img src="<?= asset('img/communaute/cercle-1.jpg') ?>" alt="" class="h-full w-full object-cover"></div>
      <div class="anim-flotte-2 absolute top-[5%] right-[6%] aspect-square w-[17%] overflow-hidden rounded-full border-4 border-white"><img src="<?= asset('img/communaute/cercle-2.jpg') ?>" alt="" class="h-full w-full object-cover"></div>
      <div class="anim-flotte absolute top-[32%] right-[2%] aspect-square w-[11%] overflow-hidden rounded-full border-4 border-bleu"><img src="<?= asset('img/communaute/cercle-3.jpg') ?>" alt="" class="h-full w-full object-cover"></div>
      <div class="anim-flotte-2 absolute bottom-[18%] left-[8%] aspect-square w-[16%] overflow-hidden rounded-full border-4 border-white"><img src="<?= asset('img/communaute/cercle-4.jpg') ?>" alt="" class="h-full w-full object-cover"></div>
      <div class="anim-flotte absolute right-[10%] bottom-[16%] aspect-square w-[17%] overflow-hidden rounded-full border-4 border-turquoise"><img src="<?= asset('img/communaute/cercle-5.jpg') ?>" alt="" class="h-full w-full object-cover"></div>
      <div class="anim-flotte-2 absolute bottom-[2%] left-[44%] aspect-square w-[12%] overflow-hidden rounded-full border-4 border-orange"><img src="<?= asset('img/communaute/cercle-6.jpg') ?>" alt="" class="h-full w-full object-cover"></div>
    </div>
  </div>
  <?php partiel('partials/mention_photo', ['texte' => 'Photos provisoires · hors Mali']) ?>
</section>

<section class="conteneur py-8 sm:py-20" aria-labelledby="titre-communautes">
  <p class="surtitre surtitre-trait">Les communautés</p>
  <h2 id="titre-communautes" class="titre-section">Trouvez vos semblables</h2>
  <p class="mt-3 text-[15px] sm:text-base">Chaque communauté s'adresse à un public précis. Un membre peut en intégrer plusieurs.</p>
  <?php if ($communautes): ?>
  <ul class="mt-6 grid gap-2.5 sm:mt-8 sm:grid-cols-2 sm:gap-5 lg:grid-cols-3">
    <?php foreach ($communautes as $k => $c): ?>
    <li class="carte survol-leve survol-zoom flex sm:flex-col">
      <a href="<?= e($c['url']) ?>" class="relative block w-[104px] shrink-0 sm:aspect-[16/8] sm:w-auto" tabindex="-1" aria-hidden="true">
        <?php if ($c['image']): ?><img src="<?= e(media($c['image'])) ?>" alt="" class="absolute inset-0 h-full w-full object-cover" loading="lazy">
        <?php else: ?><img src="<?= asset('img/communaute/carte-' . ($k % 6 + 1) . '.jpg') ?>" alt="" class="absolute inset-0 h-full w-full object-cover" loading="lazy"><?php endif ?>
        <span class="absolute inset-0 bg-gradient-to-t from-vert/85 via-vert/20 to-transparent"></span>
        <?php if ($c['nb_membres']): ?><span class="absolute bottom-2 left-2 leading-none text-white sm:bottom-3 sm:left-4"><span class="block text-[17px] font-bold sm:inline sm:text-3xl"><?= $c['nb_membres'] ?></span> <span class="text-[10px] sm:text-sm">membre<?= pluriel($c['nb_membres']) ?></span></span>
        <?php else: ?><span class="absolute bottom-2 left-2 text-[11px] leading-tight font-bold text-white sm:bottom-3 sm:left-4 sm:text-sm">Nouvelle communauté</span><?php endif ?>
        <?php if ($c['statut_membre'] === 'acceptee'): ?><span class="etiquette absolute top-2 left-2 rounded-full bg-white text-[10px] text-vert sm:top-3 sm:left-3 sm:text-xs">Membre</span>
        <?php elseif ($c['statut_membre'] === 'en_attente'): ?><span class="etiquette absolute top-2 left-2 rounded-full bg-orange text-[10px] text-white sm:top-3 sm:left-3 sm:text-xs">En cours de traitement</span><?php endif ?>
      </a>
      <div class="flex min-w-0 flex-1 flex-col p-3 sm:p-5">
        <h3 class="text-base leading-tight font-bold sm:text-xl"><a href="<?= e($c['url']) ?>" class="transition-colors hover:text-orange"><?= e($c['nom']) ?></a></h3>
        <p class="mt-1.5 text-xs leading-snug text-encre sm:text-sm"><strong>Pour :</strong> <?= e($c['public_vise']) ?></p>
        <?php if ($c['description']): ?><p class="mt-1 line-clamp-2 text-xs leading-snug text-texte sm:text-sm"><?= e($c['description']) ?></p><?php endif ?>
        <div class="mt-3 sm:mt-auto sm:pt-4 [&_.btn]:min-h-10 [&_.btn]:px-3.5 [&_.btn]:text-[13px] sm:[&_.btn]:min-h-11 sm:[&_.btn]:text-sm">
          <?php partiel('partials/bouton_adhesion', ['c' => $c, 'contexte' => 'liste']) ?>
        </div>
      </div>
    </li>
    <?php endforeach ?>
  </ul>
  <?php else: partiel('partials/vide', ['message' => 'Les communautés seront bientôt ouvertes.', 'lien_admin' => url('/gestion/communautes/ajouter')]); endif ?>
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

<?php partiel('partials/bandeau_lets_connekt') ?>
