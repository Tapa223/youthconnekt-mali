<?php
/** Page Opportunités : recherche, choix du type (liste gérée dans l'administration), cartes triées par date limite */
$nom_type = $type_actif ? ($types[$type_actif]['nom'] ?? '') : '';
$total_ouvertes = array_sum($nombres);
?>
<section class="relative isolate overflow-hidden bg-vert text-white">
  <?php partiel('partials/reseau_deco') ?>
  <div class="conteneur anim-entree relative pt-4 pb-4 sm:pt-8 sm:pb-6">
    <h1 class="text-[1.75rem] leading-none font-bold text-white sm:text-5xl">Opportunités</h1>
    <p class="mt-2 max-w-lg text-sm text-white/90 sm:mt-3 sm:text-base">Formations, appels à candidatures, bourses, stages et événements relayés par le chapitre.</p>
  </div>
</section>

<?php /* Recherche et type : restent visibles en haut de l'écran pendant qu'on fait défiler */ ?>
<div class="sticky top-16 z-30 border-b border-ligne/70 bg-gris/95 backdrop-blur">
  <form method="get" action="<?= url('/opportunites') ?>" class="conteneur flex gap-2 py-2.5 sm:py-3" role="search">
    <?php if ($type_actif): ?><input type="hidden" name="type" value="<?= e($type_actif) ?>"><?php endif ?>
    <label for="recherche" class="sr-only">Rechercher une opportunité</label>
    <div class="relative min-w-0 flex-1 sm:max-w-md">
      <input id="recherche" name="q" type="search" value="<?= e($recherche) ?>" placeholder="Rechercher…" maxlength="100"
             class="min-h-11 w-full rounded-full border border-ligne bg-white pr-12 pl-4 text-[15px] text-encre placeholder:text-texte/70 focus:border-vert focus:ring-[3px] focus:ring-orange/35 focus:outline-none">
      <button type="submit" class="absolute top-1/2 right-1 grid size-9 -translate-y-1/2 place-items-center rounded-full bg-orange text-white transition-colors hover:bg-vert" aria-label="Rechercher">
        <svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
      </button>
    </div>
    <details class="choix-type relative shrink-0" data-choix-type>
      <summary class="flex min-h-11 cursor-pointer list-none items-center gap-2 rounded-full border-2 px-4 text-sm font-bold transition-colors [&::-webkit-details-marker]:hidden <?= $type_actif ? 'border-orange bg-orange text-white' : 'border-vert bg-white text-vert hover:bg-vert-clair' ?>">
        <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M4 6h16M7 12h10M10 18h4"/></svg>
        <span class="max-w-[9rem] truncate sm:max-w-none"><?= $type_actif ? e($nom_type) : 'Type' ?></span>
        <svg class="choix-fleche size-4 shrink-0 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
      </summary>
      <ul class="absolute right-0 z-40 mt-2 max-h-[60vh] w-64 overflow-y-auto rounded-2xl border border-ligne bg-white p-1.5 shadow-2xl shadow-vert/15">
        <li><a href="<?= url('/opportunites', ['q' => $recherche]) ?>" class="flex min-h-11 items-center justify-between gap-3 rounded-xl px-3 text-[15px] font-bold <?= !$type_actif ? 'bg-orange text-white' : 'text-encre hover:bg-gris' ?>">Tous les types <span class="text-sm opacity-75"><?= $total_ouvertes ?></span></a></li>
        <?php foreach ($types as $slug_type => $t): $actif = $type_actif === $slug_type; ?>
        <li><a href="<?= url('/opportunites', ['type' => $slug_type, 'q' => $recherche]) ?>" class="flex min-h-11 items-center justify-between gap-3 rounded-xl px-3 text-[15px] font-bold <?= $actif ? 'bg-orange text-white' : 'text-encre hover:bg-gris' ?>">
          <span class="flex items-center gap-2.5"><span class="size-2.5 shrink-0 rounded-full <?= $actif ? 'bg-white' : fond($t['couleur']) ?>" aria-hidden="true"></span><?= e($t['nom']) ?></span>
          <span class="text-sm opacity-75"><?= $nombres[$slug_type] ?? 0 ?></span>
        </a></li>
        <?php endforeach ?>
      </ul>
    </details>
  </form>
</div>

<section class="bg-gris pt-4 pb-10 sm:pt-6 sm:pb-14" aria-labelledby="titre-resultats">
  <div class="conteneur">
    <h2 id="titre-resultats" class="sr-only">Liste des opportunités</h2>
    <?php if ($recherche !== '' || $type_actif): ?><p class="mb-3 text-sm sm:mb-5">Résultats<?= $type_actif ? ' · <strong class="text-vert">' . e($nom_type) . '</strong>' : '' ?><?= $recherche !== '' ? ' pour « <strong class="text-vert">' . e($recherche) . '</strong> »' : '' ?> <a href="<?= url('/opportunites') ?>" class="btn-discret ml-1">Tout afficher</a></p><?php endif ?>
    <?php if ($liste): ?>
    <ul class="grid grid-cols-2 gap-2.5 sm:gap-5 lg:grid-cols-3">
      <?php foreach ($liste as $o): ?><li><?php partiel('cartes/opportunite', ['o' => $o]) ?></li><?php endforeach ?>
    </ul>
    <?php partiel('partials/pagination', ['pagination' => $pagination]) ?>
    <?php elseif ($recherche !== '' || $type_actif): ?>
      <?php partiel('partials/vide', ['message' => 'Aucune opportunité ouverte ne correspond à votre recherche.']) ?>
    <?php else: ?>
      <?php partiel('partials/vide', ['message' => 'Aucune opportunité ouverte pour le moment. Abonnez-vous à la newsletter pour être prévenu des prochaines.', 'lien_admin' => url('/gestion/opportunites/ajouter')]) ?>
    <?php endif ?>
  </div>
</section>

<section class="conteneur py-10 sm:py-16">
  <div class="relative overflow-hidden rounded-3xl bg-orange px-5 py-6 text-white sm:px-10 sm:py-10">
    <svg class="pointer-events-none absolute top-0 right-[22%] hidden h-full w-56 md:block" viewBox="0 0 220 140" aria-hidden="true">
      <g stroke="rgb(255 255 255 / .55)" stroke-width="1.2" fill="none"><path d="M20 130 L100 20 L190 80"/></g>
      <circle cx="100" cy="20" r="8" fill="#035E4D"/><circle cx="20" cy="130" r="6" fill="#fff"/><circle cx="190" cy="80" r="6" fill="#fff"/>
    </svg>
    <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h2 class="text-xl font-bold text-white sm:text-3xl">Vous avez une opportunité à partager ?</h2>
        <p class="mt-1 max-w-xl text-white/90">Formation, bourse, stage, appel à candidatures : proposez-la au chapitre. Elle sera publiée après validation par l'équipe.</p>
      </div>
      <a href="<?= url('/compte/connexion', ['suivant' => url('/opportunites') . '?proposer=1']) ?>" class="btn self-start border-white bg-white text-vert hover:border-vert hover:bg-vert hover:text-white sm:self-auto" data-ouvrir-dialogue="dialogue-opportunite">Proposer une opportunité</a>
    </div>
  </div>
</section>

<?php partiel('partials/proposer_opportunite', ['types' => $types]) ?>
