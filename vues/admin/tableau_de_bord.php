<div class="ycm-entete-page">
  <div>
    <h1>Bonjour<?= $prenom ? ', ' . e($prenom) : '' ?></h1>
    <p class="ycm-sous-titre">Voici ce qui se passe sur YouthConnekt Mali aujourd'hui.</p>
  </div>
  <?php if ($actions): ?><a href="<?= e($actions[0]['url']) ?>" class="ycm-bouton-orange"><?= e($actions[0]['libelle']) ?></a><?php endif ?>
</div>

<?php if ($cartes): ?>
<section class="ycm-cartes-chiffres" aria-label="Le site en chiffres">
  <?php foreach ($cartes as $c): ?>
  <a class="ycm-carte-chiffre" href="<?= e($c['url']) ?>">
    <span class="ycm-carte-chiffre-tete"><?= e($c['libelle']) ?><span class="ycm-icone-ronde ycm-fond-<?= $c['couleur'] ?>" aria-hidden="true"><svg viewBox="0 0 24 24"><?= $c['icone'] ?></svg></span></span>
    <strong><?= $c['valeur'] ?></strong>
    <small><?= e($c['detail']) ?></small>
  </a>
  <?php endforeach ?>
</section>
<?php endif ?>

<?php if ($graphiques): $g = $graphiques; $max_mois = max(1, max($g['mois'])); $total_annee = array_sum($g['mois']); $max_reg = max(1, $g['regions'] ? max($g['regions']) : 1);
  $noms_mois = ['01' => 'janv.', '02' => 'févr.', '03' => 'mars', '04' => 'avr.', '05' => 'mai', '06' => 'juin', '07' => 'juil.', '08' => 'août', '09' => 'sept.', '10' => 'oct.', '11' => 'nov.', '12' => 'déc.']; ?>
<div class="ycm-graphiques">
  <section class="ycm-boite ycm-boite--pad" aria-labelledby="tdb-evolution">
    <div class="ycm-boite-tete ycm-boite-tete--nu"><h2 id="tdb-evolution">Inscriptions sur 12 mois</h2><span class="ycm-discret"><?= $total_annee ?> au total</span></div>
    <div class="ycm-histogramme" role="img" aria-label="Inscriptions par mois sur les 12 derniers mois">
      <?php foreach ($g['mois'] as $m => $n): ?>
      <div class="ycm-histo-col" title="<?= e($noms_mois[substr($m, 5)] . ' ' . substr($m, 0, 4)) ?> : <?= $n ?> inscription<?= pluriel($n) ?>">
        <span class="ycm-histo-val"><?= $n ?: '' ?></span>
        <span class="ycm-histo-barre<?= $m === date('Y-m') ? ' actuel' : '' ?>" style="--h: <?= max(2, round($n * 100 / $max_mois)) ?>%"></span>
        <span class="ycm-histo-mois"><?= e($noms_mois[substr($m, 5)]) ?></span>
      </div>
      <?php endforeach ?>
    </div>
  </section>
  <section class="ycm-boite ycm-boite--pad" aria-labelledby="tdb-regions">
    <div class="ycm-boite-tete ycm-boite-tete--nu"><h2 id="tdb-regions">Membres par région</h2></div>
    <?php if ($g['regions']): ?>
    <ul class="ycm-barres">
      <?php foreach (array_slice($g['regions'], 0, 7, true) as $r => $n): ?>
      <li><span><?= e($r) ?></span><span class="ycm-barres-piste"><span style="--l: <?= round($n * 100 / $max_reg) ?>%"></span></span><strong><?= $n ?></strong></li>
      <?php endforeach ?>
    </ul>
    <?php else: ?><p class="ycm-discret">Les régions apparaîtront quand les membres auront complété leur profil.</p><?php endif ?>
  </section>
  <?php if ($g['presence']): ?>
  <section class="ycm-boite ycm-boite--pad ycm-graphiques-large" aria-labelledby="tdb-presence">
    <div class="ycm-boite-tete ycm-boite-tete--nu"><h2 id="tdb-presence">Présence aux initiatives</h2><span class="ycm-discret">Présents sur billets délivrés</span></div>
    <ul class="ycm-barres ycm-barres--presence">
      <?php foreach ($g['presence'] as $x): $taux = $x['billets'] ? round($x['presents'] * 100 / $x['billets']) : 0; ?>
      <li><a href="<?= url('/gestion/initiatives/' . $x['id'] . '/participations') ?>"><?= e(couper($x['titre'], 46)) ?></a><span class="ycm-barres-piste"><span style="--l: <?= $taux ?>%"></span></span><strong><?= (int) $x['presents'] ?> / <?= (int) $x['billets'] ?></strong></li>
      <?php endforeach ?>
    </ul>
  </section>
  <?php endif ?>
</div>
<?php endif ?>

<?php if ($participation): ?>
<section class="ycm-boite ycm-boite--pad ycm-participation" aria-labelledby="tdb-participation">
  <div class="ycm-boite-tete ycm-boite-tete--nu"><h2 id="tdb-participation">Participation des membres</h2></div>
  <p class="ycm-sous-titre">Ce que les membres apportent eux-mêmes au chapitre.</p>
  <div class="ycm-participation-grille">
    <?php foreach ($participation as [$libelle, $nombre, $detail, $lien]): ?>
    <a href="<?= e($lien) ?>" class="ycm-participation-chiffre"><strong><?= $nombre ?></strong><span><?= e($libelle) ?></span><small><?= e($detail) ?></small></a>
    <?php endforeach ?>
  </div>
  <?php if ($profil_membres): $pm = $profil_membres; $non = $pm['total'] - $pm['femmes'] - $pm['hommes']; ?>
  <h3 class="ycm-participation-titre">Qui sont les membres individuels</h3>
  <div class="ycm-profil-membres">
    <div>
      <p class="ycm-profil-ligne"><strong><?= $pm['femmes'] ?></strong> femme<?= pluriel($pm['femmes']) ?> · <strong><?= $pm['hommes'] ?></strong> homme<?= pluriel($pm['hommes']) ?><?= $non > 0 ? ' · <span class="ycm-discret">' . $non . ' non renseigné' . pluriel($non) . '</span>' : '' ?></p>
      <?php $pf = $pm['total'] ? round($pm['femmes'] * 100 / $pm['total']) : 0; $ph = $pm['total'] ? round($pm['hommes'] * 100 / $pm['total']) : 0; ?>
      <div class="ycm-barre-genre" role="img" aria-label="<?= $pf ?> % de femmes, <?= $ph ?> % d'hommes"><span style="width:<?= $pf ?>%"></span><span style="width:<?= $ph ?>%"></span></div>
      <?php if ($pm['age_moyen'] !== null): ?><p class="ycm-discret">Âge moyen : <?= (int) $pm['age_moyen'] ?> ans</p><?php endif ?>
    </div>
    <ul>
      <?php foreach ($pm['ages'] as $cle => [$lib, $n]): ?>
      <li><a href="<?= url('/gestion/membres', ['age' => $cle]) ?>"><span><?= e($lib) ?></span><strong><?= $n ?></strong></a></li>
      <?php endforeach ?>
    </ul>
  </div>
  <?php endif ?>
  <?php if ($contributeurs): ?>
  <h3 class="ycm-participation-titre">Les plus actifs</h3>
  <ul class="ycm-participation-liste">
    <?php foreach ($contributeurs as $k => $m): ?>
    <li><a href="<?= url('/gestion/comptes/' . $m['id']) ?>"><span class="ycm-avatar ycm-avatar--<?= ['orange', 'turquoise', 'bleu', 'vert'][$k % 4] ?>" aria-hidden="true"><?= e($m['initiales']) ?></span><span><?= e($m['nom_affiche']) ?></span><small><?= $m['nb'] ?> contribution<?= pluriel($m['nb']) ?></small></a></li>
    <?php endforeach ?>
  </ul>
  <?php endif ?>
</section>
<?php endif ?>

<div class="ycm-tdb-grille">
  <div class="ycm-tdb-principal">
    <?php if ($url_membres): ?>
    <section class="ycm-boite" aria-labelledby="tdb-inscriptions">
      <div class="ycm-boite-tete">
        <h2 id="tdb-inscriptions">Dernières inscriptions</h2>
        <a href="<?= e($url_membres) ?>" class="ycm-lien">Tous les membres</a>
      </div>
      <?php if ($inscriptions): ?>
      <div class="ycm-tableau-defile">
        <table class="ycm-tableau ycm-tableau--tdb">
          <thead><tr><th>Nom</th><th>Profil</th><th>Région</th><th>Inscription</th><th><span class="ycm-cache">Action</span></th></tr></thead>
          <tbody>
            <?php foreach ($inscriptions as $k => $i): ?>
            <tr>
              <td><span class="ycm-personne"><?= avatar($i['initiales'], $k) ?><strong><?= e($i['nom']) ?></strong></span></td>
              <td><?= e($i['profil']) ?></td>
              <td><?= e($i['region']) ?></td>
              <td><?= e(date_courte($i['date'])) ?></td>
              <td class="ycm-droite"><?= liens_ligne(['Voir' => $i['url']]) ?></td>
            </tr>
            <?php endforeach ?>
          </tbody>
        </table>
      </div>
      <?php else: ?>
      <p class="ycm-vide">Aucune inscription pour l'instant.</p>
      <?php endif ?>
    </section>
    <?php endif ?>
  </div>

  <div class="ycm-tdb-cote">
    <?php if ($bientot): ?>
    <section class="ycm-boite ycm-boite--pad" aria-labelledby="tdb-bientot">
      <h2 id="tdb-bientot">Bientôt clôturées</h2>
      <ul class="ycm-bientot">
        <?php foreach ($bientot as $o): ?>
        <li>
          <span class="ycm-jours<?= $o['jours'] <= 25 ? ' ycm-jours--urgent' : '' ?>"><small>J-</small><?= $o['jours'] ?></span>
          <a href="<?= e($o['url']) ?>"><strong><?= e($o['titre']) ?></strong><small>Clôture le <?= e(jour_mois($o['date'])) ?></small></a>
        </li>
        <?php endforeach ?>
      </ul>
    </section>
    <?php endif ?>

    <?php if ($actions): ?>
    <section class="ycm-actions-rapides" aria-labelledby="tdb-actions">
      <h2 id="tdb-actions">Actions rapides</h2>
      <ul>
        <?php foreach ($actions as $a): ?><li><a href="<?= e($a['url']) ?>"><svg viewBox="0 0 24 24" aria-hidden="true"><?= $a['icone'] ?></svg><?= e($a['libelle']) ?></a></li><?php endforeach ?>
      </ul>
    </section>
    <?php endif ?>
  </div>
</div>

<?php /* Les chiffres clés se modifient à un seul endroit : Administration > Chiffres clés (avec l'aperçu de l'accueil). */ ?>
