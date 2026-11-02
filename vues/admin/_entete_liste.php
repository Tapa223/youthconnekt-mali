<?php /* En-tête d'une liste : titre, phrase, bouton d'ajout, onglets, filtres, export. */ ?>
<div class="ycm-entete-page">
  <div>
    <h1><?= e($r['titre']) ?></h1>
    <?php if ($r['description']): ?><p class="ycm-sous-titre"><?= e($r['description']) ?></p><?php endif ?>
  </div>
  <?php if ($r['peut_ajouter'] && $peut_modifier && $r['groupes']): ?><a href="<?= url('/gestion/' . $r['slug'] . '/ajouter') ?>" class="ycm-bouton-orange"><?= e($r['libelle_ajout'] ?? 'Ajouter') ?></a><?php endif ?>
</div>
<?php if ($onglets || $filtres || $url_export): ?>
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
    <?php if ($url_export): ?><a href="<?= e($url_export) ?>" class="ycm-bouton-blanc">Exporter en CSV</a><?php endif ?>
  </div>
</div>
<?php endif ?>
<?php if ($q !== ''): ?><p class="ycm-recherche-active">Recherche : « <?= e($q) ?> » · <?= $total ?> résultat<?= pluriel($total) ?> <a href="<?= e(avec_parametres(['q' => null, 'p' => null])) ?>">Effacer</a></p><?php endif ?>
