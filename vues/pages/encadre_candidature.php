<?php $infos = ['lieu' => 'Lieu', 'duree' => 'Durée', 'public' => 'Public', 'cout' => 'Coût']; ?>
<div class="rounded-2xl border border-ligne bg-white p-5 shadow-lg shadow-vert/5">
  <?php if ($o['date_limite']): ?>
  <div class="flex items-center gap-4 rounded-xl bg-gris p-3.5">
    <?php if (!$o['est_expiree']): ?>
    <span class="grid min-w-14 rounded-xl bg-orange px-2 py-2 text-center leading-none text-white"><span class="text-[10px] font-bold">J-</span><span class="text-2xl font-bold"><?= $o['jours_restants'] ?></span></span>
    <?php endif ?>
    <p class="text-sm">Clôture des candidatures<br><strong class="text-rouge"><?= e(date_longue($o['date_limite'])) ?></strong></p>
  </div>
  <?php endif ?>
  <dl class="mt-3 divide-y divide-ligne text-sm">
    <div class="flex justify-between gap-4 py-2.5"><dt>Date limite</dt><dd class="text-right font-bold text-rouge"><?= $o['date_limite'] ? e(date_longue($o['date_limite'])) : 'Aucune' ?></dd></div>
    <?php foreach ($infos as $c => $l): if ($o[$c]): ?>
    <div class="flex justify-between gap-4 py-2.5"><dt><?= $l ?></dt><dd class="text-right font-bold <?= $c === 'cout' ? 'text-vert' : 'text-encre' ?>"><?= e($o[$c]) ?></dd></div>
    <?php endif; endforeach ?>
  </dl>
  <?php if ($o['est_expiree']): ?>
    <p class="mt-4 rounded-lg bg-gris p-3 text-center text-sm font-bold text-rouge">Les candidatures sont closes.</p>
  <?php elseif ($o['lien_candidature']): ?>
    <a href="<?= e($o['lien_candidature']) ?>" target="_blank" rel="noopener" class="btn btn-orange mt-4 min-h-12 w-full text-base">Postuler</a>
  <?php else: ?>
    <p class="mt-4 rounded-lg border border-dashed border-ligne p-3 text-center text-sm">Le lien de candidature sera bientôt disponible.</p>
  <?php endif ?>
  <div class="mt-3 grid grid-cols-2 gap-2">
    <button type="button" class="btn btn-contour px-2 text-sm" data-partager data-titre="<?= e($o['titre']) ?>">Partager</button>
    <button type="button" class="btn btn-contour px-2 text-sm" data-copier-lien>Copier le lien</button>
  </div>
  <?php if ($o['lien_candidature'] && !$o['est_expiree']): ?><p class="mt-3 text-xs">Le bouton « Postuler » ouvre le formulaire de l'organisateur.</p><?php endif ?>
</div>
