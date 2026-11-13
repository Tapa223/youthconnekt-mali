<?php
$u = utilisateur();
$photo_membre = $u ? ($u['profil']['photo'] ?? $u['profil']['logo'] ?? '') : '';
$nb_notifs_entete = $u ? nb_notifications_non_lues($u) : 0;
$pastille = fn (string $taille) => $photo_membre
    ? '<img src="' . e(media($photo_membre)) . '" alt="" class="' . $taille . ' shrink-0 rounded-full bg-white object-cover">'
    : '<span class="grid ' . $taille . ' shrink-0 place-items-center rounded-full bg-orange text-xs" aria-hidden="true">' . e($u['initiales']) . '</span>';
$section = explode('/', trim(chemin_actuel(), '/'))[0];
$navigation = [
    ['/', 'Accueil', ''],
    ['/a-propos', 'À propos', 'a-propos'],
    ['/initiatives', 'Initiatives', 'initiatives'],
    ['/opportunites', 'Opportunités', 'opportunites'],
    ['/actualites', 'Actualités', 'actualites'],
    ['/communaute', 'Communauté', 'communaute'],
    ['/contact', 'Contact', 'contact'],
];
?>
<header class="site-entete sticky top-0 z-40 border-b border-transparent bg-white/95 backdrop-blur transition-[border-color,box-shadow] duration-200 data-[defile]:border-ligne data-[defile]:shadow-sm" data-entete>
  <div class="conteneur flex h-16 items-center justify-between gap-4 lg:max-w-none lg:px-10 xl:px-8 2xl:max-w-[100rem] 2xl:gap-6 2xl:px-14">
    <a href="<?= url('/') ?>" class="shrink-0 rounded-md" aria-label="YouthConnekt Mali, retour à l'accueil">
      <?php partiel('partials/logo', ['classes' => 'h-10 w-auto xl:h-11']) ?>
    </a>

    <nav class="hidden xl:block" aria-label="Menu principal">
      <ul class="flex items-center gap-1 2xl:gap-2">
        <?php foreach ($navigation as [$lien, $libelle, $prefixe]): $actif = $section === $prefixe; ?>
        <li>
          <a href="<?= url($lien) ?>"
             class="relative flex h-16 items-center px-2.5 text-[15px] font-medium whitespace-nowrap transition-colors 2xl:px-3 <?= $actif ? 'text-vert after:absolute after:inset-x-2.5 2xl:after:inset-x-3 after:bottom-0 after:h-[3px] after:rounded-t after:bg-orange' : 'text-encre hover:text-orange' ?>"
             <?= $actif ? 'aria-current="page"' : '' ?>><?= e($libelle) ?></a>
        </li>
        <?php endforeach ?>
      </ul>
    </nav>

    <div class="hidden items-center gap-2 xl:flex 2xl:gap-2.5">
      <?php if ($u): ?>
        <a href="<?= url(est_membre($u) ? '/compte/mon-espace' : '/gestion') ?>" class="inline-flex min-h-11 items-center gap-2.5 rounded-full bg-vert py-1.5 pr-5 pl-1.5 text-[15px] font-bold text-white hover:bg-vert-fonce">
          <span class="relative"><?= $pastille('size-8') ?><?php if ($nb_notifs_entete): ?><span class="absolute -top-1.5 -right-1.5 grid min-w-[18px] place-items-center rounded-full bg-orange px-1 text-[10px] leading-[18px] font-bold text-white ring-2 ring-vert" aria-label="<?= $nb_notifs_entete ?> notification<?= pluriel($nb_notifs_entete) ?>"><?= $nb_notifs_entete > 9 ? '9+' : $nb_notifs_entete ?></span><?php endif ?></span>
          <?= est_membre($u) ? 'Mon espace' : 'Administration' ?>
        </a>
      <?php else: ?>
        <a href="<?= url('/compte/connexion') ?>" class="btn btn-contour px-4 whitespace-nowrap">Se connecter</a>
        <a href="<?= url('/compte/inscription') ?>" class="btn btn-vert px-5 whitespace-nowrap">Rejoindre le chapitre</a>
      <?php endif ?>
    </div>

    <div class="-mr-2 flex items-center xl:hidden">
    <button type="button" class="relative grid size-12 place-items-center rounded-lg text-vert hover:bg-vert-clair" aria-controls="menu-mobile" aria-expanded="false" data-ouvrir-menu>
      <span class="sr-only">Ouvrir le menu</span>
      <?php if ($nb_notifs_entete): ?><span class="absolute top-2.5 right-2 size-2.5 rounded-full bg-orange ring-2 ring-white" aria-hidden="true"></span><?php endif ?>
      <svg class="size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
    </button>
    </div>
  </div>
</header>


<div id="menu-mobile" class="fixed inset-0 z-50 hidden xl:hidden" role="dialog" aria-modal="true" aria-label="Menu" data-menu-mobile>
  <div class="absolute inset-0 bg-encre/35 backdrop-blur-[3px]" data-fermer-menu></div>
  <div class="absolute top-2 right-2 flex max-h-[calc(100svh-1rem)] w-[70%] max-w-[17.5rem] flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
    <div class="flex h-14 items-center justify-between border-b border-ligne px-3">
      <?php partiel('partials/logo', ['classes' => 'h-8 w-auto']) ?>
      <button type="button" class="-mr-1 grid size-10 place-items-center rounded-lg text-vert hover:bg-vert-clair" data-fermer-menu>
        <span class="sr-only">Fermer le menu</span>
        <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
      </button>
    </div>
    <nav class="min-h-0 overflow-y-auto px-2.5 py-2.5" aria-label="Menu principal">
      <ul class="space-y-0.5">
        <?php foreach ($navigation as [$lien, $libelle, $prefixe]): $actif = $section === $prefixe; ?>
        <li>
          <a href="<?= url($lien) ?>" class="flex min-h-11 items-center justify-between rounded-lg px-3 text-[15px] font-medium <?= $actif ? 'bg-vert-clair text-vert' : 'text-encre hover:bg-gris' ?>" <?= $actif ? 'aria-current="page"' : '' ?>>
            <?= e($libelle) ?>
            <?php if ($actif): ?><span class="size-2 rounded-full bg-orange" aria-hidden="true"></span><?php endif ?>
          </a>
        </li>
        <?php endforeach ?>
      </ul>
    </nav>
    <div class="space-y-2 border-t border-ligne p-3 pb-[max(0.75rem,env(safe-area-inset-bottom))]">
      <?php if ($u): ?>
        <a href="<?= url(est_membre($u) ? '/compte/mon-espace' : '/gestion') ?>" class="btn btn-vert w-full"><?= $pastille('size-7') ?> <?= est_membre($u) ? 'Mon espace' : 'Administration' ?><?php if ($nb_notifs_entete): ?> <span class="rounded-full bg-orange px-2 text-xs leading-5"><?= $nb_notifs_entete ?></span><?php endif ?></a>
        <form method="post" action="<?= url('/compte/deconnexion') ?>" data-confirmer="Êtes-vous sûr de vouloir vous déconnecter ?" data-confirmer-oui="Se déconnecter"><?= csrf() ?><button type="submit" class="btn btn-contour w-full">Se déconnecter</button></form>
      <?php else: ?>
        <a href="<?= url('/compte/inscription') ?>" class="btn btn-vert min-h-10 w-full text-sm">Rejoindre le chapitre</a>
        <a href="<?= url('/compte/connexion') ?>" class="btn btn-contour min-h-10 w-full text-sm">Se connecter</a>
      <?php endif ?>
    </div>
  </div>
</div>
