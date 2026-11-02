<?php if ($lien_valide): ?>
<h1 class="text-3xl font-bold">Nouveau mot de passe</h1>
<form method="post" class="mt-6 space-y-4" novalidate><?= csrf() ?>
  <?php partiel('partials/champ', ['nom' => 'password1', 'libelle' => 'Nouveau mot de passe', 'mdp' => true, 'erreur' => $erreurs['password1'] ?? '', 'aide' => '8 caractères au minimum, pas uniquement des chiffres.', 'attributs' => ['autocomplete' => 'new-password']]) ?>
  <?php partiel('partials/champ', ['nom' => 'password2', 'libelle' => 'Confirmer le nouveau mot de passe', 'type' => 'password', 'erreur' => $erreurs['password2'] ?? '', 'attributs' => ['autocomplete' => 'new-password']]) ?>
  <button type="submit" class="btn btn-vert min-h-13 w-full text-base">Enregistrer</button>
</form>
<?php else: ?>
<h1 class="text-3xl font-bold">Ce lien n'est plus valide</h1>
<p class="mt-3">Il a peut-être déjà été utilisé ou il a expiré. Faites une nouvelle demande.</p>
<a href="<?= url('/compte/mot-de-passe-oublie') ?>" class="btn btn-vert mt-6 w-full">Nouvelle demande</a>
<?php endif ?>
