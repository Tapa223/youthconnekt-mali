<?php partiel('comptes/etapes', ['etape' => $etape]) ?>
<h1 class="text-4xl font-bold">Rejoindre le chapitre</h1>
<p class="mt-1">Deux types de comptes existent. Choisissez celui qui vous correspond.</p>
<?php $types = [
    'individuel' => ['orange', 'text-white', 'Membre individuel', 'Pour vous, à titre personnel.',
        ['Jeunes', 'Étudiants', 'Professionnels', 'Entrepreneurs', 'Porteurs de projets', 'Innovateurs', "Leaders d'associations"],
        ['Les opportunités dès leur publication', 'Les rencontres et événements', 'Les communautés qui vous ressemblent', 'Votre espace membre'],
        '<circle cx="12" cy="8" r="4"/><path d="M4 21c1.5-4 4.5-6 8-6s6.5 2 8 6"/>'],
    'organisation' => ['turquoise', 'text-encre', 'Organisation ou entreprise', 'Pour une structure qui agit avec ou pour les jeunes.',
        ['Entreprises', 'Startups', 'Associations', 'ONG'],
        ['Une page pour présenter votre structure', 'Partager vos opportunités avec le chapitre', 'Monter des initiatives ensemble', 'Toucher des jeunes partout au Mali'],
        '<rect x="5" y="3" width="14" height="18" rx="1.5"/><path d="M9 7h2M13 7h2M9 11h2M13 11h2M10 21v-4h4v4"/>'],
]; ?>
<div class="mt-7 grid gap-4 sm:grid-cols-2">
  <?php foreach ($types as $cle => [$couleur, $texte_icone, $nom, $pour, $publics, $avantages, $icone]): ?>
  <article class="flex flex-col rounded-2xl border border-t-[5px] border-ligne <?= $couleur === 'orange' ? 'border-t-orange' : 'border-t-turquoise' ?> bg-white p-5">
    <div class="flex items-start gap-3">
      <span class="grid size-11 shrink-0 place-items-center rounded-full <?= $couleur === 'orange' ? 'bg-orange' : 'bg-turquoise' ?> <?= $texte_icone ?>" aria-hidden="true"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><?= $icone ?></svg></span>
      <div><h2 class="text-xl leading-tight font-bold"><?= e($nom) ?></h2><p class="text-xs"><?= e($pour) ?></p></div>
    </div>
    <ul class="mt-4 flex flex-wrap gap-1.5 text-xs font-bold text-vert">
      <?php foreach ($publics as $pub): ?><li class="rounded-full bg-gris px-2.5 py-1"><?= e($pub) ?></li><?php endforeach ?>
    </ul>
    <ul class="mt-4 flex-1 space-y-2 text-sm text-encre">
      <?php foreach ($avantages as $av): ?><li class="flex gap-2"><?php partiel('partials/coche') ?><?= e($av) ?></li><?php endforeach ?>
    </ul>
    <a href="<?= url('/compte/inscription/' . $cle) ?>" class="btn btn-vert mt-5 w-full">Choisir ce compte</a>
  </article>
  <?php endforeach ?>
</div>
<p class="mt-6 flex items-center justify-center gap-3 text-sm">Déjà membre ? <a href="<?= url('/compte/connexion') ?>" class="btn-lien">Se connecter</a></p>
