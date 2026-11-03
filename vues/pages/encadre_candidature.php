<?php
/* Fiche d'une opportunité (colonne de droite sur ordinateur) : informations, postuler, partager. Paramètre : $o */
$infos = ['date_limite' => 'Date limite', 'lieu' => 'Lieu', 'duree' => 'Durée', 'public' => 'Public', 'cout' => 'Coût'];
?>
<div class="rounded-2xl border border-ligne bg-white p-5">
  <dl class="divide-y divide-ligne text-sm">
    <?php foreach ($infos as $c => $l):
        $v = $c === 'date_limite' ? ($o['date_limite'] ? date_longue($o['date_limite']) : 'Aucune') : $o[$c];
        if ($v === '') continue; ?>
    <div class="flex justify-between gap-4 py-2.5 first:pt-0"><dt class="text-texte"><?= $l ?></dt><dd class="text-right font-bold <?= $c === 'date_limite' ? 'text-rouge' : ($c === 'cout' ? 'text-vert' : 'text-encre') ?>"><?= e($v) ?></dd></div>
    <?php endforeach ?>
  </dl>
  <?php if ($o['est_expiree']): ?>
    <p class="mt-4 rounded-lg bg-gris p-3 text-center text-sm font-bold text-rouge">Les candidatures sont closes.</p>
  <?php elseif ($o['lien_candidature']): ?>
    <a href="<?= e($o['lien_candidature']) ?>" target="_blank" rel="noopener" class="btn btn-orange mt-4 min-h-12 w-full text-base">Postuler</a>
  <?php endif ?>
  <?php partiel('partials/partage', ['titre' => $o['titre'], 'classes' => 'mt-5 border-t border-ligne pt-4']) ?>
</div>
