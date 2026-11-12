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
  // Confirme la copie : une coche pour un bouton en icône, sinon le texte « Lien copié »
  var annoncer = function (bouton, texte) {
    var initial = bouton.innerHTML;
    if (bouton.querySelector("svg")) {
      bouton.innerHTML = '<svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7"/></svg><span class="sr-only">' + texte + '</span>';
    } else {
      bouton.textContent = texte;
    }
    setTimeout(function () { bouton.innerHTML = initial; }, 2000);
  };
  window.ycmAnnoncer = annoncer;
  var copier = function (bouton, adresse) {
    var url = adresse || window.location.href.split("#")[0];
    if (navigator.clipboard && window.isSecureContext) {
      navigator.clipboard.writeText(url).then(function () { annoncer(bouton, "Lien copié"); });
    } else {
      var champ = document.createElement("textarea");
      champ.value = url; document.body.appendChild(champ); champ.select();
      try { document.execCommand("copy"); annoncer(bouton, "Lien copié"); } catch (e) {}
      champ.remove();
    }
  };
  window.ycmCopier = copier;
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

/* Confirmation avant chaque action sensible (se déconnecter, quitter une communauté, rendre son profil visible…) :
   une petite fenêtre aux couleurs du site. Attributs : data-confirmer (question), data-confirmer-oui (bouton),
   data-confirmer-texte (précision facultative). Sans fenêtre possible, on retombe sur la confirmation du navigateur. */
(function () {
  "use strict";
  var boite = null;
  function fenetre() {
    if (boite) return boite;
    boite = document.createElement("dialog");
    boite.className = "dialogue confirmation m-auto w-[min(26rem,calc(100vw-2rem))] rounded-3xl border-0 bg-white p-0 shadow-2xl";
    boite.innerHTML = '<div class="p-6"><p class="text-lg leading-snug font-bold text-vert" data-question></p><p class="mt-2 hidden text-sm" data-precision></p>'
      + '<div class="mt-5 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end"><button type="button" class="btn btn-contour min-h-11" data-non>Annuler</button>'
      + '<button type="button" class="btn btn-orange min-h-11" data-oui>Confirmer</button></div></div>';
    document.body.appendChild(boite);
    boite.addEventListener("click", function (e) { if (e.target === boite) boite.close(); });
    boite.querySelector("[data-non]").addEventListener("click", function () { boite.close(); });
    return boite;
  }
  document.querySelectorAll("form[data-confirmer]").forEach(function (form) {
    form.addEventListener("submit", function (e) {
      if (form.dataset.confirme === "1") return;
      e.preventDefault();
      var question = form.getAttribute("data-confirmer");
      if (!window.HTMLDialogElement) { if (window.confirm(question)) { form.dataset.confirme = "1"; form.submit(); } return; }
      var b = fenetre();
      b.querySelector("[data-question]").textContent = question;
      var precision = b.querySelector("[data-precision]");
      precision.textContent = form.getAttribute("data-confirmer-texte") || "";
      precision.classList.toggle("hidden", !precision.textContent);
      var oui = b.querySelector("[data-oui]");
      oui.textContent = form.getAttribute("data-confirmer-oui") || "Confirmer";
      oui.onclick = function () { form.dataset.confirme = "1"; b.close(); form.submit(); };
      b.showModal();
      oui.focus();
    });
  });
})();

/* Prendre part : afficher les questions du rôle choisi et adapter le bouton (inscription ou candidature) */
(function () {
  "use strict";
  document.querySelectorAll("form[data-participer]").forEach(function (form) {
    var appliquer = function (radio) {
      form.querySelectorAll("[data-questions-role]").forEach(function (bloc) {
        var actif = bloc.getAttribute("data-questions-role") === radio.value;
        bloc.hidden = !actif;
        bloc.querySelectorAll("input, textarea, select").forEach(function (champ) { champ.disabled = !actif; });
      });
      var bouton = form.querySelector("[data-bouton-envoi]");
      if (bouton) bouton.textContent = radio.getAttribute("data-bouton");
      form.setAttribute("data-confirmer", radio.getAttribute("data-question"));
      form.setAttribute("data-confirmer-texte", radio.getAttribute("data-precision"));
      form.setAttribute("data-confirmer-oui", radio.getAttribute("data-oui"));
    };
    form.querySelectorAll("[data-role-choix]").forEach(function (radio) {
      radio.addEventListener("change", function () { if (radio.checked) appliquer(radio); });
    });
  });
})();

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
    if (total < 2) { blocs.forEach(function (bloc) { var i = bloc.querySelector("[data-diapo-image]"); if (i) i.classList.add("active"); }); return; }
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
    if (/^(19|20)\d{2}$/.test(texte)) return; // une année (2021) s'affiche telle quelle
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
  // Seuil 0 : un bloc apparaît dès que son haut entre à l'écran, même s'il est très haut (longues listes)
  }, { rootMargin: "0px 0px -40px 0px", threshold: 0 });
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
    var telecharger = boite.querySelector("[data-telecharger]"), partager = boite.querySelector("[data-partager-photo]");
    function montrer(n) {
      i = (n + boutons.length) % boutons.length;
      var source = boutons[i].querySelector("img");
      image.src = source.src; image.alt = source.alt;
      legende.textContent = (source.alt ? source.alt + " · " : "") + (i + 1) + " / " + boutons.length;
      if (telecharger) telecharger.href = source.src;
    }
    // Partager la photo affichée : menu de partage du téléphone, sinon copie du lien de la photo
    if (partager) partager.addEventListener("click", function () {
      var url = new URL(image.getAttribute("src"), window.location.href).href;
      if (navigator.share) {
        navigator.share({ title: document.title, url: url }).catch(function () {});
      } else if (window.ycmCopier) {
        window.ycmCopier(partager, url);
      }
    });
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
  var fil = aide.querySelector("[data-fil]");
  var form = aide.querySelector("[data-form-aide]");
  var champ = aide.querySelector("[data-champ-message]");
  var identite = aide.querySelector("[data-identite]");

  function basculer(ouvrir) {
    panneau.hidden = !ouvrir;
    bouton.setAttribute("aria-expanded", ouvrir ? "true" : "false");
    if (ouvrir) aide.setAttribute("data-ouvert", ""); else aide.removeAttribute("data-ouvert");
  }
  function defiler() { fil.scrollTop = fil.scrollHeight; }
  function bulle(classe, texte) {
    var d = document.createElement("div");
    d.className = classe;
    d.textContent = texte;
    fil.appendChild(d);
    defiler();
    return d;
  }
  function repondre(noeud) {
    var attente = bulle("aide-bot aide-ecrit", "…");
    setTimeout(function () { attente.remove(); fil.appendChild(noeud); defiler(); }, 450);
  }

  // Première visite : un petit message apparaît à côté de la bulle (fermée), une seule fois
  var invite = aide.querySelector("[data-aide-invite]");
  var lire = function (c) { try { return localStorage.getItem(c); } catch (e) { return "1"; } };
  var noter = function (c) { try { localStorage.setItem(c, "1"); } catch (e) {} };
  var cacherInvite = function () { if (invite && !invite.hidden) { invite.classList.remove("visible"); setTimeout(function () { invite.hidden = true; }, 250); noter("ycm-invite-vue"); } };
  if (invite && !aide.classList.contains("aide-espace") && !lire("ycm-invite-vue")) {
    setTimeout(function () {
      if (!panneau.hidden) return;
      invite.hidden = false;
      requestAnimationFrame(function () { invite.classList.add("visible"); });
    }, 2500);
    invite.querySelector("[data-aide-invite-fermer]").addEventListener("click", cacherInvite);
    invite.querySelector("[data-aide-invite-ouvrir]").addEventListener("click", function () { cacherInvite(); basculer(true); });
  }

  bouton.addEventListener("click", function () { cacherInvite(); basculer(panneau.hidden); });
  // Lien vers un endroit de la même page (newsletter) : on referme l'assistant pour laisser voir le formulaire
  panneau.addEventListener("click", function (e) {
    var lien = e.target.closest("a.aide-lien");
    if (lien && lien.hash && lien.pathname === location.pathname) basculer(false);
  });
  // Espace membre : la bulle est cachée, l'assistant s'ouvre depuis le tableau de bord
  document.querySelectorAll("[data-ouvrir-assistant]").forEach(function (b) {
    b.addEventListener("click", function (e) { e.stopPropagation(); var menu = b.closest("details"); if (menu) menu.open = false; basculer(true); var c = panneau.querySelector("button, textarea"); if (c) c.focus(); });
  });
  aide.querySelector("[data-fermer-aide]").addEventListener("click", function () { basculer(false); bouton.focus(); });
  document.addEventListener("keydown", function (e) { if (e.key === "Escape" && !panneau.hidden) { basculer(false); bouton.focus(); } });
  document.addEventListener("click", function (e) { if (!panneau.hidden && e.target.isConnected && !aide.contains(e.target)) basculer(false); });

  // Sujets proposés : réponse prête
  aide.querySelectorAll("[data-sujet]").forEach(function (b) {
    b.addEventListener("click", function () {
      bulle("aide-moi", b.textContent);
      var modele = aide.querySelector('template[data-reponse="' + b.getAttribute("data-sujet") + '"]');
      repondre(modele.content.firstElementChild.cloneNode(true));
    });
  });

  // Le champ s'agrandit avec le texte ; la touche Entrée passe simplement à la ligne
  champ.addEventListener("input", function () { champ.style.height = "auto"; champ.style.height = Math.min(champ.scrollHeight, 128) + "px"; });

  // Message libre : envoyé à l'équipe
  form.addEventListener("submit", function (e) {
    e.preventDefault();
    var texte = champ.value.trim();
    if (texte === "") { champ.focus(); return; }
    if (identite && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(identite.value.trim())) {
      bulle("aide-bot aide-alerte", identite.value.trim() === "" ? "Indiquez votre adresse e-mail pour recevoir la réponse." : "Cette adresse e-mail n'est pas valide.");
      identite.focus();
      return;
    }
    if (!form.hasAttribute("data-confirme")) {
      // On demande une confirmation dans la conversation avant d'envoyer
      var ancienne = fil.querySelector("[data-confirmation]");
      if (ancienne) ancienne.remove();
      var boite = document.createElement("div");
      boite.className = "aide-bot";
      boite.setAttribute("data-confirmation", "");
      boite.innerHTML = '<span class="block">Envoyer ce message à l\'équipe du chapitre ?</span><span class="mt-2.5 flex flex-wrap gap-2"><button type="button" class="aide-lien !mt-0" data-oui>Oui, envoyer</button><button type="button" class="aide-sujet" data-non>Modifier</button></span>';
      fil.appendChild(boite);
      defiler();
      boite.querySelector("[data-oui]").addEventListener("click", function () {
        boite.remove();
        form.setAttribute("data-confirme", "");
        form.requestSubmit ? form.requestSubmit() : form.dispatchEvent(new Event("submit", { cancelable: true }));
      });
      boite.querySelector("[data-non]").addEventListener("click", function () { boite.remove(); champ.focus(); });
      return;
    }
    form.removeAttribute("data-confirme");
    var envoyer = form.querySelector("button[type=submit]");
    envoyer.disabled = true;
    bulle("aide-moi", texte);
    fetch(form.action, { method: "POST", body: new FormData(form), headers: { "X-Requested-With": "fetch" }, credentials: "same-origin" })
      .then(function (r) { return r.json(); })
      .then(function (d) {
        var b = document.createElement("div");
        if (d.ok) {
          b.className = "aide-bot";
          b.textContent = "Merci, votre message a bien été transmis. L'équipe du chapitre vous répondra par e-mail.";
          champ.value = ""; champ.style.height = "";
        } else {
          b.className = "aide-bot aide-alerte";
          b.textContent = Object.keys(d.erreurs || {}).map(function (k) { return d.erreurs[k]; }).join(" ");
        }
        repondre(b);
      })
      .catch(function () { bulle("aide-bot aide-alerte", "Le message n'a pas pu partir. Rechargez la page puis réessayez."); })
      .then(function () { envoyer.disabled = false; });
  });

  // Couleur du bouton selon le fond qui se trouve derrière lui
  function fondDerriere() {
    var r = bouton.getBoundingClientRect();
    var x = r.left + r.width / 2, y = r.top + r.height / 2;
    var pile = document.elementsFromPoint ? document.elementsFromPoint(x, y) : [];
    for (var i = 0; i < pile.length; i++) {
      if (aide.contains(pile[i])) continue;
      for (var el = pile[i]; el && el !== document.documentElement; el = el.parentElement) {
        var c = getComputedStyle(el).backgroundColor.match(/[\d.]+/g);
        if (c && (c.length < 4 || +c[3] > 0.5)) {
          var lum = (0.299 * c[0] + 0.587 * c[1] + 0.114 * c[2]) / 255;
          return lum < 0.5;
        }
      }
      break;
    }
    return false;
  }
  var attente = null;
  function majCouleur() {
    attente = null;
    if (fondDerriere()) aide.setAttribute("data-sur-vert", ""); else aide.removeAttribute("data-sur-vert");
  }
  function planifier() { if (!attente) attente = window.requestAnimationFrame(majCouleur); }
  window.addEventListener("scroll", planifier, { passive: true });
  window.addEventListener("resize", planifier);
  window.addEventListener("load", majCouleur);
  majCouleur();
})();

/* « Voir plus » : déplie un texte replié sur téléphone (le bouton n'apparaît que si le texte dépasse). */
(function () {
  "use strict";
  document.querySelectorAll("[data-voir-plus]").forEach(function (bouton) {
    var bloc = document.getElementById(bouton.getAttribute("aria-controls"));
    if (!bloc) return;
    function verifier() {
      if (bloc.classList.contains("deplie")) return;
      bouton.classList.toggle("actif", bloc.scrollHeight > bloc.clientHeight + 4);
    }
    bouton.addEventListener("click", function () {
      var ouvert = bloc.classList.toggle("deplie");
      bouton.setAttribute("aria-expanded", ouvert ? "true" : "false");
      bouton.querySelector("[data-libelle]").textContent = ouvert ? "Voir moins" : "Voir plus";
      if (!ouvert) bloc.scrollIntoView({ block: "nearest" });
    });
    verifier();
    window.addEventListener("resize", verifier);
    window.addEventListener("load", verifier);
  });
})();

/* Fenêtres de proposition (initiative, opportunité) : ouverture, envoi sans quitter la page, remerciement. */
(function () {
  "use strict";
  function ouvrir(boite) {
    var form = boite.querySelector("[data-form-dialogue]");
    var merci = boite.querySelector("[data-merci-dialogue]");
    if (form) { form.classList.remove("hidden"); var err = boite.querySelector("[data-erreur-dialogue]"); if (err) err.classList.add("hidden"); }
    if (merci) merci.classList.add("hidden");
    boite.showModal();
    var premier = boite.querySelector("input:not([type=hidden]):not([tabindex]), select, textarea, a.btn");
    if (premier) premier.focus();
  }
  document.querySelectorAll("dialog.dialogue").forEach(function (boite) {
    if (!boite.showModal) return;
    boite.querySelectorAll("[data-fermer-dialogue]").forEach(function (b) { b.addEventListener("click", function () { boite.close(); }); });
    boite.addEventListener("click", function (e) { if (e.target === boite) boite.close(); });
    var form = boite.querySelector("[data-form-dialogue]");
    if (!form) return;
    var merci = boite.querySelector("[data-merci-dialogue]");
    var erreur = boite.querySelector("[data-erreur-dialogue]");
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      var envoyer = form.querySelector("button[type=submit]");
      envoyer.disabled = true;
      erreur.classList.add("hidden");
      fetch(form.action, { method: "POST", body: new FormData(form), headers: { "X-Requested-With": "fetch" }, credentials: "same-origin" })
        .then(function (r) { return r.json(); })
        .then(function (d) {
          if (d.ok) {
            form.reset(); form.classList.add("hidden"); merci.classList.remove("hidden");
            // Espace membre : la liste « Mes propositions » se met à jour quand on ferme la fenêtre
            if (document.querySelector("[data-recharger-apres-envoi]")) boite.addEventListener("close", function () { location.reload(); }, { once: true });
          }
          else { erreur.textContent = Object.keys(d.erreurs || {}).map(function (k) { return d.erreurs[k]; }).join(" "); erreur.classList.remove("hidden"); erreur.scrollIntoView({ block: "nearest" }); }
        })
        .catch(function () { erreur.textContent = "L'envoi n'a pas abouti. Rechargez la page puis réessayez."; erreur.classList.remove("hidden"); })
        .then(function () { envoyer.disabled = false; });
    });
  });
  document.querySelectorAll("[data-ouvrir-dialogue]").forEach(function (lien) {
    lien.addEventListener("click", function (e) {
      var boite = document.getElementById(lien.getAttribute("data-ouvrir-dialogue"));
      if (!boite || !boite.showModal) return;
      e.preventDefault();
      ouvrir(boite);
    });
  });
  // Retour de connexion avec ?proposer=1 : la fenêtre s'ouvre directement
  if (/[?&]proposer=1/.test(location.search)) {
    var boite = document.getElementById("dialogue-opportunite");
    if (boite && boite.showModal) ouvrir(boite);
  }
  // Liste « Type » des opportunités : se referme quand on clique ailleurs
  document.querySelectorAll("[data-choix-type]").forEach(function (d) {
    document.addEventListener("click", function (e) { if (d.open && !d.contains(e.target)) d.open = false; });
  });
})();

/* Newsletter du pied de page : vérification et confirmation sous le champ, sans recharger la page. */
(function () {
  "use strict";
  document.querySelectorAll("[data-form-newsletter]").forEach(function (form) {
    var retour = form.querySelector("[data-retour-newsletter]");
    var champ = form.querySelector("input[type=email]");
    var accord = form.querySelector("input[name=accord]");
    function afficher(ok, texte) {
      retour.textContent = texte;
      retour.classList.remove("hidden", "bg-white", "text-rouge", "bg-orange", "text-white");
      retour.classList.add("flex");
      retour.classList.add.apply(retour.classList, ok ? ["bg-orange", "text-white"] : ["bg-white", "text-rouge"]);
    }
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      var email = champ.value.trim();
      if (email === "") { afficher(false, "Indiquez votre adresse e-mail."); champ.focus(); return; }
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { afficher(false, "Cette adresse e-mail n'est pas valide."); champ.focus(); return; }
      if (!accord.checked) { afficher(false, "Cochez la case pour accepter de recevoir la newsletter."); accord.focus(); return; }
      var bouton = form.querySelector("button[type=submit]");
      bouton.disabled = true;
      fetch(form.action, { method: "POST", body: new FormData(form), headers: { "X-Requested-With": "fetch" }, credentials: "same-origin" })
        .then(function (r) { return r.json(); })
        .then(function (d) { afficher(d.ok, d.message); if (d.ok) form.reset(); })
        .catch(function () { afficher(false, "L'inscription n'a pas abouti. Rechargez la page puis réessayez."); })
        .then(function () { bouton.disabled = false; });
    });
  });

  // Défilement continu (Actualités) : la suite de la liste se charge quand on approche du bas
  document.querySelectorAll("[data-suite-liste]").forEach(function (zone) {
    var liste = zone.parentElement.querySelector("[data-liste-continue]");
    var bouton = zone.querySelector("[data-suite-bouton]");
    var chargement = zone.querySelector("[data-suite-chargement]");
    if (!liste || !("IntersectionObserver" in window)) return;
    var occupe = false;
    bouton.classList.add("hidden");
    chargement.classList.remove("hidden");
    chargement.classList.add("inline-flex");
    var charger = function () {
      var adresse = zone.getAttribute("data-suite-liste");
      if (occupe || !adresse) return;
      occupe = true;
      fetch(adresse, { headers: { "X-Requested-With": "fetch" } })
        .then(function (r) { return r.json(); })
        .then(function (d) {
          liste.insertAdjacentHTML("beforeend", d.html);
          if (d.suivante) { zone.setAttribute("data-suite-liste", d.suivante); occupe = false; }
          else { observateur.disconnect(); zone.remove(); }
        })
        .catch(function () {
          // En cas d'échec, on rend la main au bouton
          observateur.disconnect();
          chargement.classList.add("hidden");
          bouton.href = adresse;
          bouton.classList.remove("hidden");
        });
    };
    var observateur = new IntersectionObserver(function (entrees) {
      if (entrees[0].isIntersecting) charger();
    }, { rootMargin: "0px 0px 600px 0px" });
    observateur.observe(zone);
  });
})();

/* Choix d'une photo (Mon profil) : nom du fichier et aperçu immédiat */
(function () {
  "use strict";
  document.querySelectorAll("[data-choix-fichier]").forEach(function (champ) {
    var bloc = champ.closest("div").parentElement;
    var nom = bloc.querySelector("[data-nom-fichier]");
    var apercu = bloc.querySelector("[data-apercu-image]");
    champ.addEventListener("change", function () {
      var f = champ.files && champ.files[0];
      if (!f) return;
      if (nom) nom.textContent = f.name;
      if (apercu && window.URL) {
        var img = document.createElement("img");
        img.src = URL.createObjectURL(f);
        img.alt = "";
        img.className = "size-16 rounded-full object-cover";
        img.setAttribute("data-apercu-image", "");
        apercu.replaceWith(img);
        apercu = img;
      }
    });
  });
})();

/* Mon profil : liens (ajouter, retirer) et ouverture des blocs repliés (« Modifier mon profil », « Modifier le mot de passe ») */
(function () {
  "use strict";
  document.querySelectorAll("[data-liens]").forEach(function (bloc) {
    var liste = bloc.querySelector("[data-liste-liens]");
    var ajouter = bloc.querySelector("[data-ajouter-lien]");
    function majBouton() { ajouter.classList.toggle("hidden", liste.children.length >= 8); }
    ajouter.addEventListener("click", function () {
      var modele = liste.querySelector("[data-ligne-lien]");
      var ligne = modele.cloneNode(true);
      ligne.querySelector("input").value = "";
      var choix = ligne.querySelector("select");
      // Propose un réseau pas encore utilisé
      var pris = Array.prototype.map.call(liste.querySelectorAll("select"), function (s) { return s.value; });
      for (var i = 0; i < choix.options.length; i++) { if (pris.indexOf(choix.options[i].value) === -1) { choix.selectedIndex = i; break; } }
      liste.appendChild(ligne);
      ligne.querySelector("input").focus();
      majBouton();
    });
    liste.addEventListener("click", function (e) {
      var bouton = e.target.closest("[data-retirer-lien]");
      if (!bouton) return;
      var ligne = bouton.closest("[data-ligne-lien]");
      if (liste.children.length > 1) ligne.remove(); else ligne.querySelector("input").value = "";
      majBouton();
    });
    majBouton();
  });
  // « Annuler » : referme la fiche sans recharger et remonte à l'aperçu du profil
  document.querySelectorAll("[data-fermer-details]").forEach(function (lien) {
    lien.addEventListener("click", function (e) {
      var bloc = lien.closest("details");
      if (!bloc) return;
      e.preventDefault();
      bloc.querySelector("form").reset();
      bloc.open = false;
      var apercu = document.getElementById("apercu");
      (apercu || bloc).scrollIntoView({ behavior: "smooth", block: "start" });
    });
  });
  document.querySelectorAll("[data-ouvrir-details]").forEach(function (lien) {
    lien.addEventListener("click", function (e) {
      var bloc = document.getElementById(lien.getAttribute("data-ouvrir-details"));
      if (!bloc) return;
      e.preventDefault();
      bloc.open = true;
      bloc.scrollIntoView({ behavior: "smooth", block: "start" });
      var premier = bloc.querySelector("input:not([type=hidden]):not(.sr-only), select, textarea");
      if (premier) setTimeout(function () { premier.focus({ preventScroll: true }); }, 350);
    });
  });
})();

/* Bouton Retour : revient exactement à la page précédente (et à l'endroit où l'on était), si elle fait partie du site */
(function () {
  "use strict";
  document.querySelectorAll("a[data-retour]").forEach(function (lien) {
    lien.addEventListener("click", function (e) {
      var precedente = document.referrer;
      if (!precedente || window.history.length < 2) return;
      try {
        var u = new URL(precedente);
        if (u.origin !== location.origin || u.href === location.href) return;
      } catch (err) { return; }
      e.preventDefault();
      history.back();
    });
  });
})();

/* ------------------------------------------------------------------------
   Signature : fils du réseau qui se tracent à l'apparition, photos chargées
   ------------------------------------------------------------------------ */
(function () {
  "use strict";
  var reseaux = document.querySelectorAll("[data-reseau]");
  if (reseaux.length) {
    if (!("IntersectionObserver" in window)) { reseaux.forEach(function (r) { r.classList.add("trace"); }); }
    else {
      var obs = new IntersectionObserver(function (entrees) {
        entrees.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add("trace"); obs.unobserve(e.target); } });
      }, { threshold: 0.2 });
      reseaux.forEach(function (r) { obs.observe(r); });
    }
  }
  // Jauges des places : remplies quand elles apparaissent à l'écran
  var jauges = document.querySelectorAll(".jauge");
  if (jauges.length) {
    if (!("IntersectionObserver" in window)) jauges.forEach(function (j) { j.classList.add("remplie"); });
    else {
      var oj = new IntersectionObserver(function (es) { es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add("remplie"); oj.unobserve(e.target); } }); }, { threshold: 0.5 });
      jauges.forEach(function (j) { oj.observe(j); });
    }
  }
  document.querySelectorAll("img.photo-attente").forEach(function (img) {
    var fini = function () { img.classList.add("chargee"); };
    if (img.complete && img.naturalWidth) fini(); else { img.addEventListener("load", fini); img.addEventListener("error", fini); }
  });
})();

/* ------------------------------------------------------------------------
   Recherche instantanée : loupe de l'en-tête, Ctrl + K ou « / »
   ------------------------------------------------------------------------ */
(function () {
  "use strict";
  var boite = document.querySelector("[data-recherche]");
  if (!boite || typeof boite.showModal !== "function") return;
  var champ = boite.querySelector("[data-recherche-champ]");
  var zone = boite.querySelector("[data-recherche-resultats]");
  var adresse = boite.getAttribute("data-suggestions");
  var aide = zone.innerHTML, minuterie = null, requete = 0, choix = -1;
  var echapper = function (t) { var d = document.createElement("div"); d.textContent = t || ""; return d.innerHTML; };
  var liens = function () { return Array.prototype.slice.call(zone.querySelectorAll("[data-suggestion]")); };
  var marquer = function (k) {
    var l = liens(); choix = k;
    l.forEach(function (a, i) { a.classList.toggle("bg-gris", i === k); a.setAttribute("aria-selected", i === k ? "true" : "false"); });
    if (l[k]) l[k].scrollIntoView({ block: "nearest" });
  };
  var afficher = function (d) {
    choix = -1;
    if (!d.groupes.length) {
      zone.innerHTML = '<p class="px-3 py-6 text-center text-sm">Aucun résultat pour « ' + echapper(d.q) + ' ». Essayez un mot plus court ou plus général.</p>';
      champ.setAttribute("aria-expanded", "false");
      return;
    }
    var html = "";
    d.groupes.forEach(function (g) {
      html += '<p class="px-3 pt-3 pb-1 text-xs font-bold tracking-wide text-texte uppercase">' + echapper(g.titre) + "</p>";
      g.elements.forEach(function (x) {
        html += '<a href="' + echapper(x.url) + '" role="option" data-suggestion class="flex min-h-14 items-center gap-3 rounded-xl px-3 py-2 hover:bg-gris">'
          + (x.image ? '<img src="' + echapper(x.image) + '" alt="" class="size-11 shrink-0 rounded-lg object-cover">' : '<span class="grid size-11 shrink-0 place-items-center rounded-lg bg-vert-clair text-vert" aria-hidden="true"><svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="6" cy="7" r="2.5"/><circle cx="18" cy="6" r="2.5"/><circle cx="13" cy="18" r="2.5"/><path d="M8.3 7.6 15.6 6.4M7.4 9.2l4.3 6.9M17 8.4l-2.9 7.5"/></svg></span>')
          + '<span class="min-w-0"><span class="block truncate font-bold text-vert">' + echapper(x.titre) + "</span>"
          + (x.detail ? '<span class="block truncate text-xs text-texte">' + echapper(x.detail) + "</span>" : "") + "</span></a>";
      });
    });
    html += '<a href="' + echapper(d.tout) + '" data-suggestion class="mt-2 flex min-h-12 items-center justify-center rounded-xl bg-vert-clair px-3 text-sm font-bold text-vert hover:bg-orange hover:text-white">Voir tous les résultats pour « ' + echapper(d.q) + " »</a>";
    zone.innerHTML = html;
    champ.setAttribute("aria-expanded", "true");
  };
  var chercher = function () {
    var q = champ.value.trim();
    if (q.length < 2) { zone.innerHTML = aide; champ.setAttribute("aria-expanded", "false"); return; }
    var n = ++requete;
    zone.setAttribute("aria-busy", "true");
    fetch(adresse + "?q=" + encodeURIComponent(q), { headers: { Accept: "application/json" } })
      .then(function (r) { return r.json(); })
      .then(function (d) { if (n === requete) afficher(d); })
      .catch(function () { if (n === requete) zone.innerHTML = '<p class="px-3 py-6 text-center text-sm">La recherche ne répond pas. Appuyez sur Entrée pour voir la page de résultats.</p>'; })
      .then(function () { zone.removeAttribute("aria-busy"); });
  };
  var ouvrir = function () {
    var menu = document.querySelector("[data-menu-mobile]");
    if (menu && !menu.classList.contains("hidden")) { var f = menu.querySelector("[data-fermer-menu]"); if (f) f.click(); }
    boite.showModal(); champ.focus(); champ.select();
  };
  document.querySelectorAll("[data-ouvrir-recherche]").forEach(function (b) { b.addEventListener("click", ouvrir); });
  boite.querySelector("[data-fermer-recherche]").addEventListener("click", function () { boite.close(); });
  boite.addEventListener("click", function (e) { if (e.target === boite) boite.close(); });
  champ.addEventListener("input", function () { clearTimeout(minuterie); minuterie = setTimeout(chercher, 180); });
  champ.addEventListener("keydown", function (e) {
    var l = liens();
    if (e.key === "ArrowDown" && l.length) { e.preventDefault(); marquer(Math.min(l.length - 1, choix + 1)); }
    else if (e.key === "ArrowUp" && l.length) { e.preventDefault(); marquer(Math.max(0, choix - 1)); }
    else if (e.key === "Enter" && choix >= 0 && l[choix]) { e.preventDefault(); window.location.href = l[choix].href; }
  });
  document.addEventListener("keydown", function (e) {
    var cible = e.target, saisie = cible && (cible.isContentEditable || /^(INPUT|TEXTAREA|SELECT)$/.test(cible.tagName));
    if ((e.key === "k" || e.key === "K") && (e.ctrlKey || e.metaKey)) { e.preventDefault(); ouvrir(); }
    else if (e.key === "/" && !saisie && !boite.open) { e.preventDefault(); ouvrir(); }
  });
})();
