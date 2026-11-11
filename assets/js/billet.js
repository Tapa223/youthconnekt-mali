/* Billet : QR code, impression, image « Je participe » à partager, consultation sans connexion */
(function () {
  "use strict";
  var zone = document.querySelector("[data-qr]");
  if (zone && window.qrcode) {
    var qr = qrcode(0, "M");
    qr.addData(zone.getAttribute("data-qr"));
    qr.make();
    zone.innerHTML = qr.createSvgTag({ cellSize: 6, margin: 2, scalable: true });
    var svg = zone.querySelector("svg");
    if (svg) { svg.setAttribute("width", "100%"); svg.removeAttribute("height"); svg.setAttribute("aria-hidden", "true"); }
  }
  var imprimer = document.querySelector("[data-imprimer]");
  if (imprimer) imprimer.addEventListener("click", function () { window.print(); });

  // Le billet reste disponible hors connexion : un petit service le garde en mémoire sur l'appareil
  if ("serviceWorker" in navigator && window.isSecureContext) {
    var base = (document.querySelector('link[rel="icon"]') || {}).href || "";
    var racine = base.split("/assets/")[0];
    if (racine) navigator.serviceWorker.register(racine + "/sw.js", { scope: racine + "/" }).catch(function () {});
  } else {
    var info = document.querySelector("[data-hors-ligne-info]");
    if (info) info.hidden = true;
  }

  // Image « Je participe » : 1080 × 1350, aux couleurs du chapitre
  var bouton = document.querySelector("[data-image-participe]");
  var apercu = document.querySelector("[data-apercu-participe]");
  if (!bouton || !apercu || !window.HTMLCanvasElement) return;
  var lignes = function (ctx, texte, largeur) {
    var mots = texte.split(" "), res = [], ligne = "";
    mots.forEach(function (m) {
      var essai = ligne ? ligne + " " + m : m;
      if (ctx.measureText(essai).width > largeur && ligne) { res.push(ligne); ligne = m; } else ligne = essai;
    });
    if (ligne) res.push(ligne);
    return res;
  };
  var dessiner = function (logo) {
    var c = document.createElement("canvas"); c.width = 1080; c.height = 1350;
    var x = c.getContext("2d"), d = bouton.dataset, police = '"Ubuntu", system-ui, sans-serif';
    x.fillStyle = "#035E4D"; x.fillRect(0, 0, 1080, 1350);
    // Réseau de points et de fils
    var pts = [[90, 160], [300, 90], [520, 210], [760, 110], [980, 230], [880, 470], [1000, 760], [990, 1010], [120, 1120], [430, 1240], [760, 1280]];
    x.strokeStyle = "rgba(255,255,255,.18)"; x.lineWidth = 3;
    [[0, 1], [1, 2], [2, 3], [3, 4], [4, 5], [5, 6], [7, 10], [8, 9], [9, 10]].forEach(function (l) { x.beginPath(); x.moveTo(pts[l[0]][0], pts[l[0]][1]); x.lineTo(pts[l[1]][0], pts[l[1]][1]); x.stroke(); });
    var couleurs = ["#F1592A", "#04B2B1", "#ffffff", "#0358D9"];
    pts.forEach(function (p, k) { x.beginPath(); x.arc(p[0], p[1], k % 3 ? 10 : 15, 0, Math.PI * 2); x.fillStyle = couleurs[k % 4]; x.fill(); });
    // Pastille « Je participe »
    x.fillStyle = "#F1592A"; x.beginPath(); x.roundRect(90, 360, 470, 96, 48); x.fill();
    x.fillStyle = "#fff"; x.font = "700 52px " + police; x.textBaseline = "middle"; x.fillText("Je participe", 130, 410);
    // Titre de l'initiative
    x.textBaseline = "alphabetic"; x.font = "700 92px " + police;
    var y = 590;
    lignes(x, d.titre, 900).slice(0, 4).forEach(function (l) { x.fillText(l, 90, y); y += 104; });
    x.font = "500 44px " + police; x.fillStyle = "rgba(255,255,255,.9)";
    if (d.quand) { x.fillText(d.quand, 90, y + 10); y += 70; }
    x.fillStyle = "#04B2B1"; x.fillRect(90, y + 20, 120, 8);
    x.fillStyle = "#fff"; x.font = "700 50px " + police; x.fillText(d.nom, 90, y + 110);
    x.font = "400 40px " + police; x.fillStyle = "rgba(255,255,255,.85)"; x.fillText("comme « " + d.role + " »", 90, y + 170);
    // Bas : logo sur fond blanc
    x.fillStyle = "#fff"; x.fillRect(0, 1190, 1080, 160);
    if (logo) { var h = 100, w = logo.width * h / logo.height; x.drawImage(logo, 90, 1220, w, h); }
    x.fillStyle = "#035E4D"; x.font = "700 40px " + police; x.textAlign = "right"; x.fillText("Let's Connekt", 990, 1285);
    return c;
  };
  var fichier = null;
  var ouvrir = function () {
    var logo = new Image();
    var suite = function (img) {
      var c = dessiner(img);
      var url = c.toDataURL("image/png");
      apercu.querySelector("[data-apercu-image]").src = url;
      var tel = apercu.querySelector("[data-apercu-telecharger]"); tel.href = url; tel.download = bouton.dataset.fichier;
      c.toBlob(function (b) { fichier = b ? new File([b], bouton.dataset.fichier, { type: "image/png" }) : null; });
      apercu.showModal();
    };
    logo.onload = function () { suite(logo); };
    logo.onerror = function () { suite(null); };
    logo.src = bouton.dataset.logo;
  };
  bouton.addEventListener("click", function () { (document.fonts ? document.fonts.ready : Promise.resolve()).then(ouvrir); });
  apercu.querySelector("[data-apercu-fermer]").addEventListener("click", function () { apercu.close(); });
  var partager = apercu.querySelector("[data-apercu-partager]");
  partager.addEventListener("click", function () {
    var donnees = { files: fichier ? [fichier] : [], title: "Je participe : " + bouton.dataset.titre, text: "Je participe à « " + bouton.dataset.titre + " » avec YouthConnekt Mali. Let's Connekt !" };
    if (fichier && navigator.canShare && navigator.canShare({ files: [fichier] })) navigator.share(donnees).catch(function () {});
    else apercu.querySelector("[data-apercu-telecharger]").click();
  });
})();
