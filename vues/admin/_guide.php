<?php
/* Repère : ce qui va dans cette rubrique, et où mettre le reste. Paramètre : $guide = [titre, texte, [[cas, rubrique, slug], …]] */
[$titre_guide, $texte_guide, $ailleurs] = $guide;
?>
<aside class="ycm-guide" aria-label="Que mettre dans cette rubrique ?">
  <div class="ycm-guide-ici">
    <p class="ycm-guide-titre"><?= e($titre_guide) ?>, c'est…</p>
    <p><?= e($texte_guide) ?></p>
  </div>
  <div class="ycm-guide-ailleurs">
    <p class="ycm-guide-titre">Ce n'est pas le bon endroit pour…</p>
    <ul>
      <?php foreach ($ailleurs as [$cas, $rubrique, $slug_r]): ?>
      <li><span><?= e($cas) ?></span><a href="<?= url('/gestion/' . $slug_r) ?>"><?= e($rubrique) ?></a></li>
      <?php endforeach ?>
    </ul>
  </div>
</aside>
