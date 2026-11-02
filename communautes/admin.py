from django.conf import settings
from django.contrib import admin, messages
from django.db.models import Count, Q
from django.http import HttpResponseRedirect
from django.urls import reverse
from django.utils.html import format_html

from core.admin_outils import AdminYCM, avatar, badge, nom_et_detail

from .models import Communaute, DemandeAdhesion

ATTENTE = DemandeAdhesion.Statut.EN_ATTENTE
ACCEPTEE = DemandeAdhesion.Statut.ACCEPTEE
REFUSEE = DemandeAdhesion.Statut.REFUSEE


@admin.register(Communaute)
class CommunauteAdmin(AdminYCM):
    """Maquette « Communautés » : demandes d'intégration en haut, communautés en cartes dessous."""

    titre_liste = "Communautés"
    description_liste = "Les communautés thématiques et les demandes d'intégration envoyées par les membres."
    libelle_ajout = "Créer une communauté"
    titre_ajout = "Créer une communauté"
    change_list_template = "admin/communautes/communaute/change_list.html"
    list_display = ("nom", "public_vise", "nombre_membres_affiche", "demandes_en_attente", "ordre", "active")
    list_filter = ("active",)
    search_fields = ("nom", "public_vise")
    prepopulated_fields = {"slug": ("nom",)}
    list_per_page = 60
    export_csv = False
    fieldsets = (
        (None, {"fields": ("nom", "public_vise", "description", "activites")}),
        ("Image", {"fields": ("image",)}),
        ("Affichage", {"classes": ("cote",), "fields": ("active", "ordre", "slug")}),
    )

    def get_queryset(self, request):
        return super().get_queryset(request).annotate(
            nb_attente=Count("demandes", filter=Q(demandes__statut=ATTENTE), distinct=True),
            nb_membres=Count("demandes", filter=Q(demandes__statut=ACCEPTEE), distinct=True),
            nb_opportunites=Count("opportunites", distinct=True),
        )

    def changelist_view(self, request, extra_context=None):
        demandes = []
        if request.user.has_perm("communautes.view_demandeadhesion") or request.user.has_perm(
            "communautes.change_demandeadhesion"
        ):
            attente = (
                DemandeAdhesion.objects.filter(statut=ATTENTE)
                .select_related("membre", "communaute", "membre__profil_individuel", "membre__profil_organisation")
                .order_by("-demandee_le")
            )
            for i, d in enumerate(attente[:20]):
                profil = d.membre.profil
                if profil is not None and hasattr(profil, "get_categorie_display"):
                    detail = profil.get_categorie_display()
                elif profil is not None:
                    detail = profil.get_type_structure_display()
                else:
                    detail = ""
                if profil is not None and profil.region:
                    detail = f"{detail} · {profil.get_region_display()}" if detail else profil.get_region_display()
                demandes.append({
                    "membre": d.membre.nom_affiche,
                    "initiales": d.membre.initiales,
                    "couleur": ["orange", "turquoise", "bleu", "vert"][i % 4],
                    "detail": detail,
                    "communaute": d.communaute.nom,
                    "date": d.demandee_le,
                    "url": reverse("admin:communautes_demandeadhesion_change", args=[d.pk]),
                })
            total_attente = attente.count()
        else:
            total_attente = None
        extra_context = {
            **(extra_context or {}),
            "demandes_attente": demandes,
            "total_attente": total_attente,
            "url_demandes": reverse("admin:communautes_demandeadhesion_changelist"),
            "acceptation_auto": getattr(settings, "COMMUNAUTES_ACCEPTATION_AUTOMATIQUE", False),
        }
        return super().changelist_view(request, extra_context)

    @admin.display(description="membres", ordering="nb_membres")
    def nombre_membres_affiche(self, obj):
        return obj.nb_membres

    @admin.display(description="demandes en attente", ordering="nb_attente")
    def demandes_en_attente(self, obj):
        return obj.nb_attente

    def has_delete_permission(self, request, obj=None):
        # On désactive une communauté plutôt que de la supprimer,
        # pour ne pas perdre l'historique des membres.
        return request.user.is_superuser


@admin.register(DemandeAdhesion)
class DemandeAdhesionAdmin(AdminYCM):
    titre_liste = "Demandes d'intégration"
    description_liste = "Toutes les demandes envoyées par les membres pour rejoindre une communauté."
    libelle_tous = "Toutes"
    onglets = (
        ("En attente", {"statut__exact": "en_attente"}),
        ("Acceptées", {"statut__exact": "acceptee"}),
        ("Refusées", {"statut__exact": "refusee"}),
    )
    filtres_select = {"communaute": "Toutes les communautés"}
    list_display = ("membre_nom", "communaute", "demandee_le_court", "statut_badge", "voir")
    list_display_links = None
    list_filter = ("statut", "communaute")
    search_fields = ("membre__email", "communaute__nom")
    readonly_fields = ("membre", "communaute", "statut", "demandee_le", "traitee_le", "traitee_par")
    fieldsets = (
        ("La demande", {"fields": ("membre", "communaute", "demandee_le")}),
        ("Décision", {"classes": ("cote",), "fields": ("statut", "traitee_le", "traitee_par")}),
    )
    actions = ["accepter", "refuser"]
    list_select_related = ("membre", "communaute", "traitee_par")
    change_form_template = "admin/communautes/demandeadhesion/change_form.html"
    nom_export = "demandes-integration"

    def has_add_permission(self, request):
        # Les demandes viennent des membres, depuis le site.
        return False

    def response_change(self, request, obj):
        for cle, statut, libelle in (("_accepter", ACCEPTEE, "acceptée"), ("_refuser", REFUSEE, "refusée")):
            if cle in request.POST and self.has_change_permission(request, obj):
                obj.marquer(statut, request.user)
                self.message_user(request, f"Demande de {obj.membre.nom_affiche} {libelle}.", messages.SUCCESS)
                return HttpResponseRedirect(reverse("admin:communautes_communaute_changelist"))
        return super().response_change(request, obj)

    @admin.display(description="membre", ordering="membre__email")
    def membre_nom(self, obj):
        return nom_et_detail(obj.membre.nom_affiche, obj.membre.email, avatar(obj.membre.initiales, obj.pk))

    @admin.display(description="date", ordering="demandee_le")
    def demandee_le_court(self, obj):
        return obj.demandee_le.strftime("%d/%m/%Y")

    @admin.display(description="statut", ordering="statut")
    def statut_badge(self, obj):
        couleur = {ATTENTE: "orange", ACCEPTEE: "vert", REFUSEE: "rouge"}.get(obj.statut, "gris")
        return badge(obj.get_statut_display(), couleur)

    @admin.display(description="actions")
    def voir(self, obj):
        return format_html(
            '<span class="ycm-actions-ligne"><a href="{}">Voir la demande</a></span>',
            reverse("admin:communautes_demandeadhesion_change", args=[obj.pk]),
        )

    def _traiter(self, request, queryset, statut, libelle):
        total = 0
        for demande in queryset.exclude(statut=statut):
            demande.marquer(statut, request.user)
            total += 1
        self.message_user(request, f"{total} demande(s) {libelle}.", messages.SUCCESS)

    @admin.action(description="Accepter les demandes sélectionnées")
    def accepter(self, request, queryset):
        self._traiter(request, queryset, ACCEPTEE, "acceptée(s)")

    @admin.action(description="Refuser les demandes sélectionnées")
    def refuser(self, request, queryset):
        self._traiter(request, queryset, REFUSEE, "refusée(s)")
