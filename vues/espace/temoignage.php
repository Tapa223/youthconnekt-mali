<?php ob_start();
/* Mon témoignage : le membre raconte ce que le chapitre lui a apporté ; l'équipe le relit avant de l'afficher sur le site. */
$etat_t = $temoignage ? ($temoignage['publie'] ? ['Affiché sur le site', 'bg-vert-clair text-vert'] : ['En relecture par l\'équipe', 'bg-orange/15 text-orange-fonce']) : null;
?>
<section id="temoignage" class="bloc">
  <div class="flex flex-wrap items-center justify-between gap-2">
    <h1 class="text-xl font-bold max-lg:sr-only sm:text-3xl">Mon témoignage</h1>
    <?php if ($etat_t): ?><span class="rounded-full px-3 py-1 text-xs font-bold <?= $etat_t[1] ?>"><?= $etat_t[0] ?></span><?php endif ?>
  </div>
  <p class="text-sm text-encre lg:mt-1">Racontez ce que YouthConnekt Mali vous a apporté : une formation, une rencontre, un projet. Les témoignages publiés apparaissent sur l'accueil et sur la page Témoignages.</p>
  <?php if ($temoignage): ?><p class="mt-4 border-l-[3px] border-orange pl-3 text-sm text-encre">« <?= e($temoignage['texte']) ?> »</p><?php endif ?>
  <form method="post" action="<?= url('/compte/mon-espace/temoignage') ?>" class="mt-4 grid gap-3" novalidate data-confirmer="Envoyer votre témoignage ?" data-confirmer-texte="L'équipe du chapitre le relit avant de l'afficher sur le site, avec votre nom et votre photo de profil." data-confirmer-oui="Envoyer">
    <?= csrf() ?>
    <label for="texte-temoignage" class="libelle mb-0">Votre témoignage</label>
    <textarea id="texte-temoignage" name="texte" rows="4" maxlength="600" class="champ min-h-28 py-3" placeholder="Ce que vous avez trouvé ou vécu grâce au chapitre : une formation, une rencontre, un projet…"><?= e($brouillon_temoignage !== '' ? $brouillon_temoignage : ($temoignage['texte'] ?? '')) ?></textarea>
    <p class="aide mt-0">600 caractères au maximum. Il sera signé <strong><?= e($u['nom_affiche']) ?></strong>, avec votre photo de profil.</p>
    <label class="flex items-start gap-2.5 text-sm"><input type="checkbox" name="accord" value="1" class="mt-0.5 size-4 shrink-0 accent-vert"> J'accepte que mon témoignage soit publié sur le site de YouthConnekt Mali.</label>
    <div><button type="submit" class="btn btn-vert min-h-11 px-6"><?= $temoignage ? 'Envoyer la nouvelle version' : 'Envoyer mon témoignage' ?></button></div>
  </form>
</section>
<?php $espace = ob_get_clean(); partiel('espace/base', get_defined_vars());
