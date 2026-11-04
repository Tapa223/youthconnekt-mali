<?php ob_start(); ?>
<section class="bloc">
  <h1 class="text-xl font-bold sm:text-3xl">Paramètres</h1>
  <dl class="mt-5 grid gap-3 text-sm sm:grid-cols-2">
    <div class="rounded-xl bg-gris p-4"><dt>Adresse e-mail de connexion</dt><dd class="mt-0.5 font-bold break-all text-encre"><?= e($u['email']) ?></dd></div>
    <div class="rounded-xl bg-gris p-4"><dt>Type de compte</dt><dd class="mt-0.5 font-bold text-encre"><?= $u['type_compte'] === 'organisation' ? 'Organisation ou entreprise' : 'Compte personnel' ?></dd></div>
  </dl>
</section>

<?php $erreur_mdp = !empty($erreurs['mdp_actuel']) || !empty($erreurs['password1']) || !empty($erreurs['password2']); ?>
<details id="mot-de-passe" class="bloc group/mdp scroll-mt-20" <?= $erreur_mdp ? 'open' : '' ?>>
  <summary class="flex min-h-10 cursor-pointer list-none flex-wrap items-center justify-between gap-3 [&::-webkit-details-marker]:hidden">
    <span><span class="block text-lg font-bold text-vert">Mot de passe</span><span class="text-sm text-texte">Changez-le quand vous le souhaitez.</span></span>
    <span class="btn-lien min-h-9 px-3.5 text-[13px] group-open/mdp:hidden">Modifier le mot de passe</span>
    <span class="hidden rounded-full bg-gris px-3 py-1.5 text-xs font-bold text-texte group-open/mdp:inline">Fermer</span>
  </summary>
  <form method="post" class="mt-4 grid max-w-lg gap-4" novalidate data-confirmer="Enregistrer le nouveau mot de passe ?" data-confirmer-oui="Enregistrer">
    <?= csrf() ?><input type="hidden" name="action" value="mdp">
    <?php partiel('partials/champ', ['nom' => 'mot_de_passe_actuel', 'libelle' => 'Mot de passe actuel', 'mdp' => true, 'erreur' => $erreurs['mdp_actuel'] ?? '', 'attributs' => ['autocomplete' => 'current-password']]) ?>
    <?php partiel('partials/champ', ['nom' => 'password1', 'libelle' => 'Nouveau mot de passe', 'type' => 'password', 'erreur' => $erreurs['password1'] ?? '', 'aide' => '8 caractères au minimum, pas uniquement des chiffres.', 'attributs' => ['autocomplete' => 'new-password']]) ?>
    <?php partiel('partials/champ', ['nom' => 'password2', 'libelle' => 'Confirmer le nouveau mot de passe', 'type' => 'password', 'erreur' => $erreurs['password2'] ?? '', 'attributs' => ['autocomplete' => 'new-password']]) ?>
    <div><button type="submit" class="btn btn-vert">Enregistrer le mot de passe</button></div>
  </form>
</details>

<?php $autre = $u['type_compte'] === 'organisation' ? 'individuel' : 'organisation'; ?>
<section class="bloc" aria-labelledby="titre-comptes">
  <h2 id="titre-comptes" class="text-lg font-bold text-vert">Changer de compte</h2>
  <p class="mt-1 text-sm text-encre">Chaque compte a sa propre adresse e-mail. <?= $autre === 'organisation'
      ? "Si vous représentez aussi une organisation ou une entreprise, créez-lui un compte avec l'adresse e-mail de la structure."
      : 'Si vous voulez aussi participer à titre personnel, créez un compte personnel avec votre propre adresse e-mail.' ?>
    Pour passer d'un compte à l'autre, déconnectez-vous puis reconnectez-vous avec l'autre adresse.</p>
  <div class="mt-4 flex flex-wrap gap-2">
    <form method="post" action="<?= url('/compte/deconnexion') ?>" data-confirmer="<?= $autre === 'organisation' ? 'Créer un compte organisation ?' : 'Créer un compte personnel ?' ?>" data-confirmer-texte="Vous serez déconnecté de ce compte, puis le formulaire d'inscription s'ouvrira. Vous pourrez revenir ici en vous reconnectant." data-confirmer-oui="Continuer">
      <?= csrf() ?><input type="hidden" name="vers" value="<?= $autre ?>">
      <button type="submit" class="btn btn-vert min-h-10 px-5 text-sm"><?= $autre === 'organisation' ? 'Ajouter un compte organisation' : 'Ajouter un compte personnel' ?></button>
    </form>
    <form method="post" action="<?= url('/compte/deconnexion') ?>" data-confirmer="Êtes-vous sûr de vouloir vous déconnecter ?" data-confirmer-texte="Vous pourrez ensuite vous connecter avec un autre compte." data-confirmer-oui="Se déconnecter">
      <?= csrf() ?><button type="submit" class="btn-lien min-h-10">Se connecter avec un autre compte</button>
    </form>
  </div>
</section>

<?php /* Suppression du compte : repliée derrière un lien discret pour éviter un clic par erreur */ ?>
<details class="px-1" <?= !empty($erreurs['supprimer_actuel']) ? 'open' : '' ?>>
  <summary class="inline-flex min-h-10 cursor-pointer list-none items-center text-sm font-bold text-rouge hover:underline [&::-webkit-details-marker]:hidden">Supprimer mon compte</summary>
  <section class="bloc mt-2 border border-rouge/20" aria-label="Supprimer mon compte">
    <p class="text-sm">Votre compte, votre profil et vos demandes d'intégration seront définitivement supprimés. Cette action est irréversible.</p>
    <form method="post" class="mt-4 grid max-w-lg gap-4" novalidate data-confirmer="Supprimer définitivement votre compte ?">
      <?= csrf() ?><input type="hidden" name="action" value="supprimer">
      <?php partiel('partials/champ', ['nom' => 'mot_de_passe_actuel', 'libelle' => 'Votre mot de passe', 'type' => 'password', 'erreur' => $erreurs['supprimer_actuel'] ?? '', 'id' => 'champ-mdp-suppression', 'attributs' => ['autocomplete' => 'current-password']]) ?>
      <div><button type="submit" class="btn border-rouge bg-rouge text-white hover:bg-white hover:text-rouge">Supprimer définitivement</button></div>
    </form>
  </section>
</details>
<?php $espace = ob_get_clean(); partiel('espace/base', get_defined_vars());
