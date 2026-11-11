<?php
$avantages = lignes_texte($o['avantages']);
$criteres = lignes_texte($o['criteres']);
$etapes = lignes_texte($o['etapes']);
?>
<div class="conteneur pt-2 pb-6 sm:pt-6 sm:pb-14">
  <?php partiel('partials/retour', ['url' => url('/opportunites'), 'classes' => 'mb-4 hidden sm:block']) ?>

  <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_340px] lg:gap-10">
    <div class="min-w-0">
      <div class="flex flex-wrap items-center gap-2">
        <?php partiel('partials/retour', ['url' => url('/opportunites'), 'classes' => 'mr-1 sm:hidden']) ?>
        <span class="etiquette rounded-full <?= fond($o['couleur']) ?>"><?= e($o['type_libelle']) ?></span>
        <?php if ($o['est_expiree']): ?><span class="etiquette rounded-full bg-gris text-rouge">Candidatures closes</span>
        <?php elseif ($o['jours_restants'] !== null): ?><span class="etiquette rounded-full bg-orange-clair text-orange-fonce">J-<?= $o['jours_restants'] ?></span><?php endif ?>
      </div>
      <h1 class="mt-2 text-[1.6rem] leading-[1.12] font-bold sm:mt-3 sm:text-[2.6rem]"><?= e($o['titre']) ?></h1>
      <?php if ($o['organisme']): ?><p class="mt-1 text-sm sm:mt-2 sm:text-[15px]">Proposée par <strong class="text-encre"><?= e($o['organisme']) ?></strong></p><?php endif ?>

      <?php /* Sur téléphone : les informations utiles en une liste simple, juste sous le titre */ ?>
      <dl class="mt-4 grid grid-cols-2 gap-x-4 gap-y-2.5 rounded-2xl bg-gris p-3.5 text-sm lg:hidden">
        <div><dt class="text-xs text-texte">Date limite</dt><dd class="font-bold text-rouge"><?= $o['date_limite'] ? e(date_courte($o['date_limite'])) : 'Aucune' ?></dd></div>
        <?php foreach (['lieu' => 'Lieu', 'duree' => 'Durée', 'public' => 'Public', 'cout' => 'Coût'] as $c => $l): if ($o[$c] !== ''): ?>
        <div><dt class="text-xs text-texte"><?= $l ?></dt><dd class="font-bold <?= $c === 'cout' ? 'text-vert' : 'text-encre' ?>"><?= e($o[$c]) ?></dd></div>
        <?php endif; endforeach ?>
      </dl>

      <?php if ($o['image']): ?>
      <div class="mt-4 aspect-[16/9] overflow-hidden rounded-2xl sm:mt-6">
        <?php partiel('partials/photo', ['transition' => 'photo-opp-' . $o['id'], 'image' => $o['image'], 'eager' => true]) ?>
      </div>
      <?php endif ?>

      <div class="prose-ycm mt-5 sm:mt-7">
        <h2 class="!mt-0">Présentation</h2>
        <?= paragraphes($o['description']) ?>
        <?php if ($avantages): ?><h2>Ce que vous gagnez</h2><ul><?php foreach ($avantages as $x): ?><li><?= e($x) ?></li><?php endforeach ?></ul><?php endif ?>
        <?php if ($criteres): ?><h2>Qui peut postuler</h2><ul><?php foreach ($criteres as $x): ?><li><?= e($x) ?></li><?php endforeach ?></ul><?php endif ?>
      </div>

      <?php if ($etapes): ?>
      <h2 class="mt-7 text-2xl font-bold sm:mt-9">Comment postuler</h2>
      <ol class="mt-4 space-y-3">
        <?php foreach ($etapes as $k => $x): ?><li class="flex items-center gap-3 text-encre"><span class="grid size-8 shrink-0 place-items-center rounded-full bg-vert text-sm font-bold text-white"><?= $k + 1 ?></span><?= e($x) ?></li><?php endforeach ?>
      </ol>
      <?php endif ?>

      <?php partiel('partials/partage', ['titre' => $o['titre'], 'classes' => 'mt-7 border-t border-ligne pt-4 lg:hidden']) ?>
      <p class="mt-3 text-xs text-texte sm:mt-5">Publiée le <?= e(date_longue($o['publie_le'])) ?></p>
    </div>

    <aside class="hidden lg:block">
      <div class="sticky top-24"><?php partiel('pages/encadre_candidature', ['o' => $o]) ?></div>
    </aside>
  </div>
</div>

<?php if ($similaires): ?>
<section class="bg-gris py-8 sm:py-16" aria-labelledby="titre-similaires">
  <div class="conteneur">
    <div class="flex items-end justify-between gap-4">
      <h2 id="titre-similaires" class="titre-section !mt-0 text-2xl">Vous aimerez aussi</h2>
      <a href="<?= url('/opportunites') ?>" class="btn btn-contour hidden sm:inline-flex">Toutes les opportunités</a>
      <a href="<?= url('/opportunites') ?>" class="btn-tout-voir sm:hidden">Tout voir</a>
    </div>
    <ul class="masquer-defilement -mx-4 mt-4 flex sm:mt-7 snap-x snap-mandatory gap-4 overflow-x-auto px-4 pb-2 sm:mx-0 sm:grid sm:grid-cols-2 sm:gap-5 sm:overflow-visible sm:px-0 lg:grid-cols-3">
      <?php foreach ($similaires as $s): ?><li class="w-[75%] max-w-72 shrink-0 snap-start sm:w-auto sm:max-w-none"><?php partiel('cartes/opportunite', ['o' => $s, 'compacte' => true]) ?></li><?php endforeach ?>
    </ul>
  </div>
</section>
<?php endif ?>

<?php if (!$o['est_expiree'] && $o['lien_candidature']): ?>
<div class="sticky bottom-0 z-30 border-t border-ligne bg-white/95 px-4 py-3 pb-[max(0.75rem,env(safe-area-inset-bottom))] backdrop-blur lg:hidden">
  <div class="mx-auto flex max-w-xl items-center justify-between gap-4">
    <p class="text-xs leading-tight">Date limite<br><strong class="text-sm text-rouge"><?= $o['date_limite'] ? e(date_courte($o['date_limite'])) : 'Aucune' ?></strong></p>
    <a href="<?= e($o['lien_candidature']) ?>" target="_blank" rel="noopener" class="btn btn-orange min-h-12 flex-1 text-base sm:flex-none sm:px-10">Postuler</a>
  </div>
</div>
<?php endif ?>
