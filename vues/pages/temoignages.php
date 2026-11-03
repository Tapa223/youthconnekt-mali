<?php /** Tous les témoignages publiés */ ?>
<section class="relative isolate overflow-hidden bg-vert text-white">
  <?php partiel('partials/reseau_deco') ?>
  <div class="conteneur relative pt-6 pb-8 sm:py-10">
    <?php partiel('partials/retour', ['url' => url('/'), 'clair' => true]) ?>
    <h1 class="text-[2.1rem] leading-[1.05] font-bold text-white sm:text-5xl">Ils en parlent mieux que nous</h1>
    <p class="mt-3 max-w-xl text-[15px] text-white/90 sm:text-lg">Jeunes, organisations et partenaires racontent ce que YouthConnekt Mali leur a apporté.</p>
  </div>
</section>

<section class="bg-gris py-10 sm:py-16">
  <div class="conteneur">
    <?php if ($temoignages): ?>
    <ul class="grid gap-4 sm:grid-cols-2 sm:gap-5 lg:grid-cols-3">
      <?php foreach (array_values($temoignages) as $i => $t): ?>
      <li>
        <figure class="survol-leve flex h-full flex-col gap-3 rounded-[18px] border-t-[5px] bg-white p-5 shadow-sm sm:p-7 <?= cycle(['border-orange', 'border-turquoise', 'border-bleu'], $i) ?>">
          <span class="h-6 text-5xl leading-[.6] font-bold text-orange" aria-hidden="true">«</span>
          <blockquote class="text-[15px] leading-relaxed text-encre sm:text-[17px]"><?= e($t['texte']) ?></blockquote>
          <figcaption class="mt-auto flex items-center gap-3 pt-2">
            <?php if ($t['photo']): ?><img src="<?= e(media($t['photo'])) ?>" alt="" class="size-11 rounded-full object-cover" loading="lazy">
            <?php else: ?><span class="grid size-11 shrink-0 place-items-center rounded-full text-sm font-bold text-white <?= cycle(['bg-vert', 'bg-turquoise', 'bg-bleu'], $i) ?>" aria-hidden="true"><?= e(initiales($t['nom'])) ?></span><?php endif ?>
            <span><strong class="block text-vert"><?= e($t['nom']) ?></strong><?php if ($t['role']): ?><span class="block text-sm text-encre"><?= e($t['role']) ?></span><?php endif ?></span>
          </figcaption>
        </figure>
      </li>
      <?php endforeach ?>
    </ul>
    <?php else: ?>
    <p class="rounded-2xl bg-white p-6 text-center">Les premiers témoignages arrivent bientôt.</p>
    <?php endif ?>
  </div>
</section>

<?php partiel('partials/bandeau_lets_connekt') ?>
