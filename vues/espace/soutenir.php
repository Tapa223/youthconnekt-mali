<?php ob_start();
/* Soutenir le chapitre : formulaire court, puis la liste de ce qui a déjà été proposé. */
$etats = ['nouveau' => 'bg-orange/15 text-orange-fonce', 'en_cours' => 'bg-turquoise-clair text-vert', 'accepte' => 'bg-vert-clair text-vert', 'decline' => 'bg-gris text-texte'];
?>
<section class="bloc">
  <h1 class="text-xl font-bold max-lg:sr-only sm:text-3xl">Soutenir le chapitre</h1>
  <p class="text-sm text-encre lg:mt-1">Un peu de votre temps, un lieu, du matériel, des stages pour les jeunes ou un financement : chaque soutien aide le chapitre à organiser ses activités. L'équipe vous recontacte.</p>
  <form method="post" class="mt-4 grid gap-4" novalidate data-confirmer="Envoyer votre proposition ?" data-confirmer-texte="L'équipe du chapitre la reçoit et vous recontacte." data-confirmer-oui="Envoyer">
    <?= csrf() ?>
    <fieldset>
      <legend class="libelle">Ce que vous proposez</legend>
      <div class="flex flex-wrap gap-2">
        <?php foreach (FORMES_SOUTIEN as $cle => $libelle): ?>
        <label class="inline-flex min-h-10 cursor-pointer items-center gap-2 rounded-full border-2 border-ligne bg-white px-3.5 text-sm has-[:checked]:border-orange has-[:checked]:bg-orange-clair/40">
          <input type="checkbox" name="formes[]" value="<?= $cle ?>" class="size-4 accent-orange"<?= in_array($cle, $formes, true) ? ' checked' : '' ?>> <?= e($libelle) ?>
        </label>
        <?php endforeach ?>
      </div>
      <?php if (!empty($erreurs['formes'])): ?><p class="erreur"><?= e($erreurs['formes']) ?></p><?php endif ?>
    </fieldset>
    <div>
      <label for="message-soutien" class="libelle">En quelques mots</label>
      <textarea id="message-soutien" name="message" rows="4" maxlength="1500" class="champ min-h-28 py-3" placeholder="Exemple : nous pouvons accueillir un atelier de 40 personnes dans nos locaux à Bamako."><?= e($message) ?></textarea>
      <?php if (!empty($erreurs['message'])): ?><p class="erreur"><?= e($erreurs['message']) ?></p><?php endif ?>
    </div>
    <div><button type="submit" class="btn btn-orange min-h-11 px-6">Envoyer ma proposition</button></div>
  </form>
</section>

<?php if ($envoyes): ?>
<section class="bloc" aria-labelledby="titre-soutiens">
  <h2 id="titre-soutiens" class="text-lg font-bold sm:text-xl">Mes propositions de soutien</h2>
  <ul class="mt-3 space-y-2.5">
    <?php foreach ($envoyes as $s): ?>
    <li class="rounded-xl border border-ligne p-3 sm:p-4">
      <div class="flex flex-wrap items-center justify-between gap-2">
        <p class="text-sm font-bold text-vert"><?= e(implode(' · ', array_map(fn ($f) => FORMES_SOUTIEN[$f] ?? $f, array_filter(explode(',', $s['formes']))))) ?></p>
        <span class="rounded-full px-3 py-1 text-xs font-bold <?= $etats[$s['statut']] ?? $etats['nouveau'] ?>"><?= e(STATUTS_SOUTIEN[$s['statut']] ?? '') ?></span>
      </div>
      <p class="mt-1.5 text-sm text-encre"><?= nl2br(e($s['message'])) ?></p>
      <p class="mt-1 text-xs text-texte">Envoyée le <?= e(date_longue($s['cree_le'])) ?></p>
    </li>
    <?php endforeach ?>
  </ul>
</section>
<?php endif ?>
<?php $espace = ob_get_clean(); partiel('espace/base', get_defined_vars());
