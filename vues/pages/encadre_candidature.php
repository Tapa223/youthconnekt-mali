<?php
/* Fiche essentielle d'une opportunité : compte à rebours, informations clés, postuler, partager. Paramètres : $o, $id_fiche */
$infos = ['lieu' => ['Lieu', '<path d="M12 21s7-6.2 7-12a7 7 0 0 0-14 0c0 5.8 7 12 7 12z"/><circle cx="12" cy="9" r="2.5"/>'],
          'duree' => ['Durée', '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>'],
          'public' => ['Public', '<circle cx="9" cy="8" r="3"/><path d="M3 20a6 6 0 0 1 12 0M16 4a3 3 0 0 1 0 6M21 20a5 5 0 0 0-4-4.9"/>'],
          'cout' => ['Coût', '<rect x="3" y="6" width="18" height="12" rx="2"/><circle cx="12" cy="12" r="2.5"/>']];
$adresse = url_absolue(adresse_actuelle());
$partages = [
    'WhatsApp' => ['https://wa.me/?text=' . rawurlencode($o['titre'] . ' ' . $adresse), '<path d="M4 20l1.3-4A8 8 0 1 1 8 18.7L4 20z"/><path d="M9.5 9.5c.3 1.6 1.7 3.4 3.6 4.4l1.2-1.1 1.7.8-.4 1.6c-3 .4-6.6-3-6.9-6l1.5-.5.8 1.7z" fill="currentColor" stroke="none"/>'],
    'Facebook' => ['https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode($adresse), '<path d="M14 8.5V6.8c0-.8.5-1.3 1.3-1.3H17V2.3h-2.6C11.9 2.3 10.5 3.9 10.5 6.3v2.2H8v3.3h2.5v9.9H14v-9.9h2.6l.5-3.3H14z" fill="currentColor" stroke="none"/>'],
    'LinkedIn' => ['https://www.linkedin.com/sharing/share-offsite/?url=' . rawurlencode($adresse), '<path d="M6.9 9.3H3.6V20h3.3V9.3zM5.2 4a1.9 1.9 0 1 0 0 3.8 1.9 1.9 0 0 0 0-3.8zM20.4 13.9c0-3-1.6-4.9-4.2-4.9-1.4 0-2.4.8-2.8 1.5V9.3h-3.2V20h3.3v-5.6c0-1.4.6-2.4 1.9-2.4 1.2 0 1.7.9 1.7 2.4V20h3.3v-6.1z" fill="currentColor" stroke="none"/>'],
];
$urgent = !$o['est_expiree'] && $o['jours_restants'] !== null && $o['jours_restants'] <= 25;
?>
<div class="overflow-hidden rounded-2xl border border-ligne bg-white shadow-lg shadow-vert/5">
  <?php if ($o['est_expiree']): ?>
  <div class="bg-gris px-5 py-4"><p class="text-lg font-bold text-rouge">Candidatures closes</p><p class="text-sm">Depuis le <?= e(date_longue($o['date_limite'])) ?></p></div>
  <?php elseif ($o['date_limite']): ?>
  <div class="flex items-center gap-4 px-5 py-4 <?= $urgent ? 'bg-orange text-white' : 'bg-vert text-white' ?>">
    <span class="shrink-0 rounded-xl bg-white px-3 py-2 text-2xl leading-none font-bold whitespace-nowrap <?= $urgent ? 'text-orange' : 'text-vert' ?>">J-<?= $o['jours_restants'] ?></span>
    <p class="leading-tight"><strong class="block text-lg text-white"><?= e(libelle_jours($o['jours_restants'])) ?></strong><span class="text-sm text-white/90">Clôture le <?= e(date_longue($o['date_limite'])) ?></span></p>
  </div>
  <?php else: ?>
  <div class="bg-vert px-5 py-4 text-white"><p class="text-lg font-bold text-white">Sans date limite</p><p class="text-sm text-white/90">Candidatures ouvertes</p></div>
  <?php endif ?>

  <div class="p-5">
    <?php $presents = array_filter(array_keys($infos), fn ($c) => $o[$c] !== ''); ?>
    <?php if ($presents): ?>
    <dl class="grid grid-cols-2 gap-x-4 gap-y-4">
      <?php foreach ($presents as $c): [$libelle, $icone] = $infos[$c]; ?>
      <div class="flex min-w-0 items-start gap-2.5">
        <span class="grid size-9 shrink-0 place-items-center rounded-full bg-gris text-vert" aria-hidden="true"><svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><?= $icone ?></svg></span>
        <div class="min-w-0"><dt class="text-xs text-texte"><?= $libelle ?></dt><dd class="text-sm leading-snug font-bold <?= $c === 'cout' ? 'text-vert' : 'text-encre' ?>"><?= e($o[$c]) ?></dd></div>
      </div>
      <?php endforeach ?>
    </dl>
    <?php endif ?>

    <?php if (!$o['est_expiree'] && $o['lien_candidature']): ?>
    <a href="<?= e($o['lien_candidature']) ?>" target="_blank" rel="noopener" class="btn btn-orange <?= $presents ? 'mt-5' : '' ?> min-h-12 w-full text-base">Postuler</a>
    <p class="mt-2 text-center text-xs">Ouvre le formulaire de l'organisateur</p>
    <?php endif ?>

    <div class="<?= $presents || (!$o['est_expiree'] && $o['lien_candidature']) ? 'mt-5 border-t border-ligne pt-4' : '' ?>">
      <p class="text-xs font-bold tracking-[0.14em] text-vert uppercase">Partager</p>
      <div class="mt-2.5 flex flex-wrap gap-2">
        <?php foreach ($partages as $nom => [$lien, $trace]): ?>
        <a href="<?= e($lien) ?>" target="_blank" rel="noopener" class="grid size-11 place-items-center rounded-full border-[1.5px] border-vert text-vert transition-colors hover:border-orange hover:bg-orange hover:text-white" aria-label="Partager sur <?= $nom ?>"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><?= $trace ?></svg></a>
        <?php endforeach ?>
        <button type="button" class="inline-flex min-h-11 flex-1 items-center justify-center gap-2 rounded-full border-[1.5px] border-vert px-4 text-sm font-bold text-vert transition-colors hover:border-orange hover:bg-orange hover:text-white" data-copier-lien>
          <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M10 14a4 4 0 0 0 5.7 0l3-3a4 4 0 0 0-5.7-5.7l-1 1M14 10a4 4 0 0 0-5.7 0l-3 3a4 4 0 0 0 5.7 5.7l1-1"/></svg>Copier le lien</button>
      </div>
    </div>
  </div>
</div>
