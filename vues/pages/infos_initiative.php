<?php /* Infos clés d'une initiative. Paramètres : $i, $classes */ ?>
<dl class="<?= e($classes) ?> grid-cols-2 gap-3 sm:grid-cols-4">
  <?php foreach (['date_libelle' => 'Date', 'lieu' => 'Lieu', 'participants' => 'Participants', 'format' => 'Format'] as $c => $l): if ($i[$c]): ?>
  <div class="rounded-xl bg-gris px-3.5 py-3"><dt class="text-xs"><?= $l ?></dt><dd class="mt-0.5 text-sm font-bold text-vert"><?= e($i[$c]) ?></dd></div>
  <?php endif; endforeach ?>
</dl>
