<?php /* Infos clés d'une initiative. Paramètres : $i, $classes */ ?>
<dl class="<?= e($classes) ?> grid-cols-2 gap-2 sm:grid-cols-4 sm:gap-3">
  <?php foreach (['date_libelle' => 'Date', 'lieu' => 'Lieu', 'participants' => 'Participants', 'format' => 'Format'] as $c => $l): if ($i[$c]): ?>
  <div class="rounded-lg bg-gris px-3 py-2 sm:rounded-xl sm:px-3.5 sm:py-3"><dt class="text-[11px] sm:text-xs"><?= $l ?></dt><dd class="text-[13px] leading-snug font-bold text-vert sm:mt-0.5 sm:text-sm"><?= e($i[$c]) ?></dd></div>
  <?php endif; endforeach ?>
</dl>
