<?php
/*
 * Questions de profil encore sans réponse, posées là où elles servent (espace membre, participation…).
 * Paramètres : $champs (clé => libellé), $valeurs, $erreurs
 */
$c = fn ($nom) => ['nom' => $nom, 'valeur' => $valeurs[$nom] ?? '', 'erreur' => $erreurs[$nom] ?? ''];
?>
<div class="grid gap-x-4 gap-y-3.5 sm:grid-cols-2">
  <?php foreach ($champs as $nom => $libelle): ?>
    <?php if ($nom === 'genre'): ?>
      <?php partiel('partials/champ', $c('genre') + ['libelle' => 'Genre', 'type' => 'select', 'options' => ['' => 'Choisir'] + GENRES, 'aide' => 'Privé : sert aux statistiques du chapitre.']) ?>
    <?php elseif ($nom === 'tranche_age'): ?>
      <?php partiel('partials/champ', $c('tranche_age') + ['libelle' => "Tranche d'âge", 'type' => 'select', 'options' => ['' => 'Choisir'] + options_tranches_age(), 'aide' => 'Privée : sert aux statistiques du chapitre.']) ?>
    <?php elseif ($nom === 'categorie'): ?>
      <?php partiel('partials/champ', $c('categorie') + ['libelle' => 'Vous êtes', 'type' => 'select', 'options' => ['' => 'Choisir'] + CATEGORIES_INDIVIDUELLES]) ?>
      <?php partiel('partials/champ', $c('categorie_autre') + ['libelle' => 'Précisez', 'si' => ['categorie', 'autre'], 'valeur_si' => $valeurs['categorie'] ?? '', 'attributs' => ['maxlength' => 80, 'placeholder' => 'Exemple : artiste, agriculteur']]) ?>
    <?php elseif ($nom === 'region'): ?>
      <?php partiel('partials/champ', $c('region') + ['libelle' => 'Région', 'type' => 'select', 'options' => ['' => 'Choisir'] + REGIONS]) ?>
    <?php elseif ($nom === 'personne_contact'): ?>
      <?php partiel('partials/champ', $c('personne_contact') + ['libelle' => 'Personne de contact', 'aide' => 'Privé : jamais affiché sur le site.', 'attributs' => ['autocomplete' => 'name', 'placeholder' => 'Prénom et nom', 'maxlength' => 120]]) ?>
    <?php endif ?>
  <?php endforeach ?>
</div>
