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
      });
    });

    // ---- Confirmation avant une action irréversible
    document.querySelectorAll("form[data-confirmer]").forEach(function (form) {
      form.addEventListener("submit", function (e) {
        if (!window.confirm(form.getAttribute("data-confirmer"))) e.preventDefault();
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
