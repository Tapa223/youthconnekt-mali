<?php
/* Mon compte d'administration : nom affiché et mot de passe. */
$mdp = ['type' => 'motdepasse', 'requis' => true];
?>
<div class="ycm-entete-page">
  <div>
    <h1>Mon compte</h1>
    <p class="ycm-sous-titre"><?= e($moi['email']) ?> · <?= e(ROLES_ADMIN[$moi['role_admin']] ?? '') ?></p>
  </div>
</div>
<div class="ycm-form-grille">
  <div class="ycm-form-principal">
    <form method="post" data-confirmer="Enregistrer vos informations ?" data-confirmer-oui="Enregistrer">
      <?= csrf() ?><input type="hidden" name="action" value="identite">
      <?php partiel('admin/_groupe', ['g' => ['Mes informations', 'description' => "Votre nom s'affiche dans l'administration, par exemple sur les demandes que vous traitez.", 'champs' => [
          'prenom_admin' => ['type' => 'texte', 'libelle' => 'Prénom', 'requis' => false, 'max' => 80, 'largeur' => 'demi'],
          'nom_admin' => ['type' => 'texte', 'libelle' => 'Nom', 'requis' => false, 'max' => 80, 'largeur' => 'demi'],
      ]], 'valeurs' => $valeurs, 'erreurs' => $erreurs, 'ligne' => $moi, 'desactive' => false]) ?>
      <p class="ycm-panneau-boutons" style="margin-top:12px"><button type="submit" class="ycm-bouton-vert">Enregistrer</button></p>
    </form>

    <form method="post" style="margin-top:24px" data-confirmer="Changer votre mot de passe ?" data-confirmer-texte="Il faudra utiliser le nouveau à la prochaine connexion." data-confirmer-oui="Changer">
      <?= csrf() ?><input type="hidden" name="action" value="mdp">
      <?php partiel('admin/_groupe', ['g' => ['Changer mon mot de passe', 'description' => '8 caractères au minimum, différent de votre adresse e-mail.', 'champs' => [
          'mot_de_passe_actuel' => $mdp + ['libelle' => 'Mot de passe actuel'],
          'password1' => $mdp + ['libelle' => 'Nouveau mot de passe', 'largeur' => 'demi'],
          'password2' => $mdp + ['libelle' => 'Confirmer le nouveau mot de passe', 'largeur' => 'demi'],
      ]], 'valeurs' => [], 'erreurs' => $erreurs, 'ligne' => $moi, 'desactive' => false]) ?>
      <p class="ycm-panneau-boutons" style="margin-top:12px"><button type="submit" class="ycm-bouton-vert">Modifier le mot de passe</button></p>
    </form>
  </div>
  <aside class="ycm-form-cote">
    <p class="ycm-encadre-info">Ce compte sert uniquement à l'administration : il n'a pas d'espace membre et ne peut pas rejoindre de communauté. Pour tester le site comme un membre, créez un compte séparé avec une autre adresse e-mail.</p>
  </aside>
</div>
