<?php ob_start();
/* Billet virtuel : à présenter à l'entrée, sur le téléphone ou imprimé. Le QR code contient le code du billet. */
?>
<section class="bloc billet-zone">
  <div class="flex justify-end print:hidden">
    <button type="button" class="btn btn-contour min-h-10 px-4 text-sm" data-imprimer>Imprimer le billet</button>
  </div>

  <article class="billet mx-auto mt-4 max-w-md overflow-hidden rounded-3xl border-2 border-vert bg-white shadow-sm" aria-label="Billet">
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
</section>
<script src="<?= e(asset('js/vendor/qrcode.js')) ?>"></script>
<script>
(function () {
  var zone = document.querySelector("[data-qr]");
  if (zone && window.qrcode) {
    var qr = qrcode(0, "M");
    qr.addData(zone.getAttribute("data-qr"));
    qr.make();
    zone.innerHTML = qr.createSvgTag({ cellSize: 6, margin: 2, scalable: true });
    var svg = zone.querySelector("svg");
    if (svg) { svg.setAttribute("width", "100%"); svg.removeAttribute("height"); svg.setAttribute("aria-hidden", "true"); }
  }
  var b = document.querySelector("[data-imprimer]");
  if (b) b.addEventListener("click", function () { window.print(); });
})();
</script>
<?php $espace = ob_get_clean(); partiel('espace/base', get_defined_vars());
