"""
Site d'administration de YouthConnekt Mali.

On garde le moteur de l'administration Django (droits, formulaires, historique)
et on l'habille comme la maquette validée :
- un menu latéral vert, rangé par rubriques, avec des pastilles (messages à lire…) ;
- une barre du haut : fil d'Ariane, recherche, « Voir le site », administrateur connecté ;
- un tableau de bord : chiffres, dernières inscriptions, opportunités bientôt closes,
  actions rapides et chiffres clés modifiables directement.
"""

from datetime import timedelta

from django.apps import apps
from django.contrib import admin, messages
from django.contrib.admin.apps import AdminConfig
from django.db.models import Q
from django.http import HttpResponseRedirect
from django.shortcuts import render
from django.urls import NoReverseMatch, path, reverse
from django.utils import timezone
from django.utils.http import urlencode

# ----------------------------------------------------------------------
# Icônes du menu (traits simples, 24 x 24)
# ----------------------------------------------------------------------
ICONES = {
    "tableau": '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>',
    "mallette": '<rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>',
    "journal": '<rect x="3" y="4" width="15" height="16" rx="2"/><path d="M18 8h3v10a2 2 0 0 1-2 2"/><path d="M7 9h7M7 13h7M7 17h4"/>',
    "drapeau": '<path d="M5 21V4"/><path d="M5 4h11l-2 4 2 4H5"/>',
    "bulle": '<path d="M4 5h16v11H9l-5 4z"/>',
    "batiment": '<rect x="4" y="3" width="16" height="18" rx="1.5"/><path d="M9 7h1M14 7h1M9 11h1M14 11h1M9 15h1M14 15h1M10 21v-3h4v3"/>',
    "personne": '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
    "groupe": '<circle cx="9" cy="8" r="3.5"/><circle cx="17" cy="9" r="2.5"/><path d="M2 20a7 7 0 0 1 14 0M15 14.5a5 5 0 0 1 7 5.5"/>',
    "enveloppe": '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
    "lettre": '<rect x="4" y="4" width="16" height="16" rx="2"/><path d="M4 9h16"/>',
    "graphique": '<path d="M4 20h16"/><path d="M7 16V9M12 16V5M17 16v-4"/>',
    "coeur": '<path d="M12 20s-7-4.5-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.5-7 10-7 10z"/>',
    "reglage": '<circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3M4.9 4.9l2.1 2.1M17 17l2.1 2.1M4.9 19.1 7 17M17 7l2.1-2.1"/>',
    "cible": '<circle cx="12" cy="12" r="8"/><circle cx="12" cy="12" r="3"/>',
    "page": '<path d="M6 3h9l4 4v14H6z"/><path d="M15 3v4h4M9 12h6M9 16h6"/>',
    "cle": '<circle cx="8" cy="15" r="4"/><path d="m11 12 9-9M17 6l3 3"/>',
    "bouclier": '<path d="M12 3 5 6v6c0 4.5 3 7.5 7 9 4-1.5 7-4.5 7-9V6z"/>',
}

# Menu latéral : (rubrique, [(libellé, "application.modele", icône), ...])
MENU = [
    (None, [("Tableau de bord", "index", "tableau")]),
    ("Contenus", [
        ("Opportunités", "contenus.opportunite", "mallette"),
        ("Actualités et événements", "contenus.actualite", "journal"),
        ("Initiatives", "contenus.initiative", "drapeau"),
        ("Témoignages", "contenus.temoignage", "bulle"),
        ("Partenaires", "contenus.partenaire", "batiment"),
    ]),
    ("Communauté", [
        ("Membres individuels", "comptes.profilindividuel", "personne"),
        ("Organisations et entreprises", "comptes.profilorganisation", "batiment"),
        ("Communautés", "communautes.communaute", "groupe"),
    ]),
    ("Échanges", [
        ("Messages de contact", "core.messagecontact", "enveloppe"),
        ("Abonnés newsletter", "core.abonnenewsletter", "lettre"),
    ]),
    ("Site", [
        ("Chiffres clés", "contenus.chiffrecle", "graphique"),
        ("Mot du ministre", "contenus.motduministre", "coeur"),
        ("Axes d'action", "contenus.axeaction", "cible"),
        ("Page À propos", "core.sectionapropos", "page"),
        ("Paramètres", "core.parametressite", "reglage"),
    ]),
    ("Accès", [
        ("Comptes et connexions", "comptes.utilisateur", "cle"),
        ("Rôles de l'équipe", "auth.group", "bouclier"),
    ]),
]


def _url(nom, *args):
    try:
        return reverse(nom, args=args)
    except NoReverseMatch:
        return None


def peut_voir(user, cle):
    app_label, modele = cle.split(".")
    return user.has_perm(f"{app_label}.view_{modele}") or user.has_perm(f"{app_label}.change_{modele}")


def initiales(texte):
    mots = [m for m in str(texte).replace("@", " ").replace(".", " ").split() if m]
    if not mots:
        return "?"
    if len(mots) == 1:
        return mots[0][:2].upper()
    return (mots[0][0] + mots[1][0]).upper()


class AdminYouthConnektMali(admin.AdminSite):
    site_header = "Administration YouthConnekt Mali"
    site_title = "YouthConnekt Mali"
    index_title = "Tableau de bord"
    site_url = "/"
    enable_nav_sidebar = True

    # ------------------------------------------------------------------
    # Données présentes sur toutes les pages : menu, administrateur connecté
    # ------------------------------------------------------------------
    def each_context(self, request):
        contexte = super().each_context(request)
        user = request.user
        if not getattr(user, "is_authenticated", False) or not user.is_staff:
            return contexte

        pastilles = {}
        if peut_voir(user, "core.messagecontact"):
            pastilles["core.messagecontact"] = apps.get_model("core", "MessageContact").objects.filter(
                traite=False
            ).count()
        if peut_voir(user, "communautes.demandeadhesion"):
            Demande = apps.get_model("communautes", "DemandeAdhesion")
            pastilles["communautes.communaute"] = Demande.objects.filter(
                statut=Demande.Statut.EN_ATTENTE
            ).count()

        chemin = request.path
        menu = []
        for rubrique, elements in MENU:
            liens = []
            for libelle, cle, icone in elements:
                if cle == "index":
                    url = reverse("admin:index")
                    actif = chemin == url
                else:
                    if not peut_voir(user, cle):
                        continue
                    app_label, modele = cle.split(".")
                    url = _url(f"admin:{app_label}_{modele}_changelist")
                    if not url:
                        continue
                    actif = chemin.startswith(url)
                    # Les demandes d'adhésion se gèrent depuis « Communautés »
                    if cle == "communautes.communaute" and "/demandeadhesion/" in chemin:
                        actif = True
                liens.append({
                    "libelle": libelle,
                    "url": url,
                    "icone": ICONES[icone],
                    "actif": actif,
                    "pastille": pastilles.get(cle) or 0,
                })
            if liens:
                menu.append({"rubrique": rubrique, "liens": liens})

        if user.is_superuser:
            role = "Administrateur principal"
        else:
            groupe = user.groups.order_by("name").first()
            role = groupe.name if groupe else "Administrateur"
        nom = getattr(user, "nom_affiche", "") or user.get_username()

        contexte.update({
            "menu_admin": menu,
            "admin_nom": nom,
            "admin_initiales": initiales(nom),
            "admin_role": role,
            "url_recherche": reverse("admin:recherche"),
        })
        return contexte

    # ------------------------------------------------------------------
    # Pages en plus : recherche globale, enregistrement rapide des chiffres
    # ------------------------------------------------------------------
    def get_urls(self):
        urls = [
            path("recherche/", self.admin_view(self.recherche), name="recherche"),
            path("chiffres-rapides/", self.admin_view(self.chiffres_rapides), name="chiffres_rapides"),
        ]
        return urls + super().get_urls()

    def recherche(self, request):
        q = request.GET.get("q", "").strip()
        user = request.user
        groupes = []
        if len(q) >= 2:
            sources = [
                ("Opportunités", "contenus", "opportunite", Q(titre__icontains=q) | Q(organisme__icontains=q)),
                ("Actualités et événements", "contenus", "actualite", Q(titre__icontains=q) | Q(chapeau__icontains=q)),
                ("Initiatives", "contenus", "initiative", Q(titre__icontains=q) | Q(resume__icontains=q)),
                ("Membres individuels", "comptes", "profilindividuel",
                 Q(prenom__icontains=q) | Q(nom__icontains=q) | Q(utilisateur__email__icontains=q)),
                ("Organisations et entreprises", "comptes", "profilorganisation",
                 Q(nom_structure__icontains=q) | Q(utilisateur__email__icontains=q)),
                ("Communautés", "communautes", "communaute", Q(nom__icontains=q)),
                ("Messages de contact", "core", "messagecontact",
                 Q(nom__icontains=q) | Q(objet__icontains=q) | Q(email__icontains=q)),
                ("Abonnés newsletter", "core", "abonnenewsletter", Q(email__icontains=q)),
            ]
            for titre, app_label, modele, filtre in sources:
                if not peut_voir(user, f"{app_label}.{modele}"):
                    continue
                Modele = apps.get_model(app_label, modele)
                resultats = list(Modele.objects.filter(filtre)[:8])
                if resultats:
                    groupes.append({
                        "titre": titre,
                        "elements": [
                            {"texte": str(r), "url": reverse(f"admin:{app_label}_{modele}_change", args=[r.pk])}
                            for r in resultats
                        ],
                        "tout": reverse(f"admin:{app_label}_{modele}_changelist") + "?" + urlencode({"q": q}),
                    })
        contexte = {
            **self.each_context(request),
            "title": "Recherche",
            "q": q,
            "groupes": groupes,
        }
        return render(request, "admin/recherche.html", contexte)

    def chiffres_rapides(self, request):
        """Enregistre les valeurs des chiffres clés depuis le tableau de bord."""
        if request.method != "POST" or not request.user.has_perm("contenus.change_chiffrecle"):
            return HttpResponseRedirect(reverse("admin:index"))
        ChiffreCle = apps.get_model("contenus", "ChiffreCle")
        modifies = 0
        for chiffre in ChiffreCle.objects.all():
            valeur = request.POST.get(f"chiffre_{chiffre.pk}", "").strip()
            if valeur and valeur != chiffre.valeur:
                chiffre.valeur = valeur[:30]
                chiffre.save(update_fields=["valeur"])
                modifies += 1
        messages.success(request, f"Chiffres clés enregistrés ({modifies} modifié(s)).")
        return HttpResponseRedirect(reverse("admin:index"))

    # ------------------------------------------------------------------
    # Tableau de bord
    # ------------------------------------------------------------------
    def index(self, request, extra_context=None):
        extra_context = {**(extra_context or {}), **self.tableau_de_bord(request)}
        return super().index(request, extra_context)

    def tableau_de_bord(self, request):
        user = request.user
        Utilisateur = apps.get_model("comptes", "Utilisateur")
        MessageContact = apps.get_model("core", "MessageContact")
        AbonneNewsletter = apps.get_model("core", "AbonneNewsletter")
        Opportunite = apps.get_model("contenus", "Opportunite")
        ChiffreCle = apps.get_model("contenus", "ChiffreCle")

        maintenant = timezone.now()
        debut_mois = timezone.localtime(maintenant).replace(day=1, hour=0, minute=0, second=0, microsecond=0)
        voit_membres = peut_voir(user, "comptes.utilisateur") or peut_voir(user, "comptes.profilindividuel")
        cartes = []

        if voit_membres:
            membres = Utilisateur.objects.filter(is_staff=False)
            cartes.append({
                "libelle": "Membres inscrits",
                "valeur": membres.count(),
                "detail": f"+{membres.filter(date_joined__gte=debut_mois).count()} ce mois-ci",
                "icone": ICONES["personne"],
                "couleur": "vert",
                "url": _url("admin:comptes_profilindividuel_changelist"),
            })
        if peut_voir(user, "contenus.opportunite"):
            publiees = Opportunite.objects.publies()
            fin_mois = (debut_mois + timedelta(days=32)).replace(day=1)
            closent = publiees.filter(date_limite__gte=maintenant.date(), date_limite__lt=fin_mois.date()).count()
            cartes.append({
                "libelle": "Opportunités publiées",
                "valeur": publiees.count(),
                "detail": f"{closent} clôture{'nt' if closent > 1 else ''} ce mois-ci",
                "icone": ICONES["mallette"],
                "couleur": "orange",
                "url": _url("admin:contenus_opportunite_changelist"),
            })
        if peut_voir(user, "core.messagecontact"):
            non_lus = MessageContact.objects.filter(traite=False)
            dernier = non_lus.order_by("-recu_le").first()
            cartes.append({
                "libelle": "Messages non lus",
                "valeur": non_lus.count(),
                "detail": f"Dernier le {timezone.localtime(dernier.recu_le):%d/%m à %H h %M}" if dernier else "Aucun en attente",
                "icone": ICONES["enveloppe"],
                "couleur": "turquoise",
                "url": _url("admin:core_messagecontact_changelist") + "?traite__exact=0",
            })
        if peut_voir(user, "core.abonnenewsletter"):
            abonnes = AbonneNewsletter.objects.filter(actif=True)
            cartes.append({
                "libelle": "Abonnés newsletter",
                "valeur": abonnes.count(),
                "detail": f"+{abonnes.filter(inscrit_le__gte=debut_mois).count()} ce mois-ci",
                "icone": ICONES["lettre"],
                "couleur": "bleu",
                "url": _url("admin:core_abonnenewsletter_changelist"),
            })

        inscriptions = []
        if voit_membres:
            for u in Utilisateur.objects.filter(is_staff=False).select_related(
                "profil_individuel", "profil_organisation"
            ).order_by("-date_joined")[:5]:
                profil = u.profil
                url = None
                if profil is not None:
                    nom_modele = "profilorganisation" if u.est_organisation else "profilindividuel"
                    url = _url(f"admin:comptes_{nom_modele}_change", profil.pk)
                inscriptions.append({
                    "nom": u.nom_affiche,
                    "initiales": u.initiales,
                    "profil": "Organisation" if u.est_organisation else "Membre individuel",
                    "region": profil.get_region_display() if profil is not None and profil.region else "—",
                    "date": u.date_joined,
                    "url": url or _url("admin:comptes_utilisateur_change", u.pk),
                })

        bientot = []
        if peut_voir(user, "contenus.opportunite"):
            ouvertes = Opportunite.objects.publies().ouvertes().filter(date_limite__isnull=False)
            for o in ouvertes.order_by("date_limite")[:3]:
                bientot.append({
                    "titre": o.titre,
                    "jours": o.jours_restants,
                    "date": o.date_limite,
                    "url": _url("admin:contenus_opportunite_change", o.pk),
                })

        actions = []
        for cle, libelle, icone in [
            ("contenus.opportunite", "Ajouter une opportunité", "mallette"),
            ("contenus.actualite", "Publier une actualité", "journal"),
            ("contenus.initiative", "Ajouter une initiative", "drapeau"),
            ("contenus.partenaire", "Ajouter un partenaire", "batiment"),
            ("contenus.temoignage", "Ajouter un témoignage", "bulle"),
        ]:
            app_label, modele = cle.split(".")
            if user.has_perm(f"{app_label}.add_{modele}"):
                actions.append({
                    "libelle": libelle,
                    "url": _url(f"admin:{app_label}_{modele}_add"),
                    "icone": ICONES[icone],
                })

        chiffres = []
        if user.has_perm("contenus.change_chiffrecle"):
            chiffres = list(ChiffreCle.objects.order_by("ordre", "pk")[:4])

        prenom = user.first_name
        profil = getattr(user, "profil", None)
        if not prenom and profil is not None and hasattr(profil, "prenom"):
            prenom = profil.prenom

        return {
            "cartes_tdb": cartes,
            "inscriptions": inscriptions,
            "bientot_closes": bientot,
            "actions_rapides": actions,
            "action_principale": actions[0] if actions else None,
            "chiffres_tdb": chiffres,
            "url_membres": _url("admin:comptes_profilindividuel_changelist") if voit_membres else None,
            "prenom_admin": prenom,
        }


class AdminYouthConnektMaliConfig(AdminConfig):
    """Remplace l'administration standard par celle de YouthConnekt Mali (voir settings)."""

    default_site = "core.admin_site.AdminYouthConnektMali"
