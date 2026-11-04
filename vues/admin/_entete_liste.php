<?php
/* En-tête d'une liste : titre, phrase, bouton d'ajout, onglets, filtres et menu « ⋯ » (sélectionner, exporter, réglages liés). */
$selection_possible = $r['actions'] && $peut_modifier && !empty($lignes) && in_array($r['vue_liste'], ['admin/liste', 'admin/liste_temoignages'], true);
$menu_plus = $selection_possible || $url_export || $r['menu_liste'];
?>
<div class="ycm-entete-page">
  <div>
    <h1><?= e($r['titre']) ?></h1>
    <?php if ($r['description']): ?><p class="ycm-sous-titre"><?= e($r['description']) ?></p><?php endif ?>
  </div>
  <?php if ($r['peut_ajouter'] && $peut_modifier && $r['groupes']): ?><a href="<?= url('/gestion/' . $r['slug'] . '/ajouter') ?>" class="<?= $r['ajout_discret'] ? 'ycm-bouton-contour ycm-petit' : 'ycm-bouton-orange' ?>"><?= e($r['libelle_ajout'] ?? 'Ajouter') ?></a><?php endif ?>
</div>
<?php if ($onglets || $filtres || $menu_plus): ?>
<div class="ycm-barre-liste">
  <?php if ($onglets): ?>
  <nav class="ycm-onglets" aria-label="Filtrer la liste">
    <?php foreach ($onglets as $o): ?><a href="<?= e($o['url']) ?>"<?= $o['actif'] ? ' class="actif" aria-current="true"' : '' ?>><?= e($o['libelle']) ?> <span>(<?= $o['nombre'] ?>)</span></a><?php endforeach ?>
  </nav>
  <?php endif ?>
  <div class="ycm-filtres-droite">
    <?php foreach ($filtres as $k => $f): ?>
      <label class="ycm-cache" for="filtre-<?= $k ?>"><?= e($f['titre']) ?></label>
      <select id="filtre-<?= $k ?>" data-filtre>
        <?php foreach ($f['options'] as $o): ?><option value="<?= e($o['url']) ?>"<?= $o['choisi'] ? ' selected' : '' ?>><?= e($o['libelle']) ?></option><?php endforeach ?>
      </select>
    <?php endforeach ?>
    <?php if ($menu_plus): ?>
    <details class="ycm-plus">
      <summary aria-label="Plus d'options"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="5" cy="12" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="19" cy="12" r="2"/></svg></summary>
      <div class="ycm-plus-menu" role="menu">
        <?php if ($selection_possible): ?><button type="button" role="menuitem" data-basculer-selection>Sélectionner</button><?php endif ?>
        <?php if ($url_export): ?><a role="menuitem" href="<?= e($url_export) ?>">Exporter en CSV</a><?php endif ?>
        <?php foreach ($r['menu_liste'] as [$libelle_m, $lien_m]): ?><a role="menuitem" href="<?= e($lien_m) ?>"><?= e($libelle_m) ?></a><?php endforeach ?>
      </div>
    </details>
    <?php endif ?>
  </div>
</div>
<?php endif ?>
<?php if ($q !== ''): ?><p class="ycm-recherche-active">Recherche : « <?= e($q) ?> » · <?= $total ?> résultat<?= pluriel($total) ?> <a href="<?= e(avec_parametres(['q' => null, 'p' => null])) ?>">Effacer</a></p><?php endif ?>
