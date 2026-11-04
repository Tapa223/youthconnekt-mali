<?php /** Tous les témoignages publiés */ ?>
<section class="relative isolate overflow-hidden bg-vert text-white">
  <?php partiel('partials/reseau_deco') ?>
  <div class="conteneur relative pt-2 pb-5 sm:py-10">
    <?php partiel('partials/retour', ['url' => url('/'), 'clair' => true]) ?>
    <h1 class="text-[1.6rem] leading-[1.1] font-bold text-white sm:text-5xl">Ils en parlent mieux que nous</h1>
    <p class="mt-2 max-w-xl text-sm text-white/90 sm:mt-3 sm:text-lg">Jeunes, organisations et partenaires racontent ce que YouthConnekt Mali leur a apporté.</p>
  </div>
</section>

<section class="bg-gris pt-4 pb-10 sm:py-16">
  <div class="conteneur">
    <?php if ($temoignages): ?>
    <ul class="grid gap-3 sm:grid-cols-2 sm:gap-5 lg:grid-cols-3">
      <?php foreach (array_values($temoignages) as $i => $t): ?>
      <li>
        <figure class="survol-leve flex h-full flex-col gap-2 rounded-2xl border-t-4 bg-white p-4 shadow-sm sm:gap-3 sm:rounded-[18px] sm:border-t-[5px] sm:p-7 <?= cycle(['border-orange', 'border-turquoise', 'border-bleu'], $i) ?>">
          <span class="hidden h-6 text-5xl leading-[.6] font-bold text-orange sm:block" aria-hidden="true">«</span>
          <blockquote class="text-sm leading-relaxed text-encre sm:text-[17px]"><span class="font-bold text-orange sm:hidden">« </span><?= e($t['texte']) ?></blockquote>
          <figcaption class="mt-auto flex items-center gap-2.5 pt-1 sm:gap-3 sm:pt-2">
            <?php if ($t['photo']): ?><img src="<?= e(media($t['photo'])) ?>" alt="" class="size-9 rounded-full object-cover sm:size-11" loading="lazy">
            <?php else: ?><span class="grid size-9 shrink-0 place-items-center rounded-full text-xs sm:size-11 sm:text-sm font-bold text-white <?= cycle(['bg-vert', 'bg-turquoise', 'bg-bleu'], $i) ?>" aria-hidden="true"><?= e(initiales($t['nom'])) ?></span><?php endif ?>
            <span><strong class="block text-sm text-vert sm:text-base"><?= e($t['nom']) ?></strong><?php if ($t['role']): ?><span class="block text-xs text-encre sm:text-sm"><?= e($t['role']) ?></span><?php endif ?></span>
          </figcaption>
        </figure>
      </li>
      <?php endforeach ?>
    </ul>
    <?php else: ?>
    <p class="rounded-2xl bg-white p-6 text-center">Les premiers témoignages arrivent bientôt.</p>
    <?php endif ?>
    <?php /* Invitation à témoigner : depuis l'espace membre (connexion demandée si besoin) */ $lien_t = url('/compte/mon-espace/propositions', ['temoigner' => 1]) . '#temoignage'; ?>
    <div class="mt-6 flex flex-col items-start gap-3 rounded-2xl bg-white p-4 sm:mt-8 sm:flex-row sm:items-center sm:justify-between sm:p-6">
      <p class="text-sm text-encre sm:text-base"><strong class="block text-vert">Vous aussi, racontez votre expérience</strong>Membre du chapitre ? Partagez ce que YouthConnekt Mali vous a apporté.</p>
      <a href="<?= est_connecte() ? $lien_t : url('/compte/connexion', ['suivant' => $lien_t]) ?>" class="btn btn-orange min-h-11 shrink-0 px-6">Témoigner</a>
    </div>
  </div>
</section>

<?php partiel('partials/bandeau_lets_connekt') ?>
