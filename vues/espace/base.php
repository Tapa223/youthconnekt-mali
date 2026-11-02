<?php
/* Cadre de l'espace membre : carte du membre et menu à gauche. Variables : $u, $profil, $rubrique, $espace (contenu) */
$liens = [
    'tableau' => ['/compte/mon-espace', 'Tableau de bord'],
    'communautes' => ['/compte/mon-espace/communautes', 'Mes communautés'],
    'opportunites' => ['/compte/mon-espace/opportunites', 'Opportunités pour moi'],
    'profil' => ['/compte/mon-espace/profil', 'Mon profil'],
    'parametres' => ['/compte/mon-espace/parametres', 'Paramètres'],
];
$image = $profil['photo'] ?? $profil['logo'] ?? '';
$detail = array_filter([TYPES_COMPTE[$u['type_compte']] ?? '', libelle_profil($profil, $u['type_compte']), REGIONS[$profil['region'] ?? ''] ?? '']);
?>
<div class="bg-gris py-6 sm:py-10">
  <div class="conteneur grid gap-5 lg:grid-cols-[250px_minmax(0,1fr)] lg:gap-6 xl:max-w-7xl">
    <aside class="min-w-0 space-y-4">
      <div class="flex items-center gap-4 rounded-2xl bg-white p-4 lg:flex-col lg:p-6 lg:text-center">
        <?php if ($image): ?><img src="<?= e(media($image)) ?>" alt="" class="size-14 rounded-full <?= isset($profil['logo']) ? 'bg-white object-contain ring-1 ring-ligne' : 'object-cover' ?> lg:size-18">
        <?php else: ?><span class="grid size-14 shrink-0 place-items-center rounded-full bg-orange text-lg font-bold text-white lg:size-18 lg:text-2xl" aria-hidden="true"><?= e($u['initiales']) ?></span><?php endif ?>
        <div class="min-w-0 flex-1">
          <p class="truncate text-lg font-bold text-encre"><?= e($u['nom_affiche']) ?></p>
          <p class="text-xs"><?= e(implode(' · ', $detail)) ?></p>
          <a href="<?= url('/compte/mon-espace/profil') ?>" class="btn btn-contour mt-3 hidden min-h-9 px-4 text-sm lg:inline-flex">Modifier mon profil</a>
        </div>
      </div>
      <nav class="masquer-defilement -mx-4 flex gap-2 overflow-x-auto px-4 lg:mx-0 lg:block lg:space-y-1 lg:rounded-2xl lg:bg-white lg:p-3" aria-label="Mon espace">
        <?php foreach ($liens as $cle => [$lien, $libelle]): ?>
        <a href="<?= url($lien) ?>" class="lien-espace <?= $rubrique === $cle ? 'actif' : '' ?>" <?= $rubrique === $cle ? 'aria-current="page"' : '' ?>><?= e($libelle) ?></a>
        <?php endforeach ?>
        <?php if (est_admin($u)): ?><a href="<?= url('/gestion') ?>" class="lien-espace">Administration</a><?php endif ?>
        <form method="post" action="<?= url('/compte/deconnexion') ?>" class="shrink-0"><?= csrf() ?><button type="submit" class="lien-espace text-rouge">Se déconnecter</button></form>
      </nav>
    </aside>
    <div class="min-w-0 space-y-5"><?= $espace ?></div>
  </div>
</div>
