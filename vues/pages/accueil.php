<?php
/** Accueil, dans l'ordre validé de la maquette. */
$admin = est_admin();
$ministre_publie = !empty($mot['publie']);
$nom_ministre = ($ministre_publie && $mot['nom']) ? $mot['nom'] : 'M. Abdoul Kassim Ibrahim Fomba';
$titre_ministre = ($ministre_publie && $mot['titre_officiel']) ? $mot['titre_officiel'] : "Ministre de la Jeunesse et des Sports, chargé de l'Instruction civique et de la Construction citoyenne";
$points = [
    [1170, 710, 9, '#F1592A'], [1110, 485, 7, '#04B2B1'], [945, 320, 7, '#fff'], [720, 260, 9, '#0358D9'],
    [495, 320, 7, '#F1592A'], [330, 485, 7, '#04B2B1'], [270, 710, 9, '#fff'], [330, 935, 7, '#0358D9'],
    [495, 1100, 7, '#F1592A'], [720, 1160, 9, '#04B2B1'], [945, 1100, 7, '#fff'], [1110, 935, 7, '#0358D9'],
];
?>

<?php /* 1. Premier écran */ ?>
<section class="relative isolate overflow-hidden bg-vert text-white lg:h-[calc(100svh-4rem)] lg:max-h-[940px] lg:min-h-[620px] xl:h-[calc(100svh-76px)]" aria-labelledby="titre-accueil" data-diapo-pause>
  <div class="absolute -inset-5 -z-10" data-diapo="accueil" aria-hidden="true">
    <img data-diapo-image src="<?= asset('img/accueil/premier-ecran.jpg') ?>" alt="" class="absolute inset-0 h-full w-full object-cover blur-[3.5px]">
    <img data-diapo-image src="<?= asset('img/accueil/premier-ecran-2.jpg') ?>" alt="" class="absolute inset-0 h-full w-full object-cover blur-[3.5px]" loading="lazy">
    <img data-diapo-image src="<?= asset('img/accueil/premier-ecran-3.jpg') ?>" alt="" class="absolute inset-0 h-full w-full object-cover blur-[3.5px]" loading="lazy">
  </div>
  <div class="absolute inset-0 -z-10 bg-[linear-gradient(180deg,rgb(3_94_77/.95)_0%,rgb(3_94_77/.88)_34%,rgb(3_94_77/.72)_60%,rgb(3_94_77/.86)_100%)]" aria-hidden="true"></div>

  <?php /* Cadre de la maquette (1440 × 820) mis à l'échelle de l'écran : anneau, grand cercle et bulles s'y alignent */ ?>
  <div class="pointer-events-none absolute inset-y-0 left-1/2 hidden aspect-[1440/820] -translate-x-1/2 lg:block" aria-hidden="true">
    <svg class="absolute inset-0 h-full w-full overflow-visible" viewBox="0 0 1440 820" fill="none">
      <circle cx="720" cy="710" r="450" stroke="#fff" stroke-opacity=".3" stroke-width="1.5" stroke-dasharray="6 8"/>
      <circle cx="720" cy="710" r="490" stroke="#fff" stroke-opacity=".1"/>
      <g class="anim-orbite" style="transform-origin: 720px 710px">
        <?php foreach ($points as $k => [$cx, $cy, $r, $c]): ?>
        <circle class="anim-point" cx="<?= $cx ?>" cy="<?= $cy ?>" r="<?= $r ?>" fill="<?= $c ?>" stroke="#035E4D" stroke-width="3" style="animation-delay:<?= number_format(0.6 + $k * 0.25, 2, '.', '') ?>s"/>
        <?php endforeach ?>
      </g>
      <g stroke="#fff" stroke-opacity=".35" stroke-width="1.5" stroke-dasharray="4 6"><path d="M195 360 L346 460"/><path d="M289 536 L303 541"/><path d="M1243 334 L1085 447"/><path d="M1143 517 L1129 523"/></g>
      <circle cx="346" cy="460" r="4" fill="#F1592A"/><circle cx="303" cy="541" r="4" fill="#04B2B1"/><circle cx="1085" cy="447" r="4" fill="#04B2B1"/><circle cx="1129" cy="523" r="4" fill="#F1592A"/>
    </svg>
    <span class="anim-flotte-2 absolute top-[34.4%] left-[7.1%] aspect-square w-[6.7%] rounded-full border-4 border-orange shadow-[0_12px_28px_rgb(0_0_0/.3)] overflow-hidden"><img src="<?= asset('img/accueil/bulle-1.jpg') ?>" alt=""></span>
    <span class="anim-flotte absolute top-[59%] left-[14.9%] aspect-square w-[5%] rounded-full border-4 border-turquoise shadow-[0_12px_28px_rgb(0_0_0/.3)] overflow-hidden"><img src="<?= asset('img/accueil/bulle-2.jpg') ?>" alt=""></span>
    <span class="anim-flotte-2 absolute top-[30.2%] left-[86%] aspect-square w-[7.2%] rounded-full border-4 border-turquoise shadow-[0_12px_28px_rgb(0_0_0/.3)] overflow-hidden"><img src="<?= asset('img/accueil/bulle-3.jpg') ?>" alt=""></span>
    <span class="anim-flotte absolute top-[56.7%] left-[79.5%] aspect-square w-[4.9%] rounded-full border-4 border-orange shadow-[0_12px_28px_rgb(0_0_0/.3)] overflow-hidden"><img src="<?= asset('img/accueil/bulle-4.jpg') ?>" alt=""></span>
    <div class="anim-soleil absolute top-[36.6%] left-[21.5%] aspect-square w-[57%]">
      <div class="absolute inset-0 overflow-hidden rounded-full bg-vert shadow-[0_0_0_8px_rgb(255_255_255/.14)]" data-diapo="accueil" data-diapo-zoom>
        <img data-diapo-image src="<?= asset('img/accueil/premier-ecran.jpg') ?>" alt="" class="absolute inset-0 h-full w-full object-cover object-[62%_30%]" fetchpriority="high">
        <img data-diapo-image src="<?= asset('img/accueil/premier-ecran-2.jpg') ?>" alt="" class="absolute inset-0 h-full w-full object-cover object-[50%_20%]" loading="lazy">
        <img data-diapo-image src="<?= asset('img/accueil/premier-ecran-3.jpg') ?>" alt="" class="absolute inset-0 h-full w-full object-cover" loading="lazy">
      </div>
    </div>
  </div>

  <div class="conteneur relative z-[2] flex flex-col items-center pt-7 pb-9 text-center sm:pt-10 lg:h-full lg:pt-[5vh] lg:pb-0">
    <div class="anim-entree order-2 flex flex-col items-center lg:order-1">
      <h1 id="titre-accueil" class="mt-7 max-w-4xl text-[2.1rem] leading-[1.05] font-bold text-white [text-shadow:0_4px_28px_rgb(0_0_0/.45)] sm:text-5xl lg:mt-0 lg:text-[clamp(2.75rem,8vh,4.75rem)]">
        <?= e(rtrim($p['accroche_accueil'] ?: 'La jeunesse malienne se connecte ici', '. ')) ?>
      </h1>
      <p class="mt-5 hidden max-w-2xl rounded-2xl border border-white/20 bg-vert/80 px-6 py-3.5 text-base leading-relaxed text-white backdrop-blur-md sm:block lg:mt-[2.5vh] lg:text-[clamp(0.95rem,2.1vh,1.15rem)]">
        <?= e($p['presentation_accueil'] ?: 'Chapitre national de YouthConnekt Africa, YouthConnekt Mali rassemble les jeunes et les organisations qui font avancer le pays : formations, opportunités, rencontres et initiatives pour une jeunesse qui agit.') ?>
      </p>
    </div>

    <div class="order-4 mt-6 grid w-full max-w-md grid-cols-2 gap-3 lg:hidden">
      <a href="#chapitre" class="btn btn-contour-blanc px-2 text-[13px] sm:text-base">Découvrir le chapitre</a>
      <a href="#rejoindre" class="btn btn-orange px-2 text-[13px] sm:text-base">Let's Connekt</a>
    </div>

    <div class="anim-soleil relative order-1 aspect-square w-[66vw] max-w-[290px] sm:max-w-[340px] lg:hidden">
      <div class="absolute -inset-3 rounded-full border-2 border-dashed border-white/35" aria-hidden="true"></div>
      <div class="absolute inset-0 overflow-hidden rounded-full bg-vert shadow-[0_0_0_6px_rgb(255_255_255/.14)]" data-diapo="accueil" data-diapo-zoom>
        <img data-diapo-image src="<?= asset('img/accueil/premier-ecran.jpg') ?>" alt="Jeunes réunis lors d'un événement YouthConnekt" class="absolute inset-0 h-full w-full object-cover object-[62%_30%]">
        <img data-diapo-image src="<?= asset('img/accueil/premier-ecran-2.jpg') ?>" alt="" class="absolute inset-0 h-full w-full object-cover" loading="lazy">
        <img data-diapo-image src="<?= asset('img/accueil/premier-ecran-3.jpg') ?>" alt="" class="absolute inset-0 h-full w-full object-cover" loading="lazy">
      </div>
    </div>

    <div class="anim-flotte absolute bottom-[15%] left-4 z-10 hidden lg:block xl:-left-4"><a href="#chapitre" class="btn btn-contour-blanc min-h-14 w-[17rem] px-6 text-lg">Découvrir le chapitre</a></div>
    <div class="anim-flotte-2 absolute right-4 bottom-[15%] z-10 hidden lg:block xl:-right-4"><a href="#rejoindre" class="btn btn-orange min-h-14 w-[17rem] px-6 text-lg shadow-lg shadow-orange/30">Let's Connekt</a></div>
  </div>
  <?php partiel('partials/mention_photo', ['texte' => 'Photos provisoires · Sommet YouthConnekt Africa, hors Mali']) ?>
</section>

<?php /* 2. Le chapitre : présentation, chiffres clés, axes */ ?>
<section id="chapitre" class="relative scroll-mt-20 overflow-hidden py-16 sm:py-20 lg:py-24" aria-labelledby="titre-chapitre">
  <div class="absolute -top-24 -right-24 hidden size-80 rounded-full bg-turquoise/10 md:block" aria-hidden="true"></div>
  <div class="conteneur relative flex flex-col gap-5 sm:gap-8 lg:grid lg:grid-cols-2 lg:items-center lg:gap-16">
    <div class="contents lg:block">
      <div class="order-1"><p class="surtitre surtitre-trait">Le chapitre</p>
      <h2 id="titre-chapitre" class="titre-section">Le lieu où la jeunesse malienne se rencontre et avance</h2></div>
      <p class="order-4 text-base leading-relaxed sm:text-lg lg:mt-5">YouthConnekt Mali accompagne les jeunes dans leurs projets, les implique dans la vie citoyenne et met en lumière celles et ceux qui font bouger le pays. Étudiants, jeunes professionnels, entrepreneurs et organisations s'y retrouvent pour apprendre, se rencontrer et agir ensemble.</p>
      <a href="<?= url('/a-propos') ?>" class="btn btn-vert survol-leve order-5 w-full sm:w-auto sm:self-start lg:mt-7">Découvrir le chapitre</a>

      <?php if ($chiffres): ?>
      <dl class="order-2 grid grid-cols-3 divide-x divide-ligne border-y border-ligne py-3.5 sm:py-5 lg:mt-9">
        <?php foreach ($chiffres as $k => $chiffre): ?>
        <div class="<?= $k > 0 ? 'pl-4 sm:pl-6' : '' ?> pr-2">
          <dt class="sr-only"><?= e($chiffre['libelle']) ?></dt>
          <dd class="text-2xl font-bold text-vert tabular-nums sm:text-4xl" data-compteur><?= e($chiffre['valeur']) ?></dd>
          <dd class="mt-2 h-[3px] w-6 rounded-full <?= cycle(['bg-orange', 'bg-turquoise', 'bg-bleu'], $k) ?>" aria-hidden="true"></dd>
          <dd class="mt-2 text-xs leading-snug text-encre sm:text-sm"><?= e($chiffre['libelle']) ?></dd>
        </div>
        <?php endforeach ?>
      </dl>
      <?php endif ?>
    </div>

    <div class="relative order-3 mx-auto h-[250px] w-full max-w-xl sm:h-[min(520px,calc(100vh-200px))] sm:min-h-[420px] lg:max-w-none" aria-hidden="true">
      <span class="absolute top-4 left-0 h-[88%] w-[56%] rounded-2xl bg-orange sm:top-[30px] sm:w-[54%] sm:rounded-[22px]"></span>
      <span class="absolute right-[2%] bottom-0 h-[34%] w-[34%] rounded-[22px] bg-turquoise"></span>
      <div class="devoile survol-zoom absolute top-0 left-3 h-[88%] w-[54%] overflow-hidden rounded-2xl shadow-[0_20px_40px_rgb(20_32_29/.2)] sm:left-6 sm:w-[52%] sm:rounded-[22px]">
        <img src="<?= asset('img/accueil/chapitre-1.jpg') ?>" alt="" class="derive -mt-[10%] block h-[120%] w-full object-cover" loading="lazy">
      </div>
      <div class="devoile-2 survol-penche absolute top-[4%] right-[4%] h-[44%] w-[38%] rotate-[4deg] overflow-hidden rounded-[20px] border-4 border-white shadow-[0_18px_36px_rgb(20_32_29/.22)] sm:border-[6px]">
        <img src="<?= asset('img/accueil/chapitre-2.jpg') ?>" alt="" class="h-full w-full object-cover" loading="lazy">
      </div>
      <div class="devoile-2 survol-penche absolute right-[6%] bottom-[4%] h-[45%] w-[40%] -rotate-3 overflow-hidden rounded-[20px] border-4 border-white shadow-[0_18px_36px_rgb(20_32_29/.22)] sm:border-[6px]">
        <img src="<?= asset('img/accueil/chapitre-3.jpg') ?>" alt="" class="h-full w-full object-cover" loading="lazy">
      </div>
      <?php partiel('partials/mention_photo', ['texte' => 'Photos provisoires · hors Mali', 'position' => 'left-9 bottom-[16%]']) ?>
    </div>
  </div>

  <?php if ($axes): ?>
  <div class="conteneur relative mt-14 sm:mt-20 lg:mt-28">
    <p class="surtitre">Nos axes d'action</p>
    <h2 class="titre-section">Ce que fait le chapitre</h2>
    <ul class="mt-4 grid grid-cols-3 gap-2 sm:mt-8 sm:gap-5">
      <?php foreach ($axes as $k => $axe): ?>
      <li class="carte survol-leve survol-zoom survol-orange relative flex flex-col rounded-[14px] border-[1.5px] sm:rounded-2xl sm:border-2">
        <div class="relative h-24 border-b-4 sm:aspect-[16/8.5] sm:h-auto <?= bordure($axe['couleur']) ?>">
          <?php partiel('partials/image_axe', ['axe' => $axe]) ?>
          <span class="absolute top-1.5 left-1.5 grid size-[22px] place-items-center rounded-full bg-white/90 text-[10px] font-bold text-vert sm:top-3 sm:left-3 sm:size-9 sm:text-xs"><?= sprintf('%02d', $k + 1) ?></span>
        </div>
        <div class="flex flex-1 flex-col px-2.5 pt-2.5 pb-3 sm:p-6">
          <h3 class="text-sm font-bold sm:text-2xl"><?= e($axe['nom']) ?></h3>
          <p class="mt-1 line-clamp-4 flex-1 text-xs leading-snug text-encre sm:mt-2 sm:line-clamp-none sm:text-[15px] sm:leading-relaxed"><?= e($axe['description']) ?></p>
          <a href="<?= url('/a-propos') ?>#axes" class="mt-2.5 self-start rounded-full bg-turquoise-clair px-2.5 py-1 text-xs font-bold text-vert transition-colors after:absolute after:inset-0 hover:bg-orange hover:text-white sm:mt-4 sm:border-2 sm:border-vert sm:bg-white sm:px-4 sm:py-1.5 sm:text-sm sm:hover:border-orange"><span class="sm:hidden">Lire</span><span class="hidden sm:inline">En savoir plus</span><span class="sr-only"> sur l'axe <?= e($axe['nom']) ?></span></a>
        </div>
      </li>
      <?php endforeach ?>
    </ul>
  </div>
  <?php endif ?>
</section>

<?php /* 3. Initiatives */ ?>
<?php if ($initiatives || $admin): ?>
<section class="bg-gris py-16 sm:py-20" aria-labelledby="titre-initiatives">
  <div class="conteneur">
    <div class="flex items-end justify-between gap-4">
      <div>
        <p class="surtitre">Sur le terrain</p>
        <h2 id="titre-initiatives" class="titre-section">Nos initiatives</h2>
      </div>
      <a href="<?= url('/initiatives') ?>" class="btn btn-vert survol-leve hidden sm:inline-flex">Toutes les initiatives</a>
      <a href="<?= url('/initiatives') ?>" class="btn-tout-voir sm:hidden">Tout voir</a>
    </div>

    <?php if ($initiatives): ?>
    <ul class="mt-5 grid grid-cols-2 gap-2 sm:mt-8 sm:gap-4 lg:grid-cols-3 lg:gap-5">
      <?php foreach ($initiatives as $k => $initiative): $premiere = $k === 0; ?>
      <li class="<?= $premiere ? 'col-span-2 lg:col-span-1' : '' ?>">
        <a href="<?= e($initiative['url']) ?>" class="survol-leve group relative block overflow-hidden rounded-2xl lg:aspect-[3/3.15] <?= $premiere ? 'aspect-[4/3]' : 'aspect-square' ?>">
          <div class="absolute inset-0 transition-transform duration-500 group-hover:scale-105">
            <?php partiel('partials/photo', ['image' => $initiative['image']]) ?>
          </div>
          <div class="absolute inset-0 bg-gradient-to-t from-vert via-vert/55 to-transparent"></div>
          <div class="absolute inset-x-0 bottom-0 p-4 sm:p-6">
            <span class="etiquette bg-white text-vert <?= !$premiere ? 'max-lg:bg-transparent max-lg:p-0 max-lg:text-white/90' : '' ?> <?= $initiative['axe_nom'] ? '' : 'invisible' ?>"><?= e($initiative['axe_nom'] ?: 'Initiative') ?></span>
            <h3 class="mt-2 line-clamp-2 text-base leading-tight font-bold text-white sm:text-2xl lg:min-h-[2.5em]"><?= e($initiative['titre']) ?></h3>
            <p class="mt-1.5 hidden text-[15px] leading-snug text-white/90 sm:line-clamp-3 lg:min-h-[4.125em] <?= !$premiere ? 'max-lg:hidden' : '' ?>"><?= e($initiative['resume']) ?></p>
          </div>
        </a>
      </li>
      <?php endforeach ?>
    </ul>
    <?php else: ?>
    <p class="mt-8 rounded-xl border border-dashed border-vert/30 bg-white p-5 text-sm">Aucune initiative publiée pour l'instant. Cette section est masquée aux visiteurs tant qu'elle est vide. <a href="<?= url('/gestion/initiatives/ajouter') ?>" class="btn-lien ml-2">Ajouter une initiative</a></p>
    <?php endif ?>
  </div>
</section>
<?php endif ?>

<?php /* 4. Mot du ministre : message complet affiché directement (maquette validée) */ ?>
<section class="py-10 sm:py-20 lg:py-24" aria-labelledby="titre-ministre">
  <div class="conteneur grid items-center gap-8 md:grid-cols-[minmax(0,5fr)_minmax(0,7fr)] lg:gap-16">
    <div class="mx-auto hidden w-full md:block md:max-w-sm">
      <div class="apparition aspect-[4/5] overflow-hidden rounded-2xl border-t-[6px] border-orange bg-white shadow-xl shadow-vert/10">
        <?php partiel('partials/photo', ['image' => $mot['photo'], 'alt' => $nom_ministre, 'mention' => 'Photo officielle du ministre à fournir, format portrait']) ?>
      </div>
    </div>
    <div>
      <?php if (!$ministre_publie && $admin): ?><p class="mb-4 inline-block rounded-md bg-orange/10 px-3 py-1 text-xs font-bold text-orange-fonce">Non publié dans l'administration : le texte proposé par la maquette est affiché</p><?php endif ?>
      <p class="surtitre surtitre-trait" id="titre-ministre">Le mot du ministre</p>
      <div class="mt-4 flex items-center gap-3.5 md:hidden">
        <div class="h-[132px] w-[104px] shrink-0 overflow-hidden rounded-xl bg-gris">
          <?php partiel('partials/photo', ['image' => $mot['photo'], 'alt' => $nom_ministre, 'mention' => 'Photo officielle']) ?>
        </div>
        <p class="border-l-[3px] border-orange pl-3 leading-snug">
          <span class="block text-[15px] font-bold text-encre"><?= e($nom_ministre) ?></span>
          <span class="mt-1 block text-xs text-texte"><?= e($titre_ministre) ?></span>
        </p>
      </div>
      <blockquote class="mt-5 space-y-2.5 text-base leading-relaxed text-vert sm:space-y-4 sm:text-xl">
        <?php if ($ministre_publie && trim((string) $mot['message']) !== ''): ?><?= paragraphes($mot['message']) ?>
        <?php elseif ($ministre_publie && trim((string) $mot['extrait']) !== ''): ?><p>« <?= e($mot['extrait']) ?> »</p>
        <?php else: ?>
        <p>« La jeunesse malienne est notre première richesse. Elle porte l'énergie, la créativité et l'ambition dont notre pays a besoin pour bâtir son avenir.</p>
        <p>Avec YouthConnekt Mali, nous voulons offrir à chaque jeune, où qu'il vive, un espace pour se former, accéder aux opportunités, entreprendre et s'engager dans la vie citoyenne.</p>
        <p>J'invite chaque jeune, chaque association et chaque entreprise à rejoindre le chapitre. Ensemble, faisons de cette plateforme un lieu d'échanges, de solidarité et de réussite pour le Mali. »</p>
        <?php endif ?>
      </blockquote>
      <p class="mt-6 hidden border-l-[3px] border-orange pl-4 md:block">
        <span class="block font-bold text-encre"><?= e($nom_ministre) ?></span>
        <span class="mt-1 block text-sm text-texte"><?= e($titre_ministre) ?></span>
      </p>
      <?php if (!$ministre_publie): ?><p class="mt-5 text-xs text-texte">Proposition de texte, à valider par le cabinet du ministre avant publication.</p><?php endif ?>
    </div>
  </div>
</section>

<?php /* 5. Communauté */ ?>
<?php if ($communautes): ?>
<section id="communaute" class="relative overflow-hidden bg-gris py-12 sm:py-14" aria-labelledby="titre-communaute">
  <span class="absolute -top-28 -right-40 hidden size-[620px] rounded-full bg-white lg:block" aria-hidden="true"></span>
  <div class="conteneur relative">
    <div class="grid items-center gap-8 lg:grid-cols-2 lg:gap-10">
      <div>
        <p class="surtitre surtitre-trait">Communauté</p>
        <h2 id="titre-communaute" class="titre-section">Votre communauté, votre réseau, vos opportunités</h2>
        <p class="mt-5 max-w-lg text-base leading-relaxed sm:text-lg"><span class="sm:hidden">Retrouvez celles et ceux qui vous ressemblent et les opportunités de votre domaine.</span><span class="hidden sm:inline">Étudiants, entrepreneurs, leaders d'associations, innovateurs : retrouvez celles et ceux qui vous ressemblent, échangez et accédez aux opportunités de votre domaine.</span></p>
        <a href="<?= url('/communaute') ?>" class="btn btn-orange mt-7 hidden min-h-14 px-8 text-lg lg:inline-flex">Découvrir les communautés</a>
      </div>

      <div class="hidden h-[400px] items-center justify-center lg:flex">
        <div class="relative h-[460px] w-[560px] shrink-0 scale-[.86]">
          <span class="anim-flotte-2 absolute top-[125px] left-[190px] z-10 size-[200px] overflow-hidden rounded-full border-[6px] border-orange bg-white shadow-[0_16px_34px_rgb(3_94_77/.22)]"><img src="<?= asset('img/communaute/cercle-centre.jpg') ?>" alt="" class="h-full w-full object-cover" loading="lazy"></span>
          <span class="anim-flotte absolute top-[70px] left-[120px] z-[9] size-[130px] overflow-hidden rounded-full border-[5px] border-turquoise bg-white shadow-[0_16px_34px_rgb(3_94_77/.22)]"><img src="<?= asset('img/communaute/cercle-1.jpg') ?>" alt="" class="h-full w-full object-cover" loading="lazy"></span>
          <span class="anim-flotte-2 absolute top-[70px] left-[330px] z-[8] size-[130px] overflow-hidden rounded-full border-[5px] border-bleu bg-white shadow-[0_16px_34px_rgb(3_94_77/.22)]"><img src="<?= asset('img/communaute/cercle-2.jpg') ?>" alt="" class="h-full w-full object-cover" loading="lazy"></span>
          <span class="anim-flotte absolute top-[250px] left-[335px] z-[7] size-[140px] overflow-hidden rounded-full border-[5px] border-turquoise bg-white shadow-[0_16px_34px_rgb(3_94_77/.22)]"><img src="<?= asset('img/communaute/cercle-3.jpg') ?>" alt="" class="h-full w-full object-cover" loading="lazy"></span>
          <span class="anim-flotte-2 absolute top-[255px] left-[120px] z-[6] size-[120px] overflow-hidden rounded-full border-[5px] border-orange bg-white shadow-[0_16px_34px_rgb(3_94_77/.22)]"><img src="<?= asset('img/communaute/cercle-4.jpg') ?>" alt="" class="h-full w-full object-cover" loading="lazy"></span>
          <span class="anim-flotte absolute top-[305px] left-[240px] z-[5] size-[100px] overflow-hidden rounded-full border-[5px] border-vert bg-white shadow-[0_16px_34px_rgb(3_94_77/.22)]"><img src="<?= asset('img/communaute/cercle-5.jpg') ?>" alt="" class="h-full w-full object-cover" loading="lazy"></span>
          <span class="anim-flotte-2 absolute top-[180px] left-[430px] z-[4] size-[100px] overflow-hidden rounded-full border-[5px] border-orange bg-white shadow-[0_16px_34px_rgb(3_94_77/.22)]"><img src="<?= asset('img/communaute/cercle-6.jpg') ?>" alt="" class="h-full w-full object-cover" loading="lazy"></span>
          <a href="<?= url('/communaute') ?>" class="anim-flotte group absolute bottom-[34px] left-[50px] z-20 flex items-center gap-4 rounded-2xl bg-white px-4 py-3 shadow-[0_16px_34px_rgb(3_94_77/.18)] transition-colors hover:bg-orange" aria-label="Voir les <?= $nb_communautes ?> communautés">
            <span class="flex flex-col border-l-4 border-orange pl-3 leading-tight transition-colors group-hover:border-white"><strong class="text-3xl text-vert group-hover:text-white tabular-nums" data-compteur data-duree="1600"><?= $nb_communautes ?></strong><span class="text-sm text-encre group-hover:text-white">communauté<?= pluriel($nb_communautes) ?> à découvrir</span></span>
          </a>
        </div>
      </div>

      <div class="flex justify-center -space-x-4 lg:hidden" aria-hidden="true">
        <?php for ($n = 1; $n <= 6; $n++): ?><span class="size-14 overflow-hidden rounded-full border-[3px] <?= cycle(['border-orange', 'border-turquoise', 'border-bleu', 'border-vert', 'border-orange', 'border-turquoise'], $n - 1) ?> bg-white shadow"><img src="<?= asset('img/communaute/cercle-' . $n . '.jpg') ?>" alt="" class="h-full w-full object-cover" loading="lazy"></span><?php endfor ?>
      </div>
    </div>


    <a href="<?= url('/communaute') ?>" class="btn btn-orange mt-6 w-full lg:hidden">Découvrir les communautés</a>
  </div>
</section>
<?php endif ?>

<?php /* 6. Opportunités */ ?>
<?php if ($opportunites || $admin): ?>
<section class="py-16 sm:py-20" aria-labelledby="titre-opportunites">
  <div class="conteneur">
    <div class="flex items-end justify-between gap-4">
      <div>
        <p class="surtitre">À saisir maintenant</p>
        <h2 id="titre-opportunites" class="titre-section">Opportunités</h2>
      </div>
      <a href="<?= url('/opportunites') ?>" class="btn btn-contour survol-leve hidden sm:inline-flex">Toutes les opportunités</a>
      <a href="<?= url('/opportunites') ?>" class="btn-tout-voir sm:hidden">Tout voir</a>
    </div>

    <?php if ($opportunites): ?>
    <ul class="mt-5 grid gap-2.5 sm:mt-8 md:grid-cols-3 md:gap-5">
      <?php foreach ($opportunites as $o): ?>
      <li>
        <a href="<?= e($o['url']) ?>" class="carte group flex h-full md:flex-col">
          <div class="relative w-[108px] shrink-0 overflow-hidden border-r-4 md:aspect-[16/7] md:w-auto md:border-r-0 md:border-b-4 <?= bordure($o['couleur']) ?>">
            <?php partiel('partials/photo', ['image' => $o['image']]) ?>
            <?php if ($o['jours_restants'] !== null && $o['jours_restants'] <= 30): ?>
            <span class="absolute top-2 right-2 hidden rounded-lg bg-orange px-2 py-1 text-center text-xs leading-tight font-bold text-white md:block">J-<?= $o['jours_restants'] ?></span>
            <?php endif ?>
          </div>
          <div class="flex flex-1 flex-col p-3.5 sm:p-6">
            <span class="text-[11px] font-bold tracking-[0.1em] uppercase md:hidden <?= texte_couleur($o['couleur']) ?>"><?= e($o['type_libelle']) ?></span>
            <span class="etiquette hidden self-start md:inline-block <?= fond($o['couleur']) ?>"><?= e($o['type_libelle']) ?></span>
            <h3 class="mt-1.5 text-[17px] leading-snug font-bold text-encre group-hover:text-orange sm:text-xl md:mt-3 md:text-base"><?= e($o['titre']) ?></h3>
            <div class="mt-auto flex items-end justify-between gap-3 pt-2 md:mt-5 md:border-t md:border-ligne md:pt-4">
              <p class="text-xs sm:text-sm">
                <?php if ($o['date_limite']): ?><span class="md:hidden">Avant le <strong class="text-rouge"><?= e(date_longue($o['date_limite'])) ?></strong></span><span class="hidden text-texte md:block">Date limite</span><span class="mt-0.5 hidden font-bold text-rouge md:block"><?= e(date_longue($o['date_limite'])) ?></span>
                <?php else: ?><span class="font-medium text-texte">Sans date limite</span><?php endif ?>
              </p>
              <span class="hidden text-sm font-bold text-vert md:inline" aria-hidden="true">Voir</span>
            </div>
          </div>
        </a>
      </li>
      <?php endforeach ?>
    </ul>
    <?php else: ?>
    <p class="mt-8 rounded-xl border border-dashed border-vert/30 bg-gris p-5 text-sm">Aucune opportunité ouverte pour l'instant. Cette section est masquée aux visiteurs tant qu'elle est vide. <a href="<?= url('/gestion/opportunites/ajouter') ?>" class="btn-lien ml-2">Ajouter une opportunité</a></p>
    <?php endif ?>
  </div>
</section>
<?php endif ?>

<?php /* 7. Événements et actualités */ ?>
<?php if ($actualite_une || $admin): ?>
<section class="bg-vert py-10 sm:bg-gris sm:py-20" aria-labelledby="titre-actualites">
  <div class="conteneur">
    <div class="flex items-end justify-between gap-4">
      <div>
        <p class="surtitre max-sm:text-white/85">La vie du chapitre</p>
        <h2 id="titre-actualites" class="titre-section max-sm:text-white">Événements et actualités</h2>
      </div>
      <a href="<?= url('/actualites') ?>" class="btn btn-contour survol-leve hidden sm:inline-flex">Toutes les actualités</a>
      <a href="<?= url('/actualites') ?>" class="btn-tout-voir bg-white sm:hidden">Tout voir</a>
    </div>

    <?php if ($actualite_une): $une = $actualite_une; ?>
    <div class="mt-5 grid gap-2.5 sm:mt-8 sm:gap-4 lg:grid-cols-[minmax(0,1.55fr)_minmax(0,1fr)] lg:gap-5">
      <a href="<?= e($une['url']) ?>" class="survol-leve group relative block h-[230px] overflow-hidden rounded-[18px] border-[3px] border-white sm:aspect-[16/10] sm:h-auto sm:rounded-2xl sm:border-0 lg:aspect-auto lg:min-h-[440px]">
        <div class="absolute inset-0 transition-transform duration-500 group-hover:scale-105">
          <?php partiel('partials/photo', ['image' => $une['image']]) ?>
        </div>
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>
        <time datetime="<?= e(date_iso($une['date_affichee'])) ?>" class="absolute top-4 left-4 grid min-w-14 rounded-xl bg-white px-2.5 py-2 text-center leading-none text-vert">
          <span class="text-2xl font-bold"><?= e(jour($une['date_affichee'])) ?></span>
          <span class="mt-1 text-[11px] font-bold uppercase"><?= e(mois_pastille($une['date_affichee'])) ?></span>
        </time>
        <div class="absolute inset-x-0 bottom-0 p-4 sm:p-7">
          <span class="etiquette bg-orange tracking-wide text-white uppercase">À la une · <?= e($une['categorie_libelle']) ?></span>
          <h3 class="mt-3 text-xl leading-tight font-bold text-white sm:text-3xl"><?= e($une['titre']) ?></h3>
          <p class="mt-2 line-clamp-2 hidden text-sm text-white/90 sm:block sm:text-base"><?= e($une['chapeau']) ?></p>
        </div>
      </a>

      <?php if ($actualites): ?>
      <ul class="grid gap-2.5 sm:gap-4">
        <?php foreach ($actualites as $actu): ?>
        <li>
          <a href="<?= e($actu['url']) ?>" class="survol-leve survol-zoom group flex h-full items-center gap-3 rounded-[14px] bg-white p-2 sm:gap-4 sm:rounded-2xl sm:p-4">
            <div class="size-16 shrink-0 overflow-hidden rounded-xl sm:size-24">
              <?php partiel('partials/photo', ['image' => $actu['image']]) ?>
            </div>
            <div class="min-w-0">
              <p class="text-[11px] font-bold tracking-[0.12em] text-vert uppercase sm:text-xs"><?= e($actu['categorie_libelle']) ?> · <time datetime="<?= e(date_iso($actu['date_affichee'])) ?>"><?= e(date_courte($actu['date_affichee'])) ?></time></p>
              <h3 class="mt-1.5 line-clamp-2 text-[15px] leading-snug font-bold text-encre group-hover:text-orange sm:text-base"><?= e($actu['titre']) ?></h3>
            </div>
          </a>
        </li>
        <?php endforeach ?>
      </ul>
      <?php endif ?>
    </div>
    <?php else: ?>
    <p class="mt-8 rounded-xl border border-dashed border-vert/30 bg-white p-5 text-sm">Aucune actualité publiée pour l'instant. Cette section est masquée aux visiteurs tant qu'elle est vide. <a href="<?= url('/gestion/actualites/ajouter') ?>" class="btn-lien ml-2">Ajouter une actualité</a></p>
    <?php endif ?>
  </div>
</section>
<?php endif ?>

<?php /* 8. Let's Connekt */ ?>
<section id="rejoindre" class="relative isolate scroll-mt-20 overflow-hidden bg-vert py-20 text-center text-white sm:py-28" aria-labelledby="titre-invitation">
  <img src="<?= asset('img/bandeaux/lets-connekt.jpg') ?>" alt="" class="derive absolute -inset-y-12 inset-x-0 -z-10 h-[calc(100%+6rem)] w-full object-cover" loading="lazy">
  <?php partiel('partials/mention_photo') ?>
  <div class="absolute inset-0 -z-10 bg-vert/85"></div>
  <div class="conteneur">
    <h2 id="titre-invitation" class="text-5xl leading-none font-bold text-white sm:text-7xl lg:text-[8.5rem]">Let's Connekt</h2>
    <p class="mx-auto mt-5 max-w-md text-base text-white/95 sm:text-lg">Jeunes, entreprises et organisations : le chapitre YouthConnekt Mali vous ouvre ses portes.</p>
    <?php if (est_connecte()): ?>
    <a href="<?= url('/communaute') ?>" class="btn btn-orange survol-grand mt-8 min-h-14 px-10 text-lg">Découvrir les communautés</a>
    <?php else: ?>
    <a href="<?= url('/compte/inscription') ?>" class="btn btn-orange survol-grand mt-8 min-h-14 px-10 text-lg">Rejoindre le chapitre</a>
    <?php endif ?>
  </div>
</section>

<?php /* 9. Partenaires */ ?>
<section class="relative overflow-hidden bg-gris py-12 sm:py-20" aria-labelledby="titre-partenaires">
  <span class="absolute -bottom-40 -left-36 size-52 rounded-full bg-turquoise-clair sm:size-96" aria-hidden="true"></span>
  <span class="absolute -top-32 -right-24 size-40 rounded-full bg-orange-clair sm:size-72" aria-hidden="true"></span>
  <div class="conteneur relative grid gap-6 sm:gap-10 lg:grid-cols-[minmax(0,4fr)_minmax(0,8fr)] lg:items-center">
    <div>
      <p class="surtitre">Partenaires</p>
      <h2 id="titre-partenaires" class="titre-section whitespace-nowrap">Nos partenaires</h2>
      <p class="mt-3 text-[15px] sm:mt-4 sm:text-lg">Ils soutiennent YouthConnekt Mali et organisent avec lui initiatives et événements.</p>
    </div>
    <?php $bords = ['border-vert', 'border-orange', 'border-turquoise', 'border-bleu'];
      $liste_partenaires = $partenaires ?: array_map(fn ($nom) => ['nom' => $nom, 'logo' => '', 'lien' => ''], $partenaires_maquette); ?>
    <ul class="flex flex-wrap justify-center gap-3 sm:gap-5 lg:justify-start">
      <?php foreach ($liste_partenaires as $k => $partenaire): $balise = $partenaire['lien'] ? 'a href="' . e($partenaire['lien']) . '" target="_blank" rel="noopener"' : 'div'; ?>
      <li class="w-[calc(50%-0.375rem)] sm:w-52 lg:w-56">
        <<?= $balise ?> class="survol-leve flex h-[88px] items-center justify-center rounded-[14px] border-2 bg-white p-3 text-center shadow-lg shadow-vert/10 sm:h-[110px] sm:rounded-[20px] <?= cycle($bords, $k) ?>">
          <?php if ($partenaire['logo']): ?><img src="<?= e(media($partenaire['logo'])) ?>" alt="<?= e($partenaire['nom']) ?>" class="max-h-full max-w-full object-contain" loading="lazy"><?php else: ?><span class="text-[13px] leading-tight font-bold text-vert sm:text-[15px]"><?= e($partenaire['nom']) ?></span><?php endif ?>
        </<?= $partenaire['lien'] ? 'a' : 'div' ?>>
      </li>
      <?php endforeach ?>
    </ul>
    
  </div>
</section>

<?php /* 10. Témoignages */ ?>
<?php partiel('partials/temoignages', ['temoignages' => $temoignages]) ?>

<?php /* 11. Contact */ ?>
<section class="py-9 sm:py-20" aria-labelledby="titre-contact">
  <div class="conteneur">
    <div class="grid overflow-hidden rounded-3xl shadow-2xl shadow-vert/10 md:grid-cols-[minmax(0,5fr)_minmax(0,7fr)]">
      <div class="relative bg-vert px-5 py-6 text-white sm:p-10">
        <p class="text-xs font-medium tracking-[0.16em] text-white/80 uppercase">Contact</p>
        <h2 id="titre-contact" class="mt-2 text-2xl leading-tight font-bold text-white sm:mt-3 sm:text-4xl">Une question ?<br>Écrivez-nous</h2>
        <a href="<?= url('/contact') ?>" class="btn btn-blanc survol-leve mt-5 sm:mt-7">Nous contacter</a>
        <svg class="pointer-events-none absolute right-0 bottom-0 hidden h-40 w-48 md:block" viewBox="0 0 200 160" aria-hidden="true">
          <g stroke="rgb(255 255 255 / .35)" stroke-width="1.2" fill="none"><path d="M20 150 L95 55 L170 15"/><path d="M95 55 L200 115"/></g>
          <circle cx="20" cy="150" r="6" fill="#F1592A"/><circle cx="95" cy="55" r="8" fill="#04B2B1"/><circle cx="170" cy="15" r="6" fill="#F1592A"/>
        </svg>
      </div>
      <div class="flex flex-col justify-center border-[1.5px] border-t-0 border-ligne bg-white p-4 sm:border-0 sm:p-8 lg:p-10">
        <?php
        $cartes_contact = array_filter([
            $p['email_contact'] ? ['Écrire un e-mail', $p['email_contact'], 'mailto:' . $p['email_contact'], 'mail', 'bg-orange'] : null,
            $p['telephone'] ? ['Appeler', $p['telephone'], 'tel:' . str_replace(' ', '', $p['telephone']), 'tel', 'bg-turquoise'] : null,
            ['Nous trouver', $p['adresse'] ?: 'Bamako, Mali', url('/contact'), 'lieu', 'bg-vert'],
            $p['horaires'] ? ['Horaires', $p['horaires'], url('/contact'), 'heure', 'bg-bleu'] : null,
        ]);
        $icones_contact = ['mail' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>', 'tel' => '<path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2"/>', 'lieu' => '<path d="M12 21s7-6.2 7-12a7 7 0 0 0-14 0c0 5.8 7 12 7 12z"/><circle cx="12" cy="9" r="2.5"/>', 'heure' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>'];
        ?>
        <ul class="grid gap-3 sm:grid-cols-2 sm:gap-4">
          <?php foreach ($cartes_contact as [$titre_carte, $valeur_carte, $lien_carte, $icone_carte, $fond_carte]): ?>
          <li>
            <a href="<?= e($lien_carte) ?>" class="survol-leve group flex h-full items-center gap-3.5 rounded-2xl border border-ligne bg-white p-4 transition-colors hover:border-orange sm:flex-col sm:items-start sm:gap-3 sm:p-5">
              <span class="grid size-11 shrink-0 place-items-center rounded-xl text-white <?= $fond_carte ?>" aria-hidden="true"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><?= $icones_contact[$icone_carte] ?></svg></span>
              <span class="min-w-0"><strong class="block text-[15px] text-vert group-hover:text-orange"><?= e($titre_carte) ?></strong><span class="mt-0.5 block text-sm break-words text-encre"><?= e($valeur_carte) ?></span></span>
            </a>
          </li>
          <?php endforeach ?>
        </ul>
        <?php if (reseaux_sociaux()): ?>
        <p class="mt-6 text-xs font-bold tracking-[0.14em] text-vert uppercase">Suivez le chapitre</p>
        <?php partiel('partials/reseaux') ?>
        <?php endif ?>
      </div>
    </div>
  </div>
</section>
