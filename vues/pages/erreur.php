<section class="relative isolate overflow-hidden bg-vert text-white">
  <?php partiel('partials/reseau_deco') ?>
  <div class="conteneur relative py-20 sm:py-28">
    <p class="surtitre surtitre-trait text-white/85">Erreur <?= (int) ($code ?? 500) ?></p>
    <h1 class="mt-3 text-4xl leading-none font-bold text-white sm:text-6xl"><?= e($titre_erreur ?? 'Erreur') ?></h1>
    <p class="mt-5 max-w-xl text-base text-white/90 sm:text-lg"><?= e($texte_erreur ?? '') ?></p>
    <div class="mt-8 flex flex-wrap gap-3">
      <a href="<?= url('/') ?>" class="btn btn-orange">Retour à l'accueil</a>
      <a href="<?= url('/contact') ?>" class="btn btn-blanc">Nous écrire</a>
    </div>
  </div>
</section>
