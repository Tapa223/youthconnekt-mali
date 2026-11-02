<?php if ($pages > 1): ?>
<p class="ycm-paginateur">
  <?php for ($n = 1; $n <= $pages; $n++): ?>
    <?php if ($n === $page): ?><span class="actuelle"><?= $n ?></span>
    <?php elseif ($n <= 2 || $n > $pages - 2 || abs($n - $page) <= 2): ?><a href="<?= e(avec_parametres(['p' => $n])) ?>"><?= $n ?></a>
    <?php elseif ($n === 3 || $n === $pages - 2): ?><span>…</span><?php endif ?>
  <?php endfor ?>
  <span class="total"><?= $total ?> élément<?= pluriel($total) ?></span>
</p>
<?php endif;
