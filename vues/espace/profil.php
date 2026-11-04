<?php ob_start();
$c = fn ($nom) => ['nom' => $nom, 'valeur' => $valeurs[$nom] ?? '', 'erreur' => $erreurs[$nom] ?? ''];
?>
<?php
$groupe = fn (string $titre) => '<h2 class="border-t border-ligne pt-5 text-base font-bold text-vert sm:col-span-2">' . e($titre) . '</h2>';
$mot_visible = $organisation ? "Afficher l'organisation dans les communautés" : 'Afficher mon profil dans les communautés';
?>
<section class="bloc">
  <div class="flex items-center justify-between gap-3">
    <h1 class="text-2xl font-bold sm:text-3xl">Mon profil</h1>
    <?php if ($profil): ?><a href="<?= url('/compte/membres/' . $u['id']) ?>" class="btn-lien min-h-9 px-3.5 text-[13px]">Voir mon profil</a><?php endif ?>
  </div>
  <form method="post" enctype="multipart/form-data" class="mt-4 grid gap-x-5 gap-y-4 sm:grid-cols-2" novalidate>
    <?= csrf() ?>
    <?php if ($erreurs): ?><p class="rounded-xl border border-rouge/30 bg-rouge/5 px-4 py-3 text-sm text-rouge sm:col-span-2" role="alert">Certaines informations sont à corriger.</p><?php endif ?>

    <?php /* Visibilité : en premier, car elle décide si le membre apparaît dans ses communautés */ ?>
    <div class="rounded-2xl bg-vert-clair p-4 sm:col-span-2">
      <?php partiel('partials/case', ['nom' => 'visible', 'libelle' => $mot_visible, 'coche' => $valeurs['visible'] === '1']) ?>
      <p class="mt-1.5 pl-8 text-sm text-texte"><?= $organisation ? "Si la case est cochée, l'organisation apparaît" : 'Si la case est cochée, vous apparaissez' ?> parmi les membres de vos communautés. Votre e-mail de connexion et votre téléphone restent privés.</p>
    </div>

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
      <?= $groupe('Liens et contact') ?>
      <?php partiel('partials/champ', $c('site_web') + ['libelle' => 'Site web', 'type' => 'url', 'requis' => false, 'attributs' => ['placeholder' => 'https://']]) ?>
      <?php partiel('partials/champ', $c('lien_linkedin') + ['libelle' => 'LinkedIn', 'type' => 'url', 'requis' => false, 'attributs' => ['placeholder' => 'https://']]) ?>
      <?php partiel('partials/champ', $c('email_contact_public') + ['libelle' => 'E-mail de contact public', 'type' => 'email', 'requis' => false, 'aide' => "Affiché sur la fiche. Différent de l'adresse de connexion, qui reste privée."]) ?>
      <?php partiel('partials/champ', $c('personne_contact') + ['libelle' => 'Personne de contact', 'requis' => false, 'aide' => 'Privé : jamais affiché sur le site.', 'attributs' => ['maxlength' => 120]]) ?>
    <?php else: ?>
      <?php partiel('partials/champ', $c('prenom') + ['libelle' => 'Prénom', 'attributs' => ['maxlength' => 80, 'autocomplete' => 'given-name']]) ?>
      <?php partiel('partials/champ', $c('nom') + ['libelle' => 'Nom', 'attributs' => ['maxlength' => 80, 'autocomplete' => 'family-name']]) ?>
      <?= $groupe('Parcours') ?>
      <?php partiel('partials/champ', $c('categorie') + ['libelle' => 'Vous êtes', 'type' => 'select', 'options' => CATEGORIES_INDIVIDUELLES]) ?>
      <?php partiel('partials/champ', $c('domaine') + ['libelle' => "Domaine d'activité ou d'études", 'requis' => false, 'attributs' => ['maxlength' => 150, 'placeholder' => 'Exemple : marketing digital']]) ?>
      <?php partiel('partials/champ', $c('region') + ['libelle' => 'Région', 'type' => 'select', 'options' => REGIONS]) ?>
      <?php partiel('partials/champ', $c('ville') + ['libelle' => 'Ville', 'requis' => false, 'attributs' => ['maxlength' => 100]]) ?>
      <?php partiel('partials/champ', $c('presentation') + ['libelle' => 'Présentation', 'type' => 'textarea', 'requis' => false, 'aide' => 'Ce que vous faites, ce qui vous anime, ce que vous cherchez.', 'classes_bloc' => 'sm:col-span-2', 'attributs' => ['rows' => 4, 'maxlength' => 1500]]) ?>
      <?= $groupe('Liens et contact') ?>
      <?php partiel('partials/champ', $c('lien_linkedin') + ['libelle' => 'LinkedIn', 'type' => 'url', 'requis' => false, 'attributs' => ['placeholder' => 'https://']]) ?>
      <?php partiel('partials/champ', $c('site_web') + ['libelle' => 'Site web', 'type' => 'url', 'requis' => false, 'attributs' => ['placeholder' => 'https://']]) ?>
      <?php partiel('partials/champ', $c('telephone') + ['libelle' => 'Téléphone', 'type' => 'tel', 'requis' => false, 'aide' => 'Privé : jamais affiché sur le site.', 'attributs' => ['maxlength' => 30, 'autocomplete' => 'tel']]) ?>
    <?php endif ?>

    <?php /* Bouton toujours à portée de main : il reste collé en bas de l'écran pendant qu'on remplit */ ?>
    <div class="sticky bottom-0 z-10 -mx-5 mt-2 border-t border-ligne bg-white/95 px-5 py-3 backdrop-blur sm:col-span-2 sm:-mx-6 sm:px-6">
      <button type="submit" class="btn btn-vert min-h-12 w-full px-8 sm:w-auto">Enregistrer</button>
    </div>
  </form>
</section>
<?php $espace = ob_get_clean(); partiel('espace/base', get_defined_vars());
