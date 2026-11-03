<?php /* Témoignages : trois cartes superposées qui flottent. Paramètre : $temoignages */
if (empty($temoignages)) return;
$rotations_tel = ['max-md:mr-6 max-md:-rotate-[1.5deg]', 'max-md:-mt-2.5 max-md:ml-6 max-md:rotate-[1.5deg]', 'max-md:-mt-2.5 max-md:mr-3 max-md:ml-1.5 max-md:-rotate-1'];
$rotations = ['md:z-[1] md:translate-x-[30px] md:translate-y-10 md:-rotate-[4deg]', 'md:z-[2] md:rotate-1', 'md:z-[1] md:-translate-x-[30px] md:translate-y-14 md:rotate-[4deg]'];
?>
<section class="relative isolate overflow-hidden bg-vert py-9 sm:py-20 lg:pb-28" aria-labelledby="titre-temoignages">
  <img src="<?= asset('img/bandeaux/temoignages.jpg') ?>" alt="" class="absolute inset-0 -z-10 h-full w-full object-cover opacity-[.12]" loading="lazy">
  <?php partiel('partials/reseau_deco') ?>
  <div class="conteneur relative">
    <p class="surtitre text-white/85 sm:flex sm:justify-center">Témoignages</p>
    <h2 id="titre-temoignages" class="titre-section text-white sm:text-center">Ils en parlent mieux que nous</h2>
    <ul class="mt-4 grid md:mt-14 md:grid-cols-3 md:gap-0 md:px-5">
      <?php foreach (array_values($temoignages) as $i => $t): ?>
      <li class="relative <?= cycle($rotations_tel, $i) ?> <?= cycle($rotations, $i) ?>">
        <div class="<?= cycle(['anim-flotte', 'anim-flotte-2', 'anim-flotte'], $i) ?> h-full">
          <figure class="survol-carte flex h-full flex-col gap-2 rounded-[14px] border-t-4 bg-white px-4 py-3.5 shadow-[0_14px_30px_rgb(0_0_0/.22)] md:gap-4 md:rounded-[22px] md:border-t-[6px] md:p-7 md:shadow-[0_22px_48px_rgb(0_0_0/.25)] <?= cycle(['border-orange', 'border-turquoise', 'border-orange'], $i) ?>">
            <span class="hidden h-7 text-6xl leading-[.6] font-bold text-orange md:block" aria-hidden="true">«</span>
            <blockquote class="text-[13px] leading-relaxed text-encre md:text-[18px] md:leading-[1.55]"><span class="md:hidden">« </span><?= e($t['texte']) ?><span class="md:hidden"> »</span></blockquote>
            <figcaption class="mt-auto flex items-center gap-2 md:gap-3">
              <?php if ($t['photo']): ?><img src="<?= e(media($t['photo'])) ?>" alt="" class="size-7 rounded-full object-cover md:size-11" loading="lazy">
              <?php else: ?><span class="grid size-7 shrink-0 place-items-center rounded-full text-[11px] font-bold text-white md:size-11 md:text-sm <?= cycle(['bg-vert', 'bg-turquoise', 'bg-bleu'], $i) ?>" aria-hidden="true"><?= e(initiales($t['nom'])) ?></span><?php endif ?>
              <span class="text-xs md:text-base"><span class="font-bold text-vert md:block"><?= e($t['nom']) ?></span><?php if ($t['role']): $parties = explode(' · ', $t['role']); ?><span class="text-texte md:block md:text-sm md:text-encre"><span class="md:hidden"> · <?= e(end($parties)) ?></span><span class="hidden md:inline"><?= e($t['role']) ?></span></span><?php endif ?></span>
            </figcaption>
          </figure>
        </div>
      </li>
      <?php endforeach ?>
    </ul>
  </div>
</section>
