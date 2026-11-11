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
  <div class="conteneur flex h-16 items-center justify-between gap-6 lg:max-w-none lg:px-10 xl:px-14 2xl:max-w-[100rem]">
    <a href="<?= url('/') ?>" class="shrink-0 rounded-md" aria-label="YouthConnekt Mali, retour à l'accueil">
      <?php partiel('partials/logo', ['classes' => 'h-10 w-auto xl:h-11']) ?>
    </a>

    <nav class="hidden xl:block" aria-label="Menu principal">
      <ul class="flex items-center gap-1 2xl:gap-2">
        <?php foreach ($navigation as [$lien, $libelle, $prefixe]): $actif = $section === $prefixe; ?>
        <li>
          <a href="<?= url($lien) ?>"
             class="relative flex h-16 items-center px-3 text-[15px] font-medium whitespace-nowrap transition-colors <?= $actif ? 'text-vert after:absolute after:inset-x-3 after:bottom-0 after:h-[3px] after:rounded-t after:bg-orange' : 'text-encre hover:text-orange' ?>"
             <?= $actif ? 'aria-current="page"' : '' ?>><?= e($libelle) ?></a>
        </li>
        <?php endforeach ?>
      </ul>
    </nav>

    <div class="hidden items-center gap-2.5 xl:flex">
      <button type="button" class="grid size-11 place-items-center rounded-full text-vert transition-colors hover:bg-vert-clair hover:text-orange" data-ouvrir-recherche aria-label="Rechercher sur le site" title="Rechercher (Ctrl + K)"><svg class="size-[22px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg></button>
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
    <button type="button" class="grid size-12 place-items-center rounded-lg text-vert hover:bg-vert-clair" data-ouvrir-recherche aria-label="Rechercher sur le site"><svg class="size-[22px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg></button>
    <button type="button" class="relative grid size-12 place-items-center rounded-lg text-vert hover:bg-vert-clair" aria-controls="menu-mobile" aria-expanded="false" data-ouvrir-menu>
      <span class="sr-only">Ouvrir le menu</span>
      <?php if ($nb_notifs_entete): ?><span class="absolute top-2.5 right-2 size-2.5 rounded-full bg-orange ring-2 ring-white" aria-hidden="true"></span><?php endif ?>
      <svg class="size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
    </button>
    </div>
  </div>
</header>

<?php /* Recherche instantanée sur tout le site (Ctrl + K ou la loupe) */ ?>
<dialog class="recherche-dialogue m-0 h-full max-h-none w-full max-w-none bg-transparent p-0 backdrop:bg-encre/45 backdrop:backdrop-blur-[3px] sm:mx-auto sm:mt-[9vh] sm:h-auto sm:w-[min(42rem,calc(100vw-2rem))]" aria-label="Rechercher sur le site" data-recherche
        data-suggestions="<?= e(url('/recherche/suggestions')) ?>">
  <div class="flex h-full flex-col bg-white sm:h-auto sm:max-h-[75vh] sm:rounded-3xl sm:shadow-2xl">
    <form method="get" action="<?= url('/recherche') ?>" class="flex items-center gap-2 border-b border-ligne px-3 py-2.5 sm:px-4" role="search">
      <span class="pl-1 text-vert" aria-hidden="true"><svg class="size-[22px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg></span>
      <label for="q-dialogue" class="sr-only">Rechercher</label>
      <input id="q-dialogue" name="q" type="search" placeholder="Une formation, une initiative, une communauté…" maxlength="100" autocomplete="off"
             class="min-h-12 min-w-0 flex-1 border-0 bg-transparent text-base text-encre placeholder:text-texte/70 focus:outline-none" data-recherche-champ
             role="combobox" aria-expanded="false" aria-controls="recherche-liste" aria-autocomplete="list">
      <button type="button" class="rounded-lg px-2.5 py-1.5 text-sm font-bold text-texte hover:bg-gris" data-fermer-recherche>Fermer</button>
    </form>
    <div class="min-h-0 flex-1 overflow-y-auto p-2 sm:p-3" id="recherche-liste" role="listbox" aria-label="Suggestions" data-recherche-resultats>
      <p class="px-3 py-6 text-center text-sm text-texte" data-recherche-aide>Tapez au moins deux lettres. Les accents et les majuscules n'ont pas d'importance.</p>
    </div>
    <p class="hidden border-t border-ligne px-4 py-2.5 text-xs text-texte sm:block"><kbd class="rounded border border-ligne px-1.5 font-sans">↑</kbd> <kbd class="rounded border border-ligne px-1.5 font-sans">↓</kbd> pour choisir · <kbd class="rounded border border-ligne px-1.5 font-sans">Entrée</kbd> pour ouvrir · <kbd class="rounded border border-ligne px-1.5 font-sans">Échap</kbd> pour fermer</p>
  </div>
</dialog>

<div id="menu-mobile" class="fixed inset-0 z-50 hidden xl:hidden" role="dialog" aria-modal="true" aria-label="Menu" data-menu-mobile>
  <div class="absolute inset-0 bg-encre/35 backdrop-blur-[3px]" data-fermer-menu></div>
  <div class="absolute inset-y-0 right-0 flex w-[84%] max-w-sm flex-col bg-white shadow-2xl">
    <div class="flex h-16 items-center justify-between border-b border-ligne px-4">
      <?php partiel('partials/logo', ['classes' => 'h-10 w-auto']) ?>
      <button type="button" class="-mr-2 grid size-12 place-items-center rounded-lg text-vert hover:bg-vert-clair" data-fermer-menu>
        <span class="sr-only">Fermer le menu</span>
        <svg class="size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
      </button>
    </div>
    <nav class="flex-1 overflow-y-auto px-4 py-4" aria-label="Menu principal">
      <ul class="space-y-1">
        <?php foreach ($navigation as [$lien, $libelle, $prefixe]): $actif = $section === $prefixe; ?>
        <li>
          <a href="<?= url($lien) ?>" class="flex min-h-12 items-center justify-between rounded-xl px-4 text-lg font-medium <?= $actif ? 'bg-vert-clair text-vert' : 'text-encre hover:bg-gris' ?>" <?= $actif ? 'aria-current="page"' : '' ?>>
            <?= e($libelle) ?>
            <?php if ($actif): ?><span class="size-2 rounded-full bg-orange" aria-hidden="true"></span><?php endif ?>
          </a>
        </li>
        <?php endforeach ?>
      </ul>
    </nav>
    <div class="space-y-3 border-t border-ligne p-4 pb-[max(1rem,env(safe-area-inset-bottom))]">
      <?php if ($u): ?>
        <a href="<?= url(est_membre($u) ? '/compte/mon-espace' : '/gestion') ?>" class="btn btn-vert w-full"><?= $pastille('size-7') ?> <?= est_membre($u) ? 'Mon espace' : 'Administration' ?><?php if ($nb_notifs_entete): ?> <span class="rounded-full bg-orange px-2 text-xs leading-5"><?= $nb_notifs_entete ?></span><?php endif ?></a>
        <form method="post" action="<?= url('/compte/deconnexion') ?>" data-confirmer="Êtes-vous sûr de vouloir vous déconnecter ?" data-confirmer-oui="Se déconnecter"><?= csrf() ?><button type="submit" class="btn btn-contour w-full">Se déconnecter</button></form>
      <?php else: ?>
        <a href="<?= url('/compte/inscription') ?>" class="btn btn-vert w-full">Rejoindre le chapitre</a>
        <a href="<?= url('/compte/connexion') ?>" class="btn btn-contour w-full">Se connecter</a>
      <?php endif ?>
    </div>
  </div>
</div>
