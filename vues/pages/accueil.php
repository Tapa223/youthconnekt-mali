<?php
/** Accueil, dans l'ordre validé de la maquette. */
$admin = est_admin();
$ministre_publie = !empty($mot['publie']);
$nom_ministre = ($ministre_publie && $mot['nom']) ? $mot['nom'] : 'M. Abdoul Kassim Ibrahim Fomba';
$titre_ministre = ($ministre_publie && $mot['titre_officiel']) ? $mot['titre_officiel'] : "Ministre de la Jeunesse et des Sports, chargé de l'Instruction civique et de la Construction citoyenne";
// Photos du premier écran et leur phrase : celles choisies dans Paramètres, sinon les photos provisoires.
// Une photo dont le fichier a disparu (actualité supprimée…) est simplement ignorée.
$existe = fn ($chemin) => $chemin !== '' && (!str_starts_with($chemin, 'uploads/') || is_file(RACINE . '/' . $chemin));
// Photo 1 : titre et présentation de l'accueil ; photos 2 et 3 : leur propre titre et texte (Paramètres)
$defauts = [
    [rtrim($p['accroche_accueil'] ?: 'La jeunesse malienne se connecte ici', '. '), $p['presentation_accueil'] ?: 'Chapitre national de YouthConnekt Africa, YouthConnekt Mali rassemble les jeunes et les organisations qui font avancer le pays : formations, opportunités, rencontres et initiatives pour une jeunesse qui agit.'],
    [trim((string) ($p['titre_accueil_2'] ?? '')) ?: 'La jeunesse malienne se forme', trim((string) ($p['texte_accueil_2'] ?? '')) ?: 'Formations, ateliers et mentorat : le chapitre relaie les opportunités qui aident les jeunes à développer leurs compétences et à réussir leurs projets.'],
    [trim((string) ($p['titre_accueil_3'] ?? '')) ?: "La jeunesse malienne s'engage", trim((string) ($p['texte_accueil_3'] ?? '')) ?: 'Initiatives citoyennes, bénévolat et actions dans les régions : les jeunes du chapitre agissent pour la paix, la citoyenneté et le développement du Mali.'],
];
$diapos = [];
foreach ([1, 2, 3] as $n) {
    $img = (string) ($p['image_accueil_' . $n] ?? '');
    if ($existe($img)) {
        $diapos[] = ['src' => media($img), 'titre' => $defauts[$n - 1][0], 'texte' => $defauts[$n - 1][1]];
    }
}
if (!$diapos) {
    foreach (['premier-ecran.jpg', 'premier-ecran-2.jpg', 'premier-ecran-3.jpg'] as $k => $f) {
        $diapos[] = ['src' => asset('img/accueil/' . $f), 'titre' => $defauts[$k][0], 'texte' => $defauts[$k][1]];
    }
}
$photos_accueil = array_column($diapos, 'src');
// Petites bulles : 4 photos fixes choisies dans Paramètres (photothèque), sinon les photos provisoires
$bulles = [];
foreach ([1, 2, 3, 4] as $n) {
    $img = (string) ($p['bulle_accueil_' . $n] ?? '');
    $bulles[$n] = $existe($img) ? media($img) : asset('img/accueil/bulle-' . $n . '.jpg');
}
// Premier écran : grand cercle photo et anneau de points (repère 1440 × 820 de la maquette)
$soleil = ['cx' => 720, 'cy' => 745, 'r' => 480, 'anneau' => 522];
$points = [];
foreach (range(0, 11) as $k) {
    $angle = deg2rad(-$k * 30);
    $points[] = [round($soleil['cx'] + $soleil['anneau'] * cos($angle)), round($soleil['cy'] + $soleil['anneau'] * sin($angle)), $k % 3 === 0 ? 9 : 7, ['#F1592A', '#04B2B1', '#fff', '#0358D9'][$k % 4]];
}
?>

<?php /* 1. Premier écran */ ?>
<section class="relative isolate overflow-hidden bg-vert text-white lg:h-[calc(100svh-4rem)] lg:max-h-[940px] lg:min-h-[620px]" aria-labelledby="titre-accueil" data-premier-ecran>
  <div class="absolute inset-0 -z-10" data-diapo="accueil" aria-hidden="true">
    <?php foreach ($photos_accueil as $k => $src): ?><img data-diapo-image src="<?= e($src) ?>" alt="" class="absolute inset-0 h-full w-full object-cover max-lg:object-contain max-lg:object-top"<?= $k ? ' loading="lazy"' : '' ?>><?php endforeach ?>
  </div>
  <div class="absolute inset-0 -z-10 bg-[linear-gradient(180deg,rgb(3_94_77/.55)_0%,rgb(3_94_77/.45)_40%,rgb(3_94_77/.8)_70%,rgb(3_94_77/.92)_100%)] lg:bg-[linear-gradient(180deg,rgb(3_94_77/.75)_0%,rgb(3_94_77/.45)_28%,rgb(3_94_77/.25)_60%,rgb(3_94_77/.55)_100%)]" aria-hidden="true"></div>

  <?php /* Cadre de la maquette (1440 × 820) mis à l'échelle de l'écran : anneau, grand cercle et bulles s'y alignent */ ?>
  <div class="pointer-events-none absolute inset-y-0 left-1/2 hidden aspect-[1440/820] -translate-x-1/2 lg:block" aria-hidden="true">
    <svg class="absolute inset-0 h-full w-full overflow-visible" viewBox="0 0 1440 820" fill="none">
      <?php /* L'anneau s'efface en descendant : aucun trait ne passe derrière les deux boutons */ ?>
      <defs>
        <linearGradient id="fondu-anneau" x1="0" y1="0" x2="0" y2="820" gradientUnits="userSpaceOnUse"><stop offset=".62" stop-color="#fff"/><stop offset=".72" stop-color="#fff" stop-opacity="0"/></linearGradient>
        <mask id="masque-anneau" maskUnits="userSpaceOnUse" x="0" y="-200" width="1440" height="1100"><rect x="0" y="-200" width="1440" height="1100" fill="url(#fondu-anneau)"/></mask>
      </defs>
      <g mask="url(#masque-anneau)">
        <circle cx="<?= $soleil['cx'] ?>" cy="<?= $soleil['cy'] ?>" r="<?= $soleil['anneau'] ?>" stroke="#fff" stroke-opacity=".3" stroke-width="1.5" stroke-dasharray="6 8"/>
        <circle cx="<?= $soleil['cx'] ?>" cy="<?= $soleil['cy'] ?>" r="<?= $soleil['anneau'] + 42 ?>" stroke="#fff" stroke-opacity=".1"/>
        <g class="anim-orbite" style="transform-origin: <?= $soleil['cx'] ?>px <?= $soleil['cy'] ?>px">
          <?php foreach ($points as $k => [$cx, $cy, $r, $c]): ?>
          <circle class="anim-point" cx="<?= $cx ?>" cy="<?= $cy ?>" r="<?= $r ?>" fill="<?= $c ?>" stroke="#035E4D" stroke-width="3" style="animation-delay:<?= number_format(0.6 + $k * 0.25, 2, '.', '') ?>s"/>
          <?php endforeach ?>
        </g>
      </g>
      <?php /* Bulles et fils comme dans la maquette, agrandis avec le cercle */ ?>
      <g stroke="#fff" stroke-opacity=".35" stroke-width="1.5" stroke-dasharray="4 6"><path d="M111 339 L286 455"/><path d="M220 543 L236 549"/><path d="M1327 309 L1143 440"/><path d="M1211 521 L1194 528"/></g>
      <circle cx="286" cy="455" r="4" fill="#F1592A"/><circle cx="236" cy="549" r="4" fill="#04B2B1"/><circle cx="1143" cy="440" r="4" fill="#04B2B1"/><circle cx="1194" cy="528" r="4" fill="#F1592A"/>
    </svg>
    <span class="anim-flotte-2 absolute top-[31.3%] left-[0.7%] aspect-square w-[6.7%] rounded-full border-4 border-orange shadow-[0_12px_28px_rgb(0_0_0/.3)] overflow-hidden"><img src="<?= e($bulles[1]) ?>" alt="" class="h-full w-full object-cover"></span>
    <span class="anim-flotte absolute top-[59.5%] left-[9.7%] aspect-square w-[5%] rounded-full border-4 border-turquoise shadow-[0_12px_28px_rgb(0_0_0/.3)] overflow-hidden"><img src="<?= e($bulles[3]) ?>" alt="" class="h-full w-full object-cover"></span>
    <span class="anim-flotte-2 absolute top-[26.5%] left-[92.3%] aspect-square w-[7.2%] rounded-full border-4 border-turquoise shadow-[0_12px_28px_rgb(0_0_0/.3)] overflow-hidden"><img src="<?= e($bulles[2]) ?>" alt="" class="h-full w-full object-cover"></span>
    <span class="anim-flotte absolute top-[56.9%] left-[84.6%] aspect-square w-[4.9%] rounded-full border-4 border-orange shadow-[0_12px_28px_rgb(0_0_0/.3)] overflow-hidden"><img src="<?= e($bulles[4]) ?>" alt="" class="h-full w-full object-cover"></span>
    <div class="anim-soleil absolute top-[32.3%] left-[16.67%] aspect-square w-[66.67%]">
      <div class="absolute inset-0 overflow-hidden rounded-full bg-vert shadow-[0_0_0_8px_rgb(255_255_255/.14)]" data-diapo="accueil">
        <?php foreach ($photos_accueil as $k => $src): ?><img data-diapo-image src="<?= e($src) ?>" alt="<?= $k ? '' : "Jeunes réunis lors d'un événement YouthConnekt" ?>" class="absolute inset-x-0 top-0 h-[58%] w-full object-cover"<?= $k ? ' loading="lazy"' : ' fetchpriority="high"' ?>><?php endforeach ?>
      </div>
    </div>
  </div>

  <div class="conteneur relative z-[2] flex flex-col items-center pt-5 pb-6 text-center sm:pt-10 sm:pb-9 lg:h-full lg:pt-[5vh] lg:pb-0">
    <div class="anim-entree order-2 flex flex-col items-center lg:order-1">
      <?php /* Le titre et le texte changent avec la photo : « se connecte », « se forme », « s'engage » ; largeur limitée pour que chaque titre tienne sur deux lignes, à la même hauteur */ ?>
      <h1 id="titre-accueil" class="mt-4 grid max-w-[12.5em] text-[1.75rem] leading-[1.08] font-bold text-white [text-shadow:0_4px_28px_rgb(0_0_0/.45)] sm:text-5xl lg:mt-0 lg:text-[clamp(2.75rem,8vh,4.75rem)]" data-diapo="accueil">
        <?php foreach ($diapos as $k => $d): ?><span data-diapo-image class="diapo-texte col-start-1 row-start-1 self-end"<?= $k ? ' aria-hidden="true"' : '' ?>><?= e($d['titre']) ?></span><?php endforeach ?>
      </h1>
      <div class="mt-2 grid w-full max-w-2xl sm:mt-5 lg:mt-[2.5vh]" data-diapo="accueil">
        <?php foreach ($diapos as $k => $d): ?>
        <p data-diapo-image class="diapo-texte col-start-1 row-start-1 self-start rounded-2xl border border-white/20 bg-vert/80 px-4 py-3 text-[13px] leading-snug text-white/95 backdrop-blur-md sm:px-6 sm:py-3.5 sm:text-base sm:leading-relaxed sm:backdrop-blur-md lg:text-[clamp(0.95rem,2.1vh,1.15rem)]"<?= $k ? ' aria-hidden="true"' : '' ?>><?= e($d['texte']) ?></p>
        <?php endforeach ?>
      </div>
    </div>

    <div class="order-4 mt-4 grid w-full max-w-md grid-cols-2 gap-2.5 sm:mt-6 sm:gap-3 lg:hidden">
      <a href="#chapitre" class="btn btn-contour-blanc min-h-11 px-3 text-[13px] whitespace-nowrap sm:min-h-12 sm:px-4 sm:text-base">Découvrir le chapitre</a>
      <a href="#rejoindre" class="btn btn-orange min-h-11 px-3 text-[13px] whitespace-nowrap sm:min-h-12 sm:px-4 sm:text-base">Let's Connekt</a>
    </div>

    <div class="anim-soleil relative order-1 mt-1 aspect-square w-[70vw] max-w-[300px] sm:w-[62vw] sm:max-w-[360px] lg:hidden">
      <div class="absolute -inset-3 rounded-full border-2 border-dashed border-white/35" aria-hidden="true"></div>
      <?php /* Téléphone : grand cercle net ; la photo de fond, entière et nette, en haut ; l'anneau porte des points qui tournent lentement, et des bulles photo flottent autour du cercle, comme sur ordinateur */ ?>
      <div class="anim-orbite absolute -inset-3 [animation-duration:45s]" aria-hidden="true">
        <span class="absolute top-[6%] left-[22%] size-3 rounded-full bg-orange"></span>
        <span class="absolute top-[48%] -right-1.5 size-2.5 rounded-full bg-white"></span>
        <span class="absolute bottom-[8%] left-[20%] size-2.5 rounded-full bg-turquoise"></span>
        <span class="absolute top-[30%] -left-1 size-2 rounded-full bg-bleu"></span>
      </div>
      <span class="anim-flotte absolute -top-1 -left-[9%] z-[1] size-[22%] overflow-hidden rounded-full border-[3px] border-orange shadow-[0_10px_22px_rgb(0_0_0/.3)]" aria-hidden="true"><img src="<?= e($bulles[1]) ?>" alt="" class="h-full w-full object-cover"></span>
      <span class="anim-flotte-2 absolute top-[16%] -right-[10%] z-[1] size-[17%] overflow-hidden rounded-full border-[3px] border-turquoise shadow-[0_10px_22px_rgb(0_0_0/.3)]" aria-hidden="true"><img src="<?= e($bulles[2]) ?>" alt="" class="h-full w-full object-cover"></span>
      <span class="anim-flotte absolute -right-[4%] bottom-[2%] z-[1] size-[20%] overflow-hidden rounded-full border-[3px] border-white shadow-[0_10px_22px_rgb(0_0_0/.3)]" aria-hidden="true"><img src="<?= e($bulles[4]) ?>" alt="" class="h-full w-full object-cover"></span>
      <span class="anim-flotte-2 absolute bottom-[20%] -left-[10%] z-[1] size-[14%] overflow-hidden rounded-full border-[3px] border-turquoise shadow-[0_10px_22px_rgb(0_0_0/.3)]" aria-hidden="true"><img src="<?= e($bulles[3]) ?>" alt="" class="h-full w-full object-cover"></span>
      <div class="absolute inset-0 overflow-hidden rounded-full bg-vert shadow-[0_0_0_6px_rgb(255_255_255/.14)]" data-diapo="accueil">
        <?php foreach ($photos_accueil as $k => $src): ?><img data-diapo-image src="<?= e($src) ?>" alt="<?= $k ? '' : "Jeunes réunis lors d'un événement YouthConnekt" ?>" class="absolute inset-0 h-full w-full object-cover"<?= $k ? ' loading="lazy"' : ' fetchpriority="high"' ?>><?php endforeach ?>
      </div>
    </div>

    <div class="anim-flotte absolute bottom-[17%] left-0 z-10 hidden lg:block xl:-left-10 2xl:-left-24"><a href="#chapitre" class="btn btn-contour-blanc btn-relie btn-relie--droite min-h-13 w-[16rem] px-6 text-[17px]">Découvrir le chapitre</a></div>
    <div class="anim-flotte-2 absolute right-0 bottom-[17%] z-10 hidden lg:block xl:-right-10 2xl:-right-24"><a href="#rejoindre" class="btn btn-orange btn-relie btn-relie--gauche min-h-13 w-[16rem] px-6 text-[17px] shadow-lg shadow-orange/30">Let's Connekt</a></div>
  </div>
  <?php if (empty($p['image_accueil_1']) && empty($p['image_accueil_2']) && empty($p['image_accueil_3'])) partiel('partials/mention_photo', ['texte' => 'Photos provisoires · Sommet YouthConnekt Africa, hors Mali', 'position' => 'right-3 bottom-3 max-lg:top-2 max-lg:bottom-auto']) ?>
</section>

<?php /* 2. Le chapitre : présentation, chiffres clés, axes */ ?>
<section id="chapitre" class="relative scroll-mt-20 overflow-hidden py-12 sm:py-16 lg:py-18" aria-labelledby="titre-chapitre">
  <div class="absolute -top-24 -right-24 hidden size-80 rounded-full bg-turquoise/10 md:block" aria-hidden="true"></div>
  <div class="conteneur relative flex flex-col gap-5 sm:gap-8 lg:grid lg:grid-cols-2 lg:items-center lg:gap-16">
    <div class="contents lg:block">
      <div class="order-1"><p class="surtitre surtitre-trait">Le chapitre</p>
      <h2 id="titre-chapitre" class="titre-section">Le lieu où la jeunesse malienne se rencontre et avance</h2></div>
      <p class="order-2 text-[15px] leading-relaxed sm:text-lg lg:mt-5">YouthConnekt Mali accompagne les jeunes dans leurs projets, les implique dans la vie citoyenne et met en lumière celles et ceux qui font bouger le pays. Étudiants, jeunes professionnels, entrepreneurs et organisations s'y retrouvent pour apprendre, se rencontrer et agir ensemble.</p>
      <a href="<?= url('/a-propos') ?>" class="btn btn-vert survol-leve order-5 self-start px-6 lg:mt-7">Découvrir le chapitre</a>

      <?php if ($chiffres): ?>
      <dl class="order-4 grid grid-cols-3 divide-x divide-ligne border-y border-ligne py-3.5 sm:py-5 lg:mt-9">
        <?php foreach ($chiffres as $k => $chiffre): ?>
        <div class="<?= $k > 0 ? 'pl-3 sm:pl-6' : '' ?> pr-2">
          <dt class="sr-only"><?= e($chiffre['libelle']) ?></dt>
          <dd class="text-[1.4rem] font-bold text-vert tabular-nums sm:text-4xl" data-compteur><?= e($chiffre['valeur']) ?></dd>
          <dd class="mt-2 h-[3px] w-6 rounded-full <?= cycle(['bg-orange', 'bg-turquoise', 'bg-bleu'], $k) ?>" aria-hidden="true"></dd>
          <dd class="mt-1.5 text-[11.5px] leading-snug text-encre sm:mt-2 sm:text-sm"><?= e(majuscule_initiale($chiffre['libelle'])) ?></dd>
        </div>
        <?php endforeach ?>
      </dl>
      <?php endif ?>
    </div>

    <div class="relative order-3 mx-auto h-[220px] w-full max-w-sm sm:h-[min(520px,calc(100vh-200px))] sm:max-w-xl sm:min-h-[420px] lg:max-w-none" aria-hidden="true">
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
  <div class="conteneur relative mt-12 sm:mt-20 lg:mt-28">
    <p class="surtitre">Nos axes d'intervention</p>
    <h2 class="titre-section">Ce que fait le chapitre</h2>
    <ul class="mt-4 grid gap-2.5 sm:mt-8 sm:gap-5 <?= count($axes) === 2 ? 'sm:grid-cols-2 lg:mx-auto lg:max-w-5xl' : 'sm:grid-cols-3' ?>">
      <?php foreach ($axes as $k => $axe): ?>
      <li class="carte survol-leve survol-zoom survol-orange relative flex overflow-hidden rounded-[14px] border-[1.5px] sm:flex-col sm:rounded-2xl sm:border-2">
        <div class="relative w-[30%] shrink-0 border-r-4 sm:aspect-[16/8.5] sm:w-auto sm:border-r-0 sm:border-b-4 <?= bordure($axe['couleur']) ?>">
          <?php partiel('partials/image_axe', ['axe' => $axe]) ?>
          <span class="absolute top-1.5 left-1.5 grid size-[22px] place-items-center rounded-full bg-white/90 text-[10px] font-bold text-vert sm:top-3 sm:left-3 sm:size-9 sm:text-xs"><?= sprintf('%02d', $k + 1) ?></span>
        </div>
        <div class="flex min-w-0 flex-1 flex-col px-3.5 py-3 sm:p-6">
          <h3 class="text-[15px] leading-snug font-bold sm:text-2xl"><?= e($axe['nom']) ?></h3>
          <p class="mt-1 line-clamp-2 flex-1 text-[13px] leading-snug text-encre sm:mt-2 sm:line-clamp-none sm:text-[15px] sm:leading-relaxed"><?= e($axe['description']) ?></p>
          <a href="<?= url('/a-propos') ?>#axes" class="mt-2 self-start rounded-full bg-turquoise-clair px-2.5 py-1 text-xs font-bold text-vert transition-colors after:absolute after:inset-0 hover:bg-orange hover:text-white sm:mt-4 sm:border-2 sm:border-vert sm:bg-white sm:px-4 sm:py-1.5 sm:text-sm sm:hover:border-orange"><span class="sm:hidden">Lire</span><span class="hidden sm:inline">En savoir plus</span><span class="sr-only"> sur l'axe <?= e($axe['nom']) ?></span></a>
        </div>
      </li>
      <?php endforeach ?>
    </ul>
  </div>
  <?php endif ?>
</section>

<?php /* 3. Initiatives */ ?>
<?php if ($initiatives || $admin): ?>
<section class="bg-gris py-12 sm:py-16" aria-labelledby="titre-initiatives">
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
            <span class="etiquette bg-white text-vert <?= !$premiere ? 'max-lg:bg-transparent max-lg:p-0 max-lg:text-white/90' : '' ?> <?= $initiative['date_libelle'] ? '' : 'invisible' ?>"><?= e($initiative['date_libelle'] ?: 'Initiative') ?></span>
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

<?php /* 4. Mot du ministre : tient sur un écran d'ordinateur ; replié sur téléphone avec « Voir plus » */ ?>
<section class="py-10 sm:py-14 lg:py-16" aria-labelledby="titre-ministre">
  <div class="conteneur grid items-center gap-8 md:grid-cols-[minmax(0,4fr)_minmax(0,8fr)] lg:gap-14">
    <div class="mx-auto hidden w-full md:block md:max-w-[19rem]">
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
      <blockquote id="texte-ministre" class="repliable mt-5 space-y-2.5 text-base leading-relaxed text-vert sm:space-y-3 sm:text-lg lg:text-[1.15rem]" data-repliable>
        <?php if ($ministre_publie && trim((string) $mot['message']) !== ''): ?><?= paragraphes($mot['message']) ?>
        <?php elseif ($ministre_publie && trim((string) $mot['extrait']) !== ''): ?><p>« <?= e($mot['extrait']) ?> »</p>
        <?php else: ?>
        <p>« La jeunesse malienne est notre première richesse. Elle porte l'énergie, la créativité et l'ambition dont notre pays a besoin pour bâtir son avenir.</p>
        <p>Avec YouthConnekt Mali, nous voulons offrir à chaque jeune, où qu'il vive, un espace pour se former, accéder aux opportunités, entreprendre et s'engager dans la vie citoyenne.</p>
        <p>J'invite chaque jeune, chaque association et chaque entreprise à rejoindre le chapitre. Ensemble, faisons de cette plateforme un lieu d'échanges, de solidarité et de réussite pour le Mali. »</p>
        <?php endif ?>
      </blockquote>
      <?php partiel('partials/voir_plus', ['cible' => 'texte-ministre']) ?>
      <p class="mt-5 hidden border-l-[3px] border-orange pl-4 md:block">
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
<section class="py-12 sm:py-16" aria-labelledby="titre-opportunites">
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
      <a href="<?= url('/actualites') ?>" class="btn-tout-voir bg-white text-vert sm:hidden">Tout voir</a>
    </div>

    <?php if ($actualite_une): $une = $actualite_une; ?>
    <div class="mt-5 grid gap-2.5 sm:mt-8 sm:gap-4 lg:grid-cols-[minmax(0,1.55fr)_minmax(0,1fr)] lg:gap-5">
      <a href="<?= e($une['url']) ?>" class="survol-leve group relative block h-[200px] overflow-hidden rounded-[18px] border-[3px] border-white sm:aspect-[16/10] sm:h-auto sm:rounded-2xl sm:border-0 lg:aspect-auto lg:min-h-[440px]">
        <div class="absolute inset-0 transition-transform duration-500 group-hover:scale-105">
          <?php partiel('partials/photo', ['image' => $une['image']]) ?>
        </div>
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>
        <time datetime="<?= e(date_iso($une['date_affichee'])) ?>" class="absolute top-3 left-3 grid min-w-12 rounded-xl bg-white px-2 py-1.5 sm:top-4 sm:left-4 sm:min-w-14 sm:px-2.5 sm:py-2 text-center leading-none text-vert">
          <span class="text-xl font-bold sm:text-2xl"><?= e(jour($une['date_affichee'])) ?></span>
          <span class="mt-1 text-[11px] font-bold uppercase"><?= e(mois_pastille($une['date_affichee'])) ?></span>
        </time>
        <div class="absolute inset-x-0 bottom-0 p-4 sm:p-7">
          <span class="etiquette bg-orange tracking-wide text-white uppercase">À la une<?= $une['categorie_libelle'] !== '' ? ' · ' . e($une['categorie_libelle']) : '' ?></span>
          <h3 class="mt-2 line-clamp-2 text-lg leading-tight font-bold text-white sm:mt-3 sm:text-3xl"><?= e($une['titre']) ?></h3>
          <p class="mt-2 line-clamp-2 hidden text-sm text-white/90 sm:block sm:text-base"><?= e($une['chapeau']) ?></p>
        </div>
      </a>

      <?php if ($actualites): ?>
      <ul class="grid gap-2.5 sm:gap-4">
        <?php foreach ($actualites as $actu): ?>
        <li>
          <a href="<?= e($actu['url']) ?>" class="survol-leve survol-zoom group flex h-full items-center gap-3 rounded-[14px] bg-white p-2 sm:gap-4 sm:rounded-2xl sm:p-4">
            <div class="size-14 shrink-0 overflow-hidden rounded-xl sm:size-24">
              <?php partiel('partials/photo', ['image' => $actu['image']]) ?>
            </div>
            <div class="min-w-0">
              <p class="text-[11px] font-bold tracking-[0.12em] text-vert uppercase sm:text-xs"><?= $actu['categorie_libelle'] !== '' ? e($actu['categorie_libelle']) . ' · ' : '' ?><time datetime="<?= e(date_iso($actu['date_affichee'])) ?>"><?= e(date_courte($actu['date_affichee'])) ?></time></p>
              <h3 class="mt-1 line-clamp-2 text-[14px] leading-snug font-bold text-encre group-hover:text-orange sm:text-base"><?= e($actu['titre']) ?></h3>
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
<?php partiel('partials/partenaires', ['partenaires' => $partenaires]) ?>

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
      <div class="flex flex-col justify-center border-[1.5px] border-t-0 border-ligne bg-white p-5 sm:border-0 sm:p-10">
        <ul class="grid gap-3 text-sm text-encre sm:gap-4 sm:text-[15px]">
          <?php if ($p['email_contact']): ?>
          <li class="flex items-center gap-3">
            <span class="grid size-9 shrink-0 place-items-center rounded-full bg-gris text-vert sm:size-10" aria-hidden="true"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg></span>
            <a href="mailto:<?= e($p['email_contact']) ?>" class="break-all hover:text-orange"><?= e($p['email_contact']) ?></a>
          </li>
          <?php endif ?>
          <?php if ($p['telephone']): ?>
          <li class="flex items-center gap-3">
            <span class="grid size-9 shrink-0 place-items-center rounded-full bg-gris text-vert sm:size-10" aria-hidden="true"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2"/></svg></span>
            <a href="tel:<?= e(str_replace(' ', '', $p['telephone'])) ?>" class="hover:text-orange"><?= e($p['telephone']) ?></a>
          </li>
          <?php endif ?>
          <li class="flex items-center gap-3">
            <span class="grid size-9 shrink-0 place-items-center rounded-full bg-gris text-vert sm:size-10" aria-hidden="true"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21s7-6.2 7-12a7 7 0 0 0-14 0c0 5.8 7 12 7 12z"/><circle cx="12" cy="9" r="2.5"/></svg></span>
            <?= e($p['adresse'] ?: 'Bamako, Mali') ?>
          </li>
          <?php if ($p['horaires']): ?>
          <li class="flex items-center gap-3">
            <span class="grid size-9 shrink-0 place-items-center rounded-full bg-gris text-vert sm:size-10" aria-hidden="true"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></span>
            <?= e($p['horaires']) ?>
          </li>
          <?php endif ?>
        </ul>
        <?php if (reseaux_sociaux()): ?>
        <p class="mt-6 text-xs font-bold tracking-[0.14em] text-vert uppercase sm:mt-7">Suivez le chapitre</p>
        <?php partiel('partials/reseaux') ?>
        <?php endif ?>
      </div>
    </div>
  </div>
</section>
