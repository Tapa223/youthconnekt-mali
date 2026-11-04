<?php ob_start();
/* Mes propositions : les opportunités que le membre a proposées et leur état. */
$etats = [
    'propose' => ['À valider', 'bg-orange/15 text-orange-fonce', "L'équipe du chapitre la relit avant de la publier."],
    'publie' => ['Publiée', 'bg-vert-clair text-vert', 'Elle est visible sur la page Opportunités.'],
    'brouillon' => ['Non publiée', 'bg-gris text-texte', "L'équipe ne l'a pas publiée pour le moment."],
];
?>
<section class="bloc">
  <div class="flex items-center justify-between gap-3">
    <h1 class="text-2xl font-bold sm:text-3xl">Mes propositions</h1>
    <a href="<?= url('/opportunites') ?>?proposer=1" class="btn-lien min-h-9 px-3.5 text-[13px]">Proposer</a>
  </div>
  <p class="mt-1 text-sm">Les opportunités que vous avez partagées avec le chapitre et où elles en sont.</p>
  <?php if ($propositions): ?>
  <ul class="mt-5 space-y-2.5">
    <?php foreach ($propositions as $o): [$libelle, $classes, $explication] = $etats[$o['statut']] ?? $etats['brouillon']; $visible = $o['statut'] === 'publie' && $o['publie_le'] <= maintenant(); ?>
    <li class="flex flex-wrap items-center gap-x-4 gap-y-2 rounded-xl border border-ligne p-3.5 sm:p-4">
      <div class="min-w-0 flex-1">
        <p class="text-[11px] font-bold tracking-[0.1em] uppercase <?= texte_couleur($o['couleur']) ?>"><?= e($o['type_libelle']) ?></p>
        <?php if ($visible): ?><a href="<?= e($o['url']) ?>" class="font-bold text-vert hover:text-orange"><?= e($o['titre']) ?></a>
        <?php else: ?><p class="font-bold text-encre"><?= e($o['titre']) ?></p><?php endif ?>
        <p class="text-xs">Proposée le <?= e(date_longue($o['cree_le'])) ?> · <?= e($explication) ?></p>
      </div>
      <span class="rounded-full px-3 py-1 text-xs font-bold <?= $classes ?>"><?= $libelle ?></span>
    </li>
    <?php endforeach ?>
  </ul>
  <?php else: ?>
  <p class="mt-5 rounded-xl bg-gris p-4 text-sm sm:p-5 sm:text-base">Vous n'avez encore rien proposé. Une formation, une bourse ou un stage à faire connaître ? Partagez-le : l'équipe le relit puis le publie.</p>
  <?php endif ?>
</section>
<?php $espace = ob_get_clean(); partiel('espace/base', get_defined_vars());
