<?php
/* Boutons de partage discrets. Paramètres : $titre, $classes */
$adresse = url_absolue(adresse_actuelle());
$partages = [
    'WhatsApp' => ['https://wa.me/?text=' . rawurlencode($titre . ' ' . $adresse), '<path d="M4 20l1.3-4A8 8 0 1 1 8 18.7L4 20z"/>'],
    'Facebook' => ['https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode($adresse), '<path d="M14 8.5V6.8c0-.8.5-1.3 1.3-1.3H17V2.3h-2.6C11.9 2.3 10.5 3.9 10.5 6.3v2.2H8v3.3h2.5v9.9H14v-9.9h2.6l.5-3.3H14z" fill="currentColor" stroke="none"/>'],
    'LinkedIn' => ['https://www.linkedin.com/sharing/share-offsite/?url=' . rawurlencode($adresse), '<path d="M6.9 9.3H3.6V20h3.3V9.3zM5.2 4a1.9 1.9 0 1 0 0 3.8 1.9 1.9 0 0 0 0-3.8zM20.4 13.9c0-3-1.6-4.9-4.2-4.9-1.4 0-2.4.8-2.8 1.5V9.3h-3.2V20h3.3v-5.6c0-1.4.6-2.4 1.9-2.4 1.2 0 1.7.9 1.7 2.4V20h3.3v-6.1z" fill="currentColor" stroke="none"/>'],
];
$icone = 'grid size-10 place-items-center rounded-full bg-gris text-vert transition-colors hover:bg-orange hover:text-white';
?>
<div class="flex items-center gap-2 <?= e($classes ?? '') ?>">
  <span class="mr-1 text-sm font-bold text-encre">Partager</span>
  <?php foreach ($partages as $nom => [$lien, $trace]): ?>
  <a href="<?= e($lien) ?>" target="_blank" rel="noopener" class="<?= $icone ?>" aria-label="Partager sur <?= $nom ?>"><svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><?= $trace ?></svg></a>
  <?php endforeach ?>
  <button type="button" class="<?= $icone ?>" data-copier-lien aria-label="Copier le lien"><svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M10 14a4 4 0 0 0 5.7 0l3-3a4 4 0 0 0-5.7-5.7l-1 1M14 10a4 4 0 0 0-5.7 0l-3 3a4 4 0 0 0 5.7 5.7l1-1"/></svg></button>
</div>
