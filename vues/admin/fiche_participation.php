<?php
/* Fiche d'une candidature : la personne, le rôle choisi, ses réponses, et la décision. */
$c = $ligne;
$rep = reponses_participation($c);
$a = age($c['date_naissance'] ?? null);
?>
<div class="ycm-entete-page">
  <div>
    <h1><?= e($c['membre_nom']) ?></h1>
    <p class="ycm-sous-titre">« <?= e($c['role_libelle']) ?> » pour <a class="ycm-lien" href="<?= url('/gestion/initiatives/' . $c['initiative_id'] . '/participations') ?>"><?= e($c['initiative_titre']) ?></a> · reçue le <?= e(date_longue($c['cree_le'])) ?></p>
  </div>
</div>
<div class="ycm-fiche-temoignage">
  <article class="ycm-boite ycm-boite--pad">
    <dl class="ycm-pa-reponses" style="border:0;margin:0;padding:0">
      <div><dt>E-mail</dt><dd><?= e($c['email']) ?></dd></div>
      <?php if (!empty($c['telephone'])): ?><div><dt>Téléphone</dt><dd><?= e($c['telephone']) ?></dd></div><?php endif ?>
      <?php if (!empty($c['genre']) || $a !== null): ?><div><dt>Genre et âge</dt><dd><?= e(implode(', ', array_filter([GENRES[$c['genre'] ?? ''] ?? '', $a !== null ? $a . ' ans' : '']))) ?></dd></div><?php endif ?>
      <?php if (!empty($c['region_i'])): ?><div><dt>Région</dt><dd><?= e(REGIONS[$c['region_i']] ?? '') ?></dd></div><?php endif ?>
    </dl>
    <?php if ($rep): ?>
    <h2 style="margin:20px 0 4px;font-size:17px;color:var(--ycm-vert)">Réponses</h2>
    <dl class="ycm-pa-reponses">
      <?php foreach ($rep as $x): ?><div><dt><?= e($x['question']) ?></dt><dd><?= $x['reponse'] !== '' ? nl2br(e($x['reponse'])) : '<span class="ycm-discret">Pas de réponse</span>' ?></dd></div><?php endforeach ?>
    </dl>
    <?php endif ?>
  </article>
  <?php if ($peut_modifier): ?>
  <aside class="ycm-boite ycm-boite--pad ycm-fiche-temoignage-actions">
    <h2><?= $c['statut'] === 'inscrit' ? 'Inscription' : 'Décision' ?></h2>
    <p><?= ['inscrit' => badge('Inscrit', 'vert'), 'en_attente' => badge('En attente', 'orange'), 'retenue' => badge('Retenue', 'vert'), 'non_retenue' => badge('Non retenue', 'gris')][$c['statut']] ?? '' ?><?= $c['present_le'] ? ' ' . badge('Présent', 'bleu') : '' ?></p>
    <?php if (billet_valable($c)): ?><p class="ycm-discret">Billet <strong><?= e((string) $c['code']) ?></strong></p><?php endif ?>
    <?php if ($c['statut'] === 'inscrit'): ?><p class="ycm-discret">Inscription directe : pas de décision à prendre.</p><?php endif ?>
    <?php foreach (['retenue' => ['Retenir', 'ycm-bouton-vert', 'Retenir ' . $c['membre_nom'] . ' ?', ''], 'non_retenue' => ['Ne pas retenir', 'ycm-bouton-rouge-contour', 'Ne pas retenir ' . $c['membre_nom'] . ' ?', ' data-confirmer-danger'], 'en_attente' => ['Remettre en attente', 'ycm-bouton-gris', 'Remettre cette candidature en attente ?', '']] as $s => [$lib, $classe, $question, $danger]):
        if ($s === $c['statut'] || $c['statut'] === 'inscrit') continue; ?>
    <form method="post" action="<?= url('/gestion/participations/' . $c['id'] . '/decision') ?>" data-confirmer="<?= e($question) ?>" data-confirmer-texte="La personne en est prévenue dans son espace." data-confirmer-oui="<?= e($lib) ?>"<?= $danger ?>>
      <?= csrf() ?><input type="hidden" name="statut" value="<?= $s ?>"><input type="hidden" name="suivant" value="<?= e(adresse_actuelle()) ?>">
      <button type="submit" class="<?= $classe ?>" style="width:100%"><?= $lib ?></button>
    </form>
    <?php endforeach ?>
  </aside>
  <?php endif ?>
</div>
