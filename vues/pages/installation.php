<h1 class="text-4xl font-bold">Installation</h1>
<p class="mt-2">Première mise en route du site YouthConnekt Mali : la base de données est préparée et le compte de l'administrateur principal est créé.</p>

<?php if ($erreur_bdd !== ''): ?>
<div class="mt-6 rounded-2xl border border-rouge/30 bg-rouge/5 p-5 text-sm text-encre" role="alert">
  <p class="font-bold text-rouge">La base de données n'est pas joignable.</p>
  <p class="mt-2">Vérifiez dans <code>config.php</code> le nom de la base, l'utilisateur et le mot de passe, et que la base existe bien (WAMP : phpMyAdmin, onglet « Bases de données »).</p>
  <?php if (en_developpement()): ?><p class="mt-3 break-words text-xs text-texte">Détail : <?= e($erreur_bdd) ?></p><?php endif ?>
</div>
<a href="<?= url('/installation') ?>" class="btn btn-vert mt-5 w-full">Réessayer</a>
<?php else: ?>
<form method="post" class="mt-7 grid gap-4 sm:grid-cols-2" novalidate>
  <?= csrf() ?>
  <?php if ($erreurs): ?><p class="rounded-xl border border-rouge/30 bg-rouge/5 px-4 py-3 text-sm text-rouge sm:col-span-2" role="alert">Certaines informations sont à corriger.</p><?php endif ?>
  <?php partiel('partials/champ', ['nom' => 'prenom', 'libelle' => 'Prénom', 'valeur' => $valeurs['prenom'], 'erreur' => $erreurs['prenom'] ?? '', 'attributs' => ['autocomplete' => 'given-name']]) ?>
  <?php partiel('partials/champ', ['nom' => 'nom', 'libelle' => 'Nom', 'valeur' => $valeurs['nom'], 'requis' => false, 'attributs' => ['autocomplete' => 'family-name']]) ?>
  <?php partiel('partials/champ', ['nom' => 'email', 'libelle' => 'Adresse e-mail', 'type' => 'email', 'valeur' => $valeurs['email'], 'erreur' => $erreurs['email'] ?? '', 'classes_bloc' => 'sm:col-span-2', 'attributs' => ['autocomplete' => 'email']]) ?>
  <?php partiel('partials/champ', ['nom' => 'mot_de_passe', 'libelle' => 'Mot de passe', 'mdp' => true, 'erreur' => $erreurs['mot_de_passe'] ?? '', 'aide' => '8 caractères au minimum, pas uniquement des chiffres.', 'attributs' => ['autocomplete' => 'new-password']]) ?>
  <?php partiel('partials/champ', ['nom' => 'mot_de_passe2', 'libelle' => 'Confirmer le mot de passe', 'type' => 'password', 'erreur' => $erreurs['mot_de_passe2'] ?? '', 'attributs' => ['autocomplete' => 'new-password']]) ?>
  <?php partiel('partials/case', ['nom' => 'exemples', 'libelle' => "Charger les contenus d'exemple des maquettes (initiatives, opportunités, actualités) pour voir les pages remplies. Ils se retirent ensuite en un clic depuis l'administration.", 'coche' => true, 'classes_bloc' => 'sm:col-span-2']) ?>
  <div class="sm:col-span-2"><button type="submit" class="btn btn-orange min-h-13 w-full text-base">Installer le site</button></div>
</form>
<?php endif ?>
