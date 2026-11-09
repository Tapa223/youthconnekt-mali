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
        if (action && /supprimer/.test(action.value) && !window.confirm("Supprimer définitivement les éléments sélectionnés ?")) e.preventDefault();
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
    document.querySelectorAll("form[data-confirmer]").forEach(function (form) {
      form.addEventListener("submit", function (e) {
        if (form.dataset.confirme === "1") return;
        e.preventDefault();
        var source = (e.submitter && e.submitter.hasAttribute("data-confirmer")) ? e.submitter : form;
        var question = source.getAttribute("data-confirmer");
        var soumettre = function () { form.dataset.confirme = "1"; if (form.requestSubmit) form.requestSubmit(e.submitter || undefined); else form.submit(); };
        if (!window.HTMLDialogElement) { if (window.confirm(question)) soumettre(); return; }
        var b = fenetre();
        b.querySelector("[data-question]").textContent = question;
        var texte = b.querySelector("[data-texte]");
        texte.textContent = source.getAttribute("data-confirmer-texte") || "";
        texte.hidden = !texte.textContent;
        var oui = b.querySelector("[data-oui]");
        oui.textContent = source.getAttribute("data-confirmer-oui") || "Confirmer";
        oui.className = source.hasAttribute("data-confirmer-danger") ? "ycm-bouton-rouge" : "ycm-bouton-vert";
        oui.onclick = function () { b.close(); soumettre(); };
        b.showModal();
        oui.focus();
      });
    });

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
