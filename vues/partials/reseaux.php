<?php /* Liens vers les réseaux sociaux officiels (saisis dans les paramètres du site) */
$reseaux = reseaux_sociaux();
if ($reseaux): ?>
<ul class="mt-4 flex flex-wrap gap-2" aria-label="Réseaux sociaux officiels">
  <?php foreach ($reseaux as $nom => $lien): ?>
  <li><a href="<?= e($lien) ?>" target="_blank" rel="noopener" class="inline-flex min-h-9 items-center rounded-full border border-vert px-3.5 text-sm font-bold text-vert hover:border-orange hover:bg-orange hover:text-white"><?= e($nom) ?></a></li>
  <?php endforeach ?>
</ul>
<?php elseif (est_admin()): ?>
<p class="mt-4 text-sm text-encre">[Réseaux sociaux officiels]</p>
<?php endif;
