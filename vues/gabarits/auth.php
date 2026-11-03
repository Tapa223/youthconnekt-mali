<?php
/** Gabarit des pages de connexion et d'inscription. Variables : $titre, $contenu, $accroche, $accroche_mobile, $largeur */
$messages = flashs();
// Bouton « Retour » : ramène à la page du site d'où la personne est venue (pas à une autre page de connexion)
$provenance = (string) ($_SERVER['HTTP_REFERER'] ?? '');
if ($provenance !== '' && parse_url($provenance, PHP_URL_HOST) === ($_SERVER['HTTP_HOST'] ?? '') && !preg_match('#/compte/(connexion|inscription|mot-de-passe|mdp|verification|confirmer)#', $provenance)) {
    $_SESSION['retour_auth'] = $provenance;
}
$retour_auth = $_SESSION['retour_auth'] ?? url('/');
?><!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script>document.documentElement.classList.add("js")</script>
  <title><?= e($titre ?? 'YouthConnekt Mali') ?></title>
  <meta name="theme-color" content="#035E4D">
  <meta name="robots" content="noindex">
  <link rel="icon" type="image/png" href="<?= asset('img/favicon.png') ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@400;500;700&display=swap">
  <link rel="stylesheet" href="<?= asset('css/site.css') ?>">
</head>
<body class="min-h-screen lg:grid lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)]">
  <aside class="relative isolate flex flex-col overflow-hidden bg-vert px-5 pt-5 pb-8 text-white lg:sticky lg:top-0 lg:h-screen lg:px-10 lg:pt-8">
    <?php partiel('partials/reseau_deco') ?>
    <a href="<?= url('/') ?>" class="relative inline-flex self-start rounded-xl bg-white px-3 py-2" aria-label="YouthConnekt Mali, retour à l'accueil">
      <?php partiel('partials/logo', ['classes' => 'h-9 w-auto lg:h-11']) ?>
    </a>
    <div class="relative hidden flex-1 flex-col items-center justify-center text-center lg:flex">
      <div class="relative size-[min(52vh,360px)]">
        <div class="absolute -inset-5 rounded-full border-2 border-dashed border-white/35" aria-hidden="true"></div>
        <div class="anim-orbite absolute inset-0" aria-hidden="true">
          <span class="absolute -top-6 left-1/2 size-3.5 -translate-x-1/2 rounded-full bg-white"></span>
          <span class="absolute top-1/2 -left-6 size-3 -translate-y-1/2 rounded-full bg-orange"></span>
          <span class="absolute top-1/2 -right-6 size-3.5 -translate-y-1/2 rounded-full bg-orange"></span>
          <span class="absolute bottom-[8%] left-[6%] size-3.5 rounded-full bg-bleu"></span>
          <span class="absolute top-[8%] right-[6%] size-3 rounded-full bg-turquoise"></span>
        </div>
        <div class="anim-soleil h-full w-full overflow-hidden rounded-full ring-8 ring-turquoise/30"><img src="<?= asset('img/connexion/cercle.jpg') ?>" alt="" class="h-full w-full object-cover"></div>
      </div>
      <p class="mt-12 text-5xl font-bold">Let's Connekt</p>
      <p class="mt-3 max-w-sm text-white/90"><?= e($accroche ?? 'Jeunes, entreprises et organisations : un seul chapitre pour avancer ensemble.') ?></p>
    </div>
    <p class="relative mt-4 text-base text-white/90 lg:hidden"><?= e($accroche_mobile ?? "Let's Connekt : un seul chapitre pour avancer ensemble.") ?></p>
    <?php partiel('partials/mention_photo') ?>
  </aside>

  <main id="contenu" class="flex justify-center px-5 py-10 sm:px-8 lg:items-center lg:py-12">
    <div class="anim-entree w-full <?= e($largeur ?? 'max-w-md') ?>">
      <?php partiel('partials/retour', ['url' => $retour_auth]) ?>
      <?php if ($messages): ?>
      <div class="mb-5 space-y-2" role="status" aria-live="polite">
        <?php foreach ($messages as $m): ?><p class="rounded-xl border px-4 py-3 text-sm font-medium <?= $m['type'] === 'erreur' ? 'border-rouge/30 text-rouge' : 'border-vert/20 bg-vert-clair text-vert' ?>"><?= e($m['texte']) ?></p><?php endforeach ?>
      </div>
      <?php endif ?>
      <?php partiel('partials/lien_email_test') ?>
      <?= $contenu ?>
    </div>
  </main>
  <script src="<?= asset('js/site.js') ?>" defer></script>
</body>
</html>
