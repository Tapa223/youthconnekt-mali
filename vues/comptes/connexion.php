<h1 class="text-4xl font-bold">Se connecter</h1>
<p class="mt-1">Bon retour parmi nous.</p>
<?php if ($contexte): ?><p class="mt-5 rounded-xl border-l-4 border-orange bg-orange-clair px-4 py-3 text-sm font-medium text-encre"><?= e($contexte) ?></p><?php endif ?>
<form method="post" action="<?= url('/compte/connexion') ?>" class="mt-7 space-y-4" novalidate>
  <?= csrf() ?>
  <input type="hidden" name="suivant" value="<?= e($suivant) ?>">
  <?php if ($erreur): ?><div class="rounded-xl border border-rouge/30 bg-rouge/5 px-4 py-3 text-sm text-rouge" role="alert"><?= e($erreur) ?></div><?php endif ?>
  <?php partiel('partials/champ', ['nom' => 'email', 'libelle' => 'Adresse e-mail', 'type' => 'email', 'valeur' => $email, 'attributs' => ['autocomplete' => 'email', 'autofocus' => 'autofocus', 'placeholder' => 'vous@exemple.com']]) ?>
  <?php partiel('partials/champ', ['nom' => 'mot_de_passe', 'libelle' => 'Mot de passe', 'mdp' => true, 'attributs' => ['autocomplete' => 'current-password']]) ?>
  <div class="flex items-center justify-between gap-3">
    <label class="flex items-center gap-2 text-sm text-encre"><input type="checkbox" name="se_souvenir" value="1" class="size-5 rounded accent-vert"> Se souvenir de moi</label>
    <a href="<?= url('/compte/mot-de-passe-oublie') ?>" class="btn-discret">Mot de passe oublié ?</a>
  </div>
  <button type="submit" class="btn btn-vert min-h-13 w-full text-base">Se connecter</button>
</form>
<?php if ($non_confirme): ?>
<form method="post" action="<?= url('/compte/inscription/renvoyer') ?>" class="mt-3 text-center"><?= csrf() ?><button type="submit" class="btn-lien">Renvoyer l'e-mail de confirmation</button></form>
<?php endif ?>
<div class="my-6 flex items-center gap-3 text-sm"><span class="h-px flex-1 bg-ligne"></span>Pas encore membre ?<span class="h-px flex-1 bg-ligne"></span></div>
<a href="<?= url('/compte/inscription') ?>" class="btn btn-contour min-h-13 w-full text-base">Rejoindre le chapitre</a>

