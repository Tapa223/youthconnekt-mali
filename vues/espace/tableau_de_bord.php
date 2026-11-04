<?php ob_start();
/*
 * Tableau de bord du membre.
 *  - nouveau membre (aucune communauté) : « Premiers pas » au lieu de chiffres à zéro ;
 *  - ensuite : chiffres, nouveautés, opportunités, communautés, événements.
 */
$nb_com = count($mes_communautes);
$debutant = !$mes_communautes;
$etapes = [
    [$completion >= 100, 'Complétez votre profil', 'Une photo et une courte présentation aident les autres membres à vous connaître.', url('/compte/mon-espace/profil'), 'Compléter'],
    [$nb_com > 0 || $demandes_en_attente, 'Rejoignez une communauté', 'Choisissez celles qui vous ressemblent : vous recevrez leurs opportunités ici.', url('/communaute'), 'Découvrir'],
    [!empty($profil['visible']), 'Rendez votre profil visible', 'Apparaissez parmi les membres de vos communautés : vous le confirmez avant.', url('/compte/mon-espace/profil'), 'Voir'],
];
$entete = fn (string $id, string $titre, string $lien, string $libelle) =>
    '<div class="flex items-center justify-between gap-3"><h2 id="' . $id . '" class="min-w-0 text-lg font-bold sm:text-xl">' . e($titre) . '</h2>'
    . '<a href="' . e($lien) . '" class="btn-lien min-h-9 px-3.5 text-[13px]">' . e($libelle) . '</a></div>';
?>
<section class="relative overflow-hidden rounded-2xl bg-vert p-5 text-white sm:p-7">
  <?php partiel('partials/reseau_deco', ['opacite' => 'opacity-50']) ?>
  <div class="relative flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
    <div class="flex items-center gap-4">
      <?php $photo_m = $profil['photo'] ?? $profil['logo'] ?? ''; if ($photo_m): ?><img src="<?= e(media($photo_m)) ?>" alt="" class="size-14 shrink-0 rounded-full border-2 border-white object-cover sm:size-16">
      <?php else: ?><span class="grid size-14 shrink-0 place-items-center rounded-full border-2 border-white/80 bg-orange text-lg font-bold text-white sm:size-16" aria-hidden="true"><?= e($u['initiales']) ?></span><?php endif ?>
      <div>
      <h1 class="text-2xl font-bold text-white sm:text-4xl">Bonjour<?= $u['prenom_affiche'] ? ', ' . e($u['prenom_affiche']) : '' ?></h1>
      <p class="mt-1 text-sm text-white/90 sm:text-base"><?= $debutant ? 'Bienvenue dans votre espace YouthConnekt Mali.' : 'Voici les nouveautés de vos communautés.' ?></p>
      </div>
    </div>
    <?php if ($completion < 100): ?>
    <div class="w-full rounded-xl border border-white/20 bg-white/10 p-3.5 md:w-72">
      <div class="flex justify-between text-sm"><span>Profil complété</span><strong><?= $completion ?> %</strong></div>
      <div class="mt-2 h-2 rounded-full bg-white/25" role="progressbar" aria-valuenow="<?= $completion ?>" aria-valuemin="0" aria-valuemax="100" aria-label="Profil complété"><div class="h-2 rounded-full bg-orange" style="width: <?= $completion ?>%"></div></div>
      <a href="<?= url('/compte/mon-espace/profil') ?>" class="btn btn-blanc mt-3 min-h-9 px-4 text-sm">Compléter mon profil</a>
    </div>
    <?php endif ?>
  </div>
</section>


<?php /* Raccourcis : les actions principales, à portée de pouce sur téléphone */ ?>
<span data-recharger-apres-envoi hidden></span>
<ul class="grid grid-cols-2 gap-2.5 sm:grid-cols-4 sm:gap-4" aria-label="Raccourcis">
  <?php foreach ([
      ['Proposer une opportunité', 'bg-vert text-white', 'dialogue-opportunite', '', '<rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M12 11v5M9.5 13.5h5"/>'],
      ['Parler de mon initiative', 'bg-orange text-white', 'dialogue-initiative', '', '<path d="M5 21V4M5 4h11l-2 4 2 4H5"/>'],
      ['Trouver une communauté', 'bg-turquoise text-encre', '', url('/communaute'), '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c.6-3.6 3.2-5.5 6.5-5.5s5.9 1.9 6.5 5.5M16 4.8a3.5 3.5 0 0 1 0 6.4M18.5 14.8c1.7.8 2.7 2.6 3 5.2"/>'],
      ['Explorer les opportunités', 'bg-bleu text-white', '', url('/opportunites'), '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>'],
  ] as [$titre_r, $couleurs, $dialogue, $lien, $trace]): ?>
  <li>
    <<?= $dialogue ? 'button type="button" data-ouvrir-dialogue="' . $dialogue . '"' : 'a href="' . e($lien) . '"' ?> class="survol-leve flex h-full w-full flex-col items-start gap-3 rounded-2xl p-4 text-left <?= $couleurs ?>">
      <span class="grid size-10 place-items-center rounded-full bg-white/20" aria-hidden="true"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><?= $trace ?></svg></span>
      <span class="text-[15px] leading-tight font-bold"><?= e($titre_r) ?></span>
    </<?= $dialogue ? 'button' : 'a' ?>>
  </li>
  <?php endforeach ?>
</ul>

<?php if ($nouveautes): ?>
<section class="bloc border-l-4 border-orange" aria-labelledby="titre-nouveau">
  <h2 id="titre-nouveau" class="flex items-center gap-2 text-lg font-bold"><span class="rounded-full bg-orange px-2.5 py-0.5 text-xs font-bold text-white">Nouveau</span> Ce qui a changé</h2>
  <ul class="mt-3 space-y-2">
    <?php foreach ($nouveautes as [$texte, $lien, $date, $ton]): ?>
    <li><a href="<?= e($lien) ?>" class="group flex items-start gap-3 rounded-xl bg-gris p-3 text-sm">
      <span class="mt-1.5 size-2 shrink-0 rounded-full <?= $ton === 'vert' ? 'bg-vert' : 'bg-rouge' ?>" aria-hidden="true"></span>
      <span class="min-w-0 flex-1"><span class="font-bold text-encre group-hover:text-orange"><?= e($texte) ?></span> <span class="block text-xs text-texte"><?= e(date_longue($date)) ?></span></span>
    </a></li>
    <?php endforeach ?>
  </ul>
</section>
<?php endif ?>

<?php if ($debutant): ?>
<section class="bloc" aria-labelledby="titre-etapes">
  <h2 id="titre-etapes" class="text-lg font-bold sm:text-xl">Premiers pas</h2>
  <ol class="mt-3 space-y-2.5">
    <?php foreach ($etapes as $k => [$fait, $titre_etape, $texte, $lien, $libelle]): ?>
    <li class="flex items-center gap-3 rounded-xl border border-ligne p-3 sm:p-4">
      <span class="grid size-9 shrink-0 place-items-center rounded-full text-sm font-bold <?= $fait ? 'bg-vert text-white' : 'bg-vert-clair text-vert' ?>" aria-hidden="true">
        <?php if ($fait): ?><svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7"/></svg><?php else: ?><?= $k + 1 ?><?php endif ?>
      </span>
      <span class="min-w-0 flex-1"><span class="block font-bold <?= $fait ? 'text-texte line-through' : 'text-vert' ?>"><?= e($titre_etape) ?></span><span class="hidden text-sm sm:block"><?= e($texte) ?></span></span>
      <?php if (!$fait): ?><a href="<?= e($lien) ?>" class="btn-lien min-h-9 px-3.5 text-[13px]"><?= e($libelle) ?></a><?php endif ?>
    </li>
    <?php endforeach ?>
  </ol>
</section>
<?php else: ?>
<ul class="grid grid-cols-3 gap-2 sm:gap-4">
  <li class="min-w-0 rounded-2xl border-t-4 border-vert bg-white p-3 sm:p-4"><span class="text-2xl font-bold text-vert sm:text-3xl"><?= $nb_com ?></span> <span class="block text-xs sm:inline sm:text-sm">communauté<?= pluriel($nb_com) ?></span></li>
  <li class="min-w-0 rounded-2xl border-t-4 border-orange bg-white p-3 sm:p-4"><span class="text-2xl font-bold text-vert sm:text-3xl"><?= $nb_opportunites ?></span> <span class="block text-xs sm:inline sm:text-sm">opportunité<?= pluriel($nb_opportunites) ?> ouverte<?= pluriel($nb_opportunites) ?></span></li>
  <li class="min-w-0 rounded-2xl border-t-4 border-turquoise bg-white p-3 sm:p-4"><span class="text-2xl font-bold text-vert sm:text-3xl"><?= $nb_evenements ?></span> <span class="block text-xs sm:inline sm:text-sm">événement<?= pluriel($nb_evenements) ?> à venir</span></li>
</ul>

<section class="bloc" aria-labelledby="titre-opp">
  <?= $entete('titre-opp', 'Mes opportunités', url('/compte/mon-espace/opportunites'), 'Tout voir') ?>
  <?php if ($opportunites): ?>
  <ul class="mt-4 grid grid-cols-2 gap-2.5 sm:gap-4 xl:grid-cols-3"><?php foreach ($opportunites as $o): ?><li><?php partiel('cartes/opportunite', ['o' => $o, 'compacte' => true, 'via' => $o['via']]) ?></li><?php endforeach ?></ul>
  <?php else: ?>
  <p class="mt-3 rounded-xl bg-gris p-4 text-sm">Aucune opportunité ouverte dans vos communautés pour le moment.</p>
  <?php endif ?>
</section>
<?php endif ?>

<div class="grid gap-4 sm:gap-5 xl:grid-cols-[minmax(0,7fr)_minmax(0,5fr)]">
  <section class="bloc" aria-labelledby="titre-com">
    <?= $entete('titre-com', 'Mes communautés', url('/communaute'), 'Découvrir') ?>
    <?php if ($mes_communautes): ?>
    <ul class="mt-4 space-y-2.5">
      <?php foreach ($mes_communautes as $k => $c): ?>
      <li><a href="<?= e($c['url']) ?>" class="survol-ligne flex items-center gap-3 rounded-xl border border-l-4 border-ligne p-3 <?= cycle(['border-l-vert', 'border-l-orange', 'border-l-bleu'], $k) ?>">
        <span class="min-w-0 flex-1"><span class="block font-bold text-vert"><?= e($c['nom']) ?></span><span class="text-xs"><?= $c['nb_membres'] ?> membre<?= pluriel($c['nb_membres']) ?> · <?= $c['nb_opportunites'] ?> opportunité<?= pluriel($c['nb_opportunites']) ?> ouverte<?= pluriel($c['nb_opportunites']) ?></span></span>
        <span class="text-sm font-bold text-vert">Voir</span>
      </a></li>
      <?php endforeach ?>
    </ul>
    <?php endif ?>

    <?php if ($demandes_en_attente): ?>
    <h3 class="mt-5 text-sm font-bold text-encre">Demandes en cours</h3>
    <ul class="mt-2 space-y-2"><?php foreach ($demandes_en_attente as $d): ?><li class="flex flex-wrap items-center justify-between gap-2 rounded-xl bg-gris px-3 py-2.5 text-sm"><span class="font-bold text-vert"><?= e($d['communaute_nom']) ?></span><span class="rounded-full bg-orange/15 px-2.5 py-0.5 text-xs font-bold text-orange-fonce">En cours de traitement</span></li><?php endforeach ?></ul>
    <?php endif ?>

    <?php if ($suggestions): ?>
    <h3 class="mt-5 text-sm font-bold text-encre"><?= $mes_communautes || $demandes_en_attente ? 'Elles pourraient aussi vous plaire' : 'Pour commencer' ?></h3>
    <ul class="mt-2 space-y-2">
      <?php foreach ($suggestions as $c): ?>
      <li><a href="<?= e($c['url']) ?>" class="group flex items-center gap-3 rounded-xl bg-gris p-3">
        <span class="min-w-0 flex-1"><span class="block font-bold text-vert group-hover:text-orange"><?= e($c['nom']) ?></span><span class="line-clamp-1 text-xs"><?= e($c['public_vise']) ?></span></span>
        <span class="shrink-0 text-sm font-bold text-vert">Voir</span>
      </a></li>
      <?php endforeach ?>
    </ul>
    <?php endif ?>
  </section>

  <section class="bloc h-fit" aria-labelledby="titre-evt">
    <?= $entete('titre-evt', 'Prochains événements', url('/actualites'), 'Actualités') ?>
    <?php if ($evenements): ?>
    <ul class="mt-4 space-y-3.5">
      <?php foreach ($evenements as $ev): ?>
      <li><a href="<?= e($ev['url']) ?>" class="group flex items-center gap-3.5">
        <time datetime="<?= e($ev['date_evenement']) ?>" class="grid min-w-13 rounded-xl bg-vert px-2 py-2 text-center leading-none text-white"><span class="text-xl font-bold"><?= e(jour($ev['date_evenement'])) ?></span><span class="mt-0.5 text-[10px] font-bold uppercase"><?= e(mois_pastille($ev['date_evenement'])) ?></span></time>
        <span class="min-w-0"><span class="block font-bold text-encre group-hover:text-orange"><?= e($ev['titre']) ?></span><?php if ($ev['lieu']): ?><span class="text-xs"><?= e($ev['lieu']) ?></span><?php endif ?></span>
      </a></li>
      <?php endforeach ?>
    </ul>
    <?php else: ?><p class="mt-3 text-sm">Aucun événement à venir pour le moment.</p><?php endif ?>
  </section>
</div>
<?php partiel('partials/proposer_opportunite', ['types' => $types]) ?>
<?php partiel('partials/proposer_initiative') ?>
<?php $espace = ob_get_clean(); partiel('espace/base', get_defined_vars());
