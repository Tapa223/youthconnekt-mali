<?php ob_start();
$c = fn ($nom) => ['nom' => $nom, 'valeur' => $valeurs[$nom] ?? '', 'erreur' => $erreurs[$nom] ?? ''];
?>
<section class="bloc">
  <div class="flex flex-wrap items-baseline justify-between gap-3">
    <h1 class="text-2xl font-bold sm:text-3xl">Mon profil</h1>
    <?php if ($profil): ?><a href="<?= url('/compte/membres/' . $u['id']) ?>" class="inline-flex min-h-10 shrink-0 items-center rounded-full border-2 border-vert px-4 text-sm font-bold text-vert transition-colors hover:border-orange hover:bg-orange hover:text-white">Voir mon profil public</a><?php endif ?>
  </div>
  <p class="mt-1 text-sm">Les informations marquées « privé » ne sont jamais affichées sur le site. Votre adresse e-mail de connexion reste privée.</p>
  <form method="post" enctype="multipart/form-data" class="mt-6 grid gap-5 sm:grid-cols-2" novalidate>
    <?= csrf() ?>
    <?php if ($erreurs): ?><p class="rounded-xl border border-rouge/30 bg-rouge/5 px-4 py-3 text-sm text-rouge sm:col-span-2" role="alert">Certaines informations sont à corriger.</p><?php endif ?>

    <div class="sm:col-span-2">
      <p class="libelle"><?= $organisation ? 'Logo' : 'Photo' ?> <span class="font-normal text-texte">(facultatif)</span></p>
      <div class="flex flex-wrap items-center gap-4">
        <?php if ($image_actuelle): ?><img src="<?= e(media($image_actuelle)) ?>" alt="" class="size-16 rounded-full <?= $organisation ? 'bg-white object-contain ring-1 ring-ligne' : 'object-cover' ?>">
        <?php else: ?><span class="grid size-16 place-items-center rounded-full bg-orange text-lg font-bold text-white" aria-hidden="true"><?= e($u['initiales']) ?></span><?php endif ?>
        <div class="text-sm">
          <input type="file" name="<?= e($champ_image) ?>" accept="image/jpeg,image/png,image/webp" class="text-sm file:mr-3 file:rounded-lg file:border-2 file:border-vert file:bg-white file:px-4 file:py-2 file:font-bold file:text-vert">
          <p class="aide">JPG, PNG ou WebP, 3 Mo maximum.</p>
          <?php if ($image_actuelle): ?><label class="mt-2 flex items-center gap-2 text-sm"><input type="checkbox" name="supprimer_image" value="1" class="size-4 accent-vert"> Retirer <?= $organisation ? 'le logo' : 'la photo' ?></label><?php endif ?>
        </div>
      </div>
      <?php if (!empty($erreurs[$champ_image])): ?><p class="erreur"><?= e($erreurs[$champ_image]) ?></p><?php endif ?>
    </div>

    <?php if ($organisation): ?>
      <?php partiel('partials/champ', $c('nom_structure') + ['libelle' => 'Nom de la structure', 'attributs' => ['maxlength' => 150]]) ?>
      <?php partiel('partials/champ', $c('type_structure') + ['libelle' => 'Type de structure', 'type' => 'select', 'options' => TYPES_STRUCTURE]) ?>
      <?php partiel('partials/champ', $c('secteur') + ['libelle' => "Secteur d'activité", 'requis' => false, 'attributs' => ['maxlength' => 150]]) ?>
      <?php partiel('partials/champ', $c('region') + ['libelle' => 'Région', 'type' => 'select', 'options' => REGIONS]) ?>
      <?php partiel('partials/champ', $c('ville') + ['libelle' => 'Ville', 'requis' => false, 'attributs' => ['maxlength' => 100]]) ?>
      <?php partiel('partials/champ', $c('personne_contact') + ['libelle' => 'Personne de contact', 'requis' => false, 'aide' => 'Privé : jamais affiché sur le site.', 'attributs' => ['maxlength' => 120]]) ?>
      <?php partiel('partials/champ', $c('presentation') + ['libelle' => 'Présentation', 'type' => 'textarea', 'requis' => false, 'classes_bloc' => 'sm:col-span-2', 'attributs' => ['rows' => 4, 'maxlength' => 1500]]) ?>
      <?php partiel('partials/champ', $c('site_web') + ['libelle' => 'Site web', 'type' => 'url', 'requis' => false, 'attributs' => ['placeholder' => 'https://']]) ?>
      <?php partiel('partials/champ', $c('lien_linkedin') + ['libelle' => 'LinkedIn', 'type' => 'url', 'requis' => false, 'attributs' => ['placeholder' => 'https://']]) ?>
      <?php partiel('partials/champ', $c('email_contact_public') + ['libelle' => 'E-mail de contact public', 'type' => 'email', 'requis' => false, 'aide' => "Différent de l'adresse de connexion, qui reste privée.", 'classes_bloc' => 'sm:col-span-2']) ?>
    <?php else: ?>
      <?php partiel('partials/champ', $c('prenom') + ['libelle' => 'Prénom', 'attributs' => ['maxlength' => 80, 'autocomplete' => 'given-name']]) ?>
      <?php partiel('partials/champ', $c('nom') + ['libelle' => 'Nom', 'attributs' => ['maxlength' => 80, 'autocomplete' => 'family-name']]) ?>
      <?php partiel('partials/champ', $c('categorie') + ['libelle' => 'Vous êtes', 'type' => 'select', 'options' => CATEGORIES_INDIVIDUELLES]) ?>
      <?php partiel('partials/champ', $c('domaine') + ['libelle' => "Domaine d'activité ou d'études", 'requis' => false, 'attributs' => ['maxlength' => 150]]) ?>
      <?php partiel('partials/champ', $c('region') + ['libelle' => 'Région', 'type' => 'select', 'options' => REGIONS]) ?>
      <?php partiel('partials/champ', $c('ville') + ['libelle' => 'Ville', 'requis' => false, 'attributs' => ['maxlength' => 100]]) ?>
      <?php partiel('partials/champ', $c('presentation') + ['libelle' => 'Présentation', 'type' => 'textarea', 'requis' => false, 'classes_bloc' => 'sm:col-span-2', 'attributs' => ['rows' => 4, 'maxlength' => 1500]]) ?>
      <?php partiel('partials/champ', $c('lien_linkedin') + ['libelle' => 'LinkedIn', 'type' => 'url', 'requis' => false, 'attributs' => ['placeholder' => 'https://']]) ?>
      <?php partiel('partials/champ', $c('site_web') + ['libelle' => 'Site web', 'type' => 'url', 'requis' => false, 'attributs' => ['placeholder' => 'https://']]) ?>
      <?php partiel('partials/champ', $c('telephone') + ['libelle' => 'Téléphone', 'type' => 'tel', 'requis' => false, 'aide' => 'Privé : jamais affiché sur le site.', 'attributs' => ['maxlength' => 30, 'autocomplete' => 'tel']]) ?>
    <?php endif ?>
    <?php partiel('partials/case', ['nom' => 'visible', 'libelle' => $organisation ? "Afficher l'organisation dans la communauté" : 'Afficher mon profil dans la communauté', 'coche' => $valeurs['visible'] === '1', 'classes_bloc' => 'sm:col-span-2']) ?>
    <div class="sm:col-span-2"><button type="submit" class="btn btn-vert min-h-12 px-8">Enregistrer</button></div>
  </form>
</section>
<?php $espace = ob_get_clean(); partiel('espace/base', get_defined_vars());
