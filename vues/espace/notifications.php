<?php ob_start();
/* Notifications : tout ce qui a changé pour le membre ces 60 derniers jours ; les nouvelles sont signalées. */
$tons = ['vert' => 'bg-vert', 'rouge' => 'bg-rouge', 'orange' => 'bg-orange'];
?>
<section class="bloc">
  <h1 class="text-xl font-bold sm:text-3xl">Notifications</h1>
  <?php if ($notifications): ?>
  <ul class="mt-4 divide-y divide-ligne">
    <?php foreach ($notifications as [$texte, $lien, $date, $ton]): $nouvelle = $date > $vues_le; ?>
    <li>
      <a href="<?= e($lien) ?>" class="group -mx-2 flex items-start gap-3 rounded-xl px-2 py-3 hover:bg-gris">
        <span class="mt-1.5 size-2.5 shrink-0 rounded-full <?= $tons[$ton] ?? 'bg-vert' ?>" aria-hidden="true"></span>
        <span class="min-w-0 flex-1">
          <span class="block text-[15px] leading-snug <?= $nouvelle ? 'font-bold text-encre' : 'text-encre/85' ?> group-hover:text-orange"><?= e($texte) ?></span>
          <span class="mt-0.5 block text-xs text-texte"><?= e(date_longue($date)) ?></span>
        </span>
        <?php if ($nouvelle): ?><span class="shrink-0 rounded-full bg-orange/15 px-2 py-0.5 text-[11px] font-bold text-orange-fonce">Nouveau</span><?php endif ?>
      </a>
    </li>
    <?php endforeach ?>
  </ul>
  <?php else: ?>
  <p class="mt-3 text-sm text-texte">Aucune notification pour le moment. Vous serez prévenu ici quand une demande sera traitée, quand une proposition sera publiée ou quand une opportunité arrivera dans vos communautés.</p>
  <?php endif ?>
</section>
<?php $espace = ob_get_clean(); partiel('espace/base', get_defined_vars());
