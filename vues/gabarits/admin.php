<?php
/*
 * Gabarit de l'administration, d'après la maquette validée :
 * menu latéral vert rangé par rubriques, barre du haut (fil d'Ariane, recherche, « Voir le site », compte).
 * Variables : $titre, $contenu, $menu, $admin, $ariane, $recherche_liste (formulaire de recherche de la liste)
 */
$messages = flashs();
$ariane = $ariane ?? [];
?><!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, nofollow">
  <title><?= e($titre ?? 'Administration YouthConnekt Mali') ?></title>
  <link rel="icon" type="image/png" href="<?= asset('img/favicon.png') ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@400;500;700&display=swap">
  <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
  <script src="<?= asset('js/admin.js') ?>" defer></script>
</head>
<body class="ycm-avec-menu">
  <nav id="menu-admin" class="ycm-menu" aria-label="Menu de l'administration">
    <div class="ycm-menu-tete">
      <a href="<?= url('/gestion') ?>" class="ycm-menu-logo" aria-label="Tableau de bord">
        <img src="<?= asset('img/logo-youthconnekt-mali.png') ?>" alt="YouthConnekt Mali" width="652" height="293">
        <span>Admin</span>
      </a>
      <button type="button" class="ycm-menu-fermer" data-fermer-menu-admin><span class="ycm-cache">Fermer le menu</span><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg></button>
    </div>
    <?php foreach ($menu as $groupe): ?>
      <?php if ($groupe['rubrique']): ?><p class="ycm-menu-rubrique"><?= e($groupe['rubrique']) ?></p><?php endif ?>
      <ul>
        <?php foreach ($groupe['liens'] as $lien): ?>
        <li>
          <a href="<?= e($lien['url']) ?>"<?= $lien['actif'] ? ' class="actif" aria-current="page"' : '' ?>>
            <svg viewBox="0 0 24 24" aria-hidden="true"><?= $lien['icone'] ?></svg>
            <span><?= e($lien['libelle']) ?></span>
            <?php if ($lien['pastille']): ?><span class="ycm-pastille" aria-label="<?= $lien['pastille'] ?> à traiter"><?= $lien['pastille'] ?></span><?php endif ?>
          </a>
        </li>
        <?php endforeach ?>
      </ul>
    <?php endforeach ?>
  </nav>
  <div class="ycm-voile" data-fermer-menu-admin hidden></div>

  <div class="ycm-page">
    <header class="ycm-barre">
      <button type="button" class="ycm-burger" data-ouvrir-menu-admin aria-controls="menu-admin" aria-expanded="false">
        <span class="ycm-cache">Ouvrir le menu</span>
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
      </button>
      <nav class="ycm-ariane" aria-label="Fil d'Ariane">
        <ol>
          <?php if ($ariane): ?><li><a href="<?= url('/gestion') ?>">Tableau de bord</a></li><?php else: ?><li aria-current="page">Tableau de bord</li><?php endif ?>
          <?php foreach ($ariane as [$libelle, $lien]): ?>
          <li<?= $lien ? '' : ' aria-current="page"' ?>><?php if ($lien): ?><a href="<?= e($lien) ?>"><?= e($libelle) ?></a><?php else: ?><?= e($libelle) ?><?php endif ?></li>
          <?php endforeach ?>
        </ol>
      </nav>
      <div class="ycm-outils">
        <?php if (!empty($recherche_liste)): ?>
        <form class="ycm-recherche" method="get" action="<?= e($recherche_liste['action']) ?>" role="search">
          <label for="ycm-q" class="ycm-cache">Rechercher dans cette liste</label>
          <input id="ycm-q" type="search" name="q" value="<?= e($recherche_liste['q']) ?>" placeholder="Rechercher…">
          <?php foreach ($recherche_liste['garder'] as $k => $v): ?><input type="hidden" name="<?= e($k) ?>" value="<?= e($v) ?>"><?php endforeach ?>
        </form>
        <?php else: ?>
        <form class="ycm-recherche" method="get" action="<?= url('/gestion/recherche') ?>" role="search">
          <label for="ycm-q" class="ycm-cache">Rechercher dans l'administration</label>
          <input id="ycm-q" type="search" name="q" value="<?= e($q ?? '') ?>" placeholder="Rechercher…">
        </form>
        <?php endif ?>
        <a href="<?= url('/') ?>" class="ycm-bouton-contour ycm-voir-site" target="_blank" rel="noopener">Voir le site</a>
        <details class="ycm-compte">
          <summary>
            <span class="ycm-avatar ycm-avatar--orange" aria-hidden="true"><?= e(initiales($admin['nom_affiche'])) ?></span>
            <span class="ycm-compte-texte"><strong><?= e($admin['nom_affiche']) ?></strong><small><?= e(ROLES_ADMIN[$admin['role_admin']] ?? '') ?></small></span>
          </summary>
          <div class="ycm-compte-menu">
            <a href="<?= url('/compte/mon-espace/parametres') ?>">Changer mon mot de passe</a>
            <a href="<?= url('/compte/mon-espace') ?>">Mon espace membre</a>
            <form method="post" action="<?= url('/compte/deconnexion') ?>"><?= csrf() ?><button type="submit">Se déconnecter</button></form>
          </div>
        </details>
      </div>
    </header>

    <?php if ($messages): ?>
    <ul class="ycm-messages" role="status" aria-live="polite">
      <?php foreach ($messages as $m): ?><li class="<?= $m['type'] === 'erreur' ? 'erreur' : '' ?>"><?= e($m['texte']) ?></li><?php endforeach ?>
    </ul>
    <?php endif ?>

    <main id="contenu" class="ycm-contenu">
      <?= $contenu ?>
    </main>
  </div>
</body>
</html>
