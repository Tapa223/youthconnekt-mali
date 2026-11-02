from django.contrib import admin, messages
from django.utils import timezone
from django.utils.html import format_html

from core.admin_outils import AdminYCM, FicheUniqueYCM, apercu, apercu_grand, badge, nom_et_detail

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

COULEURS_CSS = {
    "orange": "#F1592A",
    "turquoise": "#04B2B1",
    "bleu": "#0358D9",
    "vert": "#035E4D",
    "rouge": "#7C0D02",
}

PUBLICATION = ("Publication", {"classes": ("cote",), "fields": ("statut", "publie_le", "mis_en_avant", "slug")})


class PubliableAdmin(AdminYCM):
    """Base commune : publication, mise en avant, recherche et aperçu."""

    list_filter = ("statut", "mis_en_avant")
    search_fields = ("titre",)
    prepopulated_fields = {"slug": ("titre",)}
    actions = ["publier", "depublier", "mettre_en_avant", "retirer_mise_en_avant"]
    readonly_fields = ("apercu_formulaire",)
    feminin = False

    @admin.display(description="image")
    def apercu_image(self, obj):
        return apercu(getattr(obj, "image", None), 40)

    @admin.display(description="aperçu de l'image")
    def apercu_formulaire(self, obj):
        return apercu_grand(getattr(obj, "image", None))

    @admin.display(description="statut", ordering="statut")
    def statut_badge(self, obj):
        e = "e" if self.feminin else ""
        if obj.statut == Statut.PUBLIE:
            return badge(f"Publié{e}", "vert")
        return badge("Brouillon", "gris")

    @admin.display(description="date", ordering="publie_le")
    def date_courte(self, obj):
        return obj.publie_le.strftime("%d/%m/%Y") if obj.publie_le else "—"

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


# ----------------------------------------------------------------------
# Opportunités
# ----------------------------------------------------------------------
@admin.register(Opportunite)
class OpportuniteAdmin(PubliableAdmin):
    titre_liste = "Opportunités"
    description_liste = "Gérez les opportunités publiées sur le site."
    libelle_ajout = "Ajouter une opportunité"
    titre_ajout = "Ajouter une opportunité"
    libelle_tous = "Toutes"
    feminin = True
    filtres_select = {"type": "Tous les types"}
    aide_cote = "Une opportunité passe automatiquement en « clôturée » après sa date limite."

    colonnes_csv = ("titre", "organisme", "type", "date_limite", "lieu", "statut", "lien_candidature")
    list_display = ("titre_organisme", "type", "date_limite_courte", "restant", "statut_badge", "actions_ligne")
    list_display_links = ("titre_organisme",)
    list_filter = ("statut", "type", "mis_en_avant", "communautes")
    search_fields = ("titre", "organisme", "resume")
    ordering = ("-publie_le",)
    fieldsets = (
        ("Informations principales", {"fields": (
            "titre", ("type", "organisme"), ("date_limite", "lieu"), ("duree", "public", "cout"), "lien_candidature",
        )}),
        ("Contenu", {"fields": ("resume", "description", "avantages", "criteres", "etapes")}),
        ("Image", {"fields": ("image", "apercu_formulaire")}),
        ("Ciblage", {
            "description": "Les membres de ces communautés verront l'opportunité dans leur espace.",
            "fields": ("communautes",),
        }),
        PUBLICATION,
    )

    def get_onglets(self, request):
        return (
            ("Publiées", {"statut__exact": "publie"}),
            ("Brouillons", {"statut__exact": "brouillon"}),
            ("Clôturées", {"date_limite__lt": timezone.localdate().isoformat()}),
        )

    @admin.display(description="opportunité", ordering="titre")
    def titre_organisme(self, obj):
        return nom_et_detail(obj.titre, obj.organisme)

    @admin.display(description="date limite", ordering="date_limite")
    def date_limite_courte(self, obj):
        return obj.date_limite.strftime("%d/%m/%Y") if obj.date_limite else "—"

    @admin.display(description="restant")
    def restant(self, obj):
        if obj.date_limite is None or obj.est_expiree:
            return format_html('<span class="ycm-jours">{}</span>', "—")
        classe = "ycm-jours ycm-jours--urgent" if obj.jours_restants <= 25 else "ycm-jours"
        return format_html('<span class="{}">J-{}</span>', classe, obj.jours_restants)

    @admin.display(description="statut", ordering="statut")
    def statut_badge(self, obj):
        if obj.statut == Statut.PUBLIE and obj.est_expiree:
            return badge("Clôturée", "rouge")
        return super().statut_badge(obj)


# ----------------------------------------------------------------------
# Actualités et événements
# ----------------------------------------------------------------------
class PhotoActualiteInline(admin.TabularInline):
    model = PhotoActualite
    extra = 1
    fields = ("image", "legende", "ordre")
    verbose_name_plural = "Galerie de photos (affichée là où l'article contient [photos])"


@admin.register(Actualite)
class ActualiteAdmin(PubliableAdmin):
    titre_liste = "Actualités et événements"
    description_liste = "Publiez des actualités et annoncez les événements du chapitre."
    libelle_ajout = "Publier une actualité"
    titre_ajout = "Publier une actualité"
    libelle_tous = "Tout"

    colonnes_csv = ("titre", "categorie", "date_affichee_courte", "lieu", "statut", "mis_en_avant")
    list_display = ("titre", "categorie_badge", "date_affichee_courte", "statut_badge", "a_la_une", "actions_ligne")
    list_display_links = ("titre",)
    list_filter = ("statut", "categorie", "mis_en_avant")
    search_fields = ("titre", "chapeau", "contenu")
    ordering = ("-publie_le",)
    inlines = [PhotoActualiteInline]
    onglets = (
        ("Événements", {"categorie__exact": "evenement"}),
        ("Actualités", {"categorie__exact": "actualite"}),
        ("Brouillons", {"statut__exact": "brouillon"}),
    )
    fieldsets = (
        ("Informations principales", {"fields": ("titre", "categorie", ("date_evenement", "lieu"), "chapeau")}),
        ("Article", {
            "description": "Mise en forme : « ## » au début d'une ligne pour un intertitre, « - » pour une liste, "
                           "« > » pour une citation, et [photos] pour placer la galerie.",
            "fields": ("contenu",),
        }),
        ("Image", {"fields": ("image", "apercu_formulaire")}),
        ("Ciblage", {"fields": ("communautes",)}),
        PUBLICATION,
    )

    def formfield_for_dbfield(self, db_field, request, **kwargs):
        champ = super().formfield_for_dbfield(db_field, request, **kwargs)
        if db_field.name == "contenu" and champ is not None:
            champ.widget.attrs["data-barre"] = "article"
        return champ

    @admin.display(description="catégorie", ordering="categorie")
    def categorie_badge(self, obj):
        if obj.categorie == Actualite.Categorie.EVENEMENT:
            return badge("Événement", "orange")
        return badge("Actualité", "turquoise")

    @admin.display(description="date", ordering="publie_le")
    def date_affichee_courte(self, obj):
        date = obj.date_affichee
        return date.strftime("%d/%m/%Y") if date else "—"

    @admin.display(description="à la une", ordering="mis_en_avant")
    def a_la_une(self, obj):
        return "Oui" if obj.mis_en_avant else "—"


# ----------------------------------------------------------------------
# Initiatives
# ----------------------------------------------------------------------
class PhotoInitiativeInline(admin.TabularInline):
    model = PhotoInitiative
    extra = 1
    fields = ("image", "legende", "ordre")
    verbose_name_plural = "Galerie « En images »"


@admin.register(Initiative)
class InitiativeAdmin(PubliableAdmin):
    titre_liste = "Initiatives"
    description_liste = "Les programmes et actions menés par le chapitre."
    libelle_ajout = "Ajouter une initiative"
    titre_ajout = "Ajouter une initiative"
    libelle_tous = "Toutes"
    feminin = True

    colonnes_csv = ("titre", "axe", "etat", "date_libelle", "lieu", "statut")
    list_display = ("image_titre", "axe_point", "date_libelle", "lieu", "statut_badge", "actions_ligne")
    list_display_links = ("image_titre",)
    list_filter = ("statut", "etat", "mis_en_avant", "axe")
    search_fields = ("titre", "resume")
    list_select_related = ("axe",)
    ordering = ("-publie_le",)
    inlines = [PhotoInitiativeInline]
    fieldsets = (
        ("Informations principales", {"fields": ("titre", ("axe", "etat"), "resume")}),
        ("Informations pratiques", {"fields": (("date_libelle", "lieu"), ("participants", "format"))}),
        ("Contenu", {"fields": ("contenu", "objectifs", "programme", "chiffres", ("citation", "citation_auteur"))}),
        ("Image", {"fields": ("image", "apercu_formulaire")}),
        PUBLICATION,
    )

    def get_onglets(self, request):
        return tuple((axe.nom, {"axe__id__exact": axe.pk}) for axe in AxeAction.objects.order_by("ordre"))

    @admin.display(description="initiative", ordering="titre")
    def image_titre(self, obj):
        return nom_et_detail(obj.titre, "", apercu(obj.image, 40) if obj.image else "")

    @admin.display(description="axe", ordering="axe__nom")
    def axe_point(self, obj):
        if not obj.axe:
            return "—"
        return format_html(
            '<span class="ycm-point" style="--couleur:{}">{}</span>',
            COULEURS_CSS.get(obj.axe.couleur, "#F1592A"),
            obj.axe.nom,
        )


# ----------------------------------------------------------------------
# Accueil et pages
# ----------------------------------------------------------------------
@admin.register(AxeAction)
class AxeActionAdmin(AdminYCM):
    titre_liste = "Axes d'action"
    description_liste = "Les trois façons d'agir du chapitre, affichées sur l'accueil et la page À propos."
    libelle_ajout = "Ajouter un axe"
    list_display = ("nom", "couleur_point", "ordre", "actions_ligne")
    list_display_links = ("nom",)
    list_editable = ("ordre",)
    prepopulated_fields = {"slug": ("nom",)}
    search_fields = ("nom",)
    export_csv = False
    fieldsets = (
        (None, {"fields": ("nom", "description", "elements")}),
        ("Image", {"fields": ("image",)}),
        ("Affichage", {"classes": ("cote",), "fields": ("couleur", "ordre", "slug")}),
    )

    @admin.display(description="couleur")
    def couleur_point(self, obj):
        return format_html(
            '<span class="ycm-point" style="--couleur:{}">{}</span>',
            COULEURS_CSS.get(obj.couleur, "#F1592A"),
            obj.get_couleur_display(),
        )


@admin.register(MotDuMinistre)
class MotDuMinistreAdmin(FicheUniqueYCM):
    titre_liste = "Mot du ministre"
    description_formulaire = "Le message affiché avant Let's Connekt sur l'accueil."
    readonly_fields = ("apercu_photo",)
    fieldsets = (
        (None, {
            "description": "Tant que « Afficher » n'est pas coché, l'accueil montre le texte proposé par la maquette, "
                           "avec la mention « Proposition de texte, à valider par le cabinet du ministre ».",
            "fields": ("nom", "titre_officiel", "extrait", "message", "publie"),
        }),
        ("Photo officielle", {"classes": ("cote",), "fields": ("photo", "apercu_photo")}),
    )

    @admin.display(description="aperçu")
    def apercu_photo(self, obj):
        return apercu_grand(obj.photo)


@admin.register(Partenaire)
class PartenaireAdmin(AdminYCM):
    titre_liste = "Partenaires"
    description_liste = "Les partenaires affichés sur le site. L'ordre ici est celui de l'accueil."
    libelle_ajout = "Ajouter un partenaire"
    titre_ajout = "Ajouter un partenaire"
    change_list_template = "admin/contenus/partenaire/change_list.html"
    list_display = ("nom", "ordre", "actif")
    list_editable = ("ordre", "actif")
    search_fields = ("nom",)
    list_per_page = 60
    export_csv = False
    readonly_fields = ("apercu_logo",)
    fieldsets = (
        (None, {"fields": ("nom", "lien")}),
        ("Logo", {"fields": ("logo", "apercu_logo")}),
        ("Affichage", {"classes": ("cote",), "fields": ("actif", "ordre")}),
    )

    @admin.display(description="aperçu")
    def apercu_logo(self, obj):
        return apercu_grand(obj.logo)


@admin.register(Temoignage)
class TemoignageAdmin(AdminYCM):
    titre_liste = "Témoignages"
    description_liste = "Témoignages de jeunes et de partenaires, publiés avec leur accord."
    libelle_ajout = "Ajouter un témoignage"
    titre_ajout = "Ajouter un témoignage"
    change_list_template = "admin/contenus/temoignage/change_list.html"
    list_display = ("nom", "role", "ordre", "publie")
    search_fields = ("nom", "texte")
    list_per_page = 60
    export_csv = False
    fieldsets = (
        (None, {"fields": ("texte", ("nom", "role"))}),
        ("Photo", {"fields": ("photo",)}),
        ("Affichage", {
            "classes": ("cote",),
            "description": "À publier uniquement avec l'accord de la personne.",
            "fields": ("publie", "ordre"),
        }),
    )


@admin.register(ChiffreCle)
class ChiffreCleAdmin(AdminYCM):
    titre_liste = "Chiffres clés"
    description_liste = "Les chiffres affichés sur l'accueil, dans la section du chapitre."
    libelle_ajout = "Ajouter un chiffre"
    change_list_template = "admin/contenus/chiffrecle/change_list.html"
    list_display = ("libelle", "valeur", "ordre", "publie")
    list_display_links = None
    list_editable = ("valeur", "ordre", "publie")
    export_csv = False
    actions = None
    fieldsets = (
        (None, {"fields": ("valeur", "libelle")}),
        ("Affichage", {"classes": ("cote",), "fields": ("publie", "ordre")}),
    )
