<?php /** Annuaire public : les organisations du réseau (celles qui ont choisi d'y apparaître). */ ?>
<section class="relative isolate overflow-hidden bg-vert text-white">
  <?php partiel('partials/reseau_deco') ?>
  <div class="conteneur anim-entree relative pt-4 pb-4 sm:pt-5 sm:pb-5">
    <h1 class="text-[1.75rem] leading-none font-bold text-white sm:text-4xl">Organisations du réseau</h1>
    <p class="mt-2 max-w-xl text-sm text-white/90 sm:text-base">Entreprises, startups, associations et ONG engagées aux côtés de la jeunesse malienne dans YouthConnekt Mali.</p>
  </div>
</section>

<section class="conteneur pt-5 pb-14 sm:pt-7 sm:pb-20">
  <form method="get" class="grid grid-cols-2 items-center gap-2 sm:flex sm:flex-wrap">
    <select name="type" class="champ min-h-11 min-w-0 !pl-3 !pr-8 text-[15px] sm:w-auto sm:!pr-10 sm:text-base" aria-label="Type de structure" onchange="this.form.submit()">
      <option value="">Tous types</option>
      <?php foreach (TYPES_STRUCTURE as $cle => $libelle): ?><option value="<?= $cle ?>"<?= $type === $cle ? ' selected' : '' ?>><?= e($libelle) ?></option><?php endforeach ?>
    </select>
    <select name="region" class="champ min-h-11 min-w-0 !pl-3 !pr-8 text-[15px] sm:w-auto sm:!pr-10 sm:text-base" aria-label="Région" onchange="this.form.submit()">
      <option value="">Toutes régions</option>
      <?php foreach (REGIONS as $cle => $libelle): ?><option value="<?= $cle ?>"<?= $region === $cle ? ' selected' : '' ?>><?= e($libelle) ?></option><?php endforeach ?>
    </select>
    <noscript><button type="submit" class="btn btn-vert min-h-11 px-5">Filtrer</button></noscript>
    <p class="col-span-2 text-sm text-texte sm:ml-auto"><?= count($organisations) ?> organisation<?= pluriel(count($organisations)) ?><?= ($type || $region) ? ' sur ' . $total : '' ?></p>
  </form>

  <?php if ($organisations): ?>
  <ul class="mt-5 grid gap-3 sm:grid-cols-2 sm:gap-4 lg:grid-cols-3">
    <?php foreach ($organisations as $o): ?>
    <li>
      <a href="<?= url('/organisations/' . $o['id']) ?>" class="carte group flex h-full items-start gap-3.5 p-4 transition-shadow hover:shadow-lg sm:p-5">
        <?php partiel('partials/logo_organisation', ['o' => $o, 'classes' => 'size-14 sm:size-16']) ?>
        <span class="min-w-0 flex-1">
          <span class="block leading-snug font-bold text-vert group-hover:text-orange sm:text-lg"><?= e($o['nom_structure']) ?></span>
          <span class="mt-0.5 block text-[13px] text-texte"><?= e(implode(' · ', array_filter([TYPES_STRUCTURE[$o['type_structure']] ?? '', $o['secteur'], REGIONS[$o['region']] ?? '']))) ?></span>
          <?php partiel('partials/badges_organisation', ['o' => $o, 'classes' => 'mt-2']) ?>
        </span>
      </a>
    </li>
    <?php endforeach ?>
  </ul>
  <?php else: ?>
  <div class="mt-6 rounded-2xl bg-gris p-6 text-center">
    <p class="font-bold text-vert"><?= ($type || $region) ? 'Aucune organisation ne correspond à ce choix.' : 'Les premières organisations arrivent bientôt.' ?></p>
    <p class="mt-1 text-sm">Votre structure soutient la jeunesse ? Rejoignez le réseau avec un compte organisation.</p>
    <a href="<?= url('/compte/inscription/organisation') ?>" class="btn btn-orange mt-4 min-h-11 px-6">Inscrire mon organisation</a>
  </div>
  <?php endif ?>
</section>
