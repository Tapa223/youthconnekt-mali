<?php
/* Prendre part à une initiative : choix du rôle, puis les questions de ce rôle (créées par l'équipe). */
$err = fn ($c) => !empty($erreurs[$c]) ? '<p class="mt-1.5 text-sm font-medium text-rouge" role="alert">' . e($erreurs[$c]) . '</p>' : '';
$val = fn ($c) => $valeurs[$c] ?? '';
$profil = $u['profil'] ?? [];
$choisi = null;
foreach ($roles as $r) {
    if ((string) $r['id'] === (string) $val('role') && !$r['complet']) {
        $choisi = $r;
    }
}
$textes = [
    'inscription' => ['Confirmer mon inscription', 'Confirmer votre inscription ?', 'Votre billet s\'affiche aussitôt : présentez-le à l\'entrée.', 'Confirmer'],
    'candidature' => ['Envoyer ma candidature', 'Envoyer votre candidature ?', "L'équipe du chapitre l'étudie et vous répond dans votre espace.", 'Envoyer'],
];
$t = $textes[$choisi['mode'] ?? 'candidature'];
?>
<section class="conteneur max-w-3xl pt-4 pb-14 sm:pt-8 sm:pb-20">
  <?php partiel('partials/retour', ['url' => url('/initiatives/' . $i['slug'])]) ?>
  <p class="surtitre">Prendre part</p>
  <h1 class="mt-1 text-[1.75rem] leading-tight font-bold sm:text-4xl"><?= e($i['titre']) ?></h1>
  <?php if ($i['date_libelle'] || $i['lieu']): ?><p class="mt-2 text-sm text-texte"><?= e(implode(' · ', array_filter([$i['date_libelle'], $i['lieu']]))) ?></p><?php endif ?>

  <?php if ($deja): ?>
  <div class="mt-6 rounded-2xl bg-vert-clair p-5 text-vert">
    <?php if ($deja['statut'] === 'inscrit'): ?>
    <p class="font-bold">Vous êtes déjà inscrit comme « <?= e($deja['role_libelle']) ?> ».</p>
    <?php else: ?>
    <p class="font-bold">Vous avez déjà proposé votre participation comme « <?= e($deja['role_libelle']) ?> ».</p>
    <p class="mt-1 text-sm">État : <?= e(STATUTS_PARTICIPATION[$deja['statut']] ?? '') ?>.<?= $deja['statut'] === 'en_attente' ? " L'équipe vous répond dans votre espace." : '' ?></p>
    <?php endif ?>
    <div class="mt-4 flex flex-wrap gap-2">
      <?php if (billet_valable($deja)): ?><a href="<?= url('/compte/mon-espace/participations/' . $deja['id'] . '/billet') ?>" class="btn btn-orange min-h-11">Voir mon billet</a><?php endif ?>
      <a href="<?= url('/compte/mon-espace/participations') ?>" class="btn btn-contour min-h-11">Mes participations</a>
    </div>
  </div>
  <?php elseif (!$ouvertes): ?>
  <div class="mt-6 rounded-2xl bg-gris p-5">
    <p class="font-bold text-vert">Les participations à cette initiative sont closes.</p>
    <p class="mt-1 text-sm">Vous pouvez toujours consulter l'initiative et suivre les prochaines.</p>
    <a href="<?= url('/initiatives/' . $i['slug']) ?>" class="btn btn-contour mt-4 min-h-11">Revenir à l'initiative</a>
  </div>
  <?php else: ?>
  <?php if (trim((string) $i['participations_intro']) !== ''): ?><p class="mt-4 text-[15px] leading-relaxed sm:text-base"><?= nl2br(e($i['participations_intro'])) ?></p><?php endif ?>

  <form method="post" class="mt-6 space-y-6" novalidate data-participer data-confirmer="<?= e($t[1]) ?>" data-confirmer-texte="<?= e($t[2]) ?>" data-confirmer-oui="<?= e($t[3]) ?>">
    <?= csrf() ?>
    <?php if ($erreurs): ?><p class="rounded-xl border border-rouge/30 bg-rouge/5 px-4 py-3 text-sm text-rouge" role="alert">Certaines réponses sont à compléter : elles sont indiquées en rouge.</p><?php endif ?>

    <fieldset<?= count($roles) === 1 && $choisi ? ' class="sr-only"' : '' ?>>
      <legend class="libelle">Comment souhaitez-vous participer ?</legend>
      <div class="mt-2 grid gap-2.5 sm:grid-cols-2">
        <?php foreach ($roles as $r): $m = $textes[$r['mode']] ?? $textes['candidature']; ?>
        <label class="flex cursor-pointer gap-3 rounded-2xl border-2 bg-white p-4 transition-colors has-[:checked]:border-orange has-[:checked]:bg-orange-clair/40 <?= $r['complet'] ? 'pointer-events-none border-ligne opacity-50' : 'border-ligne hover:border-vert' ?>">
          <input type="radio" name="role" value="<?= (int) $r['id'] ?>" class="mt-1 size-4 shrink-0 accent-orange" data-role-choix
                 data-bouton="<?= e($m[0]) ?>" data-question="<?= e($m[1]) ?>" data-precision="<?= e($m[2]) ?>" data-oui="<?= e($m[3]) ?>"<?= $choisi && (int) $choisi['id'] === (int) $r['id'] ? ' checked' : '' ?><?= $r['complet'] ? ' disabled' : '' ?>>
          <span class="min-w-0">
            <span class="block font-bold text-vert"><?= e($r['nom']) ?></span>
            <?php if ($r['description'] !== ''): ?><span class="mt-0.5 block text-sm leading-snug"><?= e($r['description']) ?></span><?php endif ?>
            <span class="mt-1 block text-xs font-bold <?= $r['complet'] ? 'text-texte' : 'text-orange-fonce' ?>"><?= $r['complet'] ? 'Complet' : ($r['mode'] === 'inscription' ? 'Inscription directe' : 'Sur candidature') . ($r['restantes'] !== null ? ' · ' . $r['restantes'] . ' place' . pluriel((int) $r['restantes']) . ' restante' . pluriel((int) $r['restantes']) : '') ?></span>
          </span>
        </label>
        <?php endforeach ?>
      </div>
      <?= $err('role') ?>
    </fieldset>
    <?php if (count($roles) === 1 && $choisi): ?>
    <p class="rounded-2xl bg-gris px-4 py-3 text-[15px]"><span class="font-bold text-vert"><?= e($choisi['nom']) ?></span><?= $choisi['description'] !== '' ? ' · ' . e($choisi['description']) : '' ?></p>
    <?php endif ?>

    <?php foreach ($roles as $r): if (!$r['champs']) continue; $actif = $choisi && (int) $choisi['id'] === (int) $r['id']; ?>
    <div class="space-y-6" data-questions-role="<?= (int) $r['id'] ?>"<?= $actif ? '' : ' hidden' ?>>
      <?php if (count($roles) > 1): ?><p class="border-t border-ligne pt-5 text-sm font-bold tracking-wide text-vert uppercase">Questions pour « <?= e($r['nom']) ?> »</p><?php endif ?>
      <?php foreach ($r['champs'] as $c): $nom = 'champ_' . $c['id']; $id = 'pa-' . $c['id']; $v = $val($nom); $off = $actif ? '' : ' disabled'; ?>
      <div>
        <?php if (in_array($c['type'], ['choix', 'cases', 'oui_non'], true)): ?>
        <fieldset>
          <legend class="libelle"><?= e($c['libelle']) ?><?php if (!$c['requis']): ?> <span class="font-normal text-texte">(facultatif)</span><?php endif ?></legend>
          <?php if ($c['aide'] !== ''): ?><p class="aide mt-0 mb-2"><?= e($c['aide']) ?></p><?php endif ?>
          <div class="flex flex-wrap gap-2">
            <?php foreach ($c['type'] === 'oui_non' ? ['Oui', 'Non'] : $c['choix'] as $choix):
                $coche = $c['type'] === 'cases' ? in_array($choix, (array) $v, true) : $v === $choix; ?>
            <label class="inline-flex min-h-11 cursor-pointer items-center gap-2 rounded-full border-2 border-ligne bg-white px-4 text-[15px] has-[:checked]:border-orange has-[:checked]:bg-orange-clair/40">
              <input type="<?= $c['type'] === 'cases' ? 'checkbox' : 'radio' ?>" name="<?= $nom ?><?= $c['type'] === 'cases' ? '[]' : '' ?>" value="<?= e($choix) ?>" class="size-4 accent-orange"<?= $coche ? ' checked' : '' ?><?= $off ?>> <?= e($choix) ?>
            </label>
            <?php endforeach ?>
          </div>
        </fieldset>
        <?php else: ?>
        <label for="<?= $id ?>" class="libelle"><?= e($c['libelle']) ?><?php if (!$c['requis']): ?> <span class="font-normal text-texte">(facultatif)</span><?php endif ?></label>
        <?php if ($c['type'] === 'zone'): ?>
        <textarea id="<?= $id ?>" name="<?= $nom ?>" rows="4" maxlength="3000" class="champ"<?= $off ?>><?= e(is_string($v) ? $v : '') ?></textarea>
        <?php else: ?>
        <input id="<?= $id ?>" name="<?= $nom ?>" type="<?= ['date' => 'date', 'nombre' => 'number'][$c['type']] ?? 'text' ?>" value="<?= e(is_string($v) ? $v : '') ?>" maxlength="300" class="champ"<?= $c['type'] === 'nombre' ? ' inputmode="decimal" step="any"' : '' ?><?= $off ?>>
        <?php endif ?>
        <?php if ($c['aide'] !== ''): ?><p class="aide"><?= e($c['aide']) ?></p><?php endif ?>
        <?php endif ?>
        <?= $err($nom) ?>
      </div>
      <?php endforeach ?>
    </div>
    <?php endforeach ?>

    <div class="rounded-2xl bg-gris p-4 text-sm">
      <p class="font-bold text-vert">Vos informations, reprises de votre compte</p>
      <p class="mt-1"><?= e($u['nom_affiche']) ?> · <?= e($u['email']) ?><?= !empty($profil['telephone']) ? ' · ' . e($profil['telephone']) : '' ?></p>
      <p class="mt-1 text-texte">Elles sont transmises à l'équipe du chapitre pour organiser l'initiative. <a href="<?= url('/compte/mon-espace/profil', ['modifier' => 1]) ?>#modifier-profil" class="font-bold text-vert hover:text-orange">Les mettre à jour</a></p>
    </div>

    <button type="submit" class="btn btn-orange min-h-12 w-full text-base sm:w-auto sm:px-10" data-bouton-envoi><?= e($t[0]) ?></button>
  </form>
  <?php endif ?>
</section>
