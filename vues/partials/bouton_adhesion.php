<?php
/*
 * Bouton d'une communauté.
 *  - dans la liste ($contexte « liste ») : toujours « Voir la communauté » ;
 *  - sur la page de la communauté : « Intégrer la communauté », la demande en cours, ou « Vous êtes membre ».
 * Paramètres : $c (communauté), $statut (null, en_attente, acceptee, refusee), $contexte, $compte_admin
 */
if (($contexte ?? 'liste') === 'liste'): ?>
  <a href="<?= e($c['url']) ?>" class="btn btn-vert">Voir la communauté</a>
<?php elseif (!empty($compte_admin)): ?>
  <span class="inline-flex min-h-11 items-center rounded-full bg-white/15 px-5 text-sm font-bold text-white">Compte d'administration : consultation seule</span>
<?php elseif ($statut === 'acceptee'): ?>
  <span class="inline-flex min-h-12 items-center rounded-full bg-white px-6 text-base font-bold text-vert">Vous êtes membre</span>
<?php elseif ($statut === 'en_attente'): ?>
  <form method="post" action="<?= url('/communaute/' . $c['slug'] . '/quitter') ?>" class="flex flex-wrap items-center gap-2" data-confirmer="Annuler votre demande pour <?= e($c['nom']) ?> ?" data-confirmer-oui="Annuler la demande"><?= csrf() ?>
    <input type="hidden" name="suivant" value="<?= e(adresse_actuelle()) ?>">
    <span class="inline-flex min-h-12 items-center rounded-full bg-white px-6 text-base font-bold text-vert">Demande en cours de traitement</span>
    <button type="submit" class="inline-flex min-h-10 items-center rounded-full bg-white/15 px-4 text-sm font-bold text-white transition-colors hover:bg-orange">Annuler la demande</button>
  </form>
<?php else: ?>
  <a href="<?= url('/communaute/' . $c['slug'] . '/integrer') ?>" class="btn btn-orange survol-grand min-h-14 rounded-full px-9 text-lg"><?= $statut === 'refusee' ? 'Renouveler ma demande' : 'Intégrer la communauté' ?></a>
<?php endif;
