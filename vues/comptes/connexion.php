<h1 class="text-[1.9rem] leading-tight font-bold sm:text-4xl">Se connecter</h1>
<p class="mt-1 text-[15px]">Retrouvez vos opportunités, vos communautés et votre espace.</p>
<?php if ($contexte): ?><p class="mt-4 rounded-xl border-l-4 border-orange bg-orange-clair px-4 py-3 text-sm font-medium text-encre"><?= e($contexte) ?></p><?php endif ?>
<form method="post" action="<?= url('/compte/connexion') ?>" class="mt-5 space-y-4 sm:mt-7" novalidate>
  <?= csrf() ?>
  <input type="hidden" name="suivant" value="<?= e($suivant) ?>">
  <?php if ($erreur): ?><div class="rounded-xl border border-rouge/30 bg-rouge/5 px-4 py-3 text-sm text-rouge" role="alert"><?= e($erreur) ?></div><?php endif ?>
  <?php partiel('partials/champ', ['nom' => 'email', 'libelle' => 'Adresse e-mail', 'type' => 'email', 'valeur' => $email, 'attributs' => ['autocomplete' => 'email', 'autofocus' => 'autofocus', 'placeholder' => 'vous@exemple.com']]) ?>
  <?php partiel('partials/champ', ['nom' => 'mot_de_passe', 'libelle' => 'Mot de passe', 'mdp' => true, 'attributs' => ['autocomplete' => 'current-password']]) ?>
  <div class="flex items-center justify-between gap-3">
    <label class="flex items-center gap-2 text-sm text-encre"><input type="checkbox" name="se_souvenir" value="1" class="size-5 rounded accent-vert"> Se souvenir de moi</label>
    <a href="<?= url('/compte/mot-de-passe-oublie') ?>" class="text-sm font-bold text-vert hover:text-orange">Mot de passe oublié ?</a>
  </div>
  <button type="submit" class="btn btn-vert min-h-12 w-full text-base">Se connecter</button>
</form>
<p class="mt-6 flex flex-wrap items-center justify-center gap-x-2 gap-y-1 border-t border-ligne pt-5 text-sm">Pas encore membre ?
  <a href="<?= url('/compte/inscription') ?>" class="font-bold text-orange hover:text-vert">Rejoindre le chapitre</a></p>
