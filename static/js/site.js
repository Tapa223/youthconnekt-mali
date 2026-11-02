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

/* ------------------------------------------------------------------------
   Animations de la maquette : diaporamas et compteurs
   ------------------------------------------------------------------------ */
(function () {
  "use strict";
  var calme = window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  // Diaporamas : toutes les images d'un même groupe changent ensemble toutes les 5 s
  var groupes = {};
  document.querySelectorAll("[data-diapo]").forEach(function (bloc) {
    var nom = bloc.getAttribute("data-diapo") || "defaut";
    (groupes[nom] = groupes[nom] || []).push(bloc);
  });
  Object.keys(groupes).forEach(function (nom) {
    var blocs = groupes[nom];
    var total = blocs[0].querySelectorAll("[data-diapo-image]").length;
    if (total < 2) return;
    var index = 0, minuteur = null;
    var afficher = function (i) {
      blocs.forEach(function (bloc) {
        bloc.querySelectorAll("[data-diapo-image]").forEach(function (img, k) {
          img.classList.toggle("active", k === i);
        });
      });
    };
    var demarrer = function () {
      if (calme) return;
      clearInterval(minuteur);
      minuteur = setInterval(function () { index = (index + 1) % total; afficher(index); }, 5000);
    };
    afficher(0);
    demarrer();
    blocs.forEach(function (bloc) {
      var zone = bloc.closest("[data-diapo-pause]") || bloc;
      zone.addEventListener("mouseenter", function () { clearInterval(minuteur); });
      zone.addEventListener("mouseleave", demarrer);
    });
    document.addEventListener("visibilitychange", function () {
      if (document.hidden) clearInterval(minuteur); else demarrer();
    });
  });

  // Compteurs : le nombre monte de 0 à sa valeur quand il apparaît à l'écran.
  // La valeur saisie peut contenir un suffixe (« 2 500+ », « 15 % ») : il est conservé.
  var compteurs = document.querySelectorAll("[data-compteur]");
  if (!compteurs.length || calme || !("IntersectionObserver" in window)) return;
  var format = function (n) { return Math.round(n).toLocaleString("fr-FR").replace(/ | /g, " "); };
  compteurs.forEach(function (el) {
    var texte = el.textContent.trim();
    var m = texte.match(/^([^\d]*)([\d\s  .,]+)(.*)$/);
    if (!m) return;
    var cible = parseFloat(m[2].replace(/[\s  ]/g, "").replace(",", "."));
    if (!isFinite(cible) || cible <= 0) return;
    var avant = m[1], apres = m[3], duree = parseInt(el.getAttribute("data-duree") || "2200", 10);
    el.setAttribute("aria-label", texte);
    el.textContent = avant + "0";
    var obs = new IntersectionObserver(function (entrees) {
      if (!entrees[0].isIntersecting) return;
      obs.disconnect();
      var t0 = performance.now();
      var pas = function (t) {
        var p = Math.min(1, (t - t0) / duree);
        var v = 1 - Math.pow(1 - p, 3);
        el.textContent = avant + format(cible * v) + (p >= 1 ? apres : "");
        if (p < 1) requestAnimationFrame(pas);
      };
      requestAnimationFrame(pas);
    }, { threshold: 0.5 });
    obs.observe(el);
  });
})();
