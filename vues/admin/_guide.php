<?php
/* Repère : ce que contient cette rubrique. Paramètre : $guide = [titre, texte] */
[$titre_guide, $texte_guide] = $guide;
?>
<aside class="ycm-guide" aria-label="Ce que contient cette rubrique">
  <p class="ycm-guide-titre"><?= e($titre_guide) ?>, c'est…</p>
  <p><?= e($texte_guide) ?></p>
</aside>
