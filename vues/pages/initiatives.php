<section class="relative overflow-hidden bg-gris">
  <svg class="pointer-events-none absolute top-0 right-0 hidden h-full w-1/2 md:block" viewBox="0 0 600 300" preserveAspectRatio="xMaxYMin meet" aria-hidden="true">
    <g stroke="#04B2B1" stroke-opacity=".35" stroke-width="1.5" fill="none"><path d="M300 210 L420 70 L560 150"/><path d="M420 70 L520 0"/><path d="M560 150 L600 20"/></g>
    <circle cx="300" cy="210" r="9" fill="#F1592A"/><circle cx="420" cy="70" r="12" fill="#04B2B1"/><circle cx="560" cy="150" r="8" fill="#0358D9"/>
  </svg>
  <div class="conteneur anim-entree relative pt-4 pb-3 sm:pt-5 sm:pb-4">
    <h1 class="text-[1.75rem] leading-none font-bold sm:text-4xl">Nos initiatives</h1>
    <p class="mt-2 max-w-xl text-sm sm:text-base">Les projets du chapitre et de ses membres, auxquels vous pouvez prendre part.</p>
  </div>
</section>

<?php /* Filtres par état : affichés seulement s'il y a au moins deux états différents (sinon ils ne servent à rien) */
$etats_presents = array_filter(array_intersect_key($nombres, ETATS_INITIATIVE));
if (count($etats_presents) >= 2 || $etat_actif): ?>
<div class="sticky top-16 z-30 border-b border-ligne/70 bg-gris/95 backdrop-blur">
  <nav class="conteneur flex gap-1.5 overflow-x-auto py-2.5 [scrollbar-width:none] sm:flex-wrap sm:gap-2" aria-label="Filtrer les initiatives">
    <?php foreach (['' => 'Toutes'] + ETATS_INITIATIVE as $cle => $libelle):
        if ($cle !== '' && empty($nombres[$cle])) continue; // un état sans initiative n'apparaît pas
        $actif = $etat_actif === $cle;
        $libelle = ['terminee' => 'Passées'][$cle] ?? $libelle; ?>
    <a href="<?= url('/initiatives', ['etat' => $cle ?: null]) ?>" class="survol-orange inline-flex min-h-9 shrink-0 items-center justify-center gap-1.5 rounded-full border-2 px-3 text-[13px] font-bold whitespace-nowrap transition-colors sm:min-h-10 sm:px-4 sm:text-sm <?= $actif ? 'border-orange bg-orange text-white' : 'border-vert bg-white text-vert hover:bg-vert-clair' ?>" <?= $actif ? 'aria-current="true"' : '' ?>><?= e($libelle) ?> <span class="opacity-70">(<?= $nombres[$cle] ?? 0 ?>)</span></a>
    <?php endforeach ?>
  </nav>
</div>
<?php endif ?>

<section class="conteneur pt-4 pb-10 sm:pt-5 sm:pb-14" aria-labelledby="titre-liste">
  <h2 id="titre-liste" class="sr-only">Liste des initiatives</h2>
  <?php if ($liste): ?>
  <ul class="grid grid-cols-2 gap-2.5 sm:gap-5 lg:grid-cols-3">
    <?php foreach ($liste as $i): ?><li><?php partiel('cartes/initiative', ['i' => $i, 'compacte_tel' => true]) ?></li><?php endforeach ?>
  </ul>
  <?php partiel('partials/pagination', ['pagination' => $pagination]) ?>
  <?php else: partiel('partials/vide', ['message' => 'Les prochaines initiatives du chapitre seront annoncées ici. Vous pourrez y prendre part.', 'lien_admin' => url('/gestion/initiatives/ajouter')]); endif ?>
</section>

<section class="conteneur pb-12 sm:pb-20">
  <div class="relative overflow-hidden rounded-3xl bg-orange px-5 py-6 text-white sm:px-10 sm:py-10">
    <svg class="pointer-events-none absolute top-0 right-[22%] hidden h-full w-56 md:block" viewBox="0 0 220 140" aria-hidden="true">
      <g stroke="rgb(255 255 255 / .55)" stroke-width="1.2" fill="none"><path d="M20 130 L100 20 L190 80"/></g>
      <circle cx="100" cy="20" r="8" fill="#035E4D"/><circle cx="20" cy="130" r="6" fill="#fff"/><circle cx="190" cy="80" r="6" fill="#fff"/>
    </svg>
    <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h2 class="text-xl font-bold text-white sm:text-3xl">Vous portez une initiative ?</h2>
        <p class="mt-1 text-white/90">Parlez-nous de votre initiative : l'équipe du chapitre peut la faire connaître et vous accompagner.</p>
      </div>
      <a href="<?= url('/contact', ['sujet' => 'initiative']) ?>" class="btn self-start border-white bg-white text-vert hover:border-vert hover:bg-vert hover:text-white sm:self-auto" data-ouvrir-dialogue="dialogue-initiative">Parler de mon initiative</a>
    </div>
  </div>
</section>

<?php partiel('partials/proposer_initiative') ?>
