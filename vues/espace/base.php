<?php
/*
 * Cadre de l'espace membre.
 *  - ordinateur : carte du membre et menu à gauche ;
 *  - téléphone : un seul bouton « Mon espace · rubrique » qui déroule le menu complet (pas de carte répétée).
 * Variables : $u, $profil, $rubrique, $espace (contenu)
 */
$liens = [
    'tableau' => ['/compte/mon-espace', 'Tableau de bord'],
    'notifications' => ['/compte/mon-espace/notifications', 'Notifications'],
    'communautes' => ['/compte/mon-espace/communautes', 'Mes communautés'],
    'opportunites' => ['/compte/mon-espace/opportunites', 'Mes opportunités'],
    'propositions' => ['/compte/mon-espace/propositions', 'Mes propositions'],
    'profil' => ['/compte/mon-espace/profil', 'Mon profil'],
    'parametres' => ['/compte/mon-espace/parametres', 'Paramètres'],
];
$compteurs = $compteurs ?? [];
// Pastille de menu : orange pour les notifications non lues, verte pour les communautés et les opportunités
$pastille_menu = function (string $cle, bool $actif) use ($compteurs): string {
    $n = (int) ($compteurs[$cle] ?? 0);
    if (!$n) return '';
    $couleur = $actif ? 'bg-white text-orange' : ($cle === 'notifications' ? 'bg-orange text-white' : 'bg-vert-clair text-vert');
    return '<span class="ml-auto grid min-w-6 place-items-center rounded-full px-1.5 text-xs leading-6 font-bold ' . $couleur . '">' . ($n > 99 ? '99+' : $n) . '</span>';
};
$nb_notifs = $nb_notifs ?? 0;
$cloche = '<svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9a6 6 0 1 1 12 0c0 6 2.5 7.5 2.5 7.5h-17S6 15 6 9zM10 20a2.2 2.2 0 0 0 4 0"/></svg>';
$badge = fn (string $classes = '') => $nb_notifs ? '<span class="grid min-w-5 place-items-center rounded-full bg-orange px-1.5 text-[11px] leading-5 font-bold text-white ' . $classes . '">' . ($nb_notifs > 9 ? '9+' : $nb_notifs) . '</span>' : '';
$image = $profil['photo'] ?? $profil['logo'] ?? '';
$detail = array_filter([libelle_profil($profil, $u['type_compte']), REGIONS[$profil['region'] ?? ''] ?? '']);
$titre_rubrique = $liens[$rubrique][1] ?? 'Mon espace';
?>
<div class="bg-gris pt-3 pb-8 sm:py-8">
  <div class="conteneur grid grid-cols-1 gap-4 lg:grid-cols-[250px_minmax(0,1fr)] lg:gap-6 xl:max-w-7xl">

    <?php /* Téléphone et tablette : sur une seule ligne, la flèche Retour (page précédente), le menu déroulant et la cloche */ ?>
    <div class="flex items-stretch gap-2 lg:hidden">
    <a href="<?= $rubrique === 'tableau' ? url('/') : url('/compte/mon-espace') ?>" data-retour class="grid size-11 shrink-0 place-items-center rounded-xl bg-white text-vert shadow-sm transition-colors hover:bg-orange hover:text-white" aria-label="Retour"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg></a>
    <details class="menu-espace relative min-w-0 flex-1" data-choix-type>
      <summary class="flex h-11 cursor-pointer list-none items-center gap-2.5 rounded-xl bg-white pr-1.5 pl-3.5 shadow-sm [&::-webkit-details-marker]:hidden">
        <span class="min-w-0 flex-1 truncate text-[15px] font-bold text-vert"><?= e($titre_rubrique) ?></span>
        <span class="flex shrink-0 items-center gap-1 rounded-full bg-vert-clair px-2 py-1.5 text-xs font-bold text-vert min-[480px]:px-3"><span class="hidden min-[480px]:inline">Menu</span><svg class="choix-fleche size-4 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg></span>
      </summary>
      <nav class="absolute inset-x-0 z-30 mt-2 rounded-2xl border border-ligne bg-white p-1.5 shadow-2xl shadow-vert/15" aria-label="Mon espace">
        <?php foreach ($liens as $cle => [$lien, $libelle]): ?>
        <a href="<?= url($lien) ?>" class="flex min-h-11 items-center rounded-xl px-3 text-[15px] font-bold <?= $rubrique === $cle ? 'bg-orange text-white' : 'text-encre hover:bg-gris' ?>" <?= $rubrique === $cle ? 'aria-current="page"' : '' ?>><?= e($libelle) ?><?= $pastille_menu($cle, $rubrique === $cle) ?></a>
        <?php endforeach ?>
        <form method="post" action="<?= url('/compte/deconnexion') ?>" class="mt-1 border-t border-ligne pt-1" data-confirmer="Êtes-vous sûr de vouloir vous déconnecter ?" data-confirmer-oui="Se déconnecter"><?= csrf() ?><button type="submit" class="flex min-h-11 w-full items-center rounded-xl px-3 text-[15px] font-bold text-rouge hover:bg-gris">Se déconnecter</button></form>
      </nav>
    </details>
    <a href="<?= url('/compte/mon-espace/notifications') ?>" class="relative grid size-11 shrink-0 place-items-center rounded-xl bg-white text-vert shadow-sm <?= $rubrique === 'notifications' ? 'ring-2 ring-orange' : '' ?>" aria-label="Notifications<?= $nb_notifs ? ' : ' . $nb_notifs . ' nouvelle' . pluriel($nb_notifs) : '' ?>"><?= $cloche ?><?= $badge('absolute -top-1.5 -right-1.5') ?></a>
    </div>

    <?php /* Ordinateur : carte du membre et menu à gauche */ ?>
    <aside class="hidden min-w-0 space-y-4 lg:block">
      <div class="flex flex-col items-center rounded-2xl bg-white p-6 text-center">
        <?php if ($image): ?><img src="<?= e(media($image)) ?>" alt="" class="size-18 rounded-full <?= isset($profil['logo']) ? 'bg-white object-contain ring-1 ring-ligne' : 'object-cover' ?>">
        <?php else: ?><span class="grid size-18 shrink-0 place-items-center rounded-full bg-orange text-2xl font-bold text-white" aria-hidden="true"><?= e($u['initiales']) ?></span><?php endif ?>
        <p class="mt-3 w-full truncate text-lg font-bold text-encre"><?= e($u['nom_affiche']) ?></p>
        <?php if ($detail): ?><p class="text-xs"><?= e(implode(' · ', $detail)) ?></p><?php endif ?>
        <a href="<?= url('/compte/membres/' . $u['id']) ?>" class="btn btn-contour mt-3 min-h-9 px-4 text-sm">Voir mon profil</a>
      </div>
      <nav class="space-y-1 rounded-2xl bg-white p-3" aria-label="Mon espace">
        <?php foreach ($liens as $cle => [$lien, $libelle]): ?>
        <a href="<?= url($lien) ?>" class="lien-espace <?= $rubrique === $cle ? 'actif' : '' ?>" <?= $rubrique === $cle ? 'aria-current="page"' : '' ?>><?= e($libelle) ?><?= $pastille_menu($cle, $rubrique === $cle) ?></a>
        <?php endforeach ?>
        <form method="post" action="<?= url('/compte/deconnexion') ?>" data-confirmer="Êtes-vous sûr de vouloir vous déconnecter ?" data-confirmer-oui="Se déconnecter"><?= csrf() ?><button type="submit" class="lien-espace text-rouge">Se déconnecter</button></form>
      </nav>
    </aside>
    <div class="min-w-0 space-y-4 sm:space-y-5"><?= $espace ?></div>
  </div>
</div>
