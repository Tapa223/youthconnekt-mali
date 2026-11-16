<?php /** Membres du réseau : liste réservée aux membres connectés (visibilité choisie par chaque membre). */ ?>
<section class="relative isolate overflow-hidden bg-vert text-white">
  <?php partiel('partials/reseau_deco') ?>
  <div class="conteneur anim-entree relative pt-4 pb-4 sm:pt-5 sm:pb-5">
    <h1 class="text-[1.75rem] leading-none font-bold text-white sm:text-4xl">Membres du réseau</h1>
    <p class="mt-2 max-w-xl text-sm text-white/90 sm:text-base"><?= $total ?> membre<?= pluriel($total) ?> visible<?= pluriel($total) ?> dans la communauté : jeunes, étudiants, entrepreneurs et porteurs de projets.</p>
  </div>
</section>

<section class="conteneur pt-5 pb-14 sm:pt-7 sm:pb-20">
  <?php if (!$connecte): ?>
  <div class="mx-auto max-w-xl rounded-2xl bg-gris p-6 text-center">
    <p class="font-bold text-vert">La liste des membres est réservée aux membres du chapitre.</p>
    <p class="mt-1 text-sm">Chaque membre choisit d'être visible des autres membres : connectez-vous pour découvrir le réseau et entrer en contact.</p>
    <div class="mt-4 flex flex-wrap justify-center gap-2">
      <a href="<?= url('/compte/connexion', ['suivant' => '/membres']) ?>" class="btn btn-vert min-h-11 px-6">Se connecter</a>
      <a href="<?= url('/compte/inscription') ?>" class="btn btn-orange min-h-11 px-6">Rejoindre le chapitre</a>
    </div>
  </div>
  <?php else: ?>
  <form method="get" class="grid grid-cols-2 items-center gap-2 sm:flex sm:flex-wrap">
    <input type="search" name="q" value="<?= e($q) ?>" placeholder="Nom ou domaine" class="champ col-span-2 min-h-11 sm:w-64" aria-label="Rechercher un membre">
    <select name="categorie" class="champ min-h-11 min-w-0 sm:w-auto" aria-label="Profil" onchange="this.form.submit()">
      <option value="">Tous les profils</option>
      <?php foreach (CATEGORIES_INDIVIDUELLES as $cle => $l): ?><option value="<?= $cle ?>"<?= $categorie === $cle ? ' selected' : '' ?>><?= e($l) ?></option><?php endforeach ?>
    </select>
    <select name="region" class="champ min-h-11 min-w-0 sm:w-auto" aria-label="Région" onchange="this.form.submit()">
      <option value="">Toutes les régions</option>
      <?php foreach (REGIONS as $cle => $l): ?><option value="<?= $cle ?>"<?= $region === $cle ? ' selected' : '' ?>><?= e($l) ?></option><?php endforeach ?>
    </select>
    <button type="submit" class="btn btn-vert col-span-2 min-h-11 px-5 sm:col-span-1">Rechercher</button>
  </form>
  <?php if ($membres): ?>
  <ul class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
    <?php foreach ($membres as $m): $nom = trim($m['prenom'] . ' ' . $m['nom']); ?>
    <li>
      <a href="<?= url('/compte/membres/' . $m['utilisateur_id']) ?>" class="carte group flex items-center gap-3 p-3.5 transition-shadow hover:shadow-lg">
        <?php if ($m['photo']): ?><img src="<?= e(media($m['photo'])) ?>" alt="" class="size-12 shrink-0 rounded-full object-cover" loading="lazy">
        <?php else: ?><span class="grid size-12 shrink-0 place-items-center rounded-full bg-orange font-bold text-white" aria-hidden="true"><?= e(initiales($nom)) ?></span><?php endif ?>
        <span class="min-w-0">
          <span class="block truncate font-bold text-vert group-hover:text-orange"><?= e($nom) ?></span>
          <span class="block truncate text-[13px] text-texte"><?= e(implode(' · ', array_filter([CATEGORIES_INDIVIDUELLES[$m['categorie']] ?? '', $m['domaine'], REGIONS[$m['region']] ?? '']))) ?></span>
        </span>
      </a>
    </li>
    <?php endforeach ?>
  </ul>
  <?php else: ?>
  <p class="mt-6 rounded-2xl bg-gris p-6 text-center font-bold text-vert"><?= ($q !== '' || $region || $categorie) ? 'Aucun membre ne correspond à cette recherche.' : 'Aucun membre visible pour le moment.' ?></p>
  <?php endif ?>
  <?php endif ?>
</section>
