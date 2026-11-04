<?php
/*
 * Cadre de l'espace membre.
 *  - ordinateur : carte du membre et menu à gauche ;
 *  - téléphone : un seul bouton « Mon espace · rubrique » qui déroule le menu complet (pas de carte répétée).
 * Variables : $u, $profil, $rubrique, $espace (contenu)
 */
$liens = [
    'tableau' => ['/compte/mon-espace', 'Tableau de bord'],
    'communautes' => ['/compte/mon-espace/communautes', 'Mes communautés'],
    'opportunites' => ['/compte/mon-espace/opportunites', 'Mes opportunités'],
    'propositions' => ['/compte/mon-espace/propositions', 'Mes propositions'],
    'profil' => ['/compte/mon-espace/profil', 'Mon profil'],
    'parametres' => ['/compte/mon-espace/parametres', 'Paramètres'],
];
$image = $profil['photo'] ?? $profil['logo'] ?? '';
$detail = array_filter([libelle_profil($profil, $u['type_compte']), REGIONS[$profil['region'] ?? ''] ?? '']);
$titre_rubrique = $liens[$rubrique][1] ?? 'Mon espace';
?>
<div class="bg-gris pt-3 pb-8 sm:py-8">
  <div class="conteneur grid gap-4 lg:grid-cols-[250px_minmax(0,1fr)] lg:gap-6 xl:max-w-7xl">

    <?php /* Téléphone et tablette : menu déroulant */ ?>
    <details class="menu-espace relative lg:hidden" data-choix-type>
      <summary class="flex min-h-13 cursor-pointer list-none items-center gap-3 rounded-2xl bg-white px-3 shadow-sm [&::-webkit-details-marker]:hidden">
        <?php if ($image): ?><img src="<?= e(media($image)) ?>" alt="" class="size-9 shrink-0 rounded-full <?= isset($profil['logo']) ? 'bg-white object-contain ring-1 ring-ligne' : 'object-cover' ?>">
        <?php else: ?><span class="grid size-9 shrink-0 place-items-center rounded-full bg-orange text-sm font-bold text-white" aria-hidden="true"><?= e($u['initiales']) ?></span><?php endif ?>
        <span class="min-w-0 flex-1 leading-tight"><span class="block text-[11px] text-texte">Mon espace</span><span class="block truncate font-bold text-vert"><?= e($titre_rubrique) ?></span></span>
        <span class="flex items-center gap-1 rounded-full bg-vert-clair px-3 py-1.5 text-xs font-bold text-vert">Menu <svg class="choix-fleche size-4 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg></span>
      </summary>
      <nav class="absolute inset-x-0 z-30 mt-2 rounded-2xl border border-ligne bg-white p-1.5 shadow-2xl shadow-vert/15" aria-label="Mon espace">
        <?php foreach ($liens as $cle => [$lien, $libelle]): ?>
        <a href="<?= url($lien) ?>" class="flex min-h-11 items-center rounded-xl px-3 text-[15px] font-bold <?= $rubrique === $cle ? 'bg-orange text-white' : 'text-encre hover:bg-gris' ?>" <?= $rubrique === $cle ? 'aria-current="page"' : '' ?>><?= e($libelle) ?></a>
        <?php endforeach ?>
        <form method="post" action="<?= url('/compte/deconnexion') ?>" class="mt-1 border-t border-ligne pt-1"><?= csrf() ?><button type="submit" class="flex min-h-11 w-full items-center rounded-xl px-3 text-[15px] font-bold text-rouge hover:bg-gris">Se déconnecter</button></form>
      </nav>
    </details>

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
        <a href="<?= url($lien) ?>" class="lien-espace <?= $rubrique === $cle ? 'actif' : '' ?>" <?= $rubrique === $cle ? 'aria-current="page"' : '' ?>><?= e($libelle) ?></a>
        <?php endforeach ?>
        <form method="post" action="<?= url('/compte/deconnexion') ?>"><?= csrf() ?><button type="submit" class="lien-espace text-rouge">Se déconnecter</button></form>
      </nav>
    </aside>
    <div class="min-w-0 space-y-4 sm:space-y-5"><?= $espace ?></div>
  </div>
</div>
