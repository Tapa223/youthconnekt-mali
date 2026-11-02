<?php
/*
 * Bouton d'intégration selon la situation du membre.
 * Paramètres : $c (communauté), $statut (null, en_attente, acceptee, refusee), $style (« clair » sur fond vert)
 */
$clair = ($style ?? '') === 'clair';
$retour = '<input type="hidden" name="suivant" value="' . e(adresse_actuelle()) . '">';
if (!est_connecte()): ?>
  <a href="<?= url('/compte/connexion', ['suivant' => $c['url']]) ?>" class="btn btn-orange">Demander à intégrer</a>
<?php elseif ($statut === 'acceptee'): ?>
  <form method="post" action="<?= url('/communaute/' . $c['slug'] . '/quitter') ?>" data-confirmer="Quitter la communauté <?= e($c['nom']) ?> ?"><?= csrf() . $retour ?>
    <button type="submit" class="btn <?= $clair ? 'btn-blanc' : 'btn-contour' ?>">Quitter la communauté</button></form>
<?php elseif ($statut === 'en_attente'): ?>
  <form method="post" action="<?= url('/communaute/' . $c['slug'] . '/quitter') ?>" class="flex flex-wrap items-center gap-x-3 gap-y-2"><?= csrf() . $retour ?>
    <span class="inline-flex items-center gap-2 rounded-full <?= $clair ? 'bg-white text-vert' : 'bg-orange/15 text-orange-fonce' ?> px-3 py-1 text-sm font-bold">En cours de traitement</span>
    <button type="submit" class="text-sm font-bold underline underline-offset-4 <?= $clair ? 'text-white' : 'text-vert' ?>">Annuler la demande</button></form>
<?php else: ?>
  <form method="post" action="<?= url('/communaute/' . $c['slug'] . '/rejoindre') ?>"><?= csrf() . $retour ?>
    <button type="submit" class="btn btn-orange">Demander à intégrer</button></form>
<?php endif;
