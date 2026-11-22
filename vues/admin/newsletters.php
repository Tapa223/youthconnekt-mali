<?php
/* Newsletter : brouillons et historique des envois. Les abonnés se gèrent dans l'onglet voisin. */
?>
<div class="ycm-entete-page">
  <div>
    <h1>Newsletter</h1>
    <p class="ycm-sous-titre"><?= $nb_abonnes ?> abonné<?= pluriel($nb_abonnes) ?> actif<?= pluriel($nb_abonnes) ?>. Rédigez une newsletter, choisissez à qui l'envoyer, vérifiez l'aperçu, puis envoyez.</p>
  </div>
  <?php if ($peut_modifier): ?><a href="<?= url('/gestion/newsletters/nouvelle') ?>" class="ycm-bouton-orange">Nouvelle newsletter</a><?php endif ?>
</div>
<nav class="ycm-onglets" aria-label="Newsletter">
  <a href="<?= url('/gestion/newsletters') ?>" class="actif" aria-current="page">Envois</a>
  <a href="<?= url('/gestion/newsletter') ?>">Abonnés (<?= $nb_abonnes ?>)</a>
</nav>
<?php if ($mode !== 'brevo'): ?>
<p class="ycm-encadre-info" style="margin:16px 0">Brevo n'est pas encore branché : vous pouvez tout préparer et vérifier l'aperçu, mais les envois sont seulement simulés (écrits dans stockage/emails.log). Ils partiront réellement dès que la clé Brevo sera ajoutée dans config.php.</p>
<?php endif ?>
<?php if ($lignes): ?>
<div class="ycm-newsletters">
  <?php foreach ($lignes as $n): $brouillon = $n['statut'] === 'brouillon'; ?>
  <article class="ycm-newsletter">
    <div class="ycm-newsletter-etat">
      <?= $brouillon ? badge('Brouillon', 'orange') : ($n['statut'] === 'envoyee' ? badge('Envoyée', 'vert') : badge('Envoi simulé', 'gris')) ?>
      <span class="ycm-discret"><?= $brouillon ? 'Modifié le ' . e(date_chiffres($n['modifie_le'])) : 'Le ' . e(date_chiffres($n['envoyee_le'])) . ' · ' . (int) $n['nb_destinataires'] . ' destinataire' . pluriel((int) $n['nb_destinataires']) ?></span>
    </div>
    <h2><a href="<?= url('/gestion/newsletters/' . $n['id']) ?>" class="ycm-newsletter-lien"><?= e($n['objet']) ?></a></h2>
    <p class="ycm-discret"><?= e(CIBLES_NEWSLETTER[$n['cible']] ?? '') ?><?= $n['auteur'] ? ' · par ' . e($n['auteur']) : '' ?></p>
  </article>
  <?php endforeach ?>
</div>
<?php else: ?>
<div class="ycm-boite ycm-boite--pad" style="margin-top:16px"><p class="ycm-vide">Aucune newsletter pour l'instant. Commencez par « Nouvelle newsletter ».</p></div>
<?php endif ?>
