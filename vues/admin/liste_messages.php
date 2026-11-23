<?php
/*
 * Messages de contact, comme une messagerie.
 * Liste : une ligne par message (expéditeur, objet, date) ; rien d'autre ne s'affiche tant qu'on n'a pas cliqué.
 * Message ouvert : il prend toute la page, avec Retour, Marquer comme non lu, Supprimer, et la réponse dessous.
 * Supprimer met à la corbeille : le message y reste 30 jours, on peut le restaurer.
 */
partiel('admin/_entete_liste', get_defined_vars());
$profil = fn ($m) => $m['profil'] === 'email' ? 'Reçu par e-mail' : (PROFILS_CONTACT[$m['profil']] ?? '') . (!empty($m['profil_autre']) ? ' : ' . $m['profil_autre'] : '');
$date_courte = function (string $quand): string {
    $t = strtotime($quand);
    return date('Y-m-d', $t) === date('Y-m-d') ? date('H:i', $t) : (date('Y', $t) === date('Y') ? jour_mois($quand) : date('d/m/Y', $t));
};
$retour_liste = avec_parametres(['ouvert' => null]);
?>
<?php if ($ouvert): $prenom = trim(explode(' ', trim($ouvert['nom']))[0]); $dans_corbeille = !empty($ouvert['supprime_le']); ?>
<article class="ycm-boite ycm-lecture">
  <div class="ycm-lecture-barre">
    <a href="<?= e($retour_liste) ?>" class="ycm-bouton-contour">&larr; Retour</a>
    <?php if ($peut_modifier && !$dans_corbeille): ?>
    <form method="post" action="<?= url('/gestion/messages/' . $ouvert['id'] . '/non-lu') ?>"><?= csrf() ?><button type="submit" class="ycm-bouton-contour">Marquer comme non lu</button></form>
    <?php endif ?>
    <?php if ($peut_supprimer): ?>
      <?php if ($dans_corbeille): ?>
      <form method="post" action="<?= url('/gestion/messages/' . $ouvert['id'] . '/corbeille') ?>"><?= csrf() ?><input type="hidden" name="quoi" value="restaurer"><button type="submit" class="ycm-bouton-contour">Restaurer</button></form>
      <form method="post" action="<?= url('/gestion/messages/' . $ouvert['id'] . '/corbeille') ?>" data-confirmer="Supprimer définitivement ce message ?" data-confirmer-texte="Il disparaîtra pour de bon, avec les réponses envoyées." data-confirmer-oui="Supprimer définitivement" data-confirmer-danger><?= csrf() ?><input type="hidden" name="quoi" value="definitif"><button type="submit" class="ycm-bouton-rouge-contour">Supprimer définitivement</button></form>
      <?php else: ?>
      <form method="post" action="<?= url('/gestion/messages/' . $ouvert['id'] . '/corbeille') ?>"><?= csrf() ?><button type="submit" class="ycm-bouton-contour">Supprimer</button></form>
      <?php endif ?>
    <?php endif ?>
  </div>
  <?php if ($dans_corbeille): ?><p class="ycm-encadre-info" style="margin:0 0 18px">Ce message est dans la corbeille. Il sera supprimé définitivement le <?= e(date_longue(date('Y-m-d', strtotime($ouvert['supprime_le'] . ' +30 days')))) ?>, sauf si vous le restaurez.</p><?php endif ?>

  <div class="ycm-lecture-tete">
    <h2><?= e($ouvert['objet']) ?></h2>
    <?php if ($ouvert['traite']): ?><?= badge('Répondu', 'vert') ?><?php endif ?>
  </div>
  <p class="ycm-lecture-meta">
    <strong><?= e($ouvert['nom']) ?></strong> &lt;<a href="mailto:<?= e($ouvert['email']) ?>"><?= e($ouvert['email']) ?></a>&gt;<br>
    <?= e(implode(' · ', array_filter([$profil($ouvert), date_longue($ouvert['recu_le']) . ', ' . date('H \h i', strtotime($ouvert['recu_le']))]))) ?>
  </p>
  <div class="ycm-lecture-texte"><?= e($ouvert['message']) ?></div>

  <?php foreach ($reponses as $rep): ?>
  <div class="ycm-reponse<?= $rep['envoyee'] ? '' : ' echec' ?>">
    <p class="ycm-reponse-tete"><strong>Réponse de <?= e($rep['auteur'] ?: "l'équipe") ?></strong> · <?= e(date_longue($rep['envoyee_le']) . ', ' . date('H \h i', strtotime($rep['envoyee_le']))) ?><?php if (!$rep['envoyee']): ?> · <?= badge('Pas partie', 'rouge') ?><?php endif ?></p>
    <div class="ycm-reponse-texte"><?= e($rep['texte']) ?></div>
  </div>
  <?php endforeach ?>

  <?php if ($peut_modifier && !$dans_corbeille): ?>
  <form method="post" action="<?= url('/gestion/messages/' . $ouvert['id'] . '/repondre') ?>" class="ycm-repondre" id="repondre" data-confirmer="Envoyer cette réponse à <?= e($ouvert['email']) ?> ?" data-confirmer-oui="Envoyer">
    <?= csrf() ?>
    <label for="reponse">Répondre à <?= e($ouvert['nom']) ?></label>
    <textarea id="reponse" name="reponse" rows="8" required><?= e("Bonjour $prenom,\n\n\n\nBien cordialement,\nL'équipe YouthConnekt Mali") ?></textarea>
    <div class="ycm-lecture-actions"><button type="submit" class="ycm-bouton-vert">Envoyer la réponse</button></div>
  </form>
  <?php endif ?>
</article>

<?php elseif ($lignes): ?>
<form method="post" action="<?= url('/gestion/messages/action') ?>" class="ycm-boite-reception" data-liste>
  <?= csrf() ?><input type="hidden" name="suivant" value="<?= e(adresse_actuelle()) ?>">
  <?php if ($peut_modifier): ?>
  <div class="ycm-reception-barre">
    <label class="ycm-reception-tout"><input type="checkbox" data-tout-cocher aria-label="Tout sélectionner"></label>
    <?php if ($corbeille): ?>
      <button type="submit" name="action" value="restaurer" class="ycm-bouton-contour ycm-petit">Restaurer</button>
    <?php else: ?>
      <button type="submit" name="action" value="lus" class="ycm-bouton-contour ycm-petit">Marquer comme lus</button>
      <?php if ($peut_supprimer): ?><button type="submit" name="action" value="corbeille" class="ycm-bouton-contour ycm-petit">Supprimer</button><?php endif ?>
    <?php endif ?>
    <span class="ycm-selection" data-compte-selection>0 sur <?= count($lignes) ?> sélectionné</span>
  </div>
  <?php endif ?>
  <?php if ($corbeille): ?><p class="ycm-reception-note">Les messages restent 30 jours dans la corbeille, puis sont supprimés définitivement.</p><?php endif ?>
  <ul class="ycm-liste-messages">
    <?php foreach ($lignes as $m): $non_lu = empty($m['lu_le']); ?>
    <li class="<?= $non_lu ? 'non-lu' : '' ?>">
      <?php if ($peut_modifier): ?><input type="checkbox" name="ids[]" value="<?= (int) $m['id'] ?>" aria-label="Sélectionner le message de <?= e($m['nom']) ?>"><?php endif ?>
      <a href="<?= e(avec_parametres(['ouvert' => $m['id']])) ?>">
        <span class="de"><?= e($m['nom']) ?></span>
        <span class="sujet"><?= e($m['objet']) ?></span>
        <span class="etat"><?php if (($m['profil'] ?? '') === 'email'): ?><span class="ycm-pastille-email">E-mail</span><?php endif ?><?php if ($m['traite']): ?><span class="ycm-pastille-traite">Répondu</span><?php endif ?><time datetime="<?= e(date('c', strtotime($m['recu_le']))) ?>"><?= e($date_courte($m['recu_le'])) ?></time></span>
      </a>
    </li>
    <?php endforeach ?>
  </ul>
</form>
<?php partiel('admin/_pagination', get_defined_vars()) ?>
<?php else: ?>
<div class="ycm-boite ycm-boite--pad"><p class="ycm-vide"><?= $corbeille ? 'La corbeille est vide.' : 'Aucun message dans cette liste.' ?></p></div>
<?php endif ?>
