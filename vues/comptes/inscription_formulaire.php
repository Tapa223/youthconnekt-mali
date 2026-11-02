<?php
$organisation = $type_compte === 'organisation';
$c = fn ($nom) => ['nom' => $nom, 'valeur' => $valeurs[$nom] ?? '', 'erreur' => $erreurs[$nom] ?? ''];
partiel('comptes/etapes', ['etape' => $etape]);
?>
<h1 class="text-4xl font-bold">Vos informations</h1>
<div class="mt-5 flex items-center justify-between gap-4 rounded-xl bg-gris px-4 py-3">
  <p class="text-sm">Compte : <strong class="text-vert"><?= $organisation ? 'Organisation ou entreprise' : 'Membre individuel' ?></strong><br>
    <span class="text-xs"><?= $organisation ? 'Pour une entreprise, une startup, une association ou une ONG qui agit avec ou pour les jeunes.' : 'Pour vous, à titre personnel : jeune, étudiant, professionnel, entrepreneur, porteur de projet, innovateur ou leader d\'association.' ?></span></p>
  <a href="<?= url('/compte/inscription') ?>" class="btn btn-contour shrink-0 px-3 text-sm">Changer de type</a>
</div>
<form method="post" class="mt-6 grid gap-4 sm:grid-cols-2" novalidate>
  <?= csrf() ?>
  <?php if ($erreurs): ?><p class="rounded-xl border border-rouge/30 bg-rouge/5 px-4 py-3 text-sm text-rouge sm:col-span-2" role="alert">Certaines informations sont à corriger : les champs concernés sont indiqués en rouge.</p><?php endif ?>
  <?php if ($organisation): ?>
    <?php partiel('partials/champ', $c('nom_structure') + ['libelle' => "Nom de l'organisation", 'attributs' => ['autocomplete' => 'organization', 'placeholder' => 'Nom de la structure', 'maxlength' => 150]]) ?>
    <?php partiel('partials/champ', $c('type_structure') + ['libelle' => 'Type', 'type' => 'select', 'options' => ['' => 'Choisir'] + TYPES_STRUCTURE]) ?>
    <?php partiel('partials/champ', $c('personne_contact') + ['libelle' => 'Personne de contact', 'aide' => 'Privé : jamais affiché sur le site.', 'attributs' => ['autocomplete' => 'name', 'placeholder' => 'Prénom et nom', 'maxlength' => 120]]) ?>
    <?php partiel('partials/champ', $c('region') + ['libelle' => 'Région', 'type' => 'select', 'options' => REGIONS]) ?>
    <?php partiel('partials/champ', $c('email') + ['libelle' => 'Adresse e-mail', 'type' => 'email', 'attributs' => ['autocomplete' => 'email', 'placeholder' => 'contact@structure.org', 'maxlength' => 190]]) ?>
    <?php partiel('partials/champ', $c('site_web') + ['libelle' => 'Site web', 'type' => 'url', 'requis' => false, 'attributs' => ['placeholder' => 'https://', 'maxlength' => 255]]) ?>
  <?php else: ?>
    <?php partiel('partials/champ', $c('prenom') + ['libelle' => 'Prénom', 'attributs' => ['autocomplete' => 'given-name', 'placeholder' => 'Aminata', 'maxlength' => 80]]) ?>
    <?php partiel('partials/champ', $c('nom') + ['libelle' => 'Nom', 'attributs' => ['autocomplete' => 'family-name', 'placeholder' => 'Keïta', 'maxlength' => 80]]) ?>
    <?php partiel('partials/champ', $c('categorie') + ['libelle' => 'Vous êtes', 'type' => 'select', 'options' => ['' => 'Choisir votre profil'] + CATEGORIES_INDIVIDUELLES]) ?>
    <?php partiel('partials/champ', $c('region') + ['libelle' => 'Région', 'type' => 'select', 'options' => REGIONS]) ?>
    <?php partiel('partials/champ', $c('email') + ['libelle' => 'Adresse e-mail', 'type' => 'email', 'attributs' => ['autocomplete' => 'email', 'placeholder' => 'vous@exemple.com', 'maxlength' => 190]]) ?>
    <?php partiel('partials/champ', $c('telephone') + ['libelle' => 'Téléphone', 'type' => 'tel', 'requis' => false, 'aide' => 'Privé : jamais affiché sur le site.', 'attributs' => ['autocomplete' => 'tel', 'inputmode' => 'tel', 'placeholder' => '+223 …', 'maxlength' => 30]]) ?>
  <?php endif ?>
  <?php partiel('partials/champ', ['nom' => 'password1', 'libelle' => 'Mot de passe', 'mdp' => true, 'erreur' => $erreurs['password1'] ?? '', 'aide' => '8 caractères au minimum, pas uniquement des chiffres.', 'attributs' => ['autocomplete' => 'new-password']]) ?>
  <?php partiel('partials/champ', ['nom' => 'password2', 'libelle' => 'Confirmer le mot de passe', 'type' => 'password', 'erreur' => $erreurs['password2'] ?? '', 'attributs' => ['autocomplete' => 'new-password']]) ?>
  <?php partiel('partials/case', ['nom' => 'conditions', 'libelle_html' => 'J\'accepte la <a href="' . url('/confidentialite') . '" target="_blank" class="lien-texte">politique de confidentialité</a> et les conditions d\'utilisation de la plateforme.', 'coche' => $valeurs['conditions'] !== '', 'erreur' => $erreurs['conditions'] ?? '', 'classes_bloc' => 'sm:col-span-2']) ?>
  <?php partiel('partials/case', ['nom' => 'newsletter', 'libelle' => 'Je souhaite recevoir les opportunités et actualités par e-mail.', 'coche' => $valeurs['newsletter'] !== '', 'classes_bloc' => 'sm:col-span-2']) ?>
  <p class="rounded-xl bg-vert-clair px-4 py-3 text-sm font-medium text-vert sm:col-span-2">En créant votre compte, vous rejoignez le chapitre YouthConnekt Mali.</p>
  <div class="sm:col-span-2"><button type="submit" class="btn btn-orange min-h-13 w-full text-base">Créer mon compte</button></div>
</form>
<p class="mt-6 flex items-center justify-center gap-3 text-sm">Déjà membre ? <a href="<?= url('/compte/connexion') ?>" class="btn-lien">Se connecter</a></p>
