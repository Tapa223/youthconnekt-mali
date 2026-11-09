<?php
/* Participations d'une initiative : onglet « Inscrits et candidatures » (tri, sélection, pointage) et onglet « Rôles et questions ». */
$ici = url('/gestion/initiatives/' . $i['id'] . '/participations');
$err = fn ($c) => !empty($erreurs[$c]) ? '<p class="ycm-erreur">' . e($erreurs[$c]) . '</p>' : '';
$d = $peut_modifier ? '' : ' disabled';
$ouvertes = participations_ouvertes($i, $roles);
// Adresse de la liste avec les filtres actuels, en changeant certains
$base = ['statut' => $f['statut'], 'role' => $f['role'] ?: '', 'region' => $f['region'], 'age' => $f['age'], 'presence' => $f['presence'],
    'tri' => $f['tri'] === 'recentes' ? '' : $f['tri'], 'affichage' => $f['affichage'] === 'cartes' ? '' : $f['affichage']];
$lien = function (array $changes = []) use ($ici, $base, $f) {
    $p = array_filter(array_merge($base, $changes), fn ($v) => $v !== '' && $v !== null);
    if ($f['q'] && !array_key_exists('role', $changes)) {
        $p['q'] = $f['q'];
    }
    return $ici . ($p ? '?' . http_build_query($p) : '');
};
$nb_billets = (int) ($compteurs['inscrit'] ?? 0) + (int) ($compteurs['retenue'] ?? 0);
$filtres_actifs = $f['statut'] || $f['role'] || $f['region'] || $f['age'] || $f['presence'] || $f['q'];
$badges = ['inscrit' => badge('Inscrit', 'vert'), 'en_attente' => badge('En attente', 'orange'), 'retenue' => badge('Retenue', 'vert'), 'non_retenue' => badge('Non retenue', 'gris')];
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
  <a href="<?= e($ici) ?>"<?= $onglet === 'liste' ? ' class="actif" aria-current="page"' : '' ?>>Inscrits et candidatures <span>(<?= $total ?>)</span></a>
  <a href="<?= e($ici) ?>?vue=formulaire"<?= $onglet === 'formulaire' ? ' class="actif" aria-current="page"' : '' ?>>Rôles et questions</a>
</nav>

<?php if ($onglet === 'liste'): ?>

<?php if ($roles): ?>
<ul class="ycm-pa-roles">
  <?php foreach ($roles as $r): ?>
  <li><a href="<?= e($lien(['role' => $f['role'] === (int) $r['id'] ? '' : $r['id']])) ?>"<?= $f['role'] === (int) $r['id'] ? ' class="actif"' : '' ?>><strong><?= e($r['nom']) ?></strong>
    <span><?= $r['mode'] === 'inscription' ? 'Inscription' : 'Candidature' ?> · <?= (int) $r['nb'] ?><?= $r['places'] !== null ? ' / ' . (int) $r['places'] . ' places' : '' ?></span></a></li>
  <?php endforeach ?>
</ul>
<?php endif ?>

<?php if ($nb_billets && $peut_modifier): ?>
<form method="post" action="<?= url('/gestion/initiatives/' . $i['id'] . '/pointage') ?>" class="ycm-pa-pointage">
  <?= csrf() ?><input type="hidden" name="suivant" value="<?= e(adresse_actuelle()) ?>">
  <div>
    <p class="ycm-pa-pointage-titre">Pointage à l'entrée</p>
    <p class="ycm-discret"><?= $nb_presents ?> présent<?= pluriel($nb_presents) ?> sur <?= $nb_billets ?> billet<?= pluriel($nb_billets) ?>. Saisissez le code du billet, ou scannez son QR code avec un lecteur.</p>
  </div>
  <div class="ycm-pa-pointage-champ">
    <input type="text" name="code" placeholder="YCM-XXXXX" autocomplete="off" autocapitalize="characters" aria-label="Code du billet" required>
    <button type="submit" class="ycm-bouton-vert">Pointer</button>
  </div>
</form>
<?php endif ?>

<form method="get" action="<?= e($ici) ?>" class="ycm-pa-outils">
  <div class="ycm-pa-filtres">
    <?php foreach (['' => 'Tous'] + array_filter(STATUTS_PARTICIPATION, fn ($s) => !empty($compteurs[$s]), ARRAY_FILTER_USE_KEY) as $cle => $lib): $n = $cle === '' ? $total : (int) $compteurs[$cle]; ?>
    <a href="<?= e($lien(['statut' => $cle])) ?>" class="ycm-pa-pastille<?= $f['statut'] === $cle ? ' actif' : '' ?>"><?= e(['inscrit' => 'Inscrits', 'retenue' => 'Retenues', 'non_retenue' => 'Non retenues'][$cle] ?? $lib) ?> <span>(<?= $n ?>)</span></a>
    <?php endforeach ?>
  </div>
  <?php foreach (['statut', 'affichage'] as $cache): if ($base[$cache] !== ''): ?><input type="hidden" name="<?= $cache ?>" value="<?= e($base[$cache]) ?>"><?php endif; endforeach ?>
  <div class="ycm-pa-droite">
    <?php if (count($roles) > 1): ?>
    <select name="role" data-envoi-auto aria-label="Rôle"><option value="">Tous les rôles</option>
      <?php foreach ($roles as $r): ?><option value="<?= (int) $r['id'] ?>"<?= $f['role'] === (int) $r['id'] ? ' selected' : '' ?>><?= e($r['nom']) ?></option><?php endforeach ?>
    </select>
    <?php elseif ($f['role']): ?><input type="hidden" name="role" value="<?= (int) $f['role'] ?>"><?php endif ?>
    <select name="region" data-envoi-auto aria-label="Région"><option value="">Toutes les régions</option>
      <?php foreach (REGIONS as $cle => $lib): ?><option value="<?= e($cle) ?>"<?= $f['region'] === $cle ? ' selected' : '' ?>><?= e($lib) ?></option><?php endforeach ?>
    </select>
    <select name="age" data-envoi-auto aria-label="Âge"><option value="">Tous les âges</option>
      <?php foreach (TRANCHES_AGE as $cle => [$lib]): ?><option value="<?= e($cle) ?>"<?= $f['age'] === $cle ? ' selected' : '' ?>><?= e($lib) ?></option><?php endforeach ?>
    </select>
    <?php if ($nb_billets): ?>
    <select name="presence" data-envoi-auto aria-label="Présence"><option value="">Présents et absents</option>
      <option value="oui"<?= $f['presence'] === 'oui' ? ' selected' : '' ?>>Présents</option><option value="non"<?= $f['presence'] === 'non' ? ' selected' : '' ?>>Pas encore pointés</option>
    </select>
    <?php endif ?>
    <?php if ($role_choisi): foreach ($role_choisi['champs'] as $c): if (!in_array($c['type'], ['choix', 'cases', 'oui_non'], true)) continue; $choix = $c['type'] === 'oui_non' ? ['Oui', 'Non'] : $c['choix']; ?>
    <select name="q[<?= (int) $c['id'] ?>]" data-envoi-auto aria-label="<?= e($c['libelle']) ?>"><option value=""><?= e(couper($c['libelle'], 34)) ?> : tout</option>
      <?php foreach ($choix as $ch): ?><option value="<?= e($ch) ?>"<?= ($f['q'][$c['id']] ?? '') === $ch ? ' selected' : '' ?>><?= e($ch) ?></option><?php endforeach ?>
    </select>
    <?php endforeach; endif ?>
    <select name="tri" data-envoi-auto aria-label="Trier"><?php foreach (['recentes' => 'Plus récentes', 'anciennes' => 'Plus anciennes', 'nom' => 'Par nom', 'age' => 'Plus jeunes d\'abord'] as $cle => $lib): ?><option value="<?= $cle ?>"<?= $f['tri'] === $cle ? ' selected' : '' ?>><?= e($lib) ?></option><?php endforeach ?></select>
    <noscript><button type="submit" class="ycm-bouton-gris ycm-petit">Filtrer</button></noscript>
  </div>
</form>

<div class="ycm-pa-barre">
  <p class="ycm-discret"><?= count($liste) ?> personne<?= pluriel(count($liste)) ?><?= $filtres_actifs ? ' · <a href="' . e($ici . ($f['affichage'] === 'tableau' ? '?affichage=tableau' : '')) . '">Retirer les filtres</a>' : '' ?></p>
  <div class="ycm-pa-droite">
    <span class="ycm-pa-bascule" role="group" aria-label="Affichage">
      <a href="<?= e($lien(['affichage' => ''])) ?>"<?= $f['affichage'] === 'cartes' ? ' class="actif" aria-current="true"' : '' ?>>Fiches</a>
      <a href="<?= e($lien(['affichage' => 'tableau'])) ?>"<?= $f['affichage'] === 'tableau' ? ' class="actif" aria-current="true"' : '' ?>>Tableau</a>
    </span>
    <?php if ($total): ?><a class="ycm-bouton-contour ycm-petit" href="<?= url('/gestion/initiatives/' . $i['id'] . '/participations/export') ?>">Exporter (Excel)</a><?php endif ?>
  </div>
</div>

<?php if (!$liste): ?>
<div class="ycm-boite ycm-boite--pad"><p class="ycm-vide"><?= $total ? 'Personne ne correspond à ces filtres.' : ($i['participations_ouvertes'] ? "Personne pour l'instant. Les rôles sont visibles sur la page de l'initiative." : "Personne : les participations ne sont pas encore ouvertes. Préparez-les dans l'onglet « Rôles et questions ».") ?></p></div>

<?php elseif ($f['affichage'] === 'tableau'): ?>
<div class="ycm-pa-defile">
  <table class="ycm-pa-tableau">
    <thead><tr>
      <th>Nom</th><th>Rôle</th><th>Décision</th><th>Âge</th><th>Région</th><th>Téléphone</th><th>Billet</th><th>Présent</th>
      <?php foreach ($colonnes_questions as $c): ?><th><?= e($c['libelle']) ?><?= !$role_choisi && count($roles) > 1 ? '<small>' . e($c['role_nom']) . '</small>' : '' ?></th><?php endforeach ?>
      <th>Reçue le</th>
    </tr></thead>
    <tbody>
      <?php foreach ($liste as $p): $rep = array_column(reponses_participation($p), 'reponse', 'id'); $a = age($p['date_naissance'] ?? null); ?>
      <tr>
        <td><a href="<?= url('/gestion/participations/' . $p['id']) ?>"><?= e($p['membre_nom']) ?></a><small><?= e($p['email']) ?></small></td>
        <td><?= e($p['role_libelle']) ?></td>
        <td><?= $badges[$p['statut']] ?? '' ?></td>
        <td><?= $a !== null ? $a : '' ?></td>
        <td><?= e(REGIONS[$p['region_i'] ?? ''] ?? '') ?></td>
        <td><?= e((string) $p['telephone']) ?></td>
        <td><?= billet_valable($p) ? e((string) $p['code']) : '' ?></td>
        <td><?= $p['present_le'] ? 'Oui' : '' ?></td>
        <?php foreach ($colonnes_questions as $c): ?><td><?= e(couper((string) ($rep[$c['id']] ?? ''), 160)) ?></td><?php endforeach ?>
        <td><?= e(date_chiffres($p['cree_le'])) ?></td>
      </tr>
      <?php endforeach ?>
    </tbody>
  </table>
</div>

<?php else: ?>
<div class="ycm-pa-liste">
  <?php foreach ($liste as $p): $rep = reponses_participation($p); $a = age($p['date_naissance'] ?? null); $candidature = $p['statut'] !== 'inscrit'; ?>
  <article class="ycm-pa-carte">
    <div class="ycm-pa-tete">
      <div>
        <p class="ycm-pa-nom"><?= e($p['membre_nom']) ?></p>
        <p class="ycm-discret"><?= e(implode(' · ', array_filter([$p['email'], $p['telephone'] ?? '', $a !== null ? $a . ' ans' : '', REGIONS[$p['region_i'] ?? ''] ?? '']))) ?></p>
      </div>
      <div class="ycm-pa-etat">
        <span class="ycm-pa-role"><?= e($p['role_libelle']) ?></span>
        <?= $badges[$p['statut']] ?? '' ?>
        <?php if ($p['present_le']): ?><?= badge('Présent', 'bleu') ?><?php endif ?>
      </div>
    </div>
    <?php if ($rep): ?>
    <dl class="ycm-pa-reponses">
      <?php foreach ($rep as $x): ?><div><dt><?= e($x['question']) ?></dt><dd><?= $x['reponse'] !== '' ? nl2br(e($x['reponse'])) : '<span class="ycm-discret">Pas de réponse</span>' ?></dd></div><?php endforeach ?>
    </dl>
    <?php endif ?>
    <div class="ycm-pa-pied">
      <span class="ycm-discret"><?= $candidature ? 'Candidature reçue' : 'Inscrit' ?> le <?= e(date_longue($p['cree_le'])) ?><?= billet_valable($p) ? ' · Billet ' . e((string) $p['code']) : '' ?></span>
      <?php if ($peut_modifier): ?>
      <span class="ycm-pa-decisions">
        <?php if (billet_valable($p)): ?>
        <form method="post" action="<?= url('/gestion/initiatives/' . $i['id'] . '/pointage') ?>" data-confirmer="<?= e($p['present_le'] ? 'Retirer la présence de ' . $p['membre_nom'] . ' ?' : 'Pointer ' . $p['membre_nom'] . ' présent ?') ?>" data-confirmer-oui="<?= $p['present_le'] ? 'Retirer' : 'Pointer présent' ?>">
          <?= csrf() ?><input type="hidden" name="participation" value="<?= (int) $p['id'] ?>"><input type="hidden" name="suivant" value="<?= e(adresse_actuelle()) ?>">
          <?php if ($p['present_le']): ?><input type="hidden" name="annuler" value="1"><?php endif ?>
          <button type="submit" class="ycm-bouton-gris ycm-petit"><?= $p['present_le'] ? 'Retirer la présence' : 'Pointer présent' ?></button>
        </form>
        <?php endif ?>
        <?php if ($candidature): foreach (['retenue' => ['Retenir', 'ycm-bouton-vert', 'Retenir ' . $p['membre_nom'] . ' comme « ' . $p['role_libelle'] . ' » ?', '', 'La personne en est prévenue et reçoit son billet dans son espace.'], 'non_retenue' => ['Ne pas retenir', 'ycm-bouton-rouge-contour', 'Ne pas retenir ' . $p['membre_nom'] . ' ?', ' data-confirmer-danger', 'La personne en est prévenue dans son espace.'], 'en_attente' => ['Remettre en attente', 'ycm-bouton-gris', 'Remettre cette candidature en attente ?', '', 'Son billet ne sera plus valable tant qu\'elle n\'est pas retenue.']] as $s => [$lib, $classe, $question, $danger, $texte]):
            if ($s === $p['statut']) continue; ?>
        <form method="post" action="<?= url('/gestion/participations/' . $p['id'] . '/decision') ?>" data-confirmer="<?= e($question) ?>" data-confirmer-texte="<?= e($texte) ?>" data-confirmer-oui="<?= e($lib) ?>"<?= $danger ?>>
          <?= csrf() ?><input type="hidden" name="statut" value="<?= $s ?>"><input type="hidden" name="suivant" value="<?= e(adresse_actuelle()) ?>">
          <button type="submit" class="<?= $classe ?> ycm-petit"><?= $lib ?></button>
        </form>
        <?php endforeach; endif ?>
      </span>
      <?php endif ?>
    </div>
  </article>
  <?php endforeach ?>
</div>
<?php endif ?>

<?php else: ?>
<form method="post" class="ycm-form-grille" data-confirmer="Enregistrer les participations ?" data-confirmer-texte="Si elles sont ouvertes, les rôles apparaissent aussitôt sur la page de l'initiative." data-confirmer-oui="Enregistrer">
  <?= csrf() ?>
  <div class="ycm-form-principal">
    <fieldset class="ycm-module">
      <legend><h2>Les rôles et leurs questions</h2></legend>
      <p class="ycm-description">Chaque rôle a sa façon de participer et ses propres questions. Par exemple : « Participant » en inscription sans question, « Intervenant » en candidature avec la thématique proposée. Le nom, l'e-mail, le téléphone, l'âge et la région viennent déjà du compte : inutile de les redemander.</p>
      <?= $err('roles') ?>
      <div class="ycm-pa-lignes" data-lignes="roles">
        <?php foreach ($roles_form ?: [['id' => 0, 'nom' => '', 'mode' => 'inscription', 'description' => '', 'places' => null, 'champs' => []]] as $k => $r): ?>
        <?php partiel('admin/_ligne_role', ['rk' => 'r' . $k, 'r' => $r, 'd' => $d, 'peut_modifier' => $peut_modifier]) ?>
        <?php endforeach ?>
      </div>
      <?php if ($peut_modifier): ?><button type="button" class="ycm-bouton-contour ycm-petit" data-ajouter="roles">Ajouter un rôle</button><?php endif ?>
    </fieldset>

    <fieldset class="ycm-module">
      <legend><h2>Message d'accueil (facultatif)</h2></legend>
      <div class="ycm-ligne-champ">
        <label for="pa-intro">Affiché au-dessus des rôles</label>
        <textarea id="pa-intro" name="participations_intro" rows="3" maxlength="1000" placeholder="Exemple : nous recherchons des intervenants et des bénévoles pour la journée."<?= $d ?>><?= e((string) ($i['participations_intro'] ?? '')) ?></textarea>
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
        <small>Après cette date, les participations se ferment toutes seules. Elles se ferment aussi quand l'initiative passe à « Terminée » ou que toutes les places sont prises. L'initiative reste visible sur le site.</small>
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

<template data-modele="roles"><?php partiel('admin/_ligne_role', ['rk' => '__role__', 'r' => ['id' => 0, 'nom' => '', 'mode' => 'candidature', 'description' => '', 'places' => null, 'champs' => []], 'd' => '', 'peut_modifier' => true]) ?></template>
<template data-modele="champs"><?php partiel('admin/_ligne_champ', ['rk' => '__role__', 'cle' => '__cle__', 'c' => ['id' => 0, 'libelle' => '', 'type' => 'texte', 'options' => '', 'aide' => '', 'requis' => 0], 'd' => '', 'peut_modifier' => true]) ?></template>
<?php endif ?>
