<?php
/*
 * Action principale d'une communauté selon la situation du membre.
 * On ne propose jamais « Quitter » ici : un membre voit la communauté, il ne la quitte
 * que depuis son espace (Mes communautés) ou en bas de la page de la communauté.
 * Paramètres : $c (communauté), $statut (null, en_attente, acceptee, refusee), $style (« clair » sur fond vert)
 */
$clair = ($style ?? '') === 'clair';
if (!est_connecte()): ?>
  <a href="<?= url('/compte/connexion', ['suivant' => $c['url']]) ?>" class="btn btn-orange">Demander à intégrer</a>
<?php elseif ($statut === 'acceptee'): ?>
  <?php if ($clair): ?>
  <span class="inline-flex min-h-11 items-center rounded-full bg-white px-5 text-sm font-bold text-vert">Vous êtes membre</span>
  <?php else: ?>
  <a href="<?= e($c['url']) ?>" class="btn btn-vert">Voir la communauté</a>
  <?php endif ?>
<?php elseif ($statut === 'en_attente'): ?>
  <form method="post" action="<?= url('/communaute/' . $c['slug'] . '/quitter') ?>" class="flex flex-wrap items-center gap-2"><?= csrf() ?>
    <input type="hidden" name="suivant" value="<?= e(adresse_actuelle()) ?>">
    <span class="inline-flex min-h-10 items-center rounded-full px-4 text-sm font-bold <?= $clair ? 'bg-white text-vert' : 'bg-orange/15 text-orange-fonce' ?>">En cours de traitement</span>
    <button type="submit" class="btn-discret">Annuler la demande</button>
  </form>
<?php else: ?>
  <form method="post" action="<?= url('/communaute/' . $c['slug'] . '/rejoindre') ?>"><?= csrf() ?>
    <input type="hidden" name="suivant" value="<?= e(adresse_actuelle()) ?>">
    <button type="submit" class="btn btn-orange">Demander à intégrer</button></form>
<?php endif;
