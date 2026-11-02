<?php /* En-tête vert des pages intérieures simples. Paramètres : $surtitre, $titre_page, $intro */ ?>
<section class="relative isolate overflow-hidden bg-vert text-white">
  <?php partiel('partials/reseau_deco') ?>
  <div class="conteneur relative py-14 sm:py-20">
    <p class="surtitre surtitre-trait text-white/85"><?= e($surtitre) ?></p>
    <h1 class="mt-3 text-4xl leading-none font-bold text-white sm:text-6xl"><?= e($titre_page) ?></h1>
    <?php if (!empty($intro)): ?><p class="mt-5 max-w-xl text-base text-white/90 sm:text-lg"><?= e($intro) ?></p><?php endif ?>
  </div>
</section>
