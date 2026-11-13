<?php partiel('comptes/etapes', ['etape' => $etape]) ?>
<h1 class="text-[1.9rem] leading-tight font-bold sm:text-4xl">Rejoindre le chapitre</h1>
<p class="mt-1 text-[15px]">Quel compte vous correspond ?</p>
<?php $types = [
    'individuel' => ['bg-orange text-white', 'border-t-orange', 'Membre individuel', 'Jeunes, étudiants, entrepreneurs, porteurs de projets, leaders d\'associations.',
        '<circle cx="12" cy="8" r="4"/><path d="M4 21c1.5-4 4.5-6 8-6s6.5 2 8 6"/>'],
    'organisation' => ['bg-turquoise text-encre', 'border-t-turquoise', 'Organisation ou entreprise', 'Entreprises, startups, associations et ONG engagées pour les jeunes.',
        '<rect x="5" y="3" width="14" height="18" rx="1.5"/><path d="M9 7h2M13 7h2M9 11h2M13 11h2M10 21v-4h4v4"/>'],
]; ?>
<ul class="mt-4 grid gap-2.5 sm:mt-7 sm:grid-cols-2 sm:gap-4">
  <?php foreach ($types as $cle => [$pastille, $bord, $nom, $pour, $icone]): ?>
  <li>
    <a href="<?= url('/compte/inscription/' . $cle) ?>" class="group flex h-full items-start gap-3.5 rounded-2xl border border-t-4 border-ligne <?= $bord ?> bg-white px-4 py-3.5 transition-[border-color,box-shadow] sm:py-4 hover:border-orange hover:shadow-lg sm:flex-col sm:gap-3 sm:p-5">
      <span class="grid size-10 shrink-0 place-items-center rounded-full sm:size-11 <?= $pastille ?>" aria-hidden="true"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><?= $icone ?></svg></span>
      <span class="min-w-0 flex-1">
        <span class="block text-lg leading-tight font-bold text-vert group-hover:text-orange"><?= e($nom) ?></span>
        <span class="mt-1 block text-[13.5px] leading-snug text-encre"><?= e($pour) ?></span>
        <span class="mt-3 hidden min-h-10 items-center rounded-full bg-vert px-4 text-sm font-bold text-white transition-colors group-hover:bg-orange sm:inline-flex">Choisir ce compte</span>
      </span>
      <svg class="mt-3 size-5 shrink-0 text-vert group-hover:text-orange sm:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg>
    </a>
  </li>
  <?php endforeach ?>
</ul>
<p class="mt-4 flex flex-wrap items-center justify-center gap-x-2 border-t border-ligne pt-4 text-sm sm:mt-6 sm:pt-5">Déjà membre ?
  <a href="<?= url('/compte/connexion') ?>" class="font-bold text-orange hover:text-vert">Se connecter</a></p>
