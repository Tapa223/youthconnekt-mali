<?php
$u = utilisateur();
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
<header class="sticky top-0 z-40 border-b border-transparent bg-white/95 backdrop-blur transition-[border-color,box-shadow] duration-200 data-[defile]:border-ligne data-[defile]:shadow-sm" data-entete>
  <div class="conteneur flex h-16 items-center justify-between gap-6 xl:h-[76px] xl:max-w-[78rem]">
    <a href="<?= url('/') ?>" class="shrink-0 rounded-md" aria-label="YouthConnekt Mali, retour à l'accueil">
      <?php partiel('partials/logo', ['classes' => 'h-10 w-auto xl:h-11']) ?>
    </a>

    <nav class="hidden xl:block" aria-label="Menu principal">
      <ul class="flex items-center gap-1 2xl:gap-2">
        <?php foreach ($navigation as [$lien, $libelle, $prefixe]): $actif = $section === $prefixe; ?>
        <li>
          <a href="<?= url($lien) ?>"
             class="relative flex h-[76px] items-center px-3 text-[15px] font-medium whitespace-nowrap transition-colors <?= $actif ? 'text-vert after:absolute after:inset-x-3 after:bottom-0 after:h-[3px] after:rounded-t after:bg-orange' : 'text-encre hover:text-orange' ?>"
             <?= $actif ? 'aria-current="page"' : '' ?>><?= e($libelle) ?></a>
        </li>
        <?php endforeach ?>
      </ul>
    </nav>

    <div class="hidden items-center gap-2.5 xl:flex">
      <?php if ($u): ?>
        <a href="<?= url(est_membre($u) ? '/compte/mon-espace' : '/gestion') ?>" class="inline-flex min-h-11 items-center gap-2.5 rounded-full bg-vert py-1.5 pr-5 pl-1.5 text-[15px] font-bold text-white hover:bg-vert-fonce">
          <span class="grid size-8 place-items-center rounded-full bg-orange text-xs" aria-hidden="true"><?= e($u['initiales']) ?></span>
          <?= est_membre($u) ? 'Mon espace' : 'Administration' ?>
        </a>
      <?php else: ?>
        <a href="<?= url('/compte/connexion') ?>" class="btn btn-contour px-4 whitespace-nowrap">Se connecter</a>
        <a href="<?= url('/compte/inscription') ?>" class="btn btn-vert px-5 whitespace-nowrap">Rejoindre le chapitre</a>
      <?php endif ?>
    </div>

    <button type="button" class="-mr-2 grid size-12 place-items-center rounded-lg text-vert hover:bg-vert-clair xl:hidden" aria-controls="menu-mobile" aria-expanded="false" data-ouvrir-menu>
      <span class="sr-only">Ouvrir le menu</span>
      <svg class="size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
    </button>
  </div>
</header>

<div id="menu-mobile" class="fixed inset-0 z-50 hidden xl:hidden" role="dialog" aria-modal="true" aria-label="Menu" data-menu-mobile>
  <div class="absolute inset-0 bg-encre/40" data-fermer-menu></div>
  <div class="absolute inset-y-0 right-0 flex w-full max-w-sm flex-col bg-white shadow-2xl">
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
        <a href="<?= url(est_membre($u) ? '/compte/mon-espace' : '/gestion') ?>" class="btn btn-vert w-full"><span class="grid size-7 place-items-center rounded-full bg-orange text-xs" aria-hidden="true"><?= e($u['initiales']) ?></span> <?= est_membre($u) ? 'Mon espace' : 'Administration' ?></a>
        <form method="post" action="<?= url('/compte/deconnexion') ?>"><?= csrf() ?><button type="submit" class="btn btn-contour w-full">Se déconnecter</button></form>
      <?php else: ?>
        <a href="<?= url('/compte/inscription') ?>" class="btn btn-vert w-full">Rejoindre le chapitre</a>
        <a href="<?= url('/compte/connexion') ?>" class="btn btn-contour w-full">Se connecter</a>
      <?php endif ?>
    </div>
  </div>
</div>
