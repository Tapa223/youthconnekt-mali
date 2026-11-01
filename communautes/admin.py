from django.contrib import admin, messages

from .models import Communaute, DemandeAdhesion


@admin.register(Communaute)
class CommunauteAdmin(admin.ModelAdmin):
    list_display = ("nom", "public_vise", "nombre_membres_affiche", "ordre", "active")
    list_editable = ("ordre", "active")
    list_filter = ("active",)
    search_fields = ("nom", "public_vise")
    prepopulated_fields = {"slug": ("nom",)}

    @admin.display(description="membres")
    def nombre_membres_affiche(self, obj):
        return obj.nombre_membres()

    def has_delete_permission(self, request, obj=None):
        # On désactive une communauté plutôt que de la supprimer,
        # pour ne pas perdre l'historique des membres.
        return request.user.is_superuser


@admin.register(DemandeAdhesion)
class DemandeAdhesionAdmin(admin.ModelAdmin):
    list_display = ("membre", "communaute", "statut", "demandee_le", "traitee_le", "traitee_par")
    list_filter = ("statut", "communaute")
    search_fields = ("membre__email", "communaute__nom")
    readonly_fields = ("membre", "communaute", "demandee_le", "traitee_le", "traitee_par")
    actions = ["accepter", "refuser"]
    list_select_related = ("membre", "communaute", "traitee_par")

    def has_add_permission(self, request):
        # Les demandes viennent des membres, depuis le site.
        return False

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
