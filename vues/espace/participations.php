<?php ob_start();
/* Espace membre : mes inscriptions et candidatures aux initiatives du chapitre. */
$couleurs = ['inscrit' => 'bg-vert-clair text-vert', 'en_attente' => 'bg-orange/15 text-orange-fonce', 'retenue' => 'bg-vert-clair text-vert', 'non_retenue' => 'bg-gris text-texte'];
?>
<section class="bloc">
  <h1 class="text-xl font-bold max-lg:sr-only sm:text-3xl">Mes participations</h1>
  <?php if ($participations): ?>
  <ul class="space-y-3 lg:mt-4">
    <?php foreach ($participations as $p): ?>
    <li class="rounded-xl border border-ligne p-3.5 sm:p-4">
      <div class="flex flex-wrap items-start justify-between gap-2">
        <div class="min-w-0">
          <a href="<?= url('/initiatives/' . $p['slug']) ?>" class="font-bold text-vert hover:text-orange"><?= e($p['titre']) ?></a>
          <p class="text-sm">Comme « <?= e($p['role_libelle']) ?> »<?= $p['date_libelle'] ? ' · ' . e($p['date_libelle']) : '' ?></p>
        </div>
        <span class="rounded-full px-2.5 py-1 text-xs font-bold <?= $couleurs[$p['statut']] ?? '' ?>"><?= e(STATUTS_PARTICIPATION[$p['statut']] ?? '') ?><?= $p['present_le'] ? ' · Présent' : '' ?></span>
      </div>
      <div class="mt-2 flex flex-wrap items-center justify-between gap-2">
        <p class="text-xs text-texte"><?= [
            'inscrit' => 'Inscrit le ' . e(date_longue($p['cree_le'])) . '. Présentez votre billet à l\'entrée.',
            'retenue' => 'Vous êtes retenu : présentez votre billet à l\'entrée. L\'équipe vous contactera pour la suite.',
            'non_retenue' => "Votre candidature n'a pas été retenue cette fois. Merci pour votre engagement.",
        ][$p['statut']] ?? 'Envoyée le ' . e(date_longue($p['cree_le'])) . ". L'équipe l'étudie." ?></p>
        <?php if (billet_valable($p)): ?><a href="<?= url('/compte/mon-espace/participations/' . $p['id'] . '/billet') ?>" class="btn btn-orange min-h-10 px-4 text-sm">Mon billet</a><?php endif ?>
      </div>
    </li>
    <?php endforeach ?>
  </ul>
  <?php else: ?>
  <p class="text-sm text-encre lg:mt-3">Vous ne participez encore à aucune initiative.</p>
  <a href="<?= url('/initiatives') ?>" class="btn-lien mt-3 min-h-10">Voir les initiatives</a>
  <?php endif ?>
</section>
<?php $espace = ob_get_clean(); partiel('espace/base', get_defined_vars());
