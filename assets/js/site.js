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

/* Apparition des blocs quand ils entrent à l'écran (une seule fois) */
(function () {
  "use strict";
  var blocs = document.querySelectorAll(".apparition, .devoile, .devoile-2, main > section:not(:first-child) > .conteneur");
  if (!("IntersectionObserver" in window)) { blocs.forEach(function (b) { b.classList.add("visible"); }); return; }
  // Un bloc masqué par « clip-path » n'est pas vu par l'observateur : on observe alors son parent.
  var cibles = new Map();
  var obs = new IntersectionObserver(function (entrees) {
    entrees.forEach(function (e) {
      if (!e.isIntersecting) return;
      (cibles.get(e.target) || []).forEach(function (b) { b.classList.add("visible"); });
      obs.unobserve(e.target);
    });
  }, { rootMargin: "0px 0px -8% 0px", threshold: 0.08 });
  blocs.forEach(function (b) {
    var cible = (b.classList.contains("devoile") || b.classList.contains("devoile-2")) ? (b.parentElement || b) : b;
    if (!cibles.has(cible)) { cibles.set(cible, []); obs.observe(cible); }
    cibles.get(cible).push(b);
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

/* Carrousel d'images (détail d'une actualité) : défilement automatique, flèches et points. */
(function () {
  "use strict";
  document.querySelectorAll("[data-carrousel]").forEach(function (bloc) {
    var vues = bloc.querySelectorAll(".carrousel-vue");
    var points = bloc.querySelectorAll("[data-point]");
    if (vues.length < 2) return;
    var i = 0, minuterie = null;
    var calme = window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    function montrer(n) {
      i = (n + vues.length) % vues.length;
      vues.forEach(function (v, k) { v.classList.toggle("actif", k === i); v.setAttribute("aria-hidden", k === i ? "false" : "true"); });
      points.forEach(function (p, k) { p.classList.toggle("actif", k === i); });
    }
    function lancer() { if (!calme) { arreter(); minuterie = setInterval(function () { montrer(i + 1); }, 5000); } }
    function arreter() { if (minuterie) clearInterval(minuterie); minuterie = null; }
    bloc.querySelector("[data-precedent]").addEventListener("click", function () { montrer(i - 1); lancer(); });
    bloc.querySelector("[data-suivant]").addEventListener("click", function () { montrer(i + 1); lancer(); });
    points.forEach(function (p) { p.addEventListener("click", function () { montrer(+p.dataset.point); lancer(); }); });
    bloc.addEventListener("mouseenter", arreter);
    bloc.addEventListener("mouseleave", lancer);
    // Glisser du doigt sur téléphone
    var depart = null;
    bloc.addEventListener("touchstart", function (e) { depart = e.touches[0].clientX; }, { passive: true });
    bloc.addEventListener("touchend", function (e) {
      if (depart === null) return;
      var dx = e.changedTouches[0].clientX - depart;
      if (Math.abs(dx) > 40) { montrer(i + (dx < 0 ? 1 : -1)); lancer(); }
      depart = null;
    });
    lancer();
  });

  /* Photothèque : une photo s'ouvre en grand, on passe à la suivante avec les flèches ou le clavier. */
  document.querySelectorAll("[data-phototheque]").forEach(function (zone) {
    var boutons = zone.querySelectorAll("[data-agrandir]");
    var boite = zone.querySelector("dialog");
    if (!boite || !boite.showModal) return;
    var image = boite.querySelector("img"), legende = boite.querySelector(".visionneuse-legende"), i = 0;
    function montrer(n) {
      i = (n + boutons.length) % boutons.length;
      var source = boutons[i].querySelector("img");
      image.src = source.src; image.alt = source.alt;
      legende.textContent = (source.alt ? source.alt + " · " : "") + (i + 1) + " / " + boutons.length;
    }
    boutons.forEach(function (b) { b.addEventListener("click", function () { montrer(+b.dataset.agrandir); boite.showModal(); }); });
    boite.querySelector("[data-fermer]").addEventListener("click", function () { boite.close(); });
    boite.querySelector("[data-precedent]").addEventListener("click", function () { montrer(i - 1); });
    boite.querySelector("[data-suivant]").addEventListener("click", function () { montrer(i + 1); });
    boite.addEventListener("click", function (e) { if (e.target === boite) boite.close(); });
    boite.addEventListener("keydown", function (e) {
      if (e.key === "ArrowLeft") montrer(i - 1);
      if (e.key === "ArrowRight") montrer(i + 1);
    });
  });
})();

/* Compte à rebours du prochain événement (accueil) et bulle d'aide. */
(function () {
  "use strict";
  document.querySelectorAll("[data-rebours]").forEach(function (bloc) {
    var cible = new Date(bloc.getAttribute("data-rebours")).getTime();
    var j = bloc.querySelector("[data-jours]"), h = bloc.querySelector("[data-heures]"), m = bloc.querySelector("[data-minutes]");
    function maj() {
      var reste = Math.max(0, cible - Date.now());
      j.textContent = Math.floor(reste / 86400000);
      h.textContent = Math.floor(reste % 86400000 / 3600000);
      m.textContent = Math.floor(reste % 3600000 / 60000);
    }
    maj();
    setInterval(maj, 30000);
  });

  var aide = document.querySelector("[data-aide]");
  if (!aide) return;
  var bouton = aide.querySelector("[data-ouvrir-aide]");
  var panneau = aide.querySelector(".aide-panneau");
  function basculer(ouvrir) {
    panneau.hidden = !ouvrir;
    bouton.setAttribute("aria-expanded", ouvrir ? "true" : "false");
    if (ouvrir) aide.setAttribute("data-ouvert", ""); else aide.removeAttribute("data-ouvert");
  }
  bouton.addEventListener("click", function () { basculer(panneau.hidden); });
  document.addEventListener("keydown", function (e) { if (e.key === "Escape" && !panneau.hidden) { basculer(false); bouton.focus(); } });
  document.addEventListener("click", function (e) { if (!panneau.hidden && !aide.contains(e.target)) basculer(false); });
})();
