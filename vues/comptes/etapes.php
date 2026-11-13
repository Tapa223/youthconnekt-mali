<?php /* Étapes de l'inscription. Paramètre : $etape (1, 2 ou 3) */
$libelles = [1 => 'Type de compte', 2 => 'Informations', 3 => "Vérification de l'e-mail"]; ?>
<ol class="mb-4 flex items-center gap-2 text-xs font-bold sm:mb-7 sm:text-sm" aria-label="Étapes de l'inscription">
  <?php foreach ($libelles as $n => $libelle): ?>
  <?php if ($n > 1): ?><li class="h-px flex-1 bg-ligne" aria-hidden="true"></li><?php endif ?>
  <li class="flex items-center gap-2 <?= $etape === $n ? 'text-vert' : 'text-encre' ?>" <?= $etape === $n ? 'aria-current="step"' : '' ?>>
    <span class="grid size-6 shrink-0 place-items-center rounded-full <?= $etape === $n ? 'bg-orange text-white' : ($n < $etape ? 'bg-vert text-white' : 'bg-gris text-encre') ?>"><?= $n ?></span><span class="hidden sm:inline"><?= e($libelle) ?></span>
  </li>
  <?php endforeach ?>
</ol>
<p class="-mt-2.5 mb-3 text-xs sm:hidden">Étape <?= $etape ?> sur 3 : <?= e($libelles[$etape]) ?></p>
