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
  <h1 class="text-xl font-bold sm:text-3xl">Mes propositions</h1>
  <p class="mt-1 text-sm">Partagez une opportunité ou présentez votre initiative : l'équipe du chapitre la relit, puis vous répond.</p>
  <div class="mt-4 grid gap-2.5 sm:grid-cols-2">
    <button type="button" class="group flex items-center gap-3 rounded-2xl bg-vert p-4 text-left text-white transition-colors hover:bg-vert-fonce" data-ouvrir-dialogue="dialogue-opportunite">
      <span class="grid size-11 shrink-0 place-items-center rounded-full bg-white/15" aria-hidden="true"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M12 11v5M9.5 13.5h5"/></svg></span>
      <span><span class="block font-bold">Proposer une opportunité</span><span class="text-sm text-white/85">Formation, bourse, stage, concours…</span></span>
    </button>
    <button type="button" class="group flex items-center gap-3 rounded-2xl bg-orange p-4 text-left text-white transition-colors hover:bg-orange-fonce" data-ouvrir-dialogue="dialogue-initiative">
      <span class="grid size-11 shrink-0 place-items-center rounded-full bg-white/15" aria-hidden="true"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 21V4M5 4h11l-2 4 2 4H5"/></svg></span>
      <span><span class="block font-bold">Parler de mon initiative</span><span class="text-sm text-white/85">Un projet que vous portez près de chez vous</span></span>
    </button>
  </div>
</section>

<section class="bloc" aria-labelledby="titre-mes-opp">
  <h2 id="titre-mes-opp" class="text-lg font-bold sm:text-xl">Opportunités proposées</h2>
  <?php if ($propositions): ?>
  <ul class="mt-3 space-y-2.5">
    <?php foreach ($propositions as $o): [$libelle, $classes, $explication] = $etats[$o['statut']] ?? $etats['brouillon']; $en_ligne = $o['statut'] === 'publie' && $o['publie_le'] <= maintenant(); ?>
    <li class="flex flex-wrap items-center gap-x-4 gap-y-2 rounded-xl border border-ligne p-3.5 sm:p-4">
      <div class="min-w-0 flex-1">
        <p class="text-[11px] font-bold tracking-[0.1em] uppercase <?= texte_couleur($o['couleur']) ?>"><?= e($o['type_libelle']) ?></p>
        <?php if ($en_ligne): ?><a href="<?= e($o['url']) ?>" class="font-bold text-vert hover:text-orange"><?= e($o['titre']) ?></a>
        <?php else: ?><p class="font-bold text-encre"><?= e($o['titre']) ?></p><?php endif ?>
        <p class="text-xs">Proposée le <?= e(date_longue($o['cree_le'])) ?> · <?= e($explication) ?></p>
      </div>
      <span class="rounded-full px-3 py-1 text-xs font-bold <?= $classes ?>"><?= $libelle ?></span>
    </li>
    <?php endforeach ?>
  </ul>
  <?php else: ?><p class="mt-2 text-sm text-texte">Vous n'avez pas encore proposé d'opportunité.</p><?php endif ?>
</section>

<section class="bloc" aria-labelledby="titre-mes-init">
  <h2 id="titre-mes-init" class="text-lg font-bold sm:text-xl">Initiatives présentées</h2>
  <?php if ($initiatives): ?>
  <ul class="mt-3 space-y-2.5">
    <?php foreach ($initiatives as $m): $nom_init = trim(preg_replace("/^Proposition d'initiative\s*:?\s*/u", '', $m['objet'])); ?>
    <li class="flex flex-wrap items-center gap-x-4 gap-y-2 rounded-xl border border-ligne p-3.5 sm:p-4">
      <div class="min-w-0 flex-1">
        <p class="font-bold text-encre"><?= e($nom_init !== '' ? $nom_init : 'Initiative sans nom') ?></p>
        <p class="text-xs">Envoyée le <?= e(date_longue($m['recu_le'])) ?> · <?= $m['traite'] ? "L'équipe l'a prise en compte." : "L'équipe vous recontactera par e-mail." ?></p>
      </div>
      <span class="rounded-full px-3 py-1 text-xs font-bold <?= $m['traite'] ? 'bg-vert-clair text-vert' : 'bg-orange/15 text-orange-fonce' ?>"><?= $m['traite'] ? 'Prise en compte' : 'Envoyée' ?></span>
    </li>
    <?php endforeach ?>
  </ul>
  <?php else: ?><p class="mt-2 text-sm text-texte">Vous n'avez pas encore présenté d'initiative.</p><?php endif ?>
</section>
<?php partiel('partials/proposer_opportunite', ['types' => $types]) ?>
<?php partiel('partials/proposer_initiative') ?>
<?php $espace = ob_get_clean(); partiel('espace/base', get_defined_vars());
