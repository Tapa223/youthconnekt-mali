<?php
/* Rédaction d'une newsletter : 1. message, 2. contenus du site à joindre, 3. destinataires, puis aperçu, test et envoi. */
$d = $modifiable ? '' : ' disabled';
$err = fn ($c) => !empty($erreurs[$c]) ? '<p class="ycm-erreur">' . e($erreurs[$c]) . '</p>' : '';
$envoyee = $n['statut'] !== 'brouillon';
?>
<div class="ycm-entete-page">
  <div>
    <h1><?= $n['id'] ? e($n['objet']) : 'Nouvelle newsletter' ?></h1>
    <p class="ycm-sous-titre">
      <?php if ($envoyee): ?><?= $n['statut'] === 'envoyee' ? 'Envoyée' : 'Envoi simulé' ?> le <?= e(date_longue($n['envoyee_le'])) ?> à <?= (int) $n['nb_destinataires'] ?> destinataire<?= pluriel((int) $n['nb_destinataires']) ?>. Un envoi parti ne se modifie plus : faites-en une copie pour le réutiliser.
      <?php else: ?>Écrivez le message, joignez des contenus du site si vous le souhaitez, choisissez les destinataires, puis enregistrez pour voir l'aperçu.<?php endif ?>
    </p>
  </div>
  <?php if ($envoyee && peut('newsletter', 'modifier')): ?>
  <form method="post" action="<?= url('/gestion/newsletters/' . $n['id'] . '/dupliquer') ?>" data-confirmer="Créer une copie de cette newsletter ?" data-confirmer-oui="Créer la copie"><?= csrf() ?><button type="submit" class="ycm-bouton-contour">Faire une copie</button></form>
  <?php endif ?>
</div>

<form method="post" enctype="multipart/form-data" novalidate class="ycm-form-grille" data-newsletter data-compter="<?= url('/gestion/newsletters/compter') ?>"
      data-confirmer="Enregistrer le brouillon ?" data-confirmer-oui="Enregistrer">
  <?= csrf() ?>
  <div class="ycm-form-principal">
    <fieldset class="ycm-module">
      <legend><h2>1. Le message</h2></legend>
      <div class="ycm-ligne-champ">
        <label for="n-objet" class="requis">Objet de l'e-mail</label>
        <input type="text" id="n-objet" name="objet" maxlength="200" value="<?= e($n['objet']) ?>" placeholder="Exemple : 5 opportunités à saisir avant la fin du mois"<?= $d ?><?= !empty($erreurs['objet']) ? ' aria-invalid="true"' : '' ?>>
        <?= $err('objet') ?>
      </div>
      <div class="ycm-ligne-champ">
        <label for="n-titre">Titre dans l'e-mail</label>
        <input type="text" id="n-titre" name="titre" maxlength="200" value="<?= e($n['titre']) ?>" placeholder="Vide : l'objet est repris"<?= $d ?>>
      </div>
      <div class="ycm-ligne-champ">
        <label for="n-message">Message</label>
        <textarea id="n-message" name="message" rows="9" placeholder="Bonjour à toutes et à tous,&#10;&#10;…"<?= $d ?><?= !empty($erreurs['message']) ? ' aria-invalid="true"' : '' ?>><?= e($n['message']) ?></textarea>
        <small>Une ligne vide sépare deux paragraphes. Commencez une ligne par « ## » pour un intertitre.</small>
        <?= $err('message') ?>
      </div>
      <div class="ycm-ligne-champ">
        <label for="n-image">Image d'en-tête (facultative)</label>
        <div class="ycm-zone-image">
          <?php if ($n['image']): ?><img src="<?= e(media($n['image'])) ?>" alt="" class="ycm-apercu"><?php endif ?>
          <?php if ($modifiable): ?>
          <input type="file" id="n-image" name="image" accept="image/jpeg,image/png,image/webp">
          <?php if ($n['image']): ?><label class="ycm-case-ligne"><input type="checkbox" name="supprimer_image" value="1"> Retirer l'image</label><?php endif ?>
          <small>JPG, PNG ou WebP, 3 Mo maximum.</small>
          <?php endif ?>
        </div>
        <?= $err('image') ?>
      </div>
    </fieldset>

    <fieldset class="ycm-module">
      <legend><h2>2. Contenus du site à joindre</h2></legend>
      <p class="ycm-description">Chaque contenu coché s'ajoute en carte, avec un bouton vers sa page sur le site.</p>
      <div class="ycm-nl-choix">
        <div>
          <h3>Opportunités ouvertes</h3>
          <?php if ($opportunites): ?>
          <ul>
            <?php foreach ($opportunites as $o): ?>
            <li><label class="ycm-case-ligne"><input type="checkbox" name="opportunites[]" value="<?= (int) $o['id'] ?>"<?= in_array((int) $o['id'], $n['opportunites_ids'], true) ? ' checked' : '' ?><?= $d ?>>
              <span><?= e($o['titre']) ?><small><?= $o['date_limite'] ? 'Date limite : ' . e(date_chiffres($o['date_limite'])) : 'Sans date limite' ?></small></span></label></li>
            <?php endforeach ?>
          </ul>
          <?php else: ?><p class="ycm-vide">Aucune opportunité ouverte.</p><?php endif ?>
        </div>
        <div>
          <h3>Actualités et événements</h3>
          <?php if ($actualites): ?>
          <ul>
            <?php foreach ($actualites as $a): ?>
            <li><label class="ycm-case-ligne"><input type="checkbox" name="actualites[]" value="<?= (int) $a['id'] ?>"<?= in_array((int) $a['id'], $n['actualites_ids'], true) ? ' checked' : '' ?><?= $d ?>>
              <span><?= e($a['titre']) ?><small><?= $a['categorie'] === 'evenement' ? 'Événement' . ($a['date_evenement'] ? ' · ' . e(date_chiffres($a['date_evenement'])) : '') : 'Actualité' ?></small></span></label></li>
            <?php endforeach ?>
          </ul>
          <?php else: ?><p class="ycm-vide">Aucune actualité publiée.</p><?php endif ?>
        </div>
      </div>
    </fieldset>

    <fieldset class="ycm-module">
      <legend><h2>3. Destinataires</h2></legend>
      <div class="ycm-nl-cibles" role="radiogroup" aria-label="Destinataires">
        <?php foreach (CIBLES_NEWSLETTER as $cle => $libelle): ?>
        <label class="ycm-nl-cible"><input type="radio" name="cible" value="<?= $cle ?>"<?= $n['cible'] === $cle ? ' checked' : '' ?><?= $d ?> data-cible> <span><?= e($libelle) ?></span></label>
        <?php endforeach ?>
      </div>
      <div class="ycm-nl-communautes" data-communautes<?= $n['cible'] === 'communautes' ? '' : ' hidden' ?>>
        <?php foreach ($communautes as $c): ?>
        <label class="ycm-case-ligne"><input type="checkbox" name="communautes[]" value="<?= (int) $c['id'] ?>"<?= in_array((int) $c['id'], $n['communautes_ids'], true) ? ' checked' : '' ?><?= $d ?> data-communaute> <?= e($c['nom']) ?> <small class="ycm-discret">(<?= (int) $c['nb'] ?> membre<?= pluriel((int) $c['nb']) ?>)</small></label>
        <?php endforeach ?>
        <?= $err('communautes') ?>
      </div>
      <p class="ycm-nl-compte"><strong data-nombre><?= $nb_destinataires ?></strong> destinataire<span data-pluriel><?= pluriel($nb_destinataires) ?></span>
        <small>Seules les personnes abonnées à la newsletter la reçoivent : choisir une communauté ou un type de compte réduit la liste, sans jamais écrire à quelqu'un qui ne l'a pas acceptée.</small></p>
    </fieldset>
  </div>

  <aside class="ycm-form-cote">
    <?php if ($modifiable): ?>
    <div class="ycm-panneau-boutons">
      <button type="submit" class="ycm-bouton-vert">Enregistrer le brouillon</button>
      <a href="<?= url('/gestion/newsletters') ?>" class="ycm-bouton-gris">Annuler</a>
      <?php if ($n['id']): ?><button type="submit" form="n-supprimer" class="ycm-lien-supprimer">Supprimer ce brouillon</button><?php endif ?>
    </div>
    <?php endif ?>
    <p class="ycm-encadre-info"><?= $mode === 'brevo' ? 'Les e-mails partent par Brevo. Chaque destinataire reçoit son propre lien de désabonnement.' : "Brevo n'est pas encore branché : le test et l'envoi sont simulés et écrits dans stockage/emails.log. L'aperçu, lui, montre déjà l'e-mail réel." ?></p>
  </aside>
</form>

<?php if ($n['id']): ?>
<section id="envoi" class="ycm-nl-envoi">
  <div class="ycm-boite ycm-boite--pad">
    <div class="ycm-boite-tete ycm-boite-tete--nu"><h2>Aperçu de l'e-mail</h2><a class="ycm-lien" href="<?= url('/gestion/newsletters/' . $n['id'] . '/apercu') ?>" target="_blank" rel="noopener">Ouvrir en grand</a></div>
    <iframe src="<?= url('/gestion/newsletters/' . $n['id'] . '/apercu') ?>" title="Aperçu de la newsletter" class="ycm-nl-apercu" loading="lazy"></iframe>
    <?php if ($modifiable): ?><p class="ycm-discret">L'aperçu montre la dernière version enregistrée.</p><?php endif ?>
  </div>
  <?php if ($modifiable): ?>
  <div class="ycm-boite ycm-boite--pad ycm-nl-actions">
    <h2>Tester puis envoyer</h2>
    <form method="post" action="<?= url('/gestion/newsletters/' . $n['id'] . '/tester') ?>" class="ycm-nl-test" data-confirmer="Envoyer un exemplaire de test ?" data-confirmer-texte="Il part seulement à l'adresse indiquée." data-confirmer-oui="Envoyer le test">
      <?= csrf() ?>
      <label for="n-test">Recevoir un test à l'adresse</label>
      <div><input type="email" id="n-test" name="email_test" value="<?= e($email_test) ?>" required><button type="submit" class="ycm-bouton-contour">Envoyer le test</button></div>
    </form>
    <form method="post" action="<?= url('/gestion/newsletters/' . $n['id'] . '/envoyer') ?>"
          data-confirmer="Envoyer la newsletter à <?= $nb_destinataires ?> destinataire<?= pluriel($nb_destinataires) ?> ?"
          data-confirmer-texte="<?= e(CIBLES_NEWSLETTER[$n['cible']] ?? '') ?>. Une fois partie, elle ne peut plus être modifiée ni rappelée." data-confirmer-oui="Envoyer maintenant">
      <?= csrf() ?>
      <button type="submit" class="ycm-bouton-orange ycm-nl-bouton-envoi"<?= $nb_destinataires ? '' : ' disabled' ?>>Envoyer à <?= $nb_destinataires ?> destinataire<?= pluriel($nb_destinataires) ?></button>
      <?php if (!$nb_destinataires): ?><p class="ycm-erreur">Aucun destinataire pour cette cible.</p><?php endif ?>
    </form>
  </div>
  <?php endif ?>
</section>
<?php if ($modifiable): ?>
<form id="n-supprimer" method="post" action="<?= url('/gestion/newsletters/' . $n['id'] . '/supprimer') ?>" hidden data-confirmer="Supprimer ce brouillon ?" data-confirmer-texte="Cette action est irréversible." data-confirmer-oui="Supprimer" data-confirmer-danger><?= csrf() ?></form>
<?php endif ?>
<?php endif ?>
