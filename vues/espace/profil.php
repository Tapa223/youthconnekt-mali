<?php ob_start();
$c = fn ($nom) => ['nom' => $nom, 'valeur' => $valeurs[$nom] ?? '', 'erreur' => $erreurs[$nom] ?? ''];
?>
<?php
$groupe = fn (string $titre) => '<h2 class="border-t border-ligne pt-5 text-base font-bold text-vert sm:col-span-2">' . e($titre) . '</h2>';
$infos = array_filter([libelle_profil($profil, $u['type_compte']), $organisation ? ($valeurs['secteur'] ?? '') : ($valeurs['domaine'] ?? ''), $valeurs['ville'] ?: (REGIONS[$valeurs['region']] ?? '')]);
$ouvert = $erreurs || get('modifier') !== '';
$lignes_liens = $liens ?: [['reseau' => 'linkedin', 'url' => '']];
?>
<?php /* Aperçu du profil : ce que voient les autres membres */ ?>
<section id="apercu" class="bloc scroll-mt-20">
  <?php if (get('enregistre') !== '' && !$erreurs): ?><p class="mb-4 flex items-center gap-2 rounded-xl bg-vert-clair px-4 py-3 text-sm font-bold text-vert" role="status"><svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7"/></svg>Vos modifications ont été enregistrées.</p><?php endif ?>
  <div class="flex items-start gap-4">
    <?php if ($image_actuelle): ?><img src="<?= e(media($image_actuelle)) ?>" alt="" class="size-16 shrink-0 rounded-full sm:size-20 <?= $organisation ? 'bg-white object-contain ring-1 ring-ligne' : 'object-cover' ?>">
    <?php else: ?><span class="grid size-16 shrink-0 place-items-center rounded-full bg-orange text-xl font-bold text-white sm:size-20 sm:text-2xl" aria-hidden="true"><?= e($u['initiales']) ?></span><?php endif ?>
    <div class="min-w-0 flex-1">
      <h1 class="text-xl leading-tight font-bold sm:text-2xl"><?= e($u['nom_affiche']) ?></h1>
      <?php if ($infos): ?><p class="mt-1 text-sm text-texte"><?= e(implode(' · ', $infos)) ?></p><?php endif ?>
      <?php if ($liens): ?>
      <ul class="mt-2.5 flex flex-wrap gap-1.5" aria-label="Mes liens">
        <?php foreach (liens_profil((int) $u['id']) as $l): ?><li><a href="<?= e($l['url']) ?>" target="_blank" rel="noopener" class="grid size-9 place-items-center rounded-full bg-gris text-vert transition-colors hover:bg-orange hover:text-white" title="<?= e($l['nom']) ?>" aria-label="<?= e($l['nom']) ?>"><svg class="size-[17px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><?= $l['trace'] ?></svg></a></li><?php endforeach ?>
      </ul>
      <?php endif ?>
    </div>
  </div>
  <?php if (trim($valeurs['presentation']) !== ''): ?><p class="mt-4 line-clamp-3 text-[15px] leading-relaxed text-encre"><?= e($valeurs['presentation']) ?></p><?php endif ?>
  <?php if ($profil): /* Visibilité : interrupteur, confirmé avant d'être appliqué */ ?>
  <form id="visibilite" method="post" class="mt-4 scroll-mt-20" data-confirmer="<?= $visible ? 'Masquer votre profil ?' : 'Rendre votre profil visible ?' ?>" data-confirmer-texte="<?= $visible ? "Vous n'apparaîtrez plus parmi les membres de vos communautés." : 'Votre nom, votre photo, votre présentation et vos liens seront visibles des membres de vos communautés. Votre e-mail et votre téléphone restent privés.' ?>" data-confirmer-oui="<?= $visible ? 'Masquer' : 'Rendre visible' ?>">
    <?= csrf() ?><input type="hidden" name="action" value="visibilite"><input type="hidden" name="visible" value="<?= $visible ? '0' : '1' ?>">
    <button type="submit" role="switch" aria-checked="<?= $visible ? 'true' : 'false' ?>" class="flex w-full items-center gap-3 rounded-xl border border-ligne px-3.5 py-2.5 text-left transition-colors hover:border-vert">
      <span class="min-w-0 flex-1"><span class="block text-sm font-bold <?= $visible ? 'text-vert' : 'text-orange-fonce' ?>"><?= $visible ? 'Profil visible' : 'Profil masqué' ?></span><span class="block text-xs text-texte"><?= $visible ? 'Les membres de vos communautés vous voient.' : "Personne d'autre que vous ne le voit." ?></span></span>
      <span class="relative h-6 w-11 shrink-0 rounded-full transition-colors <?= $visible ? 'bg-vert' : 'bg-ligne' ?>" aria-hidden="true"><span class="absolute top-0.5 size-5 rounded-full bg-white shadow transition-all <?= $visible ? 'left-[22px]' : 'left-0.5' ?>"></span></span>
    </button>
  </form>
  <?php endif ?>
  <div class="mt-3 grid grid-cols-2 gap-2">
    <a href="#modifier-profil" class="btn btn-vert min-h-10 px-3 text-sm" data-ouvrir-details="modifier-profil">Modifier</a>
    <?php if ($profil): ?><a href="<?= url('/compte/membres/' . $u['id']) ?>" class="btn btn-contour min-h-10 px-3 text-sm">Voir mon profil</a><?php endif ?>
  </div>
</section>

<?php /* Formulaire : replié, il s'ouvre avec « Modifier mon profil » */ ?>
<details id="modifier-profil" class="bloc group/modif scroll-mt-20" <?= $ouvert ? 'open' : '' ?>>
  <summary class="flex min-h-10 cursor-pointer list-none items-center justify-between gap-3 [&::-webkit-details-marker]:hidden">
    <span class="text-lg font-bold text-vert">Mes informations</span>
    <span class="rounded-full bg-gris px-3 py-1.5 text-xs font-bold text-vert group-open/modif:hidden">Ouvrir</span>
    <span class="hidden rounded-full bg-gris px-3 py-1.5 text-xs font-bold text-texte group-open/modif:inline">Fermer</span>
  </summary>
  <form method="post" enctype="multipart/form-data" class="mt-4 grid gap-x-5 gap-y-4 sm:grid-cols-2" novalidate data-confirmer="Enregistrer vos informations ?" data-confirmer-oui="Enregistrer">
    <?= csrf() ?>
    <?php if ($erreurs): ?><p class="rounded-xl border border-rouge/30 bg-rouge/5 px-4 py-3 text-sm text-rouge sm:col-span-2" role="alert">Certaines informations sont à corriger.</p><?php endif ?>

    <?= $groupe($organisation ? "L'organisation" : 'Identité') ?>
    <div class="sm:col-span-2">
      <p class="libelle"><?= $organisation ? 'Logo' : 'Photo' ?> <span class="font-normal text-texte">(facultatif)</span></p>
      <div class="flex flex-wrap items-center gap-4">
        <?php if ($image_actuelle): ?><img src="<?= e(media($image_actuelle)) ?>" alt="" class="size-16 rounded-full <?= $organisation ? 'bg-white object-contain ring-1 ring-ligne' : 'object-cover' ?>" data-apercu-image>
        <?php else: ?><span class="grid size-16 place-items-center rounded-full bg-orange text-lg font-bold text-white" aria-hidden="true" data-apercu-image><?= e($u['initiales']) ?></span><?php endif ?>
        <div class="text-sm">
          <label class="btn btn-contour min-h-10 cursor-pointer px-4 text-sm">
            <input type="file" name="<?= e($champ_image) ?>" accept="image/jpeg,image/png,image/webp" class="sr-only" data-choix-fichier>
            <?= $image_actuelle ? ($organisation ? 'Changer le logo' : 'Changer la photo') : ($organisation ? 'Ajouter un logo' : 'Ajouter une photo') ?>
          </label>
          <p class="aide" data-nom-fichier>JPG, PNG ou WebP, 3 Mo maximum.</p>
          <?php if ($image_actuelle): ?><label class="mt-2 flex items-center gap-2 text-sm"><input type="checkbox" name="supprimer_image" value="1" class="size-4 accent-vert"> Retirer <?= $organisation ? 'le logo' : 'la photo' ?></label><?php endif ?>
        </div>
      </div>
      <?php if (!empty($erreurs[$champ_image])): ?><p class="erreur"><?= e($erreurs[$champ_image]) ?></p><?php endif ?>
    </div>

    <?php if ($organisation): ?>
      <?php partiel('partials/champ', $c('nom_structure') + ['libelle' => 'Nom de la structure', 'attributs' => ['maxlength' => 150]]) ?>
      <?php partiel('partials/champ', $c('type_structure') + ['libelle' => 'Type de structure', 'type' => 'select', 'options' => TYPES_STRUCTURE]) ?>
      <?php partiel('partials/champ', $c('secteur') + ['libelle' => "Secteur d'activité", 'requis' => false, 'classes_bloc' => 'sm:col-span-2', 'attributs' => ['maxlength' => 150]]) ?>
      <?= $groupe('Où la trouver') ?>
      <?php partiel('partials/champ', $c('region') + ['libelle' => 'Région', 'type' => 'select', 'options' => REGIONS]) ?>
      <?php partiel('partials/champ', $c('ville') + ['libelle' => 'Ville', 'requis' => false, 'attributs' => ['maxlength' => 100]]) ?>
      <?= $groupe('Présentation') ?>
      <?php partiel('partials/champ', $c('presentation') + ['libelle' => "En quelques lignes, ce que fait l'organisation", 'type' => 'textarea', 'requis' => false, 'classes_bloc' => 'sm:col-span-2', 'attributs' => ['rows' => 4, 'maxlength' => 1500]]) ?>
      <?= $groupe('Mes liens') ?>
      <div class="sm:col-span-2" data-liens>
        <p class="aide mt-0 mb-3">Ajoutez vos réseaux sociaux ou votre site : choisissez le nom, puis collez l'adresse.</p>
        <ul class="space-y-2" data-liste-liens>
          <?php foreach ($lignes_liens as $l): ?>
          <li class="flex gap-2" data-ligne-lien>
            <select name="lien_reseau[]" class="champ w-[38%] shrink-0 sm:w-44" aria-label="Nom du lien"><?php foreach (RESEAUX_PROFIL as $cle => [$nom]): ?><option value="<?= $cle ?>" <?= $l['reseau'] === $cle ? 'selected' : '' ?>><?= e($nom) ?></option><?php endforeach ?></select>
            <input type="url" name="lien_url[]" value="<?= e($l['url']) ?>" placeholder="https://" maxlength="255" class="champ min-w-0 flex-1" aria-label="Adresse du lien">
            <button type="button" class="grid size-12 shrink-0 place-items-center rounded-lg border border-ligne text-texte hover:border-rouge hover:text-rouge" data-retirer-lien aria-label="Retirer ce lien"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg></button>
          </li>
          <?php endforeach ?>
        </ul>
        <button type="button" class="btn-lien mt-3 min-h-10" data-ajouter-lien>Ajouter un lien</button>
        <?php if (!empty($erreurs['liens'])): ?><p class="erreur"><?= e($erreurs['liens']) ?></p><?php endif ?>
      </div>
      <?= $groupe('Contact') ?>
      <?php partiel('partials/champ', $c('email_contact_public') + ['libelle' => 'E-mail de contact public', 'type' => 'email', 'requis' => false, 'aide' => "Affiché sur la fiche. Différent de l'adresse de connexion, qui reste privée."]) ?>
      <?php partiel('partials/champ', $c('personne_contact') + ['libelle' => 'Personne de contact', 'requis' => false, 'aide' => 'Privé : jamais affiché sur le site.', 'attributs' => ['maxlength' => 120]]) ?>
    <?php else: ?>
      <?php partiel('partials/champ', $c('prenom') + ['libelle' => 'Prénom', 'attributs' => ['maxlength' => 80, 'autocomplete' => 'given-name']]) ?>
      <?php partiel('partials/champ', $c('nom') + ['libelle' => 'Nom', 'attributs' => ['maxlength' => 80, 'autocomplete' => 'family-name']]) ?>
      <?php partiel('partials/champ', $c('genre') + ['libelle' => 'Genre', 'type' => 'select', 'options' => ['' => 'Choisir'] + GENRES, 'aide' => 'Privé : sert seulement aux statistiques du chapitre.']) ?>
      <?php partiel('partials/champ', $c('date_naissance') + ['libelle' => 'Date de naissance', 'type' => 'date', 'aide' => 'Privée : jamais affichée sur le site.', 'attributs' => ['max' => date('Y-m-d', strtotime('-10 years')), 'min' => date('Y-m-d', strtotime('-100 years'))]]) ?>
      <?= $groupe('Parcours') ?>
      <?php partiel('partials/champ', $c('categorie') + ['libelle' => 'Vous êtes', 'type' => 'select', 'options' => CATEGORIES_INDIVIDUELLES]) ?>
      <?php partiel('partials/champ', $c('domaine') + ['libelle' => "Domaine d'activité ou d'études", 'requis' => false, 'attributs' => ['maxlength' => 150, 'placeholder' => 'Exemple : marketing digital']]) ?>
      <?php partiel('partials/champ', $c('region') + ['libelle' => 'Région', 'type' => 'select', 'options' => REGIONS]) ?>
      <?php partiel('partials/champ', $c('ville') + ['libelle' => 'Ville', 'requis' => false, 'attributs' => ['maxlength' => 100]]) ?>
      <?php partiel('partials/champ', $c('presentation') + ['libelle' => 'Présentation', 'type' => 'textarea', 'requis' => false, 'aide' => 'Ce que vous faites, ce qui vous anime, ce que vous cherchez.', 'classes_bloc' => 'sm:col-span-2', 'attributs' => ['rows' => 4, 'maxlength' => 1500]]) ?>
      <?= $groupe('Mes liens') ?>
      <div class="sm:col-span-2" data-liens>
        <p class="aide mt-0 mb-3">Ajoutez vos réseaux sociaux ou votre site : choisissez le nom, puis collez l'adresse.</p>
        <ul class="space-y-2" data-liste-liens>
          <?php foreach ($lignes_liens as $l): ?>
          <li class="flex gap-2" data-ligne-lien>
            <select name="lien_reseau[]" class="champ w-[38%] shrink-0 sm:w-44" aria-label="Nom du lien"><?php foreach (RESEAUX_PROFIL as $cle => [$nom]): ?><option value="<?= $cle ?>" <?= $l['reseau'] === $cle ? 'selected' : '' ?>><?= e($nom) ?></option><?php endforeach ?></select>
            <input type="url" name="lien_url[]" value="<?= e($l['url']) ?>" placeholder="https://" maxlength="255" class="champ min-w-0 flex-1" aria-label="Adresse du lien">
            <button type="button" class="grid size-12 shrink-0 place-items-center rounded-lg border border-ligne text-texte hover:border-rouge hover:text-rouge" data-retirer-lien aria-label="Retirer ce lien"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg></button>
          </li>
          <?php endforeach ?>
        </ul>
        <button type="button" class="btn-lien mt-3 min-h-10" data-ajouter-lien>Ajouter un lien</button>
        <?php if (!empty($erreurs['liens'])): ?><p class="erreur"><?= e($erreurs['liens']) ?></p><?php endif ?>
      </div>
      <?= $groupe('Contact') ?>
      <?php partiel('partials/champ', $c('telephone') + ['libelle' => 'Téléphone', 'type' => 'tel', 'requis' => false, 'aide' => 'Privé : jamais affiché sur le site.', 'attributs' => ['maxlength' => 30, 'autocomplete' => 'tel']]) ?>
    <?php endif ?>

    <?php /* Bouton toujours à portée de main : il reste collé en bas de l'écran pendant qu'on remplit */ ?>
    <div class="sticky bottom-0 z-10 -mx-5 mt-2 flex gap-2 border-t border-ligne bg-white/95 px-5 py-3 backdrop-blur sm:col-span-2 sm:-mx-6 sm:px-6">
      <button type="submit" class="btn btn-vert min-h-12 flex-1 px-8 sm:flex-none">Enregistrer</button>
      <a href="<?= url('/compte/mon-espace/profil') ?>#apercu" class="btn btn-contour min-h-12" data-fermer-details>Annuler</a>
    </div>
  </form>
</details>
<?php $espace = ob_get_clean(); partiel('espace/base', get_defined_vars());
