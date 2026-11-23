<?php
/*
 * Messages de contact, comme une messagerie : une ligne par message (expéditeur, objet, début du texte, date).
 * Le message s'ouvre au clic, avec la réponse à écrire juste dessous. Sur ordinateur, la liste reste à gauche.
 */
partiel('admin/_entete_liste', get_defined_vars());
$profil = fn ($m) => $m['profil'] === 'email' ? 'Reçu par e-mail' : (PROFILS_CONTACT[$m['profil']] ?? '') . (!empty($m['profil_autre']) ? ' : ' . $m['profil_autre'] : '');
$date_courte = function (string $quand): string {
    $t = strtotime($quand);
    return date('Y-m-d', $t) === date('Y-m-d') ? date('H:i', $t) : (date('Y', $t) === date('Y') ? jour_mois($quand) : date('d/m/Y', $t));
};
$prenom = $ouvert ? trim(explode(' ', trim($ouvert['nom']))[0]) : '';
?>
<p class="ycm-webmail">Les messages du site et les e-mails écrits directement à l'adresse de contact arrivent ici. Les pièces jointes s'ouvrent dans la <a href="<?= e($webmail) ?>" target="_blank" rel="noopener">boîte contact</a>.</p>
<?php if ($lignes): ?>
<div class="ycm-boite-reception<?= $ouvert ? ' avec-lecture' : '' ?>">
  <ul class="ycm-liste-messages">
    <?php foreach ($lignes as $m): $non_lu = empty($m['lu_le']); ?>
    <li>
      <a href="<?= e(avec_parametres(['ouvert' => $m['id']])) ?>" class="<?= $non_lu ? 'non-lu' : '' ?><?= $ouvert && (int) $ouvert['id'] === (int) $m['id'] ? ' ouvert' : '' ?>"<?= $ouvert && (int) $ouvert['id'] === (int) $m['id'] ? ' aria-current="true"' : '' ?>>
        <span class="de"><?= e($m['nom']) ?></span>
        <span class="sujet"><strong><?= e($m['objet']) ?></strong><span class="extrait"> · <?= e(couper(preg_replace('/\s+/', ' ', $m['message']), 140)) ?></span></span>
        <span class="etat"><?php if (($m['profil'] ?? '') === 'email'): ?><span class="ycm-pastille-email" title="Reçu par e-mail">E-mail</span><?php endif ?><?php if ($m['traite']): ?><span class="ycm-pastille-traite" title="Traité">Traité</span><?php endif ?><time datetime="<?= e(date('c', strtotime($m['recu_le']))) ?>"><?= e($date_courte($m['recu_le'])) ?></time></span>
      </a>
    </li>
    <?php endforeach ?>
  </ul>
  <?php if ($ouvert): ?>
  <article class="ycm-lecture">
    <a href="<?= e(avec_parametres(['ouvert' => null])) ?>" class="ycm-retour-liste">&larr; Tous les messages</a>
    <div class="ycm-lecture-tete">
      <h2><?= e($ouvert['objet']) ?></h2>
      <?= $ouvert['traite'] ? badge('Traité', 'vert') : badge('À traiter', 'orange') ?>
    </div>
    <p class="ycm-lecture-meta">
      <strong><?= e($ouvert['nom']) ?></strong> · <a href="mailto:<?= e($ouvert['email']) ?>"><?= e($ouvert['email']) ?></a><br>
      <?= e(implode(' · ', array_filter([$profil($ouvert), date_longue($ouvert['recu_le']) . ', ' . date('H \h i', strtotime($ouvert['recu_le']))]))) ?>
    </p>
    <div class="ycm-lecture-texte"><?= e($ouvert['message']) ?></div>

    <?php foreach ($reponses as $rep): ?>
    <div class="ycm-reponse<?= $rep['envoyee'] ? '' : ' echec' ?>">
      <p class="ycm-reponse-tete"><strong>Réponse de <?= e($rep['auteur'] ?: "l'équipe") ?></strong> · <?= e(date_longue($rep['envoyee_le']) . ', ' . date('H \h i', strtotime($rep['envoyee_le']))) ?> · <?= $rep['envoyee'] ? badge('Envoyée', 'vert') : badge('Pas partie', 'rouge') ?></p>
      <div class="ycm-reponse-texte"><?= e($rep['texte']) ?></div>
    </div>
    <?php endforeach ?>

    <?php if ($peut_modifier): ?>
    <form method="post" action="<?= url('/gestion/messages/' . $ouvert['id'] . '/repondre') ?>" class="ycm-repondre" id="repondre" data-confirmer="Envoyer cette réponse à <?= e($ouvert['email']) ?> ?" data-confirmer-oui="Envoyer">
      <?= csrf() ?>
      <label for="reponse"><?= $reponses ? 'Répondre de nouveau' : 'Répondre' ?> à <?= e($ouvert['nom']) ?></label>
      <textarea id="reponse" name="reponse" rows="8" required><?= e("Bonjour $prenom,\n\n\n\nBien cordialement,\nL'équipe YouthConnekt Mali") ?></textarea>
      <p class="ycm-discret">La réponse part avec le logo et les coordonnées du chapitre, et le message d'origine en dessous. Si la personne répond, sa réponse arrive dans la boîte contact.</p>
      <div class="ycm-lecture-actions">
        <button type="submit" class="ycm-bouton-vert">Envoyer la réponse</button>
      </div>
    </form>
    <form method="post" action="<?= url('/gestion/messages/' . $ouvert['id'] . '/traiter') ?>" class="ycm-traiter">
      <?= csrf() ?><input type="hidden" name="suivant" value="<?= e(adresse_actuelle()) ?>"><input type="hidden" name="traite" value="<?= $ouvert['traite'] ? '0' : '1' ?>">
      <button type="submit" class="ycm-bouton-contour"><?= $ouvert['traite'] ? 'Remettre à traiter' : 'Marquer comme traité sans répondre' ?></button>
    </form>
    <?php endif ?>
  </article>
  <?php endif ?>
</div>
<?php partiel('admin/_pagination', get_defined_vars()) ?>
<?php else: ?>
<div class="ycm-boite ycm-boite--pad"><p class="ycm-vide">Aucun message dans cette liste.</p></div>
<?php endif ?>
