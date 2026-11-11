<?php $code = (int) ($code ?? 500); ?>
<section class="relative isolate overflow-hidden bg-vert text-white">
  <?php partiel('partials/reseau_deco', ['opacite' => 'opacity-80']) ?>
  <div class="conteneur relative grid items-center gap-10 py-16 sm:py-24 lg:grid-cols-[1.2fr_1fr]">
    <div>
      <p class="surtitre surtitre-trait text-white/85">Erreur <?= $code ?></p>
      <h1 class="mt-3 text-4xl leading-none font-bold text-white sm:text-6xl"><?= e($titre_erreur ?? 'Erreur') ?></h1>
      <p class="mt-5 max-w-xl text-base text-white/90 sm:text-lg"><?= e($texte_erreur ?? '') ?></p>
      <?php if ($code === 404): ?>
      <form method="get" action="<?= url('/recherche') ?>" class="mt-7 max-w-lg" role="search">
        <label for="q-erreur" class="text-sm font-bold text-white/90">Cherchez ce dont vous avez besoin</label>
        <div class="relative mt-2">
          <input id="q-erreur" name="q" type="search" placeholder="Une formation, une initiative…" maxlength="100" class="min-h-13 w-full rounded-full border-0 bg-white pr-14 pl-5 text-base text-encre focus:ring-[3px] focus:ring-orange/60 focus:outline-none">
          <button type="submit" class="absolute top-1/2 right-1.5 grid size-10 -translate-y-1/2 place-items-center rounded-full bg-orange text-white hover:bg-vert-fonce" aria-label="Rechercher"><svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg></button>
        </div>
      </form>
      <?php endif ?>
      <div class="mt-7 flex flex-wrap gap-3">
        <a href="<?= url('/') ?>" class="btn btn-orange">Retour à l'accueil</a>
        <?php if ($code === 404): ?>
        <a href="<?= url('/opportunites') ?>" class="btn btn-contour-blanc">Opportunités</a>
        <a href="<?= url('/initiatives') ?>" class="btn btn-contour-blanc">Initiatives</a>
        <?php else: ?>
        <a href="<?= url('/contact') ?>" class="btn btn-blanc">Nous écrire</a>
        <?php endif ?>
      </div>
    </div>
    <?php /* Un point s'est détaché du réseau : il cherche son chemin */ ?>
    <svg class="erreur-reseau mx-auto hidden w-full max-w-sm lg:block" viewBox="0 0 320 260" aria-hidden="true">
      <g stroke="#fff" stroke-opacity=".35" stroke-width="2" fill="none"><path d="M40 60 L130 30 L220 80 L280 40"/><path d="M130 30 L110 160 L200 220"/><path d="M220 80 L250 190"/></g>
      <path class="erreur-fil" d="M220 80 L262 128" stroke="#F1592A" stroke-width="2" stroke-dasharray="5 7" fill="none"/>
      <g fill="#fff"><circle cx="40" cy="60" r="7"/><circle cx="130" cy="30" r="9" fill="#04B2B1"/><circle cx="220" cy="80" r="8"/><circle cx="280" cy="40" r="6" fill="#04B2B1"/><circle cx="110" cy="160" r="7" fill="#0358D9"/><circle cx="200" cy="220" r="8"/><circle cx="250" cy="190" r="7" fill="#04B2B1"/></g>
      <circle class="erreur-point" cx="282" cy="150" r="11" fill="#F1592A" stroke="#035E4D" stroke-width="4"/>
    </svg>
  </div>
</section>
