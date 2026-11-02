"""
Site d'administration de YouthConnekt Mali.

On part de l'administration de Django et on la réorganise :
- le menu est rangé par rubriques métier (et non par application technique) ;
- la page d'accueil devient un tableau de bord : ce qu'il reste à traiter,
  les chiffres du site et des raccourcis pour publier.
"""

from datetime import timedelta

from django.apps import apps
from django.contrib import admin
from django.contrib.admin.apps import AdminConfig
from django.urls import NoReverseMatch, reverse
from django.utils import timezone

# Rubriques du menu, dans l'ordre d'affichage.
# Chaque modèle est désigné par "application.modele" (en minuscules).
RUBRIQUES = [
    ("Publications", "Initiatives, opportunités et actualités", [
        "contenus.actualite", "contenus.opportunite", "contenus.initiative",
    ]),
    ("Accueil et pages", "Mot du ministre, chiffres, axes, partenaires, témoignages", [
        "contenus.motduministre", "contenus.chiffrecle", "contenus.axeaction",
        "contenus.partenaire", "contenus.temoignage", "core.sectionapropos",
    ]),
    ("Communautés", "Communautés thématiques et demandes d'adhésion", [
        "communautes.demandeadhesion", "communautes.communaute",
    ]),
    ("Membres", "Comptes, profils et rôles des administrateurs", [
        "comptes.utilisateur", "comptes.profilindividuel", "comptes.profilorganisation",
        "auth.group",
    ]),
    ("Messages et newsletter", "Formulaire de contact et abonnés", [
        "core.messagecontact", "core.abonnenewsletter",
    ]),
    ("Réglages du site", "Coordonnées et réseaux sociaux officiels", [
        "core.parametressite",
    ]),
]


def _url(nom, *args):
    try:
        return reverse(nom, args=args)
    except NoReverseMatch:
        return None


class AdminYouthConnektMali(admin.AdminSite):
    site_header = "Administration YouthConnekt Mali"
    site_title = "YouthConnekt Mali"
    index_title = "Tableau de bord"
    site_url = "/"
    enable_nav_sidebar = True

    # ------------------------------------------------------------------
    # Menu rangé par rubriques
    # ------------------------------------------------------------------
    def get_app_list(self, request, app_label=None):
        liste = super().get_app_list(request, app_label)
        if app_label:
            # Page d'une application précise : on garde le comportement normal.
            return liste

        modeles = {}
        for app in liste:
            for modele in app["models"]:
                cle = f"{app['app_label']}.{modele['object_name'].lower()}"
                modeles[cle] = modele

        rubriques = []
        ranges = set()
        for numero, (nom, description, cles) in enumerate(RUBRIQUES, start=1):
            contenu = [modeles[c] for c in cles if c in modeles]
            ranges.update(c for c in cles if c in modeles)
            if contenu:
                rubriques.append({
                    "name": nom,
                    "description": description,
                    "app_label": f"rubrique{numero}",
                    "app_url": reverse("admin:index"),
                    "has_module_perms": True,
                    "models": contenu,
                })

        # Tout modèle oublié dans RUBRIQUES reste accessible.
        autres = [m for c, m in modeles.items() if c not in ranges]
        if autres:
            rubriques.append({
                "name": "Autres",
                "description": "",
                "app_label": "autres",
                "app_url": reverse("admin:index"),
                "has_module_perms": True,
                "models": autres,
            })
        return rubriques

    # ------------------------------------------------------------------
    # Tableau de bord
    # ------------------------------------------------------------------
    def index(self, request, extra_context=None):
        extra_context = {**(extra_context or {}), **self.tableau_de_bord(request)}
        return super().index(request, extra_context)

    def tableau_de_bord(self, request):
        user = request.user

        def peut_voir(cle):
            app_label, modele = cle.split(".")
            return user.has_perm(f"{app_label}.view_{modele}") or user.has_perm(
                f"{app_label}.change_{modele}"
            )

        Utilisateur = apps.get_model("comptes", "Utilisateur")
        DemandeAdhesion = apps.get_model("communautes", "DemandeAdhesion")
        MessageContact = apps.get_model("core", "MessageContact")
        AbonneNewsletter = apps.get_model("core", "AbonneNewsletter")
        Opportunite = apps.get_model("contenus", "Opportunite")
        Actualite = apps.get_model("contenus", "Actualite")
        Initiative = apps.get_model("contenus", "Initiative")

        il_y_a_7_jours = timezone.now() - timedelta(days=7)
        a_traiter, chiffres = [], []

        if peut_voir("communautes.demandeadhesion"):
            attente = DemandeAdhesion.objects.filter(statut=DemandeAdhesion.Statut.EN_ATTENTE)
            a_traiter.append({
                "titre": "Demandes d'adhésion en attente",
                "nombre": attente.count(),
                "lien": reverse("admin:communautes_demandeadhesion_changelist") + "?statut__exact=en_attente",
                "libelle_lien": "Traiter les demandes",
                "elements": [
                    {
                        "texte": f"{d.membre} · {d.communaute}",
                        "date": d.demandee_le,
                        "lien": reverse("admin:communautes_demandeadhesion_change", args=[d.pk]),
                    }
                    for d in attente.select_related("membre", "communaute").order_by("-demandee_le")[:4]
                ],
                "vide": "Aucune demande en attente.",
            })

        if peut_voir("core.messagecontact"):
            non_lus = MessageContact.objects.filter(traite=False)
            a_traiter.append({
                "titre": "Messages de contact à traiter",
                "nombre": non_lus.count(),
                "lien": reverse("admin:core_messagecontact_changelist") + "?traite__exact=0",
                "libelle_lien": "Lire les messages",
                "elements": [
                    {
                        "texte": f"{m.objet} · {m.nom}",
                        "date": m.recu_le,
                        "lien": reverse("admin:core_messagecontact_change", args=[m.pk]),
                    }
                    for m in non_lus.order_by("-recu_le")[:4]
                ],
                "vide": "Aucun message en attente.",
            })

        if peut_voir("comptes.utilisateur"):
            membres = Utilisateur.objects.filter(is_staff=False)
            chiffres += [
                {"valeur": membres.count(), "libelle": "membres inscrits",
                 "lien": reverse("admin:comptes_utilisateur_changelist") + "?is_staff__exact=0"},
                {"valeur": membres.filter(date_joined__gte=il_y_a_7_jours).count(),
                 "libelle": "nouveaux comptes cette semaine",
                 "lien": reverse("admin:comptes_utilisateur_changelist")},
                {"valeur": membres.filter(email_confirme=False).count(),
                 "libelle": "e-mails non confirmés",
                 "lien": reverse("admin:comptes_utilisateur_changelist") + "?email_confirme__exact=0"},
            ]
        if peut_voir("contenus.opportunite"):
            chiffres.append({
                "valeur": Opportunite.objects.publies().ouvertes().count(),
                "libelle": "opportunités ouvertes",
                "lien": reverse("admin:contenus_opportunite_changelist"),
            })
        if peut_voir("core.abonnenewsletter"):
            chiffres.append({
                "valeur": AbonneNewsletter.objects.filter(actif=True).count(),
                "libelle": "abonnés à la newsletter",
                "lien": reverse("admin:core_abonnenewsletter_changelist") + "?actif__exact=1",
            })

        brouillons = []
        for modele, cle, nom in [
            (Actualite, "contenus.actualite", "actualité"),
            (Opportunite, "contenus.opportunite", "opportunité"),
            (Initiative, "contenus.initiative", "initiative"),
        ]:
            if peut_voir(cle):
                n = modele.objects.filter(statut="brouillon").count()
                if n:
                    brouillons.append({
                        "texte": f"{n} {nom}{'s' if n > 1 else ''} en brouillon",
                        "lien": reverse(f"admin:{cle.replace('.', '_')}_changelist") + "?statut__exact=brouillon",
                    })

        raccourcis = []
        for cle, libelle in [
            ("contenus.actualite", "Publier une actualité"),
            ("contenus.opportunite", "Publier une opportunité"),
            ("contenus.initiative", "Ajouter une initiative"),
            ("contenus.partenaire", "Ajouter un partenaire"),
        ]:
            app_label, modele = cle.split(".")
            if user.has_perm(f"{app_label}.add_{modele}"):
                raccourcis.append({"libelle": libelle, "lien": _url(f"admin:{app_label}_{modele}_add")})

        return {
            "a_traiter": a_traiter,
            "chiffres_admin": chiffres,
            "brouillons": brouillons,
            "raccourcis": raccourcis,
        }


class AdminYouthConnektMaliConfig(AdminConfig):
    """Remplace l'administration standard par celle de YouthConnekt Mali (voir settings)."""

    default_site = "core.admin_site.AdminYouthConnektMali"
