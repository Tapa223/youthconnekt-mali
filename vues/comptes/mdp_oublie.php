<h1 class="text-3xl font-bold">Mot de passe oublié</h1>
<p class="mt-2">Indiquez l'adresse e-mail de votre compte : nous vous enverrons un lien pour choisir un nouveau mot de passe.</p>
<form method="post" class="mt-6 space-y-4" novalidate><?= csrf() ?>
  <?php partiel('partials/champ', ['nom' => 'email', 'libelle' => 'Adresse e-mail', 'type' => 'email', 'valeur' => $email, 'erreur' => $erreur, 'attributs' => ['autocomplete' => 'email']]) ?>
  <button type="submit" class="btn btn-vert min-h-13 w-full text-base">Envoyer le lien</button>
</form>
<p class="mt-5 text-center"><a href="<?= url('/compte/connexion') ?>" class="btn btn-contour min-h-11 w-full sm:w-auto">Retour à la connexion</a></p>
