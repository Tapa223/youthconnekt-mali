<?php
/* Participations d'une initiative : onglet « Candidatures » (décisions) et onglet « Formulaire » (rôles, questions, ouverture). */
$ici = url('/gestion/initiatives/' . $i['id'] . '/participations');
$total = array_sum(array_map('intval', $compteurs));
$err = fn ($c) => !empty($erreurs[$c]) ? '<p class="ycm-erreur">' . e($erreurs[$c]) . '</p>' : '';
$d = $peut_modifier ? '' : ' disabled';
$ouvertes = participations_ouvertes($i, array_map(fn ($r) => $r + ['complet' => false], $roles));
?>
<div class="ycm-entete-page">
  <div>
    <h1>Participations</h1>
    <p class="ycm-sous-titre"><?= e($i['titre']) ?> ·
      <?= $i['participations_ouvertes'] ? ($ouvertes ? badge('Ouvertes sur le site', 'vert') : badge('Fermées (date passée, initiative terminée ou places pleines)', 'gris')) : badge('Fermées', 'gris') ?></p>
  </div>
  <a href="<?= url('/initiatives/' . $i['slug']) ?>" class="ycm-bouton-contour" target="_blank" rel="noopener">Voir la page de l'initiative</a>
</div>

<nav class="ycm-onglets" aria-label="Participations">
  <a href="<?= e($ici) ?>"<?= $onglet === 'candidatures' ? ' class="actif" aria-current="page"' : '' ?>>Candidatures <span>(<?= $total ?>)</span></a>
  <a href="<?= e($ici) ?>?vue=formulaire"<?= $onglet === 'formulaire' ? ' class="actif" aria-current="page"' : '' ?>>Rôles et formulaire</a>
</nav>

<?php if ($onglet === 'candidatures'): ?>
<div class="ycm-pa-outils">
  <div class="ycm-pa-filtres">
    <?php foreach (['' => 'Toutes', 'en_attente' => 'En attente', 'retenue' => 'Retenues', 'non_retenue' => 'Non retenues'] as $cle => $lib): $n = $cle === '' ? $total : (int) ($compteurs[$cle] ?? 0); ?>
    <a href="<?= e($ici . ($cle || $role_filtre ? '?' . http_build_query(array_filter(['statut' => $cle, 'role' => $role_filtre])) : '')) ?>" class="ycm-pa-pastille<?= $statut === $cle ? ' actif' : '' ?>"><?= e($lib) ?> <span>(<?= $n ?>)</span></a>
    <?php endforeach ?>
  </div>
  <div class="ycm-pa-droite">
    <?php if (count($roles) > 1): ?>
    <select data-filtre aria-label="Filtrer par rôle">
      <option value="<?= e($ici . ($statut ? '?statut=' . $statut : '')) ?>">Tous les rôles</option>
      <?php foreach ($roles as $r): ?><option value="<?= e($ici . '?' . http_build_query(array_filter(['statut' => $statut, 'role' => $r['id']]))) ?>"<?= $role_filtre === (int) $r['id'] ? ' selected' : '' ?>><?= e($r['nom']) ?> (<?= (int) $r['nb'] ?>)</option><?php endforeach ?>
    </select>
    <?php endif ?>
    <?php if ($total): ?><a class="ycm-bouton-contour ycm-petit" href="<?= url('/gestion/initiatives/' . $i['id'] . '/participations/export') ?>">Exporter en tableau</a><?php endif ?>
  </div>
</div>

<?php if ($roles): ?>
<ul class="ycm-pa-roles">
  <?php foreach ($roles as $r): ?>
  <li><strong><?= e($r['nom']) ?></strong><span><?= (int) $r['nb'] ?><?= $r['places'] !== null ? ' / ' . (int) $r['places'] . ' places' : ' candidature' . pluriel((int) $r['nb']) ?></span></li>
  <?php endforeach ?>
</ul>
<?php endif ?>

<?php if ($candidatures): ?>
<div class="ycm-pa-liste">
  <?php foreach ($candidatures as $c): $rep = reponses_participation($c); $a = age($c['date_naissance'] ?? null); ?>
  <article class="ycm-pa-carte">
    <div class="ycm-pa-tete">
      <div>
        <p class="ycm-pa-nom"><?= e($c['membre_nom']) ?></p>
        <p class="ycm-discret"><?= e(implode(' · ', array_filter([$c['email'], $c['telephone'] ?? '', GENRES[$c['genre'] ?? ''] ?? '', $a !== null ? $a . ' ans' : '', REGIONS[$c['region_i'] ?? ''] ?? '']))) ?></p>
      </div>
      <div class="ycm-pa-etat">
        <span class="ycm-pa-role"><?= e($c['role_libelle']) ?></span>
        <?= ['en_attente' => badge('En attente', 'orange'), 'retenue' => badge('Retenue', 'vert'), 'non_retenue' => badge('Non retenue', 'gris')][$c['statut']] ?? '' ?>
      </div>
    </div>
    <?php if ($rep): ?>
    <dl class="ycm-pa-reponses">
      <?php foreach ($rep as $x): ?><div><dt><?= e($x['question']) ?></dt><dd><?= $x['reponse'] !== '' ? nl2br(e($x['reponse'])) : '<span class="ycm-discret">Pas de réponse</span>' ?></dd></div><?php endforeach ?>
    </dl>
    <?php endif ?>
    <div class="ycm-pa-pied">
      <span class="ycm-discret">Reçue le <?= e(date_longue($c['cree_le'])) ?></span>
      <?php if ($peut_modifier): ?>
      <span class="ycm-pa-decisions">
        <?php foreach (['retenue' => ['Retenir', 'ycm-bouton-vert', 'Retenir ' . $c['membre_nom'] . ' comme « ' . $c['role_libelle'] . ' » ?', ''], 'non_retenue' => ['Ne pas retenir', 'ycm-bouton-rouge-contour', 'Ne pas retenir ' . $c['membre_nom'] . ' ?', ' data-confirmer-danger'], 'en_attente' => ['Remettre en attente', 'ycm-bouton-gris', 'Remettre cette candidature en attente ?', '']] as $s => [$lib, $classe, $question, $danger]):
            if ($s === $c['statut'] || ($s === 'en_attente' && $c['statut'] === 'en_attente')) continue; ?>
        <form method="post" action="<?= url('/gestion/participations/' . $c['id'] . '/decision') ?>" data-confirmer="<?= e($question) ?>" data-confirmer-texte="La personne en est prévenue dans son espace." data-confirmer-oui="<?= e($lib) ?>"<?= $danger ?>>
          <?= csrf() ?><input type="hidden" name="statut" value="<?= $s ?>"><input type="hidden" name="suivant" value="<?= e(adresse_actuelle()) ?>">
          <button type="submit" class="<?= $classe ?> ycm-petit"><?= $lib ?></button>
        </form>
        <?php endforeach ?>
      </span>
      <?php endif ?>
    </div>
  </article>
  <?php endforeach ?>
</div>
<?php else: ?>
<div class="ycm-boite ycm-boite--pad"><p class="ycm-vide"><?= $total ? 'Aucune candidature ne correspond à ce filtre.' : ($i['participations_ouvertes'] ? "Aucune candidature pour l'instant. Le formulaire est visible sur la page de l'initiative." : "Aucune candidature : les participations ne sont pas encore ouvertes. Préparez-les dans l'onglet « Rôles et formulaire ».") ?></p></div>
<?php endif ?>

<?php else: ?>
<form method="post" class="ycm-form-grille" data-confirmer="Enregistrer les participations ?" data-confirmer-texte="Si elles sont ouvertes, le formulaire apparaît aussitôt sur la page de l'initiative." data-confirmer-oui="Enregistrer">
  <?= csrf() ?>
  <div class="ycm-form-principal">
    <fieldset class="ycm-module">
      <legend><h2>1. Les rôles proposés</h2></legend>
      <p class="ycm-description">Comment les jeunes peuvent-ils prendre part ? Par exemple : Bénévole accueil, Bénévole communication, Intervenant, Participant. Laissez « Places » vide s'il n'y a pas de limite.</p>
      <?= $err('roles') ?>
      <div class="ycm-pa-lignes" data-lignes="roles">
        <?php foreach ($roles ?: [['id' => 0, 'nom' => '', 'description' => '', 'places' => null]] as $k => $r): $cle = 'r' . $k; ?>
        <div class="ycm-pa-ligne" data-ligne>
          <input type="hidden" name="role_id[<?= $cle ?>]" value="<?= (int) $r['id'] ?>">
          <div class="ycm-pa-champs-role">
            <label>Rôle<input type="text" name="role_nom[<?= $cle ?>]" value="<?= e($r['nom']) ?>" maxlength="120" placeholder="Exemple : Bénévole accueil"<?= $d ?>></label>
            <label>Places<input type="number" min="1" name="role_places[<?= $cle ?>]" value="<?= e((string) ($r['places'] ?? '')) ?>" placeholder="Sans limite"<?= $d ?>></label>
            <label class="ycm-pa-large">Ce que fera la personne (facultatif)<input type="text" name="role_description[<?= $cle ?>]" value="<?= e($r['description']) ?>" maxlength="300" placeholder="Exemple : accueillir et orienter les participants"<?= $d ?>></label>
          </div>
          <?php if ($peut_modifier): ?><div class="ycm-pa-boutons-ligne"><button type="button" data-monter aria-label="Monter">↑</button><button type="button" data-descendre aria-label="Descendre">↓</button><button type="button" data-retirer class="ycm-pa-retirer">Retirer</button></div><?php endif ?>
        </div>
        <?php endforeach ?>
      </div>
      <?php if ($peut_modifier): ?><button type="button" class="ycm-bouton-contour ycm-petit" data-ajouter="roles">Ajouter un rôle</button><?php endif ?>
    </fieldset>

    <fieldset class="ycm-module">
      <legend><h2>2. Les questions à poser</h2></legend>
      <p class="ycm-description">Le nom, l'e-mail, le téléphone, le genre, l'âge et la région viennent déjà du compte de la personne : inutile de les redemander. Ajoutez seulement ce dont vous avez besoin (disponibilités, compétences, langues, taille de t-shirt…).</p>
      <?= $err('champs') ?>
      <div class="ycm-pa-lignes" data-lignes="champs">
        <?php foreach ($champs as $k => $c): $cle = 'c' . $k; ?>
        <?php partiel('admin/_ligne_champ', ['cle' => $cle, 'c' => $c, 'd' => $d, 'peut_modifier' => $peut_modifier]) ?>
        <?php endforeach ?>
      </div>
      <?php if (!$champs): ?><p class="ycm-discret" data-aucune-question>Aucune question pour l'instant : la personne choisit seulement son rôle.</p><?php endif ?>
      <?php if ($peut_modifier): ?><button type="button" class="ycm-bouton-contour ycm-petit" data-ajouter="champs">Ajouter une question</button><?php endif ?>
    </fieldset>

    <fieldset class="ycm-module">
      <legend><h2>3. Message d'accueil (facultatif)</h2></legend>
      <div class="ycm-ligne-champ">
        <label for="pa-intro">Affiché au-dessus du formulaire</label>
        <textarea id="pa-intro" name="participations_intro" rows="3" maxlength="1000" placeholder="Exemple : nous recherchons des bénévoles motivés pour l'accueil et la logistique du forum."<?= $d ?>><?= e((string) ($i['participations_intro'] ?? '')) ?></textarea>
      </div>
    </fieldset>
  </div>

  <aside class="ycm-form-cote">
    <div class="ycm-module">
      <h2>Ouverture</h2>
      <label class="ycm-case-ligne" style="margin-top:8px"><input type="checkbox" name="participations_ouvertes" value="1"<?= $i['participations_ouvertes'] ? ' checked' : '' ?><?= $d ?>> Ouvrir les participations sur le site</label>
      <div class="ycm-ligne-champ" style="margin-top:14px">
        <label for="pa-fin">Date limite (facultative)</label>
        <input type="date" id="pa-fin" name="participations_fin" value="<?= e((string) ($i['participations_fin'] ?? '')) ?>"<?= $d ?>>
        <small>Après cette date, le formulaire se ferme tout seul. Il se ferme aussi quand l'initiative passe à « Terminée » ou que toutes les places sont prises.</small>
        <?= $err('participations_fin') ?>
      </div>
    </div>
    <?php if ($peut_modifier): ?>
    <div class="ycm-panneau-boutons">
      <button type="submit" class="ycm-bouton-vert">Enregistrer</button>
      <a href="<?= e($ici) ?>" class="ycm-bouton-gris">Annuler</a>
    </div>
    <?php endif ?>
  </aside>
</form>

<template data-modele="roles">
  <div class="ycm-pa-ligne" data-ligne>
    <input type="hidden" name="role_id[__cle__]" value="0">
    <div class="ycm-pa-champs-role">
      <label>Rôle<input type="text" name="role_nom[__cle__]" maxlength="120" placeholder="Exemple : Bénévole accueil"></label>
      <label>Places<input type="number" min="1" name="role_places[__cle__]" placeholder="Sans limite"></label>
      <label class="ycm-pa-large">Ce que fera la personne (facultatif)<input type="text" name="role_description[__cle__]" maxlength="300" placeholder="Exemple : accueillir et orienter les participants"></label>
    </div>
    <div class="ycm-pa-boutons-ligne"><button type="button" data-monter aria-label="Monter">↑</button><button type="button" data-descendre aria-label="Descendre">↓</button><button type="button" data-retirer class="ycm-pa-retirer">Retirer</button></div>
  </div>
</template>
<template data-modele="champs"><?php partiel('admin/_ligne_champ', ['cle' => '__cle__', 'c' => ['id' => 0, 'libelle' => '', 'type' => 'texte', 'options' => '', 'aide' => '', 'requis' => 0], 'd' => '', 'peut_modifier' => true]) ?></template>
<?php endif ?>
