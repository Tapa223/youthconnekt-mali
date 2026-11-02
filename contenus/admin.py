from django.contrib import admin, messages

from core.admin_outils import apercu, apercu_grand, badge

from .models import (
    Actualite,
    AxeAction,
    ChiffreCle,
    Initiative,
    MotDuMinistre,
    Opportunite,
    Partenaire,
    PhotoActualite,
    PhotoInitiative,
    Statut,
    Temoignage,
)


class PubliableAdmin(admin.ModelAdmin):
    """Base commune : publication, mise en avant, recherche et aperçu."""

    list_filter = ("statut", "mis_en_avant", "publie_le")
    search_fields = ("titre",)
    date_hierarchy = "publie_le"
    prepopulated_fields = {"slug": ("titre",)}
    actions = ["publier", "depublier", "mettre_en_avant", "retirer_mise_en_avant"]
    list_per_page = 25

    readonly_fields = ("apercu_formulaire",)

    @admin.display(description="image")
    def apercu_image(self, obj):
        return apercu(getattr(obj, "image", None))

    @admin.display(description="aperçu de l'image")
    def apercu_formulaire(self, obj):
        return apercu_grand(getattr(obj, "image", None))

    @admin.display(description="statut", ordering="statut")
    def statut_badge(self, obj):
        if obj.statut == Statut.PUBLIE:
            return badge("Publié", "vert")
        return badge("Brouillon", "gris")

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
    list_display = ("nom", "couleur", "ordre")
    list_editable = ("ordre",)
    prepopulated_fields = {"slug": ("nom",)}
    search_fields = ("nom",)


class PhotoInitiativeInline(admin.TabularInline):
    model = PhotoInitiative
    extra = 1
    fields = ("image", "legende", "ordre")


@admin.register(Initiative)
class InitiativeAdmin(PubliableAdmin):
    list_display = ("apercu_image", "titre", "axe", "etat", "statut_badge", "mis_en_avant", "publie_le")
    list_display_links = ("apercu_image", "titre")
    list_filter = ("statut", "etat", "mis_en_avant", "axe")
    search_fields = ("titre", "resume")
    inlines = [PhotoInitiativeInline]
    fieldsets = (
        (None, {"fields": ("titre", "axe", "etat", "resume", "contenu", "image", "apercu_formulaire")}),
        ("Informations pratiques", {"fields": ("date_libelle", "lieu", "participants", "format")}),
        ("Détails", {"fields": ("objectifs", "programme", "chiffres", "citation", "citation_auteur")}),
        ("Publication", {"fields": ("statut", "publie_le", "mis_en_avant", "slug")}),
    )


@admin.register(Opportunite)
class OpportuniteAdmin(PubliableAdmin):
    list_display = ("apercu_image", "titre", "type", "date_limite", "etat", "statut_badge", "mis_en_avant")
    list_display_links = ("apercu_image", "titre")
    list_filter = ("statut", "type", "mis_en_avant", "communautes")
    search_fields = ("titre", "organisme", "resume")
    filter_horizontal = ("communautes",)
    date_hierarchy = "date_limite"
    fieldsets = (
        (None, {"fields": ("titre", "type", "organisme", "resume", "description", "image", "apercu_formulaire")}),
        ("Informations pratiques", {"fields": ("lieu", "duree", "public", "cout")}),
        ("Détails", {"fields": ("avantages", "criteres", "etapes")}),
        ("Candidature", {"fields": ("date_limite", "lien_candidature")}),
        ("Ciblage", {"fields": ("communautes",)}),
        ("Publication", {"fields": ("statut", "publie_le", "mis_en_avant", "slug")}),
    )

    @admin.display(description="état")
    def etat(self, obj):
        if obj.est_expiree:
            return badge("Expirée", "rouge")
        if obj.jours_restants is not None and obj.jours_restants <= 7:
            return badge(f"Ferme dans {obj.jours_restants} j", "orange")
        return badge("Ouverte", "vert")


class PhotoActualiteInline(admin.TabularInline):
    model = PhotoActualite
    extra = 1
    fields = ("image", "legende", "ordre")


@admin.register(Actualite)
class ActualiteAdmin(PubliableAdmin):
    list_display = ("apercu_image", "titre", "categorie", "statut_badge", "mis_en_avant", "publie_le")
    list_display_links = ("apercu_image", "titre")
    list_filter = ("statut", "categorie", "mis_en_avant")
    search_fields = ("titre", "chapeau", "contenu")
    filter_horizontal = ("communautes",)
    inlines = [PhotoActualiteInline]
    fieldsets = (
        (None, {"fields": ("titre", "categorie", "chapeau", "contenu", "image", "apercu_formulaire")}),
        ("Événement", {"fields": ("date_evenement", "lieu")}),
        ("Ciblage", {"fields": ("communautes",)}),
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
    fieldsets = (
        (None, {
            "description": "Tant que « Publié » n'est pas coché, l'accueil affiche le texte proposé par la maquette, "
                           "avec la mention « Proposition de texte, à valider par le cabinet du ministre ».",
            "fields": ("nom", "titre_officiel", "photo", "apercu_photo", "extrait", "message", "publie"),
        }),
    )

    @admin.display(description="aperçu")
    def apercu_photo(self, obj):
        return apercu_grand(obj.photo)


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
