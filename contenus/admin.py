from django.contrib import admin, messages
from django.utils.html import format_html

from .models import (
    Actualite,
    AxeAction,
    ChiffreCle,
    Initiative,
    MotDuMinistre,
    Opportunite,
    Partenaire,
    PhotoInitiative,
    Statut,
    Temoignage,
)


def apercu(image, hauteur=48):
    if not image:
        return "—"
    return format_html(
        '<img src="{}" alt="" style="height:{}px;width:auto;border-radius:6px;object-fit:cover">',
        image.url,
        hauteur,
    )


class PubliableAdmin(admin.ModelAdmin):
    """Base commune : publication, mise en avant, recherche et aperçu."""

    list_filter = ("statut", "mis_en_avant", "publie_le")
    search_fields = ("titre",)
    date_hierarchy = "publie_le"
    prepopulated_fields = {"slug": ("titre",)}
    actions = ["publier", "depublier", "mettre_en_avant", "retirer_mise_en_avant"]
    list_per_page = 25

    @admin.display(description="image")
    def apercu_image(self, obj):
        return apercu(getattr(obj, "image", None))

    @admin.action(description="Publier")
    def publier(self, request, queryset):
        n = queryset.update(statut=Statut.PUBLIE)
        self.message_user(request, f"{n} élément(s) publié(s).", messages.SUCCESS)

    @admin.action(description="Repasser en brouillon")
    def depublier(self, request, queryset):
        n = queryset.update(statut=Statut.BROUILLON)
        self.message_user(request, f"{n} élément(s) repassé(s) en brouillon.", messages.SUCCESS)

    @admin.action(description="Mettre en avant sur l'accueil")
    def mettre_en_avant(self, request, queryset):
        n = queryset.update(mis_en_avant=True)
        self.message_user(request, f"{n} élément(s) mis en avant.", messages.SUCCESS)

    @admin.action(description="Retirer de l'accueil")
    def retirer_mise_en_avant(self, request, queryset):
        n = queryset.update(mis_en_avant=False)
        self.message_user(request, f"{n} élément(s) retiré(s) de l'accueil.", messages.SUCCESS)


@admin.register(AxeAction)
class AxeActionAdmin(admin.ModelAdmin):
    list_display = ("nom", "ordre")
    list_editable = ("ordre",)
    prepopulated_fields = {"slug": ("nom",)}
    search_fields = ("nom",)


class PhotoInitiativeInline(admin.TabularInline):
    model = PhotoInitiative
    extra = 1
    fields = ("image", "legende", "ordre")


@admin.register(Initiative)
class InitiativeAdmin(PubliableAdmin):
    list_display = ("apercu_image", "titre", "axe", "statut", "mis_en_avant", "publie_le")
    list_display_links = ("apercu_image", "titre")
    list_filter = ("statut", "mis_en_avant", "axe")
    search_fields = ("titre", "resume")
    inlines = [PhotoInitiativeInline]
    fieldsets = (
        (None, {"fields": ("titre", "axe", "resume", "contenu", "image")}),
        ("Publication", {"fields": ("statut", "publie_le", "mis_en_avant", "slug")}),
    )


@admin.register(Opportunite)
class OpportuniteAdmin(PubliableAdmin):
    list_display = ("titre", "type", "date_limite", "etat", "statut", "mis_en_avant")
    list_filter = ("statut", "type", "mis_en_avant", "communautes")
    search_fields = ("titre", "organisme", "resume")
    filter_horizontal = ("communautes",)
    date_hierarchy = "date_limite"
    fieldsets = (
        (None, {"fields": ("titre", "type", "organisme", "resume", "description", "criteres", "image")}),
        ("Candidature", {"fields": ("date_limite", "lien_candidature")}),
        ("Ciblage", {"fields": ("communautes",)}),
        ("Publication", {"fields": ("statut", "publie_le", "mis_en_avant", "slug")}),
    )

    @admin.display(description="état")
    def etat(self, obj):
        if obj.est_expiree:
            return format_html('<span style="color:#7C0D02;font-weight:600">{}</span>', "Expirée")
        return format_html('<span style="color:#035E4D;font-weight:600">{}</span>', "Ouverte")


@admin.register(Actualite)
class ActualiteAdmin(PubliableAdmin):
    list_display = ("apercu_image", "titre", "categorie", "statut", "mis_en_avant", "publie_le")
    list_display_links = ("apercu_image", "titre")
    list_filter = ("statut", "categorie", "mis_en_avant")
    search_fields = ("titre", "chapeau", "contenu")
    fieldsets = (
        (None, {"fields": ("titre", "categorie", "chapeau", "contenu", "image")}),
        ("Publication", {"fields": ("statut", "publie_le", "mis_en_avant", "slug")}),
    )


class FicheUniqueAdmin(admin.ModelAdmin):
    """Fiches uniques : pas d'ajout multiple ni de suppression."""

    def has_add_permission(self, request):
        return not self.model.objects.exists()

    def has_delete_permission(self, request, obj=None):
        return False


@admin.register(MotDuMinistre)
class MotDuMinistreAdmin(FicheUniqueAdmin):
    list_display = ("__str__", "nom", "publie")
    readonly_fields = ("apercu_photo",)
    fields = ("nom", "titre_officiel", "photo", "apercu_photo", "extrait", "message", "publie")

    @admin.display(description="aperçu")
    def apercu_photo(self, obj):
        return apercu(obj.photo, 120)


@admin.register(Partenaire)
class PartenaireAdmin(admin.ModelAdmin):
    list_display = ("apercu_logo", "nom", "ordre", "actif")
    list_display_links = ("apercu_logo", "nom")
    list_editable = ("ordre", "actif")
    search_fields = ("nom",)

    @admin.display(description="logo")
    def apercu_logo(self, obj):
        return apercu(obj.logo, 36)


@admin.register(Temoignage)
class TemoignageAdmin(admin.ModelAdmin):
    list_display = ("nom", "role", "ordre", "publie")
    list_editable = ("ordre", "publie")
    search_fields = ("nom", "texte")


@admin.register(ChiffreCle)
class ChiffreCleAdmin(admin.ModelAdmin):
    list_display = ("valeur", "libelle", "ordre", "publie")
    list_editable = ("ordre", "publie")
