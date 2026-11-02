<?php $image = $p['photo'] ?? $p['logo'] ?? ''; $organisation = $m['type_compte'] === 'organisation'; ?>
<section class="bg-gris py-10 sm:py-14">
  <div class="conteneur max-w-3xl">
    <div class="rounded-3xl bg-white p-6 sm:p-9">
      <?php if (!$p['visible']): ?><p class="mb-5 rounded-lg bg-orange/10 px-4 py-2 text-sm font-bold text-orange-fonce"><?= $est_moi ? 'Votre profil est masqué : seuls vous le voyez.' : 'Ce profil est masqué aux autres membres.' ?></p><?php endif ?>
      <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
        <?php if ($image): ?><img src="<?= e(media($image)) ?>" alt="" class="size-24 rounded-full <?= $organisation ? 'bg-white object-contain ring-1 ring-ligne' : 'object-cover' ?>">
        <?php else: ?><span class="grid size-24 place-items-center rounded-full bg-orange text-3xl font-bold text-white" aria-hidden="true"><?= e($m['initiales']) ?></span><?php endif ?>
        <div>
          <h1 class="text-3xl font-bold"><?= e($m['nom_affiche']) ?></h1>
          <p class="mt-1"><?= e(libelle_profil($p, $m['type_compte'])) ?><?php $d = $organisation ? $p['secteur'] : $p['domaine']; if ($d): ?> · <?= e($d) ?><?php endif ?></p>
          <p class="text-sm"><?= $p['ville'] ? e($p['ville']) . ', ' : '' ?><?= e(REGIONS[$p['region']] ?? '') ?></p>
        </div>
      </div>
      <?php if (trim((string) $p['presentation']) !== ''): ?><p class="mt-6 text-base leading-relaxed text-encre"><?= nl2br(e($p['presentation'])) ?></p><?php endif ?>
      <?php $liens = array_filter(['Site web' => $p['site_web'], 'LinkedIn' => $p['lien_linkedin']]); $mail_public = $p['email_contact_public'] ?? ''; ?>
      <?php if ($liens || $mail_public): ?>
      <ul class="mt-6 flex flex-wrap gap-2">
        <?php foreach ($liens as $nom => $lien): ?><li><a href="<?= e($lien) ?>" target="_blank" rel="noopener nofollow" class="btn btn-contour min-h-10 px-4 text-sm"><?= $nom ?></a></li><?php endforeach ?>
        <?php if ($mail_public): ?><li><a href="mailto:<?= e($mail_public) ?>" class="btn btn-contour min-h-10 px-4 text-sm">Écrire à l'organisation</a></li><?php endif ?>
      </ul>
      <?php endif ?>
      <?php if ($communautes): ?>
      <h2 class="mt-8 text-lg font-bold">Communautés</h2>
      <ul class="mt-3 flex flex-wrap gap-2"><?php foreach ($communautes as $c): ?><li><a href="<?= e($c['url']) ?>" class="inline-flex min-h-9 items-center rounded-full bg-vert-clair px-3.5 text-sm font-bold text-vert"><?= e($c['nom']) ?></a></li><?php endforeach ?></ul>
      <?php endif ?>
      <?php if ($est_moi): ?><a href="<?= url('/compte/mon-espace/profil') ?>" class="btn btn-vert mt-8">Modifier mon profil</a><?php endif ?>
    </div>
  </div>
</section>
