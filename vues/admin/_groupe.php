<?php /* Un groupe de champs, dans une carte. Paramètres : $g, $valeurs, $erreurs, $ligne, $desactive */ ?>
<fieldset class="ycm-module">
  <?php if (!empty($g[0])): ?><legend><h2><?= e($g[0]) ?></h2></legend><?php endif ?>
  <?php if (!empty($g['description'])): ?><p class="ycm-description"><?= e($g['description']) ?></p><?php endif ?>
  <div class="ycm-champs">
    <?php foreach ($g['champs'] as $nom => $def):
        partiel('admin/_champ', ['nom' => $nom, 'def' => $def + ['requis' => true], 'valeur' => $valeurs[$nom] ?? '', 'erreur' => $erreurs[$nom] ?? '', 'ligne' => $ligne, 'desactive' => $desactive]);
    endforeach ?>
  </div>
</fieldset>
