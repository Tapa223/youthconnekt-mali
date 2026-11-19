<?php
/** Gabarit des pages publiques et de l'espace membre. Variables : $titre, $description, $contenu, $noindex, $image_partage, $donnees_structurees */
$messages = flashs();
?><!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script>document.documentElement.classList.add("js")</script>
  <?php
  $titre_seo = titre_page($titre ?? null);
  $description_seo = description_page($description ?? null);
  // Adresse officielle de la page (les listes gardent leur numéro de page)
  $page_n = (int) get('page');
  $canonique = url_absolue(chemin_actuel()) . ($page_n > 1 ? '?page=' . $page_n : '');
  $image_og = !empty($image_partage) ? url_absolue_media($image_partage) : url_absolue('/assets/img/accueil/premier-ecran.jpg');
  $blocs_seo = $donnees_structurees ?? [];
  if (chemin_actuel() === '/') {
      $blocs_seo = array_merge([schema_organisation(), schema_site()], $blocs_seo);
  }
  ?>
  <title><?= e($titre_seo) ?></title>
  <meta name="description" content="<?= e($description_seo) ?>">
  <?php if (!empty($noindex)): ?><meta name="robots" content="noindex"><?php else: ?><meta name="robots" content="index, follow, max-image-preview:large"><?php endif ?>
  <?php if ((string) config('google_verification', '') !== ''): ?><meta name="google-site-verification" content="<?= e(config('google_verification')) ?>"><?php endif ?>
  <meta name="theme-color" content="#035E4D">
  <link rel="canonical" href="<?= e($canonique) ?>">
  <meta property="og:type" content="<?= !empty($image_partage) ? 'article' : 'website' ?>">
  <meta property="og:title" content="<?= e($titre_seo) ?>">
  <meta property="og:description" content="<?= e($description_seo) ?>">
  <meta property="og:url" content="<?= e($canonique) ?>">
  <meta property="og:image" content="<?= e($image_og) ?>">
  <meta property="og:image:alt" content="<?= e($titre_seo) ?>">
  <meta property="og:locale" content="fr_FR">
  <meta property="og:site_name" content="YouthConnekt Mali">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?= e($titre_seo) ?>">
  <meta name="twitter:description" content="<?= e($description_seo) ?>">
  <meta name="twitter:image" content="<?= e($image_og) ?>">
  <?= donnees_structurees_html($blocs_seo) ?>
  <link rel="icon" type="image/png" href="<?= asset('img/favicon.png') ?>">
  <link rel="apple-touch-icon" href="<?= asset('img/favicon.png') ?>">
  <link rel="preload" href="<?= base_url() ?>/assets/fonts/ubuntu-latin-400-normal.woff2" as="font" type="font/woff2" crossorigin>
  <link rel="stylesheet" href="<?= asset('css/polices.css') ?>">
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
