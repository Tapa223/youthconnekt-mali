<?php partiel('admin/_entete_liste', get_defined_vars()); $couleurs = ['var(--ycm-turquoise)', 'var(--ycm-orange)', 'var(--ycm-vert)', 'var(--ycm-bleu)']; $av = ['orange', 'turquoise', 'bleu', 'vert']; ?>
<?php if ($voit_demandes): ?>
<div class="ycm-sous-section"><h2>Demandes d'intégration</h2><span>(<?= $total_attente ?> en attente)</span></div>
<?php if ($acceptation_auto): ?><p class="ycm-alerte"><strong>Règle actuelle :</strong> les membres intègrent les communautés automatiquement (Paramètres). Les demandes ci-dessous datent d'avant ce réglage.</p><?php endif ?>
<div class="ycm-boite" style="margin-bottom:8px">
  <?php if ($demandes): ?>
  <div class="ycm-tableau-defile">
    <table class="ycm-tableau">
      <thead><tr><th>Membre</th><th>Communauté demandée</th><th>Date</th><th>Statut</th><th class="ycm-droite">Actions</th></tr></thead>
      <tbody>
      <?php foreach ($demandes as $k => $d):
          $detail = array_filter([$d['categorie'] ? (CATEGORIES_INDIVIDUELLES[$d['categorie']] ?? '') : (TYPES_STRUCTURE[$d['type_structure'] ?? ''] ?? ''), REGIONS[$d['region_i'] ?: ($d['region_o'] ?? '')] ?? '']); ?>
        <tr>
          <td><?= nom_et_detail($d['membre_nom'], implode(' · ', $detail), '<span class="ycm-avatar ycm-avatar--' . $av[$k % 4] . '" aria-hidden="true">' . e(initiales($d['membre_nom'])) . '</span>') ?></td>
          <td><strong style="color:var(--ycm-vert)"><?= e($d['communaute_nom']) ?></strong></td>
          <td><?= e(date_courte($d['demandee_le'])) ?></td>
          <td><?= badge('En attente', 'orange') ?><?php $reste = (int) ceil((strtotime($d['demandee_le']) + DELAI_ADHESION_HEURES * 3600 - time()) / 3600); ?><br><small class="ycm-discret">Acceptée automatiquement dans <?= max(1, $reste) ?> h</small></td>
          <td class="ycm-droite"><?= liens_ligne(['Voir la demande' => url('/gestion/demandes/' . $d['id'])]) ?></td>
        </tr>
      <?php endforeach ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
  <p class="ycm-vide" style="padding:20px">Aucune demande en attente.</p>
  <?php endif ?>
  <p class="ycm-paginateur ycm-paginateur--boite"><span><?= $total_attente ?> demande<?= pluriel($total_attente) ?> en attente</span><a class="ycm-lien" href="<?= url('/gestion/demandes') ?>">Historique des demandes</a></p>
</div>
<?php endif ?>

<div class="ycm-sous-section"><h2>Les communautés</h2><span>(<?= $total ?>)</span></div>
<?php if ($lignes): ?>
<div class="ycm-grille-cartes">
  <?php foreach ($lignes as $k => $c): ?>
  <article class="ycm-carte-simple ycm-carte-communaute" style="--couleur: <?= $couleurs[$k % 4] ?>">
    <div class="ycm-carte-image"><img src="<?= $c['image'] ? e(media($c['image'])) : asset('img/communaute/carte-' . ($k % 6 + 1) . '.jpg') ?>" alt="" loading="lazy"></div>
    <div class="ycm-carte-corps">
      <h3><?= e($c['nom']) ?></h3>
      <div class="ycm-carte-ligne"><span><?= $c['nb_membres'] ?> membre<?= pluriel((int) $c['nb_membres']) ?></span><span><?= $c['nb_opportunites'] ?> opportunité<?= pluriel((int) $c['nb_opportunites']) ?> ciblée<?= pluriel((int) $c['nb_opportunites']) ?></span></div>
      <?php if (!$c['active']): ?><span style="align-self:flex-start"><?= badge('Désactivée', 'gris') ?></span><?php endif ?>
      <div class="ycm-carte-pied">
        <a href="<?= url('/gestion/communautes/' . $c['id']) ?>"><?= $peut_modifier ? 'Modifier' : 'Voir' ?></a>
        <?php if ($voit_demandes): ?><a href="<?= url('/gestion/demandes', ['onglet' => 2, 'communaute' => $c['id']]) ?>">Voir les membres</a><?php endif ?>
        <?php if ($c['nb_attente']): ?><?= badge($c['nb_attente'] . ' en attente', 'orange') ?><?php endif ?>
      </div>
    </div>
  </article>
  <?php endforeach ?>
</div>
<?php else: ?>
<div class="ycm-boite ycm-boite--pad"><p class="ycm-vide">Aucune communauté pour l'instant.</p></div>
<?php endif ?>
