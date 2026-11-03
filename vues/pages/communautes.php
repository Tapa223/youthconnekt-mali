<?php $connecte = est_connecte(); ?>
<section class="relative isolate overflow-hidden bg-vert text-white">
  <?php partiel('partials/reseau_deco') ?>
  <div class="conteneur anim-entree relative pt-6 pb-5 sm:pt-10 sm:pb-8">
    <h1 class="text-[2.1rem] leading-none font-bold text-white sm:text-6xl">Communauté</h1>
    <p class="mt-2.5 text-[15px] text-white/90 sm:mt-3 sm:text-lg">Découvrez nos différentes communautés.</p>
  </div>
</section>

<section class="conteneur pt-5 pb-10 sm:pt-8 sm:pb-16" aria-label="Les communautés">
  <?php if ($communautes): ?>
  <ul class="grid grid-cols-2 gap-2.5 sm:gap-5 lg:grid-cols-3">
    <?php foreach ($communautes as $k => $c): ?>
    <li>
      <a href="<?= e($c['url']) ?>" class="carte group flex h-full flex-col transition-shadow hover:shadow-lg">
        <span class="relative block aspect-[4/3] sm:aspect-[16/9]">
          <?php if ($c['image']): ?><img src="<?= e(media($c['image'])) ?>" alt="" class="absolute inset-0 h-full w-full object-cover" loading="lazy">
          <?php else: ?><img src="<?= asset('img/communaute/carte-' . ($k % 6 + 1) . '.jpg') ?>" alt="" class="absolute inset-0 h-full w-full object-cover" loading="lazy"><?php endif ?>
          <span class="absolute inset-0 bg-gradient-to-t from-vert/85 via-vert/15 to-transparent"></span>
          <span class="absolute bottom-2 left-2.5 leading-none text-white sm:bottom-3 sm:left-4">
            <?php if ($c['nb_membres']): ?><span class="text-lg font-bold sm:text-2xl"><?= $c['nb_membres'] ?></span> <span class="text-[11px] sm:text-sm">membre<?= pluriel($c['nb_membres']) ?></span>
            <?php else: ?><span class="text-[11px] font-bold sm:text-sm">Nouvelle communauté</span><?php endif ?>
          </span>
          <?php if ($c['statut_membre'] === 'acceptee'): ?><span class="etiquette absolute top-2 left-2 rounded-full bg-white text-[10px] text-vert sm:top-3 sm:left-3 sm:text-xs">Membre</span>
          <?php elseif ($c['statut_membre'] === 'en_attente'): ?><span class="etiquette absolute top-2 left-2 rounded-full bg-orange text-[10px] text-white sm:top-3 sm:left-3 sm:text-xs">Demande en cours</span><?php endif ?>
        </span>
        <span class="flex flex-1 flex-col p-3 sm:p-5">
          <span class="text-[15px] leading-tight font-bold text-vert group-hover:text-orange sm:text-xl"><?= e($c['nom']) ?></span>
          <span class="mt-1 line-clamp-2 text-xs leading-snug text-texte sm:text-sm"><?= e($c['public_vise']) ?></span>
          <span class="mt-auto block pt-3 sm:pt-4"><?php partiel('partials/bouton_carte', ['texte' => 'Découvrir']) ?></span>
        </span>
      </a>
    </li>
    <?php endforeach ?>
  </ul>
  <?php else: partiel('partials/vide', ['message' => 'Les communautés seront bientôt ouvertes.', 'lien_admin' => url('/gestion/communautes/ajouter')]); endif ?>
</section>

<section class="bg-gris py-14 sm:py-20" aria-labelledby="titre-mode-emploi">
  <div class="conteneur">
    <div class="flex flex-wrap items-end justify-between gap-5">
      <div>
        <p class="surtitre surtitre-trait">Mode d'emploi</p>
        <h2 id="titre-mode-emploi" class="titre-section">Comment intégrer une communauté ?</h2>
      </div>
      <?php if (!$connecte): ?><a href="<?= url('/compte/inscription') ?>" class="btn btn-vert hidden sm:inline-flex">Rejoindre le chapitre</a><?php endif ?>
    </div>
    <?php $etapes = [
        ['orange', 'text-white', 'Rejoignez le chapitre', 'Créez votre compte : vous devenez membre du chapitre YouthConnekt Mali.'],
        ['turquoise', 'text-encre', 'Choisissez une communauté', 'Parcourez les communautés et repérez celles qui vous ressemblent.'],
        ['bleu', 'text-white', "Demandez à l'intégrer", 'Depuis votre compte, envoyez votre demande en un clic.'],
        ['vert', 'text-white', 'Suivez votre demande', 'Retrouvez vos communautés et vos demandes dans votre espace membre.'],
    ]; ?>
    <ol class="mt-8 grid gap-3.5 sm:grid-cols-2 sm:gap-[18px] lg:grid-cols-4">
      <?php foreach ($etapes as $k => [$couleur, $texte_pastille, $t, $texte]): ?>
      <li class="flex gap-4 rounded-2xl border-t-[5px] border-<?= $couleur ?> bg-white p-5 sm:flex-col sm:gap-2.5 sm:p-6">
        <span class="grid size-10 shrink-0 place-items-center rounded-full bg-<?= $couleur ?> text-lg font-bold <?= $texte_pastille ?> sm:size-[46px] sm:text-xl" aria-hidden="true"><?= $k + 1 ?></span>
        <span class="flex flex-col gap-1.5"><strong class="text-[17px] leading-snug text-vert sm:text-[19px]"><?= e($t) ?></strong><span class="text-sm leading-relaxed text-texte sm:text-[15px]"><?= e($texte) ?></span></span>
      </li>
      <?php endforeach ?>
    </ol>
    <p class="mt-6 text-[15px] text-texte"><strong class="text-encre">Créer son compte, c'est rejoindre le chapitre.</strong> L'intégration à une communauté se demande ensuite, communauté par communauté.</p>
    <?php if (!$connecte): ?><a href="<?= url('/compte/inscription') ?>" class="btn btn-vert mt-6 w-full sm:hidden">Rejoindre le chapitre</a><?php endif ?>
  </div>
</section>

<?php partiel('partials/bandeau_lets_connekt') ?>
