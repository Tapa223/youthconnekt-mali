<?php
/* Fiche d'une opportunité (colonne de droite sur ordinateur) : date limite, informations, postuler, partager. Paramètre : $o */
$infos = ['lieu' => ['Lieu', '<path d="M12 21s7-6.2 7-12a7 7 0 0 0-14 0c0 5.8 7 12 7 12z"/><circle cx="12" cy="9" r="2.5"/>'],
          'duree' => ['Durée', '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>'],
          'public' => ['Public', '<circle cx="9" cy="8" r="3"/><path d="M3 20a6 6 0 0 1 12 0M16 4a3 3 0 0 1 0 6M21 20a5 5 0 0 0-4-4.9"/>'],
          'cout' => ['Coût', '<rect x="3" y="6" width="18" height="12" rx="2"/><circle cx="12" cy="12" r="2.5"/>']];
?>
<div class="overflow-hidden rounded-2xl border border-ligne bg-white shadow-xl shadow-vert/10">
  <div class="relative isolate overflow-hidden bg-vert px-6 py-5 text-white">
    <svg class="pointer-events-none absolute -bottom-3 left-[38%] -z-10 h-full w-32 opacity-50" viewBox="0 0 160 100" aria-hidden="true"><g stroke="rgb(255 255 255 / .3)" stroke-width="1.2" fill="none"><path d="M20 90 L80 30 L150 60"/></g><circle cx="80" cy="30" r="6" fill="#04B2B1"/><circle cx="150" cy="60" r="5" fill="#F1592A"/></svg>
    <?php if ($o['est_expiree']): ?>
      <p class="text-xs font-bold tracking-[0.14em] text-white/75 uppercase">Statut</p>
      <p class="mt-1 text-xl font-bold text-white">Candidatures closes</p>
    <?php else: ?>
      <p class="text-xs font-bold tracking-[0.14em] text-white/75 uppercase">Date limite</p>
      <div class="mt-1.5 flex items-center justify-between gap-3">
        <p class="text-xl font-bold text-white"><?= $o['date_limite'] ? e(date_longue($o['date_limite'])) : 'Aucune' ?></p>
        <?php if ($o['jours_restants'] !== null): ?><span class="shrink-0 rounded-full bg-orange px-3 py-1 text-sm font-bold whitespace-nowrap text-white">J-<?= $o['jours_restants'] ?></span><?php endif ?>
      </div>
    <?php endif ?>
  </div>

  <div class="p-6">
    <?php $presents = array_filter(array_keys($infos), fn ($c) => $o[$c] !== ''); ?>
    <?php if ($presents): ?>
    <dl class="space-y-4">
      <?php foreach ($presents as $c): [$libelle, $icone] = $infos[$c]; ?>
      <div class="flex items-center gap-3">
        <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-vert-clair text-vert" aria-hidden="true"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><?= $icone ?></svg></span>
        <div class="min-w-0"><dt class="text-xs text-texte"><?= $libelle ?></dt><dd class="text-[15px] leading-snug font-bold <?= $c === 'cout' ? 'text-vert' : 'text-encre' ?>"><?= e($o[$c]) ?></dd></div>
      </div>
      <?php endforeach ?>
    </dl>
    <?php endif ?>

    <?php if (!$o['est_expiree'] && $o['lien_candidature']): ?>
    <a href="<?= e($o['lien_candidature']) ?>" target="_blank" rel="noopener" class="btn btn-orange survol-leve <?= $presents ? 'mt-6' : '' ?> min-h-13 w-full text-base">Postuler</a>
    <?php endif ?>
    <?php partiel('partials/partage', ['titre' => $o['titre'], 'classes' => 'mt-5 border-t border-ligne pt-4']) ?>
  </div>
</div>
