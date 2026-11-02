<?php $p = $membre['profil']; $organisation = $membre['type_compte'] === 'organisation'; ?>
<div class="ycm-entete-page">
  <div>
    <h1><?= e($d['membre_nom']) ?></h1>
    <p class="ycm-sous-titre">Demande pour intégrer la communauté « <?= e($d['communaute_nom']) ?> ».</p>
  </div>
</div>
<div class="ycm-form-grille">
  <div class="ycm-form-principal">
    <fieldset class="ycm-module">
      <legend><h2>La demande</h2></legend>
      <dl class="ycm-fiche">
        <div><dt>Membre</dt><dd><?= nom_et_detail($d['membre_nom'], $d['email'], avatar($membre['initiales'], (int) $d['id'], $p['photo'] ?? $p['logo'] ?? null)) ?></dd></div>
        <div><dt>Type de compte</dt><dd><?= e(TYPES_COMPTE[$membre['type_compte']] ?? '') ?><?= $p ? ' · ' . e(libelle_profil($p, $membre['type_compte'])) : '' ?></dd></div>
        <?php if ($p): ?><div><dt>Région</dt><dd><?= e(($p['ville'] ? $p['ville'] . ', ' : '') . (REGIONS[$p['region']] ?? '')) ?></dd></div><?php endif ?>
        <?php if ($p && trim((string) $p['presentation']) !== ''): ?><div><dt>Présentation</dt><dd><?= nl2br(e($p['presentation'])) ?></dd></div><?php endif ?>
        <div><dt>Motivation</dt><dd><?= trim((string) ($d['motivation'] ?? '')) !== '' ? nl2br(e($d['motivation'])) : '<span class="ycm-discret">Non renseignée (demande envoyée avant le formulaire)</span>' ?></dd></div>
        <?php if (trim((string) ($d['apport'] ?? '')) !== ''): ?><div><dt>Ce qu'il peut apporter</dt><dd><?= nl2br(e($d['apport'])) ?></dd></div><?php endif ?>
        <div><dt>Communauté demandée</dt><dd><strong style="color:var(--ycm-vert)"><?= e($d['communaute_nom']) ?></strong></dd></div>
        <div><dt>Demandée le</dt><dd><?= e(date_longue($d['demandee_le'])) ?></dd></div>
      </dl>
      <?php if ($p): ?><p style="margin:16px 0 0"><a class="ycm-lien" href="<?= url('/gestion/' . ($organisation ? 'organisations' : 'membres') . '/' . $p['id']) ?>">Voir la fiche complète du membre</a></p><?php endif ?>
    </fieldset>
  </div>
  <aside class="ycm-form-cote">
    <fieldset class="ycm-module">
      <legend><h2>Décision</h2></legend>
      <dl class="ycm-fiche">
        <div><dt>Statut</dt><dd><?= badge(STATUTS_DEMANDE[$d['statut']] ?? '', ['en_attente' => 'orange', 'acceptee' => 'vert', 'refusee' => 'rouge'][$d['statut']] ?? 'gris') ?></dd></div>
        <?php if ($d['traitee_le']): ?><div><dt>Traitée le</dt><dd><?= e(date_longue($d['traitee_le'])) ?><?= $d['traitee_par_email'] ? ' par ' . e($d['traitee_par_email']) : '' ?></dd></div><?php endif ?>
      </dl>
    </fieldset>
    <?php if ($peut_modifier): ?>
    <form method="post" class="ycm-panneau-boutons">
      <?= csrf() ?>
      <?php if ($d['statut'] !== 'acceptee'): ?><button type="submit" name="decision" value="accepter" class="ycm-bouton-vert">Accepter la demande</button><?php endif ?>
      <?php if ($d['statut'] !== 'refusee'): ?><button type="submit" name="decision" value="refuser" class="ycm-bouton-supprimer"><?= $d['statut'] === 'acceptee' ? 'Retirer de la communauté' : 'Refuser la demande' ?></button><?php endif ?>
      <a href="<?= url('/gestion/communautes') ?>" class="ycm-bouton-gris">Retour</a>
    </form>
    <?php endif ?>
    <p class="ycm-encadre-info">Le membre suit l'état de sa demande dans son espace (« En cours de traitement », puis « Membre » ou « Non retenue »).</p>
  </aside>
</div>
