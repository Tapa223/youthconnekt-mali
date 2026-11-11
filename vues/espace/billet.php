<?php ob_start();
/* Billet virtuel : à présenter à l'entrée, sur le téléphone ou imprimé. Le QR code contient le code du billet.
   Il reste consultable sans connexion une fois ouvert, et peut devenir une image « Je participe » à partager. */
?>
<section class="bloc billet-zone">
  <article class="billet mx-auto max-w-md overflow-hidden rounded-3xl border-2 border-vert bg-white shadow-sm" aria-label="Billet">
    <div class="bg-vert px-5 py-4 text-white">
      <p class="text-xs font-bold tracking-widest uppercase opacity-80">YouthConnekt Mali · Billet</p>
      <p class="mt-1 text-xl leading-tight font-bold"><?= e($p['titre']) ?></p>
      <?php if ($p['date_libelle'] || $p['lieu']): ?><p class="mt-1 text-sm opacity-90"><?= e(implode(' · ', array_filter([$p['date_libelle'], $p['lieu']]))) ?></p><?php endif ?>
    </div>
    <div class="px-5 py-5 text-center">
      <p class="text-lg font-bold text-vert"><?= e($u['nom_affiche']) ?></p>
      <p class="text-sm">« <?= e($p['role_libelle']) ?> »</p>
      <div class="mx-auto mt-4 w-52 max-w-full" data-qr="<?= e($p['code']) ?>" role="img" aria-label="QR code du billet <?= e($p['code']) ?>"></div>
      <p class="mt-3 font-mono text-2xl font-bold tracking-[0.18em] text-vert"><?= e($p['code']) ?></p>
      <p class="mt-1 text-xs text-texte">À présenter à l'entrée. L'équipe scanne le QR code ou saisit ce code.</p>
      <?php if ($p['present_le']): ?><p class="mt-3 inline-flex rounded-full bg-vert-clair px-3 py-1 text-xs font-bold text-vert">Présence enregistrée le <?= e(date_longue($p['present_le'])) ?></p><?php endif ?>
    </div>
  </article>

  <div class="mx-auto mt-5 grid max-w-md gap-2.5 print:hidden">
    <button type="button" class="btn btn-orange min-h-12" data-image-participe
            data-titre="<?= e($p['titre']) ?>" data-role="<?= e($p['role_libelle']) ?>" data-nom="<?= e($u['nom_affiche']) ?>"
            data-quand="<?= e(implode(' · ', array_filter([$p['date_libelle'], $p['lieu']]))) ?>" data-logo="<?= e(asset('img/logo-youthconnekt-mali.png')) ?>"
            data-fichier="je-participe-<?= e($p['slug']) ?>.png">Partager « Je participe »</button>
    <div class="grid grid-cols-2 gap-2.5">
      <?php if (!empty($p['date_debut']) && $p['etat'] !== 'terminee'): ?>
      <a href="<?= url('/initiatives/' . $p['slug'] . '/agenda') ?>" class="btn btn-contour min-h-11 px-3 text-sm" download>Ajouter à l'agenda</a>
      <?php endif ?>
      <button type="button" class="btn btn-contour min-h-11 px-3 text-sm<?= empty($p['date_debut']) || $p['etat'] === 'terminee' ? ' col-span-2' : '' ?>" data-imprimer>Imprimer le billet</button>
    </div>
    <p class="text-center text-xs text-texte" data-hors-ligne-info>Une fois ouvert, ce billet reste consultable même sans connexion sur cet appareil.</p>
  </div>

  <dialog class="dialogue m-auto w-[min(26rem,calc(100vw-2rem))] rounded-3xl border-0 bg-white p-0 shadow-2xl" data-apercu-participe>
    <div class="p-4">
      <img alt="Aperçu de l'image « Je participe »" class="w-full rounded-2xl" data-apercu-image>
      <div class="mt-4 grid grid-cols-2 gap-2">
        <a class="btn btn-contour min-h-11" data-apercu-telecharger download>Télécharger</a>
        <button type="button" class="btn btn-orange min-h-11" data-apercu-partager>Partager</button>
      </div>
      <button type="button" class="mt-2 w-full rounded-lg py-2 text-sm font-bold text-texte hover:bg-gris" data-apercu-fermer>Fermer</button>
    </div>
  </dialog>
</section>
<script src="<?= e(asset('js/vendor/qrcode.js')) ?>"></script>
<script src="<?= e(asset('js/billet.js')) ?>"></script>
<?php $espace = ob_get_clean(); partiel('espace/base', get_defined_vars());
