<?php /* Section « Nos partenaires ». Paramètres : $partenaires (lignes actives), $fond (classe du fond) */ ?>
<section class="relative overflow-hidden <?= e($fond ?? 'bg-gris') ?> py-12 sm:py-20" aria-labelledby="titre-partenaires">
  <span class="absolute -bottom-40 -left-36 size-52 rounded-full bg-turquoise-clair sm:size-96" aria-hidden="true"></span>
  <span class="absolute -top-32 -right-24 size-40 rounded-full bg-orange-clair sm:size-72" aria-hidden="true"></span>
  <div class="conteneur relative grid gap-6 sm:gap-10 lg:grid-cols-[minmax(0,4fr)_minmax(0,8fr)] lg:items-center">
    <div>
      <p class="surtitre">Partenaires</p>
      <h2 id="titre-partenaires" class="titre-section whitespace-nowrap">Nos partenaires</h2>
      <p class="mt-3 text-[15px] sm:mt-4 sm:text-lg">Ils soutiennent YouthConnekt Mali et organisent avec lui initiatives et événements.</p>
    </div>
    <?php $bords = ['border-vert', 'border-orange', 'border-turquoise', 'border-bleu'];
      // Tant qu'aucun logo n'est saisi dans l'administration, seuls les partenaires connus sont affichés
      $liste_partenaires = $partenaires ?: array_map(fn ($nom) => ['nom' => $nom, 'logo' => '', 'lien' => ''], ['PNUD', 'Ministère de la Jeunesse et des Sports']); ?>
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
