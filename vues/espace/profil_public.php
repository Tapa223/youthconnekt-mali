<?php
/* Profil d'un membre, visible des membres connectés (si le membre l'a rendu visible). */
$image = $p['photo'] ?? $p['logo'] ?? '';
$organisation = $m['type_compte'] === 'organisation';
$infos = array_filter([
    libelle_profil($p, $m['type_compte']),
    $organisation ? $p['secteur'] : $p['domaine'],
    trim(($p['ville'] ? $p['ville'] . ', ' : '') . (REGIONS[$p['region']] ?? ''), ', '),
]);
$liens = liens_profil((int) $m['id']);
$mail_public = $p['email_contact_public'] ?? '';
$presentation = trim((string) $p['presentation']);
?>
<section class="bg-gris pt-3 pb-10 sm:pt-6 sm:pb-14">
  <div class="conteneur max-w-4xl">
    <?php partiel('partials/retour', ['url' => $est_moi ? url('/compte/mon-espace') : url('/communaute')]) ?>
    <?php if (!$p['visible']): ?><p class="mb-4 rounded-xl bg-orange/10 px-4 py-2.5 text-sm font-bold text-orange-fonce"><?= $est_moi ? 'Votre profil est masqué : vous seul le voyez. Vous pouvez le rendre visible dans « Mon profil ».' : 'Ce profil est masqué aux autres membres.' ?></p><?php endif ?>

    <div class="overflow-hidden rounded-3xl bg-white">
      <div class="relative h-12 bg-vert sm:h-28"><?php partiel('partials/reseau_deco', ['opacite' => 'opacity-50']) ?></div>
      <div class="px-5 pb-6 sm:px-9 sm:pb-9">
        <div class="relative -mt-8 flex flex-col gap-4 sm:-mt-14 sm:flex-row sm:items-end sm:justify-between">
          <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:gap-4">
            <?php if ($image): ?><img src="<?= e(media($image)) ?>" alt="" class="size-16 shrink-0 rounded-full border-4 border-white sm:size-28 <?= $organisation ? 'bg-white object-contain' : 'object-cover' ?>">
            <?php else: ?><span class="grid size-16 shrink-0 place-items-center rounded-full border-4 border-white bg-orange text-xl font-bold text-white sm:size-28 sm:text-3xl" aria-hidden="true"><?= e($m['initiales']) ?></span><?php endif ?>
            <div class="min-w-0 pb-1">
              <h1 class="text-2xl leading-tight font-bold sm:text-3xl"><?= e($m['nom_affiche']) ?></h1>
              <p class="text-xs text-texte">Membre depuis <?= e(mois_annee($m['inscrit_le'])) ?></p>
            </div>
          </div>
          <?php if ($est_moi): ?><a href="<?= url('/compte/mon-espace/profil') ?>" class="btn-lien min-h-10 self-start sm:self-auto">Modifier mon profil</a><?php endif ?>
        </div>

        <?php if ($infos): ?>
        <ul class="mt-5 flex flex-wrap gap-2">
          <?php foreach ($infos as $info): ?><li class="rounded-full bg-gris px-3.5 py-1.5 text-sm font-bold text-encre"><?= e($info) ?></li><?php endforeach ?>
        </ul>
        <?php endif ?>

        <h2 class="mt-7 text-lg font-bold">À propos</h2>
        <?php if ($presentation !== ''): ?><p class="mt-2 text-[15px] leading-relaxed text-encre sm:text-base"><?= nl2br(e($presentation)) ?></p>
        <?php elseif ($est_moi): ?><p class="mt-2 rounded-xl bg-gris p-4 text-sm">Vous n'avez pas encore écrit de présentation. Quelques lignes sur ce que vous faites et ce que vous cherchez aident les autres membres à vous connaître. <a href="<?= url('/compte/mon-espace/profil') ?>" class="btn-lien mt-3 sm:mt-0 sm:ml-2">Écrire ma présentation</a></p>
        <?php else: ?><p class="mt-2 text-sm text-texte"><?= e($organisation ? $m['nom_affiche'] : $m['prenom_affiche']) ?> n'a pas encore écrit de présentation.</p><?php endif ?>

        <?php if ($liens || $mail_public): ?>
        <ul class="mt-6 flex flex-wrap gap-2">
          <?php foreach ($liens as $l): ?><li><a href="<?= e($l['url']) ?>" target="_blank" rel="noopener nofollow" class="inline-flex min-h-10 items-center gap-2 rounded-full border-2 border-vert px-4 text-sm font-bold text-vert transition-colors hover:border-orange hover:bg-orange hover:text-white"><svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><?= $l['trace'] ?></svg><?= e($l['nom']) ?></a></li><?php endforeach ?>
          <?php if ($mail_public): ?><li><a href="mailto:<?= e($mail_public) ?>" class="btn-lien min-h-10">Écrire à l'organisation</a></li><?php endif ?>
        </ul>
        <?php endif ?>

        <h2 class="mt-7 text-lg font-bold">Communautés</h2>
        <?php if ($communautes): ?>
        <ul class="mt-3 flex flex-wrap gap-2"><?php foreach ($communautes as $c): ?><li><a href="<?= e($c['url']) ?>" class="inline-flex min-h-9 items-center rounded-full bg-vert-clair px-3.5 text-sm font-bold text-vert transition-colors hover:bg-vert hover:text-white"><?= e($c['nom']) ?></a></li><?php endforeach ?></ul>
        <?php else: ?><p class="mt-2 text-sm text-texte"><?= $est_moi ? "Vous n'avez pas encore rejoint de communauté." : "Aucune communauté pour l'instant." ?></p><?php endif ?>
      </div>
    </div>
  </div>
</section>
