<?php ob_start();
/* Ma fiche organisation : ce que voient les visiteurs dans l'annuaire, et comment la rendre plus complète. */
[$pourcent, $points] = $etat;
$dans = !array_key_exists('dans_annuaire', $p) || !empty($p['dans_annuaire']);
$coche = '<svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12l5 5 9-10"/></svg>';
$modifier = url('/compte/mon-espace/profil', ['modifier' => 1]) . '#modifier-profil';
?>
<?php /* La fiche telle qu'elle apparaît dans l'annuaire */ ?>
<section class="bloc">
  <h1 class="text-xl font-bold max-lg:sr-only sm:text-3xl">Ma fiche organisation</h1>
  <p class="text-sm text-encre lg:mt-1">Votre fiche présente votre structure aux jeunes et aux partenaires dans l'annuaire « Organisations du réseau ».</p>
  <div class="mt-4 flex items-start gap-3.5 rounded-2xl border border-ligne p-4">
    <?php partiel('partials/logo_organisation', ['o' => $p + ['nom_structure' => $u['nom_affiche']], 'classes' => 'size-14 sm:size-16']) ?>
    <div class="min-w-0 flex-1">
      <p class="font-bold text-vert sm:text-lg"><?= e($p['nom_structure'] ?? $u['nom_affiche']) ?></p>
      <p class="text-[13px] text-texte"><?= e(implode(' · ', array_filter([TYPES_STRUCTURE[$p['type_structure'] ?? ''] ?? '', $p['secteur'] ?? '', REGIONS[$p['region'] ?? ''] ?? '']))) ?></p>
      <?php partiel('partials/badges_organisation', ['o' => $p, 'classes' => 'mt-2']) ?>
    </div>
  </div>
  <div class="mt-3 flex flex-wrap gap-2">
    <?php if ($dans && !empty($p['id'])): ?><a href="<?= url('/organisations/' . $p['id']) ?>" class="btn btn-vert min-h-10 px-5 text-sm">Voir ma fiche publique</a><?php endif ?>
    <a href="<?= e($modifier) ?>" class="btn btn-contour min-h-10 px-5 text-sm">Modifier les informations</a>
  </div>
</section>

<div class="grid gap-4 sm:gap-5 md:grid-cols-2">
  <?php /* Fiche à compléter */ ?>
  <section class="bloc">
    <div class="flex items-center justify-between gap-3">
      <h2 class="text-lg font-bold sm:text-xl">Fiche complète à <?= $pourcent ?> %</h2>
    </div>
    <div class="mt-2 h-2 overflow-hidden rounded-full bg-gris" aria-hidden="true"><div class="h-full rounded-full <?= $pourcent === 100 ? 'bg-vert' : 'bg-orange' ?>" style="width: <?= $pourcent ?>%"></div></div>
    <ul class="mt-3 space-y-1.5 text-sm">
      <?php foreach ($points as $libelle => $ok): ?>
      <li class="flex items-center gap-2">
        <span class="grid size-5 shrink-0 place-items-center rounded-full <?= $ok ? 'bg-vert text-white' : 'border-2 border-ligne' ?>"><?= $ok ? $coche : '' ?></span>
        <span class="<?= $ok ? 'text-texte' : 'font-bold text-encre' ?>"><?= e($libelle) ?></span>
        <?php if (!$ok): ?><a href="<?= e($modifier) ?>" class="ml-auto text-xs font-bold text-orange hover:text-vert">Ajouter</a><?php endif ?>
      </li>
      <?php endforeach ?>
    </ul>
  </section>

  <div class="space-y-4 sm:space-y-5">
    <?php /* Visibilité dans l'annuaire */ ?>
    <section class="bloc">
      <h2 class="text-lg font-bold sm:text-xl">Visibilité</h2>
      <form method="post" class="mt-2">
        <?= csrf() ?>
        <label class="flex cursor-pointer items-start gap-2.5 text-sm"><input type="checkbox" name="dans_annuaire" value="1" class="mt-0.5 size-4 shrink-0 accent-vert"<?= $dans ? ' checked' : '' ?> onchange="this.form.requestSubmit ? this.form.requestSubmit() : this.form.submit()"> <span>Apparaître dans l'annuaire public des organisations du réseau.</span></label>
        <noscript><button type="submit" class="btn btn-vert mt-2 min-h-9 px-4 text-sm">Enregistrer</button></noscript>
      </form>
      <p class="mt-2 text-xs text-texte"><?= $publiees ?> opportunité<?= pluriel($publiees) ?> publiée<?= pluriel($publiees) ?> avec votre logo et un lien vers votre fiche.</p>
    </section>

    <?php /* Reconnaissance par le chapitre */ ?>
    <section class="bloc">
      <h2 class="text-lg font-bold sm:text-xl">Reconnaissance</h2>
      <ul class="mt-2 space-y-3 text-sm">
        <li>
          <p class="font-bold <?= !empty($p['verifiee']) ? 'text-vert' : 'text-encre' ?>"><?= !empty($p['verifiee']) ? 'Organisation vérifiée' : 'Pas encore vérifiée' ?></p>
          <p class="text-[13px] text-texte"><?= !empty($p['verifiee']) ? "Le badge apparaît sur votre fiche et vos opportunités." : "L'équipe du chapitre vérifie les organisations après un échange. Une fiche complète l'aide à le faire." ?></p>
        </li>
        <li>
          <p class="font-bold <?= !empty($p['partenaire_chapitre']) ? 'text-orange-fonce' : 'text-encre' ?>"><?= !empty($p['partenaire_chapitre']) ? 'Partenaire du chapitre' : 'Devenir partenaire du chapitre' ?></p>
          <p class="text-[13px] text-texte"><?= !empty($p['partenaire_chapitre']) ? 'Merci pour votre soutien aux activités du chapitre.' : 'Proposez un soutien (salle, intervenants, stages, financement…) : la mention apparaît sur votre fiche.' ?></p>
          <?php if (empty($p['partenaire_chapitre'])): ?><a href="<?= url('/compte/mon-espace/soutenir') ?>" class="btn btn-orange mt-2 min-h-9 px-4 text-sm">Soutenir le chapitre</a><?php endif ?>
        </li>
      </ul>
    </section>
  </div>
</div>
<?php $espace = ob_get_clean(); partiel('espace/base', get_defined_vars());
