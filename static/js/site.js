/* YouthConnekt Mali : interactions de l'interface (sans dépendance). */
(function () {
  "use strict";

  // En-tête : ombre légère dès que la page défile
  var entete = document.querySelector("[data-entete]");
  if (entete) {
    var majEntete = function () {
      if (window.scrollY > 8) entete.setAttribute("data-defile", "");
      else entete.removeAttribute("data-defile");
    };
    majEntete();
    window.addEventListener("scroll", majEntete, { passive: true });
  }

  // Menu mobile
  var menu = document.querySelector("[data-menu-mobile]");
  var boutonOuvrir = document.querySelector("[data-ouvrir-menu]");
  if (menu && boutonOuvrir) {
    var focusables = function () {
      return menu.querySelectorAll("a[href], button:not([disabled])");
    };
    var ouvrir = function () {
      menu.classList.remove("hidden");
      document.documentElement.style.overflow = "hidden";
      boutonOuvrir.setAttribute("aria-expanded", "true");
      var liens = focusables();
      if (liens.length) liens[0].focus();
    };
    var fermer = function () {
      menu.classList.add("hidden");
      document.documentElement.style.overflow = "";
      boutonOuvrir.setAttribute("aria-expanded", "false");
      boutonOuvrir.focus();
    };
    boutonOuvrir.addEventListener("click", ouvrir);
    menu.querySelectorAll("[data-fermer-menu]").forEach(function (el) {
      el.addEventListener("click", fermer);
    });
    document.addEventListener("keydown", function (e) {
      if (menu.classList.contains("hidden")) return;
      if (e.key === "Escape") fermer();
      // Garder le focus clavier à l'intérieur du menu ouvert
      if (e.key === "Tab") {
        var liens = focusables();
        var premier = liens[0], dernier = liens[liens.length - 1];
        if (e.shiftKey && document.activeElement === premier) { e.preventDefault(); dernier.focus(); }
        else if (!e.shiftKey && document.activeElement === dernier) { e.preventDefault(); premier.focus(); }
      }
    });
    // Le menu se referme si l'écran passe en format ordinateur
    window.matchMedia("(min-width: 1280px)").addEventListener("change", function (mq) {
      if (mq.matches && !menu.classList.contains("hidden")) fermer();
    });
  }

  // Messages de confirmation : fermeture manuelle et disparition automatique
  document.querySelectorAll("[data-fermer-message]").forEach(function (bouton) {
    bouton.addEventListener("click", function () { bouton.parentElement.remove(); });
  });
  var zoneMessages = document.querySelector("[data-messages]");
  if (zoneMessages) {
    setTimeout(function () { zoneMessages.remove(); }, 7000);
  }
})();

/* Partage et copie du lien de la page */
(function () {
  "use strict";
  var annoncer = function (bouton, texte) {
    var initial = bouton.textContent;
    bouton.textContent = texte;
    setTimeout(function () { bouton.textContent = initial; }, 2000);
  };
  var copier = function (bouton) {
    var url = window.location.href.split("#")[0];
    if (navigator.clipboard && window.isSecureContext) {
      navigator.clipboard.writeText(url).then(function () { annoncer(bouton, "Lien copié"); });
    } else {
      var champ = document.createElement("textarea");
      champ.value = url; document.body.appendChild(champ); champ.select();
      try { document.execCommand("copy"); annoncer(bouton, "Lien copié"); } catch (e) {}
      champ.remove();
    }
  };
  document.querySelectorAll("[data-copier-lien]").forEach(function (b) {
    b.addEventListener("click", function () { copier(b); });
  });
  document.querySelectorAll("[data-partager]").forEach(function (b) {
    b.addEventListener("click", function () {
      if (navigator.share) {
        navigator.share({ title: b.dataset.titre || document.title, url: window.location.href.split("#")[0] }).catch(function () {});
      } else {
        copier(b);
      }
    });
  });
  // Afficher ou masquer un mot de passe
  document.querySelectorAll("[data-afficher-mdp]").forEach(function (b) {
    b.addEventListener("click", function () {
      var champ = document.getElementById(b.getAttribute("aria-controls"));
      if (!champ) return;
      var visible = champ.type === "text";
      champ.type = visible ? "password" : "text";
      b.textContent = visible ? "Afficher" : "Masquer";
      b.setAttribute("aria-pressed", String(!visible));
    });
  });
})();

/* Confirmation avant une action sensible (quitter une communauté, supprimer son compte) */
document.querySelectorAll("form[data-confirmer]").forEach(function (form) {
  form.addEventListener("submit", function (e) {
    if (!window.confirm(form.getAttribute("data-confirmer"))) e.preventDefault();
  });
});
