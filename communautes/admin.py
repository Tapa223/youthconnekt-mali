from django.contrib import admin, messages
from django.db.models import Count, Q
from django.urls import reverse
from django.utils.html import format_html

from core.admin_outils import badge

from .models import Communaute, DemandeAdhesion


@admin.register(Communaute)
class CommunauteAdmin(admin.ModelAdmin):
    list_display = ("nom", "public_vise", "nombre_membres_affiche", "demandes_en_attente", "ordre", "active")
    list_editable = ("ordre", "active")
    list_filter = ("active",)
    search_fields = ("nom", "public_vise")
    prepopulated_fields = {"slug": ("nom",)}

    def get_queryset(self, request):
        return super().get_queryset(request).annotate(
            nb_attente=Count("demandes", filter=Q(demandes__statut=DemandeAdhesion.Statut.EN_ATTENTE))
        )

    @admin.display(description="membres")
    def nombre_membres_affiche(self, obj):
        return obj.nombre_membres()

    @admin.display(description="demandes en attente", ordering="nb_attente")
    def demandes_en_attente(self, obj):
        if not obj.nb_attente:
            return "—"
        lien = (reverse("admin:communautes_demandeadhesion_changelist")
                + f"?statut__exact=en_attente&communaute__id__exact={obj.pk}")
        return format_html('<a href="{}">{}</a>', lien, badge(f"{obj.nb_attente} à traiter", "orange"))

    def has_delete_permission(self, request, obj=None):
        # On désactive une communauté plutôt que de la supprimer,
        # pour ne pas perdre l'historique des membres.
        return request.user.is_superuser


@admin.register(DemandeAdhesion)
class DemandeAdhesionAdmin(admin.ModelAdmin):
    list_display = ("membre", "communaute", "statut_badge", "demandee_le", "traitee_le", "traitee_par")
    list_filter = ("statut", "communaute")
    search_fields = ("membre__email", "communaute__nom")
    readonly_fields = ("membre", "communaute", "demandee_le", "traitee_le", "traitee_par")
    actions = ["accepter", "refuser"]
    list_select_related = ("membre", "communaute", "traitee_par")

    def has_add_permission(self, request):
        # Les demandes viennent des membres, depuis le site.
        return False

    @admin.display(description="statut", ordering="statut")
    def statut_badge(self, obj):
        couleur = {"en_attente": "orange", "acceptee": "vert", "refusee": "rouge"}.get(obj.statut, "gris")
        return badge(obj.get_statut_display(), couleur)

    def _traiter(self, request, queryset, statut, libelle):
        total = 0
        for demande in queryset.exclude(statut=statut):
            demande.marquer(statut, request.user)
            total += 1
        self.message_user(request, f"{total} demande(s) {libelle}.", messages.SUCCESS)

    @admin.action(description="Accepter les demandes sélectionnées")
    def accepter(self, request, queryset):
        self._traiter(request, queryset, DemandeAdhesion.Statut.ACCEPTEE, "acceptée(s)")

    @admin.action(description="Refuser les demandes sélectionnées")
    def refuser(self, request, queryset):
        self._traiter(request, queryset, DemandeAdhesion.Statut.REFUSEE, "refusée(s)")
