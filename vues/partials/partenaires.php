<?php
/* Section « Nos partenaires », en deux familles : institutionnels et techniques, puis secteur privé.
   Paramètres : $partenaires (lignes actives), $fond (classe du fond) */
$familles = ['institutionnel' => [], 'prive' => []];
foreach ($partenaires as $partenaire) {
    $familles[($partenaire['categorie'] ?? '') === 'prive' ? 'prive' : 'institutionnel'][] = $partenaire;
}
// Tant qu'aucun partenaire n'est saisi dans l'administration, seuls les partenaires connus sont affichés
if (!$partenaires) {
    $familles['institutionnel'] = array_map(fn ($nom) => ['nom' => $nom, 'logo' => '', 'lien' => ''], ['Ministère de la Jeunesse et des Sports', 'PNUD Mali', 'UNFPA Mali', 'Bureau du Coordonnateur résident des Nations unies']);
}
$titres = [
    'institutionnel' => ['Partenaires institutionnels et techniques', 'border-vert'],
    'prive' => ['Partenaires du secteur privé', 'border-orange'],
];
?>
<section class="relative overflow-hidden <?= e($fond ?? 'bg-gris') ?> py-12 sm:py-20" aria-labelledby="titre-partenaires">
  <span class="absolute -bottom-40 -left-36 size-52 rounded-full bg-turquoise-clair sm:size-96" aria-hidden="true"></span>
  <span class="absolute -top-32 -right-24 size-40 rounded-full bg-orange-clair sm:size-72" aria-hidden="true"></span>
  <div class="conteneur relative">
    <div class="max-w-2xl">
      <p class="surtitre">Partenaires</p>
      <h2 id="titre-partenaires" class="titre-section">Nos partenaires</h2>
      <p class="mt-3 text-[15px] sm:mt-4 sm:text-lg">Ils soutiennent YouthConnekt Mali et organisent avec lui initiatives et événements.</p>
    </div>
    <div class="mt-8 space-y-4 sm:mt-10 sm:space-y-5">
      <?php foreach ($familles as $cle => $liste): if (!$liste) continue; [$titre_famille, $bord] = $titres[$cle]; ?>
      <div class="grid gap-4 rounded-[20px] bg-white/80 p-4 shadow-sm sm:p-6 lg:grid-cols-[15rem_minmax(0,1fr)] lg:items-center lg:gap-8">
        <h3 class="border-l-4 <?= $bord ?> pl-3 text-base leading-snug font-bold text-vert sm:text-lg"><?= e($titre_famille) ?></h3>
        <ul class="grid grid-cols-2 gap-3 sm:flex sm:flex-wrap sm:gap-4 lg:border-l lg:border-ligne lg:pl-8">
          <?php foreach ($liste as $partenaire): $balise = $partenaire['lien'] ? 'a href="' . e($partenaire['lien']) . '" target="_blank" rel="noopener"' : 'div'; ?>
          <li class="sm:w-44">
            <<?= $balise ?> class="survol-leve flex h-20 items-center justify-center rounded-[14px] border border-ligne bg-white p-3 text-center sm:h-24" title="<?= e($partenaire['nom']) ?>">
              <?php if ($partenaire['logo']): ?><img src="<?= e(media($partenaire['logo'])) ?>" alt="<?= e($partenaire['nom']) ?>" class="max-h-full max-w-full object-contain" loading="lazy"><?php else: ?><span class="text-[13px] leading-tight font-bold text-vert sm:text-sm"><?= e($partenaire['nom']) ?></span><?php endif ?>
            </<?= $partenaire['lien'] ? 'a' : 'div' ?>>
          </li>
          <?php endforeach ?>
        </ul>
      </div>
      <?php endforeach ?>
    </div>
  </div>
</section>
