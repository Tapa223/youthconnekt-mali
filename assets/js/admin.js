/* Administration YouthConnekt Mali : menu sur téléphone, filtres, cases à cocher, barre d'outils de texte. */
(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", function () {
    // ---- Menu latéral sur téléphone
    var corps = document.body;
    var voile = document.querySelector(".ycm-voile");
    var bouton = document.querySelector("[data-ouvrir-menu-admin]");
    function ouvrir() {
      corps.classList.add("ycm-menu-ouvert");
      if (voile) voile.hidden = false;
      if (bouton) bouton.setAttribute("aria-expanded", "true");
    }
    function fermer() {
      corps.classList.remove("ycm-menu-ouvert");
      if (voile) voile.hidden = true;
      if (bouton) bouton.setAttribute("aria-expanded", "false");
    }
    if (bouton) bouton.addEventListener("click", ouvrir);
    document.querySelectorAll("[data-fermer-menu-admin]").forEach(function (el) {
      el.addEventListener("click", fermer);
    });
    document.addEventListener("keydown", function (e) { if (e.key === "Escape") fermer(); });

    // ---- Listes déroulantes de filtre : on change de page dès qu'on choisit
    document.querySelectorAll("select[data-filtre]").forEach(function (select) {
      select.addEventListener("change", function () { window.location = select.value; });
    });

    // ---- Cases à cocher des listes : « tout sélectionner » et compteur
    document.querySelectorAll("form[data-liste]").forEach(function (form) {
      var tout = form.querySelector("[data-tout-cocher]");
      var cases = form.querySelectorAll("input[name='ids[]']");
      var compteur = form.querySelector("[data-compte-selection]");
      function maj() {
        var n = form.querySelectorAll("input[name='ids[]']:checked").length;
        if (compteur) compteur.textContent = n + " sur " + cases.length + " sélectionné" + (n > 1 ? "s" : "");
        if (tout) tout.checked = n > 0 && n === cases.length;
      }
      if (tout) tout.addEventListener("change", function () {
        cases.forEach(function (c) { c.checked = tout.checked; });
        maj();
      });
      cases.forEach(function (c) { c.addEventListener("change", maj); });
      form.addEventListener("submit", function (e) {
        var action = form.querySelector("select[name=action]");
        // Rien de choisi : ni bouton d'action rapide, ni action dans la liste
        if (action && !action.value && !(e.submitter && e.submitter.name === "action")) { e.preventDefault(); action.focus(); }
        if (!form.querySelector("input[name='ids[]']:checked")) { e.preventDefault(); }
      });
    });

    // ---- Mode sélection des listes : le menu « ⋯ » > « Sélectionner » fait apparaître les cases et les actions
    var listeSel = document.querySelector("form[data-liste]");
    document.querySelectorAll("[data-basculer-selection]").forEach(function (bouton) {
      bouton.addEventListener("click", function () {
        if (!listeSel) return;
        var actif = listeSel.classList.toggle("ycm-selection-active");
        document.querySelectorAll(".ycm-plus [data-basculer-selection]").forEach(function (b) { b.textContent = actif ? "Annuler la sélection" : "Sélectionner"; });
        var menu = bouton.closest("details"); if (menu) menu.open = false;
        if (!actif) listeSel.querySelectorAll("input[type=checkbox]").forEach(function (c) { if (c.checked) { c.checked = false; c.dispatchEvent(new Event("change")); } });
      });
    });
    // Menu « ⋯ » : se referme quand on clique ailleurs
    document.addEventListener("click", function (e) {
      document.querySelectorAll("details.ycm-plus[open]").forEach(function (d) { if (!d.contains(e.target)) d.open = false; });
    });

    // ---- Newsletter : communautés selon la cible, nombre de destinataires en direct,
    //      test et envoi bloqués tant que les changements ne sont pas enregistrés
    document.querySelectorAll("form[data-newsletter]").forEach(function (form) {
      var bloc = form.querySelector("[data-communautes]");
      var nombre = form.querySelector("[data-nombre]");
      var pluriel = form.querySelector("[data-pluriel]");
      var compter = function () {
        var cible = form.querySelector("[data-cible]:checked");
        if (!cible) return;
        if (bloc) bloc.hidden = cible.value !== "communautes";
        var p = new URLSearchParams({ cible: cible.value });
        form.querySelectorAll("[data-communaute]:checked").forEach(function (c) { p.append("communautes[]", c.value); });
        fetch(form.getAttribute("data-compter") + "?" + p.toString(), { headers: { "X-Requested-With": "fetch" } })
          .then(function (r) { return r.json(); })
          .then(function (d) { if (nombre) { nombre.textContent = d.nombre; pluriel.textContent = d.nombre > 1 ? "s" : ""; } })
          .catch(function () {});
      };
      form.querySelectorAll("[data-cible], [data-communaute]").forEach(function (c) { c.addEventListener("change", compter); });
      var avertir = function () {
        var zone = document.querySelector(".ycm-nl-actions");
        if (!zone || zone.dataset.modifie) return;
        zone.dataset.modifie = "1";
        zone.querySelectorAll("button, input").forEach(function (b) { b.disabled = true; });
        var note = document.createElement("p");
        note.className = "ycm-alerte";
        note.textContent = "Vous avez modifié la newsletter : enregistrez le brouillon pour mettre à jour l'aperçu, puis tester et envoyer.";
        zone.insertBefore(note, zone.children[1] || null);
      };
      form.addEventListener("input", avertir);
      form.addEventListener("change", avertir);
    });

    // ---- Participations : rôles (chacun avec ses questions) ajoutés, retirés et ordonnés par l'administrateur
    var compteurLignes = 0;
    var aMoi = function (ligne, el) { return el && el.closest("[data-ligne]") === ligne; };
    var premier = function (ligne, sel) {
      var tous = ligne.querySelectorAll(sel);
      for (var k = 0; k < tous.length; k++) if (aMoi(ligne, tous[k])) return tous[k];
      return null;
    };
    var brancherLigne = function (ligne) {
      if (ligne.hasAttribute("data-branche")) return;
      ligne.setAttribute("data-branche", "");
      var type = premier(ligne, "[data-type-champ]"), opts = premier(ligne, "[data-options]");
      if (type && opts) type.addEventListener("change", function () { opts.hidden = !/^(choix|cases)$/.test(type.value); });
      var m = premier(ligne, "[data-monter]"), dsc = premier(ligne, "[data-descendre]"), r = premier(ligne, "[data-retirer]");
      if (m) m.addEventListener("click", function () { var p = ligne.previousElementSibling; if (p) ligne.parentNode.insertBefore(ligne, p); });
      if (dsc) dsc.addEventListener("click", function () { var n = ligne.nextElementSibling; if (n) ligne.parentNode.insertBefore(n, ligne); });
      if (r) r.addEventListener("click", function () {
        var q = r.getAttribute("data-retirer-question");
        if (q && ligne.querySelector("[data-ligne]") && !window.confirm(q)) return;
        ligne.remove();
      });
    };
    document.querySelectorAll("[data-ligne]").forEach(brancherLigne);
    document.addEventListener("click", function (e) {
      var bouton = e.target.closest ? e.target.closest("[data-ajouter]") : null;
      if (!bouton) return;
      var nom = bouton.getAttribute("data-ajouter");
      var modele = document.querySelector('template[data-modele="' + nom + '"]');
      var role = bouton.closest("[data-role-cle]");
      var zone = nom === "champs" && role ? role.querySelector('[data-lignes="champs"]') : document.querySelector('[data-lignes="' + nom + '"]');
      if (!modele || !zone) return;
      var cle = "n" + Date.now() + (compteurLignes++);
      var html = modele.innerHTML.replace(/__role__/g, nom === "champs" && role ? role.getAttribute("data-role-cle") : cle).replace(/__cle__/g, cle);
      var tmp = document.createElement("div");
      tmp.innerHTML = html.trim();
      var ligne = tmp.firstElementChild;
      zone.appendChild(ligne);
      ligne.querySelectorAll("[data-ligne]").forEach(brancherLigne);
      brancherLigne(ligne);
      if (role) { var vide = role.querySelector("[data-aucune-question]"); if (vide && nom === "champs") vide.remove(); }
      var champ = ligne.querySelector("input[type=text]");
      if (champ) champ.focus();
    });

    // ---- Listes déroulantes qui s'appliquent dès qu'on choisit (profil d'un témoignage)
    document.querySelectorAll("select[data-envoi-auto]").forEach(function (select) {
      select.addEventListener("change", function () { var f = select.form; if (f) { if (f.requestSubmit) f.requestSubmit(); else f.submit(); } });
    });

    // ---- Confirmation avant d'enregistrer ou de supprimer : petite fenêtre « Annuler / Confirmer »
    var boite = null;
    function fenetre() {
      if (boite) return boite;
      boite = document.createElement("dialog");
      boite.className = "ycm-confirmation";
      boite.innerHTML = '<p class="ycm-confirmation-question" data-question></p><p class="ycm-confirmation-texte" data-texte hidden></p>'
        + '<div class="ycm-confirmation-boutons"><button type="button" class="ycm-bouton-gris" data-non>Annuler</button><button type="button" class="ycm-bouton-vert" data-oui>Confirmer</button></div>';
      document.body.appendChild(boite);
      boite.querySelector("[data-non]").addEventListener("click", function () { boite.close(); });
      boite.addEventListener("click", function (e) { if (e.target === boite) boite.close(); });
      return boite;
    }
    // Une seule écoute pour toute la page : le bouton cliqué, puis le formulaire, portent la question ;
    // les listes (actions groupées) construisent la leur. Rien ne part sans « Confirmer ».
    document.addEventListener("submit", function (e) {
      var form = e.target;
      if (!(form instanceof HTMLFormElement) || form.dataset.confirme === "1") return;
      var sub = e.submitter || null;
      var q = null, texte = "", libelleOui = "Confirmer", danger = false;
      var source = (sub && sub.hasAttribute("data-confirmer")) ? sub : (form.hasAttribute("data-confirmer") ? form : null);
      if (source) {
        q = source.getAttribute("data-confirmer");
        texte = source.getAttribute("data-confirmer-texte") || "";
        libelleOui = source.getAttribute("data-confirmer-oui") || "Confirmer";
        danger = source.hasAttribute("data-confirmer-danger");
      } else if (form.hasAttribute("data-liste")) {
        var n = form.querySelectorAll("input[name='ids[]']:checked").length;
        var choix = (sub && sub.name === "action") ? sub : form.querySelector("select[name=action]");
        if (!n || !choix || !choix.value) return; // le contrôle de la liste s'en charge
        var libelle = choix.tagName === "SELECT" ? choix.options[choix.selectedIndex].text : choix.textContent.trim();
        q = libelle + " : " + n + " élément" + (n > 1 ? "s" : "") + " sélectionné" + (n > 1 ? "s" : "") + ". Confirmer ?";
        libelleOui = libelle;
        danger = /supprimer|retirer|désactiver|refuser|désabonner/i.test(choix.value + " " + libelle);
        texte = /supprimer/i.test(choix.value) ? "Cette action est irréversible." : "";
      }
      if (!q) return;
      e.preventDefault();
      e.stopImmediatePropagation();
      var soumettre = function () {
        form.dataset.confirme = "1";
        if (form.requestSubmit) form.requestSubmit(sub || undefined); else form.submit();
        setTimeout(function () { delete form.dataset.confirme; }, 0);
      };
      if (!window.HTMLDialogElement) { if (window.confirm(q)) soumettre(); return; }
      var b = fenetre();
      b.querySelector("[data-question]").textContent = q;
      var t = b.querySelector("[data-texte]");
      t.textContent = texte;
      t.hidden = !texte;
      var oui = b.querySelector("[data-oui]");
      oui.textContent = libelleOui;
      oui.className = danger ? "ycm-bouton-rouge" : "ycm-bouton-vert";
      oui.onclick = function () { b.close(); soumettre(); };
      b.showModal();
      oui.focus();
    }, true);

    // ---- Barre d'outils au-dessus des zones de texte (mise en forme simple du site)
    document.querySelectorAll("textarea[data-barre]").forEach(function (zone) {
      var outils = [["Intertitre", "## "], ["Liste", "- "], ["Citation", "> "]];
      if (zone.dataset.barre === "article") outils.push(["Photos", "[photos]"]);
      var barre = document.createElement("div");
      barre.className = "ycm-barre-texte";
      outils.forEach(function (o) {
        var b = document.createElement("button");
        b.type = "button";
        b.textContent = o[0];
        b.addEventListener("click", function () {
          var debut = zone.value.lastIndexOf("\n", zone.selectionStart - 1) + 1;
          var insertion = o[1] === "[photos]" ? "\n[photos]\n" : o[1];
          var position = o[1] === "[photos]" ? zone.selectionStart : debut;
          zone.value = zone.value.slice(0, position) + insertion + zone.value.slice(position);
          zone.focus();
          zone.selectionStart = zone.selectionEnd = position + insertion.length;
        });
        barre.appendChild(b);
      });
      zone.parentNode.insertBefore(barre, zone);
      zone.classList.add("ycm-avec-barre");
    });

    // ---- Aperçu immédiat d'une image choisie
    document.querySelectorAll(".ycm-zone-image input[type=file]:not([multiple])").forEach(function (champ) {
      champ.addEventListener("change", function () {
        var fichier = champ.files && champ.files[0];
        if (!fichier || !/^image\//.test(fichier.type)) return;
        var zone = champ.closest(".ycm-zone-image");
        var apercu = zone.querySelector("img.ycm-apercu");
        if (!apercu) {
          apercu = document.createElement("img");
          apercu.className = "ycm-apercu";
          apercu.alt = "";
          zone.insertBefore(apercu, zone.firstChild);
        }
        apercu.src = URL.createObjectURL(fichier);
      });
    });
  });
})();

/* Garder la position dans la page : après un filtre, un onglet, un enregistrement ou un bouton
   qui ramène sur la même page, on revient exactement là où l'on était au lieu de remonter en haut. */
(function () {
  "use strict";
  var CLE = "ycm-defilement";
  function memoriser() {
    try { sessionStorage.setItem(CLE, JSON.stringify({ p: location.pathname, y: window.scrollY, t: Date.now() })); } catch (e) {}
  }
  window.ycmMemoriserDefilement = memoriser;
  try {
    var d = JSON.parse(sessionStorage.getItem(CLE) || "null");
    sessionStorage.removeItem(CLE);
    if (d && d.p === location.pathname && Date.now() - d.t < 20000 && !location.hash) {
      if ("scrollRestoration" in history) history.scrollRestoration = "manual";
      var revenir = function () { window.scrollTo(0, d.y); };
      revenir();
      document.addEventListener("DOMContentLoaded", revenir);
      window.addEventListener("load", revenir);
    }
  } catch (e) {}
  document.addEventListener("click", function (e) {
    var lien = e.target.closest && e.target.closest("a[href]");
    if (!lien || lien.target === "_blank" || lien.hasAttribute("download")) return;
    try {
      var cible = new URL(lien.href, location.href);
      if (cible.origin === location.origin && cible.pathname === location.pathname && !cible.hash) memoriser();
    } catch (err) {}
  });
  document.addEventListener("submit", function (e) {
    if (!e.defaultPrevented) memoriser();
  });
  document.addEventListener("change", function (e) {
    if (e.target.matches && e.target.matches("select[data-filtre]")) memoriser();
  }, true);
})();

/* Pointage : lecture du QR code des billets avec la caméra (téléphone ou ordinateur) */
(function () {
  "use strict";
  var bouton = document.querySelector("[data-scanner]");
  if (!bouton) return;
  var form = bouton.closest("form"), zone = form.querySelector("[data-camera]"), video = zone.querySelector("video");
  var etat = zone.querySelector("[data-camera-etat]"), champ = form.querySelector("[data-code-billet]");
  var flux = null, actif = false, detecteur = null, toile = document.createElement("canvas"), ctx = toile.getContext("2d", { willReadFrequently: true });
  if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) { bouton.hidden = true; return; }
  var arreter = function () {
    actif = false;
    if (flux) flux.getTracks().forEach(function (t) { t.stop(); });
    flux = null; zone.hidden = true; bouton.hidden = false;
  };
  var trouve = function (texte) {
    var m = /YCM-[A-Z0-9]{5}/i.exec(texte || "");
    if (!m) return false;
    champ.value = m[0].toUpperCase();
    etat.textContent = "Billet " + champ.value + " lu : pointage en cours…";
    arreter();
    form.dataset.confirme = "1";
    if (form.requestSubmit) form.requestSubmit(); else form.submit();
    return true;
  };
  var lire = function () {
    if (!actif) return;
    if (video.readyState < 2) { requestAnimationFrame(lire); return; }
    if (detecteur) {
      detecteur.detect(video).then(function (codes) { if (!(codes[0] && trouve(codes[0].rawValue))) setTimeout(lire, 180); }).catch(function () { setTimeout(lire, 300); });
      return;
    }
    toile.width = video.videoWidth; toile.height = video.videoHeight;
    ctx.drawImage(video, 0, 0);
    var img = ctx.getImageData(0, 0, toile.width, toile.height);
    var res = window.jsQR ? window.jsQR(img.data, img.width, img.height, { inversionAttempts: "dontInvert" }) : null;
    if (!(res && trouve(res.data))) setTimeout(lire, 200);
  };
  var charger = function () {
    return new Promise(function (ok) {
      if ("BarcodeDetector" in window) {
        try { detecteur = new window.BarcodeDetector({ formats: ["qr_code"] }); return ok(); } catch (e) { detecteur = null; }
      }
      if (window.jsQR) return ok();
      var s = document.createElement("script"); s.src = bouton.getAttribute("data-jsqr"); s.onload = ok; s.onerror = ok; document.head.appendChild(s);
    });
  };
  bouton.addEventListener("click", function () {
    zone.hidden = false; bouton.hidden = true; etat.textContent = "Ouverture de la caméra…";
    charger().then(function () {
      return navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" }, audio: false });
    }).then(function (f) {
      flux = f; video.srcObject = f; video.play(); actif = true;
      etat.textContent = "Placez le QR code du billet dans le cadre.";
      lire();
    }).catch(function () {
      etat.textContent = "La caméra n'est pas accessible : autorisez-la dans le navigateur (le site doit être en https), ou saisissez le code.";
    });
  });
  zone.querySelector("[data-camera-fermer]").addEventListener("click", arreter);
  window.addEventListener("pagehide", arreter);
})();
