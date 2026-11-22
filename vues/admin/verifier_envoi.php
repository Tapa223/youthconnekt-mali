<?php
/* Vérifier l'envoi des e-mails : réglages, compte Brevo, test et parcours du test chez Brevo. */
$evenements_fr = [
    'requests' => ['Accepté par Brevo', 'gris'], 'request' => ['Accepté par Brevo', 'gris'],
    'delivered' => ['Délivré dans la boîte', 'vert'], 'opened' => ['Ouvert', 'vert'], 'clicks' => ['Lien cliqué', 'vert'], 'click' => ['Lien cliqué', 'vert'],
    'deferred' => ['Retardé (nouvel essai en cours)', 'orange'], 'softBounces' => ['Rejet temporaire', 'orange'], 'softBounce' => ['Rejet temporaire', 'orange'],
    'hardBounces' => ['Rejeté par la messagerie', 'rouge'], 'hardBounce' => ['Rejeté par la messagerie', 'rouge'],
    'blocked' => ['Bloqué par Brevo', 'rouge'], 'invalid' => ['Adresse invalide', 'rouge'], 'error' => ['Erreur', 'rouge'],
    'spam' => ['Signalé comme spam', 'rouge'], 'unsubscribed' => ['Désinscrit', 'gris'],
];
$expediteur_actif = null;
foreach ((array) $expediteurs as $s) {
    if (mb_strtolower($s['email'] ?? '') === mb_strtolower($expediteur)) {
        $expediteur_actif = !empty($s['active']);
    }
}
$plan = $compte['plan'][0] ?? null;
?>
<div class="ycm-entete-page">
  <div>
    <h1>Vérifier l'envoi des e-mails</h1>
    <p class="ycm-sous-titre">Envoyez-vous un e-mail de test et suivez ce qu'il devient chez Brevo, sans quitter l'administration.</p>
  </div>
</div>
<nav class="ycm-onglets" aria-label="Newsletter">
  <a href="<?= url('/gestion/newsletters') ?>">Envois</a>
  <a href="<?= url('/gestion/newsletter') ?>">Abonnés</a>
  <a href="<?= url('/gestion/newsletters/verifier-envoi') ?>" class="actif" aria-current="page">Vérifier l'envoi</a>
</nav>

<div class="ycm-form-grille" style="margin-top:16px">
  <div class="ycm-form-principal">
    <section class="ycm-boite ycm-boite--pad">
      <h2>1. Envoyer un e-mail de test</h2>
      <form method="post" class="ycm-nl-test" style="margin-top:10px">
        <?= csrf() ?>
        <label for="test-adresse">Adresse qui doit le recevoir</label>
        <div><input type="email" id="test-adresse" name="email_test" value="<?= e($test['adresse'] ?? '') ?>" required placeholder="vous@exemple.com"><button type="submit" class="ycm-bouton-vert">Envoyer le test</button></div>
      </form>
      <p class="ycm-discret" style="margin-top:8px">De préférence une adresse Gmail ou Yahoo personnelle : on voit tout de suite si l'e-mail arrive en boîte de réception ou en spam.</p>
    </section>

    <?php if ($test): ?>
    <section class="ycm-boite ycm-boite--pad" style="margin-top:16px">
      <h2>2. Ce qu'est devenu le dernier test</h2>
      <p style="margin-top:6px">Envoyé à <strong><?= e($test['adresse']) ?></strong> le <?= e(date_chiffres($test['heure'])) ?> à <?= e(substr($test['heure'], 11, 5)) ?>.</p>
      <?php if (!$test['ok']): ?>
        <p class="ycm-erreur" style="margin-top:10px"><strong>Pas parti.</strong> <?= e($test['erreur']) ?></p>
      <?php elseif ($mode !== 'brevo'): ?>
        <p class="ycm-encadre-info" style="margin-top:10px">Le site n'est pas en mode Brevo (mode actuel : « <?= e($mode) ?> »). En mode « journal », rien ne part : il faut mettre 'mode' => 'brevo' dans config.php.</p>
      <?php elseif ($evenements === []): ?>
        <p class="ycm-encadre-info" style="margin-top:10px">Brevo a accepté l'e-mail mais n'a pas encore donné de suivi. Attendez une minute puis <a href="<?= url('/gestion/newsletters/verifier-envoi') ?>">actualisez cette page</a>.</p>
      <?php else: ?>
        <ul style="margin-top:12px;display:grid;gap:8px">
          <?php foreach (array_reverse((array) $evenements) as $ev): [$lib, $couleur] = $evenements_fr[$ev['event'] ?? ''] ?? [$ev['event'] ?? '?', 'gris']; ?>
          <li style="display:flex;flex-wrap:wrap;gap:8px;align-items:baseline">
            <?= badge($lib, $couleur) ?>
            <span class="ycm-discret"><?= e(isset($ev['date']) ? date('d/m/Y H:i', strtotime($ev['date'])) : '') ?></span>
            <?php if (!empty($ev['reason'])): ?><span style="flex-basis:100%;font-size:13px">Raison donnée : <?= e($ev['reason']) ?></span><?php endif ?>
          </li>
          <?php endforeach ?>
        </ul>
        <?php $types = array_column((array) $evenements, 'event'); ?>
        <?php if (array_intersect($types, ['delivered', 'opened'])): ?>
          <p class="ycm-encadre-info" style="margin-top:12px">L'e-mail est <strong>arrivé</strong> sur la messagerie du destinataire. S'il n'est pas dans la boîte de réception, il est dans <strong>Spam</strong>, <strong>Promotions</strong> ou <strong>Notifications</strong> : marquez-le « Non spam ».</p>
        <?php elseif (array_intersect($types, ['blocked'])): ?>
          <p class="ycm-erreur" style="margin-top:12px">Brevo a <strong>bloqué</strong> l'envoi. Le plus souvent : l'adresse figure dans la liste des contacts bloqués de Brevo (Transactionnel, Contacts bloqués), ou le compte Brevo attend encore sa validation. Lisez la raison ci-dessus.</p>
        <?php elseif (array_intersect($types, ['hardBounces', 'hardBounce', 'invalid'])): ?>
          <p class="ycm-erreur" style="margin-top:12px">La messagerie du destinataire a <strong>refusé</strong> l'e-mail. Vérifiez l'adresse, ou essayez avec une autre (Gmail par exemple).</p>
        <?php elseif (array_intersect($types, ['deferred', 'softBounces', 'softBounce'])): ?>
          <p class="ycm-encadre-info" style="margin-top:12px">La messagerie du destinataire fait <strong>patienter</strong> l'e-mail. Brevo réessaie tout seul : actualisez dans quelques minutes.</p>
        <?php else: ?>
          <p class="ycm-encadre-info" style="margin-top:12px">Brevo traite l'e-mail. Actualisez cette page dans une minute pour voir s'il est délivré.</p>
        <?php endif ?>
      <?php endif ?>
      <p style="margin-top:12px"><a href="<?= url('/gestion/newsletters/verifier-envoi') ?>" class="ycm-bouton-contour">Actualiser le suivi</a></p>
    </section>
    <?php endif ?>
  </div>

  <aside class="ycm-form-cote">
    <section class="ycm-boite ycm-boite--pad">
      <h2>Réglages du site</h2>
      <dl style="display:grid;gap:8px;margin-top:10px;font-size:14px">
        <div><dt class="ycm-discret">Mode d'envoi</dt><dd style="margin:0"><?= $mode === 'brevo' ? badge('Brevo', 'vert') : badge($mode === 'journal' ? 'Journal : rien ne part' : $mode, 'orange') ?></dd></div>
        <div><dt class="ycm-discret">Expéditeur</dt><dd style="margin:0"><?= e($expediteur ?: '—') ?>
          <?php if ($mode === 'brevo' && $expediteur_actif === true): ?><?= badge('Validé dans Brevo', 'vert') ?>
          <?php elseif ($mode === 'brevo' && $expediteur_actif === false): ?><?= badge('Pas encore validé dans Brevo', 'rouge') ?>
          <?php elseif ($mode === 'brevo' && $code_compte === 200): ?><?= badge('Absent des expéditeurs Brevo', 'rouge') ?><?php endif ?></dd></div>
        <div><dt class="ycm-discret">Clé Brevo</dt><dd style="margin:0"><?= $cle !== '' ? e($cle) : badge('Absente', 'rouge') ?></dd></div>
      </dl>
    </section>
    <?php if ($mode === 'brevo'): ?>
    <section class="ycm-boite ycm-boite--pad" style="margin-top:16px">
      <h2>Compte Brevo</h2>
      <?php if ($code_compte === 200): ?>
      <dl style="display:grid;gap:8px;margin-top:10px;font-size:14px">
        <div><dt class="ycm-discret">Compte</dt><dd style="margin:0"><?= e(($compte['companyName'] ?? '') ?: ($compte['email'] ?? '')) ?></dd></div>
        <?php if ($plan): ?><div><dt class="ycm-discret">Formule</dt><dd style="margin:0"><?= e(ucfirst((string) ($plan['type'] ?? ''))) ?><?= isset($plan['credits']) ? ' · ' . (int) $plan['credits'] . ' e-mail' . pluriel((int) $plan['credits']) . ' restant' . pluriel((int) $plan['credits']) . ($plan['type'] === 'free' ? " aujourd'hui" : '') : '' ?></dd></div><?php endif ?>
      </dl>
      <p class="ycm-discret" style="margin-top:10px">La clé fonctionne : le site arrive bien à parler à Brevo.</p>
      <?php else: ?>
      <p class="ycm-erreur" style="margin-top:10px"><?= $code_compte === 0 ? "Le serveur du site n'arrive pas à joindre Brevo." : 'Brevo refuse la connexion (' . (int) $code_compte . ') : ' . e((string) ($compte['message'] ?? '')) ?>
        <?php if (preg_match('/IP/i', (string) ($compte['message'] ?? ''))): ?><br>Autorisez l'adresse IP du serveur dans Brevo : Paramètres, Sécurité, IP autorisées.<?php elseif ($code_compte === 401): ?><br>La clé est incorrecte : générez-en une nouvelle et remplacez brevo_cle dans config.php.<?php endif ?></p>
      <?php endif ?>
    </section>
    <?php endif ?>
  </aside>
</div>
