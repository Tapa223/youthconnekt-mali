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

<?php /* Mon témoignage : le membre raconte ce que le chapitre lui a apporté ; l'équipe le relit avant de l'afficher */
$etat_t = $temoignage ? ($temoignage['publie'] ? ['Affiché sur le site', 'bg-vert-clair text-vert'] : ['En relecture', 'bg-orange/15 text-orange-fonce']) : null;
$ouvrir_t = $brouillon_temoignage !== '' || get('temoigner') !== '';
?>
<details id="temoignage" class="bloc group/tem scroll-mt-20" <?= $ouvrir_t ? 'open' : '' ?>>
  <summary class="flex min-h-10 cursor-pointer list-none items-center justify-between gap-3 [&::-webkit-details-marker]:hidden">
    <span class="min-w-0"><span class="block text-lg font-bold text-vert sm:text-xl">Mon témoignage</span>
      <?php if ($etat_t): ?><span class="mt-1 inline-block rounded-full px-2.5 py-0.5 text-xs font-bold <?= $etat_t[1] ?>"><?= $etat_t[0] ?></span>
      <?php else: ?><span class="block text-sm text-texte">Racontez ce que YouthConnekt Mali vous a apporté.</span><?php endif ?>
    </span>
    <span class="btn-lien min-h-9 shrink-0 px-3.5 text-[13px] group-open/tem:hidden"><?= $temoignage ? 'Modifier' : 'Témoigner' ?></span>
    <span class="hidden shrink-0 rounded-full bg-gris px-3 py-1.5 text-xs font-bold text-texte group-open/tem:inline">Fermer</span>
  </summary>
  <?php if ($temoignage && !$ouvrir_t): ?><p class="mt-3 border-l-[3px] border-orange pl-3 text-sm text-encre">« <?= e($temoignage['texte']) ?> »</p><?php endif ?>
  <form method="post" action="<?= url('/compte/mon-espace/temoignage') ?>" class="mt-4 grid gap-3" novalidate data-confirmer="Envoyer votre témoignage ?" data-confirmer-texte="L'équipe du chapitre le relit avant de l'afficher sur le site, avec votre nom et votre photo de profil." data-confirmer-oui="Envoyer">
    <?= csrf() ?>
    <label for="texte-temoignage" class="libelle mb-0">Votre témoignage</label>
    <textarea id="texte-temoignage" name="texte" rows="4" maxlength="600" class="champ min-h-28 py-3" placeholder="Ce que vous avez trouvé ou vécu grâce au chapitre : une formation, une rencontre, un projet…"><?= e($brouillon_temoignage !== '' ? $brouillon_temoignage : ($temoignage['texte'] ?? '')) ?></textarea>
    <p class="aide mt-0">600 caractères au maximum. Il sera signé <strong><?= e($u['nom_affiche']) ?></strong>, avec votre photo de profil.</p>
    <label class="flex items-start gap-2.5 text-sm"><input type="checkbox" name="accord" value="1" class="mt-0.5 size-4 shrink-0 accent-vert"> J'accepte que mon témoignage soit publié sur le site de YouthConnekt Mali.</label>
    <div><button type="submit" class="btn btn-vert min-h-11 px-6"><?= $temoignage ? 'Envoyer la nouvelle version' : 'Envoyer mon témoignage' ?></button></div>
  </form>
</details>

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
