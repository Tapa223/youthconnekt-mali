<?php
/* Prendre part à une initiative : choix du rôle et réponses aux questions créées par l'équipe. */
$err = fn ($c) => !empty($erreurs[$c]) ? '<p class="mt-1.5 text-sm font-medium text-rouge" role="alert">' . e($erreurs[$c]) . '</p>' : '';
$val = fn ($c) => $valeurs[$c] ?? '';
$profil = $u['profil'] ?? [];
?>
<section class="conteneur max-w-3xl pt-4 pb-14 sm:pt-8 sm:pb-20">
  <?php partiel('partials/retour', ['url' => url('/initiatives/' . $i['slug'])]) ?>
  <p class="surtitre">Prendre part</p>
  <h1 class="mt-1 text-[1.75rem] leading-tight font-bold sm:text-4xl"><?= e($i['titre']) ?></h1>
  <?php if ($i['date_libelle'] || $i['lieu']): ?><p class="mt-2 text-sm text-texte"><?= e(implode(' · ', array_filter([$i['date_libelle'], $i['lieu']]))) ?></p><?php endif ?>

  <?php if ($deja): ?>
  <div class="mt-6 rounded-2xl bg-vert-clair p-5 text-vert">
    <p class="font-bold">Vous avez déjà proposé votre participation comme « <?= e($deja['role_libelle']) ?> ».</p>
    <p class="mt-1 text-sm">État : <?= e(STATUTS_PARTICIPATION[$deja['statut']] ?? '') ?>. L'équipe vous répond dans votre espace.</p>
    <a href="<?= url('/compte/mon-espace/participations') ?>" class="btn btn-vert mt-4 min-h-11">Voir mes participations</a>
  </div>
  <?php elseif (!$ouvertes): ?>
  <div class="mt-6 rounded-2xl bg-gris p-5">
    <p class="font-bold text-vert">Les participations à cette initiative sont fermées.</p>
    <a href="<?= url('/initiatives/' . $i['slug']) ?>" class="btn btn-contour mt-4 min-h-11">Revenir à l'initiative</a>
  </div>
  <?php else: ?>
  <?php if (trim((string) $i['participations_intro']) !== ''): ?><p class="mt-4 text-[15px] leading-relaxed sm:text-base"><?= nl2br(e($i['participations_intro'])) ?></p><?php endif ?>

  <form method="post" class="mt-6 space-y-6" novalidate data-confirmer="Envoyer votre candidature ?" data-confirmer-texte="L'équipe du chapitre l'étudie et vous répond dans votre espace." data-confirmer-oui="Envoyer">
    <?= csrf() ?>
    <?php if ($erreurs): ?><p class="rounded-xl border border-rouge/30 bg-rouge/5 px-4 py-3 text-sm text-rouge" role="alert">Certaines réponses sont à compléter : elles sont indiquées en rouge.</p><?php endif ?>

    <fieldset>
      <legend class="libelle">Comment souhaitez-vous participer ?</legend>
      <div class="mt-2 grid gap-2.5 sm:grid-cols-2">
        <?php foreach ($roles as $r): ?>
        <label class="flex cursor-pointer gap-3 rounded-2xl border-2 bg-white p-4 transition-colors has-[:checked]:border-orange has-[:checked]:bg-orange-clair/40 <?= $r['complet'] ? 'pointer-events-none border-ligne opacity-50' : 'border-ligne hover:border-vert' ?>">
          <input type="radio" name="role" value="<?= (int) $r['id'] ?>" class="mt-1 size-4 shrink-0 accent-orange"<?= (string) $r['id'] === (string) $val('role') ? ' checked' : '' ?><?= $r['complet'] ? ' disabled' : '' ?>>
          <span class="min-w-0">
            <span class="block font-bold text-vert"><?= e($r['nom']) ?></span>
            <?php if ($r['description'] !== ''): ?><span class="mt-0.5 block text-sm leading-snug"><?= e($r['description']) ?></span><?php endif ?>
            <span class="mt-1 block text-xs font-bold <?= $r['complet'] ? 'text-texte' : 'text-orange-fonce' ?>"><?= $r['complet'] ? 'Complet' : ($r['restantes'] !== null ? $r['restantes'] . ' place' . pluriel((int) $r['restantes']) . ' restante' . pluriel((int) $r['restantes']) : 'Places ouvertes') ?></span>
          </span>
        </label>
        <?php endforeach ?>
      </div>
      <?= $err('role') ?>
    </fieldset>

    <?php foreach ($champs as $c): $nom = 'champ_' . $c['id']; $id = 'pa-' . $c['id']; $v = $val($nom); ?>
    <div>
      <?php if (in_array($c['type'], ['choix', 'cases', 'oui_non'], true)): ?>
      <fieldset>
        <legend class="libelle"><?= e($c['libelle']) ?><?php if (!$c['requis']): ?> <span class="font-normal text-texte">(facultatif)</span><?php endif ?></legend>
        <?php if ($c['aide'] !== ''): ?><p class="aide mt-0 mb-2"><?= e($c['aide']) ?></p><?php endif ?>
        <div class="flex flex-wrap gap-2">
          <?php foreach ($c['type'] === 'oui_non' ? ['Oui', 'Non'] : $c['choix'] as $choix):
              $coche = $c['type'] === 'cases' ? in_array($choix, (array) $v, true) : $v === $choix; ?>
          <label class="inline-flex min-h-11 cursor-pointer items-center gap-2 rounded-full border-2 border-ligne bg-white px-4 text-[15px] has-[:checked]:border-orange has-[:checked]:bg-orange-clair/40">
            <input type="<?= $c['type'] === 'cases' ? 'checkbox' : 'radio' ?>" name="<?= $nom ?><?= $c['type'] === 'cases' ? '[]' : '' ?>" value="<?= e($choix) ?>" class="size-4 accent-orange"<?= $coche ? ' checked' : '' ?>> <?= e($choix) ?>
          </label>
          <?php endforeach ?>
        </div>
      </fieldset>
      <?php else: ?>
      <label for="<?= $id ?>" class="libelle"><?= e($c['libelle']) ?><?php if (!$c['requis']): ?> <span class="font-normal text-texte">(facultatif)</span><?php endif ?></label>
      <?php if ($c['type'] === 'zone'): ?>
      <textarea id="<?= $id ?>" name="<?= $nom ?>" rows="4" maxlength="3000" class="champ"><?= e(is_string($v) ? $v : '') ?></textarea>
      <?php else: ?>
      <input id="<?= $id ?>" name="<?= $nom ?>" type="<?= ['date' => 'date', 'nombre' => 'number'][$c['type']] ?? 'text' ?>" value="<?= e(is_string($v) ? $v : '') ?>" maxlength="300" class="champ"<?= $c['type'] === 'nombre' ? ' inputmode="decimal" step="any"' : '' ?>>
      <?php endif ?>
      <?php if ($c['aide'] !== ''): ?><p class="aide"><?= e($c['aide']) ?></p><?php endif ?>
      <?php endif ?>
      <?= $err($nom) ?>
    </div>
    <?php endforeach ?>

    <div class="rounded-2xl bg-gris p-4 text-sm">
      <p class="font-bold text-vert">Vos informations, reprises de votre compte</p>
      <p class="mt-1"><?= e($u['nom_affiche']) ?> · <?= e($u['email']) ?><?= !empty($profil['telephone']) ? ' · ' . e($profil['telephone']) : '' ?></p>
      <p class="mt-1 text-texte">Elles sont transmises à l'équipe du chapitre pour organiser l'initiative. <a href="<?= url('/compte/mon-espace/profil', ['modifier' => 1]) ?>#modifier-profil" class="font-bold text-vert hover:text-orange">Les mettre à jour</a></p>
    </div>

    <button type="submit" class="btn btn-orange min-h-12 w-full text-base sm:w-auto sm:px-10">Envoyer ma candidature</button>
  </form>
  <?php endif ?>
</section>
