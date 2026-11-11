<?php
/** Gabarit des pages publiques et de l'espace membre. Variables : $titre, $description, $contenu, $noindex */
$messages = flashs();
?><!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script>document.documentElement.classList.add("js")</script>
  <title><?= e($titre ?? 'YouthConnekt Mali') ?></title>
  <meta name="description" content="<?= e($description ?? 'YouthConnekt Mali relie les jeunes du pays aux opportunités, aux initiatives du chapitre et entre eux.') ?>">
  <?php if (!empty($noindex)): ?><meta name="robots" content="noindex"><?php endif ?>
  <meta name="theme-color" content="#035E4D">
  <?php $image_og = !empty($image_partage) ? url_absolue_media($image_partage) : url_absolue('/assets/img/accueil/premier-ecran.jpg'); ?>
  <link rel="canonical" href="<?= e(url_absolue(chemin_actuel())) ?>">
  <meta property="og:type" content="<?= !empty($image_partage) ? 'article' : 'website' ?>">
  <meta property="og:title" content="<?= e($titre ?? 'YouthConnekt Mali') ?>">
  <meta property="og:description" content="<?= e($description ?? 'YouthConnekt Mali relie les jeunes du pays aux opportunités, aux initiatives du chapitre et entre eux.') ?>">
  <meta property="og:url" content="<?= e(url_absolue(chemin_actuel())) ?>">
  <meta property="og:image" content="<?= e($image_og) ?>">
  <meta property="og:locale" content="fr_FR">
  <meta property="og:site_name" content="YouthConnekt Mali">
  <meta name="twitter:card" content="summary_large_image">
  <link rel="icon" type="image/png" href="<?= asset('img/favicon.png') ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@400;500;700&display=swap">
  <link rel="stylesheet" href="<?= asset('css/site.css') ?>">
</head>
<body class="flex min-h-screen flex-col">
  <a href="#contenu" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[60] focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:font-bold focus:text-vert focus:shadow">Aller au contenu</a>

  <?php partiel('partials/entete') ?>

  <?php partiel('partials/lien_email_test') ?>

  <?php if ($messages): ?>
  <div class="fixed inset-x-4 bottom-4 z-50 mx-auto max-w-md space-y-2 sm:right-6 sm:left-auto" role="status" aria-live="polite" data-messages>
    <?php foreach ($messages as $m): ?>
    <div class="flex items-start gap-3 rounded-xl border px-4 py-3 text-sm shadow-lg <?= $m['type'] === 'erreur' ? 'border-rouge/30 bg-white text-rouge' : 'border-vert/20 bg-white text-vert' ?>">
      <p class="flex-1 font-medium"><?= e($m['texte']) ?></p>
      <button type="button" class="-m-1 grid size-8 shrink-0 place-items-center rounded-md hover:bg-gris" data-fermer-message aria-label="Fermer ce message">
        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
      </button>
    </div>
    <?php endforeach ?>
  </div>
  <?php endif ?>

  <main id="contenu" class="flex-1">
    <?= $contenu ?>
  </main>

  <?php partiel('partials/pied') ?>

  <?php partiel('partials/aide') ?>

  <script src="<?= asset('js/site.js') ?>" defer></script>
</body>
</html>
