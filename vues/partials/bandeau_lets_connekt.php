<section class="relative isolate overflow-hidden bg-vert text-white" aria-label="Rejoindre le chapitre">
  <img src="<?= asset('img/bandeaux/lets-connekt.jpg') ?>" alt="" class="absolute inset-0 -z-10 h-full w-full object-cover" loading="lazy">
  <div class="absolute inset-0 -z-10 bg-vert/85"></div>
  <?php partiel('partials/mention_photo') ?>
  <div class="conteneur flex flex-col items-start gap-6 py-14 sm:py-16 md:flex-row md:items-center md:justify-between">
    <div>
      <p class="text-5xl leading-none font-bold sm:text-6xl lg:text-7xl">Let's Connekt</p>
      <p class="mt-3 max-w-lg text-base text-white/90">Jeunes, entreprises et organisations : le chapitre YouthConnekt Mali vous ouvre ses portes.</p>
    </div>
    <?php if (est_connecte()): ?>
    <a href="<?= url('/communaute') ?>" class="btn btn-orange btn-connekt min-h-13 px-7 text-lg"><span class="points-connekt" aria-hidden="true"><i></i><i></i><i></i><i></i></span>Découvrir les communautés</a>
    <?php else: ?>
    <a href="<?= url('/compte/inscription') ?>" class="btn btn-orange btn-connekt min-h-13 px-7 text-lg"><span class="points-connekt" aria-hidden="true"><i></i><i></i><i></i><i></i></span>Rejoindre le chapitre</a>
    <?php endif ?>
  </div>
</section>
