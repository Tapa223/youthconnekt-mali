<?php ob_start();
/*
 * Mes propositions : le membre propose une opportunité ou présente une initiative sans quitter son espace,
 * puis suit ce qu'il a envoyé.
 */
$etats = [
    'propose' => ['À valider', 'bg-orange/15 text-orange-fonce', "L'équipe la relit avant de la publier."],
    'publie' => ['Publiée', 'bg-vert-clair text-vert', 'Visible sur la page Opportunités.'],
    'brouillon' => ['Non publiée', 'bg-gris text-texte', "L'équipe ne l'a pas publiée pour le moment."],
];
?>
<span data-recharger-apres-envoi hidden></span>
<section class="bloc">
  <h1 class="text-xl font-bold max-lg:sr-only sm:text-3xl">Mes propositions</h1>
  <p class="text-sm text-encre lg:mt-1">L'équipe du chapitre relit chaque proposition, puis vous répond.</p>
  <div class="mt-3 grid grid-cols-2 gap-2">
    <button type="button" class="btn btn-vert min-h-11 px-3 text-[13px] leading-tight whitespace-normal sm:text-sm" data-ouvrir-dialogue="dialogue-opportunite">Proposer une opportunité</button>
    <button type="button" class="btn btn-orange min-h-11 px-3 text-[13px] leading-tight whitespace-normal sm:text-sm" data-ouvrir-dialogue="dialogue-initiative">Parler de mon initiative</button>
  </div>
</section>

<section class="bloc" aria-labelledby="titre-mes-opp">
  <h2 id="titre-mes-opp" class="text-lg font-bold sm:text-xl">Opportunités proposées</h2>
  <?php if ($propositions): ?>
  <ul class="mt-3 space-y-2.5">
    <?php foreach ($propositions as $o): [$libelle, $classes, $explication] = $etats[$o['statut']] ?? $etats['brouillon']; $en_ligne = $o['statut'] === 'publie' && $o['publie_le'] <= maintenant(); ?>
    <li class="flex items-center gap-3 rounded-xl border border-ligne p-3 sm:p-4">
      <div class="min-w-0 flex-1">
        <p class="text-[11px] font-bold tracking-[0.1em] uppercase <?= texte_couleur($o['couleur']) ?>"><?= e($o['type_libelle']) ?></p>
        <?php if ($en_ligne): ?><a href="<?= e($o['url']) ?>" class="font-bold text-vert hover:text-orange"><?= e($o['titre']) ?></a>
        <?php else: ?><p class="font-bold text-encre"><?= e($o['titre']) ?></p><?php endif ?>
        <p class="text-xs">Proposée le <?= e(date_longue($o['cree_le'])) ?></p>
      </div>
      <span class="rounded-full px-3 py-1 text-xs font-bold <?= $classes ?>"><?= $libelle ?></span>
    </li>
    <?php endforeach ?>
  </ul>
  <?php else: ?><p class="mt-2 text-sm text-encre">Aucune pour le moment.</p><?php endif ?>
</section>

<section class="bloc" aria-labelledby="titre-mes-init">
  <h2 id="titre-mes-init" class="text-lg font-bold sm:text-xl">Initiatives présentées</h2>
  <?php if ($initiatives): ?>
  <ul class="mt-3 space-y-2.5">
    <?php foreach ($initiatives as $m): $nom_init = trim(preg_replace("/^Proposition d'initiative\s*:?\s*/u", '', $m['objet'])); ?>
    <li class="flex items-center gap-3 rounded-xl border border-ligne p-3 sm:p-4">
      <div class="min-w-0 flex-1">
        <p class="font-bold text-encre"><?= e($nom_init !== '' ? $nom_init : 'Initiative sans nom') ?></p>
        <p class="text-xs">Envoyée le <?= e(date_longue($m['recu_le'])) ?></p>
      </div>
      <span class="rounded-full px-3 py-1 text-xs font-bold <?= $m['traite'] ? 'bg-vert-clair text-vert' : 'bg-orange/15 text-orange-fonce' ?>"><?= $m['traite'] ? 'Prise en compte' : 'Envoyée' ?></span>
    </li>
    <?php endforeach ?>
  </ul>
  <?php else: ?><p class="mt-2 text-sm text-encre">Aucune pour le moment.</p><?php endif ?>
</section>
<?php partiel('partials/proposer_opportunite', ['types' => $types]) ?>
<?php partiel('partials/proposer_initiative') ?>
<?php $espace = ob_get_clean(); partiel('espace/base', get_defined_vars());
