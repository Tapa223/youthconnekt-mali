<?php ob_start();
/*
 * Tableau de bord : épuré pour le téléphone.
 *  - salutation selon l'heure, profil à compléter ;
 *  - trois chiffres cliquables (communautés, opportunités, événements) ;
 *  - quatre actions rapides ;
 *  - un aperçu de mes opportunités.
 * Les nouveautés sont dans « Notifications » (la cloche).
 */
$nb_com = count($mes_communautes);
$heure = (int) date('G');
$salut = $heure >= 18 || $heure < 4 ? 'Bonsoir' : 'Bonjour';
$evenements_lien = isset(categories_actualite()['evenement']) ? url('/actualites', ['categorie' => 'evenement']) : url('/actualites');
$chiffres = [
    // À zéro, une invitation remplace le chiffre
    $nb_com ? [$nb_com, 'communauté' . pluriel($nb_com), url('/compte/mon-espace/communautes'), 'border-vert'] : ['Rejoindre', 'une communauté', url('/communaute'), 'border-vert'],
    $nb_opportunites ? [$nb_opportunites, 'opportunité' . pluriel($nb_opportunites), url('/compte/mon-espace/opportunites'), 'border-orange'] : ['Explorer', 'les opportunités', url('/opportunites'), 'border-orange'],
    [$nb_evenements, 'événement' . pluriel($nb_evenements) . ' à venir', $evenements_lien, 'border-turquoise'],
];
$actions = [
    ['Proposer une opportunité', 'bg-vert', 'dialogue-opportunite', '', '<rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M12 11v5M9.5 13.5h5"/>'],
    ['Parler de mon initiative', 'bg-orange', 'dialogue-initiative', '', '<path d="M5 21V4M5 4h11l-2 4 2 4H5"/>'],
    ['Trouver une communauté', 'bg-turquoise', '', url('/communaute'), '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c.6-3.6 3.2-5.5 6.5-5.5s5.9 1.9 6.5 5.5M16 4.8a3.5 3.5 0 0 1 0 6.4M18.5 14.8c1.7.8 2.7 2.6 3 5.2"/>'],
    ['Explorer les opportunités', 'bg-bleu', '', url('/opportunites'), '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>'],
];
?>
<span data-recharger-apres-envoi hidden></span>
<?php if (!empty($_SESSION['bienvenue'])): unset($_SESSION['bienvenue']); ?>
<section class="bienvenue relative mb-4 overflow-hidden rounded-2xl border-2 border-orange/30 bg-white p-5 sm:flex sm:items-center sm:gap-6 sm:p-6" aria-labelledby="titre-bienvenue">
  <svg class="bienvenue-reseau mx-auto h-28 w-56 shrink-0 sm:mx-0" viewBox="0 0 240 120" aria-hidden="true">
    <g stroke="#035E4D" stroke-opacity=".25" stroke-width="1.5" fill="none"><path d="M30 30 L90 18 L150 40 L210 22"/><path d="M30 30 L40 95 L110 100"/><path d="M150 40 L200 92"/></g>
    <g class="bienvenue-fils" stroke="#F1592A" stroke-width="2" fill="none"><path pathLength="1" d="M120 66 L90 18"/><path pathLength="1" d="M120 66 L150 40"/><path pathLength="1" d="M120 66 L110 100"/><path pathLength="1" d="M120 66 L200 92"/></g>
    <g fill="#035E4D"><circle cx="30" cy="30" r="5"/><circle cx="90" cy="18" r="6" fill="#04B2B1"/><circle cx="150" cy="40" r="5"/><circle cx="210" cy="22" r="5" fill="#0358D9"/><circle cx="40" cy="95" r="5" fill="#04B2B1"/><circle cx="110" cy="100" r="5"/><circle cx="200" cy="92" r="6" fill="#04B2B1"/></g>
    <circle class="bienvenue-point" cx="120" cy="66" r="9" fill="#F1592A" stroke="#fff" stroke-width="3"/>
  </svg>
  <div class="mt-3 text-center sm:mt-0 sm:text-left">
    <h2 id="titre-bienvenue" class="text-xl font-bold text-vert sm:text-2xl">Bienvenue dans le réseau, <?= e($profil['prenom'] ?? $u['nom_affiche']) ?></h2>
    <p class="mt-1 text-[15px]">Votre adresse est confirmée : vous êtes maintenant membre du chapitre YouthConnekt Mali. Commencez par rejoindre une communauté qui vous ressemble.</p>
    <a href="<?= url('/communaute') ?>" class="btn btn-orange mt-3 min-h-11">Choisir une communauté</a>
  </div>
</section>
<?php endif ?>
<section class="relative overflow-hidden rounded-2xl bg-vert px-5 py-4 text-white sm:px-7 sm:py-6">
  <?php partiel('partials/reseau_deco', ['opacite' => 'opacity-40']) ?>
  <div class="relative">
    <div class="flex items-center gap-3 sm:gap-4">
      <?php $photo_m = $profil['photo'] ?? $profil['logo'] ?? ''; if ($photo_m): ?><img src="<?= e(media($photo_m)) ?>" alt="" class="size-12 shrink-0 rounded-full border-2 border-white object-cover sm:size-16">
      <?php else: ?><a href="<?= url('/compte/mon-espace/profil', ['modifier' => 1]) ?>#modifier-profil" class="grid size-12 shrink-0 place-items-center rounded-full border-2 border-white/80 bg-orange text-base font-bold text-white sm:size-16 sm:text-xl" title="Ajouter une photo"><?= e($u['initiales']) ?></a><?php endif ?>
      <h1 class="text-[1.5rem] leading-tight font-bold text-white sm:text-4xl"><?= $salut ?><?= $u['prenom_affiche'] ? ', ' . e($u['prenom_affiche']) : '' ?></h1>
    </div>
    <?php if ($completion < 100): ?>
    <a href="<?= url('/compte/mon-espace/profil', ['modifier' => 1]) ?>#modifier-profil" class="group mt-3 block max-w-sm">
      <span class="flex items-center justify-between text-sm text-white/90"><span>Profil complété à <strong class="text-white"><?= $completion ?> %</strong></span><span class="font-bold text-white underline-offset-2 group-hover:underline">Compléter</span></span>
      <span class="mt-1.5 block h-1.5 rounded-full bg-white/25" role="progressbar" aria-valuenow="<?= $completion ?>" aria-valuemin="0" aria-valuemax="100" aria-label="Profil complété"><span class="block h-1.5 rounded-full bg-orange" style="width: <?= $completion ?>%"></span></span>
    </a>
    <?php else: ?><p class="mt-1 text-sm text-white/90">Voici vos opportunités du moment.</p><?php endif ?>
  </div>
</section>

<ul class="grid grid-cols-3 gap-2 sm:gap-4" aria-label="En chiffres">
  <?php foreach ($chiffres as [$nombre, $libelle, $lien, $bord]): ?>
  <li><a href="<?= e($lien) ?>" class="flex h-full flex-col rounded-2xl border-t-4 bg-white p-3 transition-shadow hover:shadow-md sm:p-4 <?= $bord ?>">
    <span class="<?= is_int($nombre) ? 'text-2xl sm:text-3xl' : 'text-base sm:text-xl' ?> leading-none font-bold text-vert"><?= e((string) $nombre) ?></span>
    <span class="mt-1 text-xs leading-tight text-encre sm:text-sm"><?= e($libelle) ?></span>
  </a></li>
  <?php endforeach ?>
</ul>

<ul class="grid grid-cols-2 gap-2 sm:grid-cols-4 sm:gap-3" aria-label="Actions rapides">
  <?php foreach ($actions as [$titre_a, $couleur, $dialogue, $lien, $trace]): ?>
  <li>
    <<?= $dialogue ? 'button type="button" data-ouvrir-dialogue="' . $dialogue . '"' : 'a href="' . e($lien) . '"' ?> class="flex h-full w-full items-center gap-2.5 rounded-xl bg-white p-2.5 text-left transition-shadow hover:shadow-md sm:p-3">
      <span class="grid size-8 shrink-0 place-items-center rounded-full text-white <?= $couleur ?>" aria-hidden="true"><svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><?= $trace ?></svg></span>
      <span class="text-[13px] leading-tight font-bold text-encre sm:text-sm"><?= e($titre_a) ?></span>
    </<?= $dialogue ? 'button' : 'a' ?>>
  </li>
  <?php endforeach ?>
</ul>

<section class="bloc" aria-labelledby="titre-opp">
  <div class="flex items-center justify-between gap-3">
    <h2 id="titre-opp" class="text-lg font-bold sm:text-xl">Mes opportunités</h2>
    <?php if ($opportunites): ?><a href="<?= url('/compte/mon-espace/opportunites') ?>" class="btn-lien min-h-9 px-3.5 text-[13px]">Tout voir</a><?php endif ?>
  </div>
  <?php if ($opportunites): ?>
  <ul class="mt-3 grid grid-cols-2 gap-2.5 sm:gap-4 xl:grid-cols-3"><?php foreach (array_slice($opportunites, 0, 3) as $k => $o): ?><li class="<?= $k === 2 ? 'hidden xl:block' : '' ?>"><?php partiel('cartes/opportunite', ['o' => $o, 'compacte' => true, 'via' => $o['via']]) ?></li><?php endforeach ?></ul>
  <?php else: ?>
  <p class="mt-2 text-sm text-encre"><?= $mes_communautes ? 'Aucune opportunité ouverte dans vos communautés pour le moment.' : 'Rejoignez une communauté : ses opportunités apparaîtront ici.' ?></p>
  <?php endif ?>
</section>
<?php partiel('partials/proposer_opportunite', ['types' => $types]) ?>
<?php partiel('partials/proposer_initiative') ?>
<?php $espace = ob_get_clean(); partiel('espace/base', get_defined_vars());
