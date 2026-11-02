<?php ob_start(); ?>
<section class="bloc">
  <h1 class="text-2xl font-bold sm:text-3xl">Paramètres</h1>
  <dl class="mt-5 grid gap-3 text-sm sm:grid-cols-2">
    <div class="rounded-xl bg-gris p-4"><dt>Adresse e-mail de connexion</dt><dd class="mt-0.5 font-bold break-all text-encre"><?= e($u['email']) ?></dd></div>
    <div class="rounded-xl bg-gris p-4"><dt>Type de compte</dt><dd class="mt-0.5 font-bold text-encre"><?= e(TYPES_COMPTE[$u['type_compte']] ?? '') ?></dd></div>
  </dl>
</section>

<section class="bloc" aria-labelledby="titre-mdp">
  <h2 id="titre-mdp" class="text-xl font-bold">Changer de mot de passe</h2>
  <form method="post" class="mt-5 grid max-w-lg gap-4" novalidate>
    <?= csrf() ?><input type="hidden" name="action" value="mdp">
    <?php partiel('partials/champ', ['nom' => 'mot_de_passe_actuel', 'libelle' => 'Mot de passe actuel', 'mdp' => true, 'erreur' => $erreurs['mdp_actuel'] ?? '', 'attributs' => ['autocomplete' => 'current-password']]) ?>
    <?php partiel('partials/champ', ['nom' => 'password1', 'libelle' => 'Nouveau mot de passe', 'type' => 'password', 'erreur' => $erreurs['password1'] ?? '', 'aide' => '8 caractères au minimum, pas uniquement des chiffres.', 'attributs' => ['autocomplete' => 'new-password']]) ?>
    <?php partiel('partials/champ', ['nom' => 'password2', 'libelle' => 'Confirmer le nouveau mot de passe', 'type' => 'password', 'erreur' => $erreurs['password2'] ?? '', 'attributs' => ['autocomplete' => 'new-password']]) ?>
    <div><button type="submit" class="btn btn-vert">Enregistrer le mot de passe</button></div>
  </form>
</section>

<section class="bloc border border-rouge/20" aria-labelledby="titre-suppr">
  <h2 id="titre-suppr" class="text-xl font-bold text-rouge">Supprimer mon compte</h2>
  <p class="mt-2 text-sm">Votre compte, votre profil et vos demandes d'intégration seront définitivement supprimés. Cette action est irréversible.</p>
  <form method="post" class="mt-5 grid max-w-lg gap-4" novalidate data-confirmer="Supprimer définitivement votre compte ?">
    <?= csrf() ?><input type="hidden" name="action" value="supprimer">
    <?php partiel('partials/champ', ['nom' => 'mot_de_passe_actuel', 'libelle' => 'Votre mot de passe', 'type' => 'password', 'erreur' => $erreurs['supprimer_actuel'] ?? '', 'id' => 'champ-mdp-suppression', 'attributs' => ['autocomplete' => 'current-password']]) ?>
    <div><button type="submit" class="btn border-rouge bg-rouge text-white hover:bg-white hover:text-rouge">Supprimer mon compte</button></div>
  </form>
</section>
<?php $espace = ob_get_clean(); partiel('espace/base', get_defined_vars());
