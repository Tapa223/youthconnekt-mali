<?php
$avantages = lignes_texte($o['avantages']);
$criteres = lignes_texte($o['criteres']);
$etapes = lignes_texte($o['etapes']);
?>
<div class="conteneur py-10 sm:py-12">
  <?php partiel('partials/retour', ['url' => url('/opportunites')]) ?>
  <div class="flex flex-wrap gap-2">
    <span class="etiquette rounded-full <?= fond($o['couleur']) ?>"><?= e($o['type_libelle']) ?></span>
    <?php if ($o['est_expiree']): ?><span class="etiquette rounded-full bg-gris text-rouge">Candidatures closes</span>
    <?php elseif ($o['jours_restants'] !== null): ?><span class="etiquette rounded-full bg-orange text-white"><?= e(libelle_jours($o['jours_restants'])) ?></span><?php endif ?>
  </div>
  <h1 class="mt-3 text-[1.875rem] leading-[1.08] font-bold sm:text-5xl"><?= e($o['titre']) ?></h1>
  <p class="mt-2 text-sm"><?php if ($o['organisme']): ?>Proposée par <strong class="text-encre"><?= e($o['organisme']) ?></strong> · <?php endif ?>Publiée le <?= e(date_longue($o['publie_le'])) ?></p>

  <div class="mt-7 grid gap-8 lg:grid-cols-[minmax(0,1fr)_340px] lg:gap-10">
    <div class="min-w-0">
      <div class="devoile relative aspect-[16/9] overflow-hidden rounded-2xl border-b-[5px] sm:aspect-[16/8] lg:border-b-8 <?= bordure($o['couleur']) ?>">
        <?php partiel('partials/photo', ['image' => $o['image'], 'eager' => true]) ?>
      </div>

      <dl class="mt-5 grid grid-cols-2 gap-x-4 gap-y-3 rounded-2xl bg-gris p-4 text-sm lg:hidden">
        <div class="col-span-2"><dt class="text-xs">Date limite</dt><dd class="font-bold text-rouge"><?= $o['date_limite'] ? e(date_longue($o['date_limite'])) : 'Aucune' ?></dd></div>
        <?php foreach (['lieu' => 'Lieu', 'duree' => 'Durée', 'public' => 'Public', 'cout' => 'Coût'] as $c => $l): if ($o[$c]): ?>
        <div><dt class="text-xs"><?= $l ?></dt><dd class="font-bold <?= $c === 'cout' ? 'text-vert' : 'text-encre' ?>"><?= e($o[$c]) ?></dd></div>
        <?php endif; endforeach ?>
      </dl>

      <div class="prose-ycm mt-8">
        <h2>Présentation</h2>
        <?= paragraphes($o['description']) ?>
        <?php if ($avantages): ?><h2>Ce que vous gagnez</h2><ul><?php foreach ($avantages as $x): ?><li><?= e($x) ?></li><?php endforeach ?></ul><?php endif ?>
        <?php if ($criteres): ?><h2>Qui peut postuler</h2><ul><?php foreach ($criteres as $x): ?><li><?= e($x) ?></li><?php endforeach ?></ul><?php endif ?>
      </div>

      <?php if ($etapes): ?>
      <h2 class="mt-9 text-2xl font-bold">Comment postuler</h2>
      <ol class="mt-4 space-y-3">
        <?php foreach ($etapes as $k => $x): ?><li class="flex items-center gap-3 text-encre"><span class="grid size-8 shrink-0 place-items-center rounded-full bg-vert text-sm font-bold text-white"><?= $k + 1 ?></span><?= e($x) ?></li><?php endforeach ?>
      </ol>
      <?php endif ?>

      <div class="mt-9 grid grid-cols-2 gap-2 border-t border-ligne pt-6 lg:hidden">
        <button type="button" class="btn btn-contour px-2 text-sm" data-partager data-titre="<?= e($o['titre']) ?>">Partager</button>
        <button type="button" class="btn btn-contour px-2 text-sm" data-copier-lien>Copier le lien</button>
      </div>
    </div>

    <aside class="hidden lg:block">
      <div class="sticky top-24"><?php partiel('pages/encadre_candidature', ['o' => $o]) ?></div>
    </aside>
  </div>
</div>

<?php if ($similaires): ?>
<section class="bg-gris py-14 sm:py-16" aria-labelledby="titre-similaires">
  <div class="conteneur">
    <div class="flex items-end justify-between gap-4">
      <h2 id="titre-similaires" class="titre-section">Vous aimerez aussi.</h2>
      <a href="<?= url('/opportunites') ?>" class="btn btn-contour hidden sm:inline-flex">Toutes les opportunités</a>
      <a href="<?= url('/opportunites') ?>" class="btn-tout-voir sm:hidden">Tout voir</a>
    </div>
    <ul class="masquer-defilement -mx-4 mt-7 flex snap-x snap-mandatory gap-4 overflow-x-auto px-4 pb-2 sm:mx-0 sm:grid sm:grid-cols-2 sm:gap-5 sm:overflow-visible sm:px-0 lg:grid-cols-3">
      <?php foreach ($similaires as $s): ?><li class="w-[75%] max-w-72 shrink-0 snap-start sm:w-auto sm:max-w-none"><?php partiel('cartes/opportunite', ['o' => $s, 'compacte' => true]) ?></li><?php endforeach ?>
    </ul>
  </div>
</section>
<?php endif ?>

<div class="sticky bottom-0 z-30 border-t border-ligne bg-white/95 px-4 py-3 pb-[max(0.75rem,env(safe-area-inset-bottom))] shadow-[0_-6px_20px_rgba(0,0,0,0.06)] backdrop-blur lg:hidden">
  <div class="mx-auto flex max-w-xl items-center justify-between gap-4">
    <p class="text-xs leading-tight"><?php if ($o['est_expiree']): ?>Statut<br><strong class="text-sm text-rouge">Candidatures closes</strong><?php else: ?>Date limite<br><strong class="text-sm text-rouge"><?= $o['date_limite'] ? e(date_longue($o['date_limite'])) : 'Aucune' ?></strong><?php endif ?></p>
    <?php if (!$o['est_expiree'] && $o['lien_candidature']): ?>
    <a href="<?= e($o['lien_candidature']) ?>" target="_blank" rel="noopener" class="btn btn-orange min-h-12 px-8 text-base">Postuler</a>
    <?php elseif (!$o['est_expiree']): ?>
    <span class="text-right text-xs">Lien de candidature<br>bientôt disponible</span>
    <?php endif ?>
  </div>
</div>
