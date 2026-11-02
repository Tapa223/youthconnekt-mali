import csv

from django.contrib import admin, messages
from django.contrib.auth.admin import UserAdmin
from django.contrib.auth.forms import UserChangeForm, UserCreationForm
from django.db.models import Count, Q
from django.http import HttpResponse
from django.urls import reverse
from django.utils.html import format_html

from communautes.models import DemandeAdhesion
from core.admin_outils import AdminYCM, avatar, badge, nom_et_detail

from .models import ProfilIndividuel, ProfilOrganisation, Utilisateur


class UtilisateurCreationForm(UserCreationForm):
    class Meta:
        model = Utilisateur
        fields = ("email", "type_compte")


class UtilisateurChangeForm(UserChangeForm):
    class Meta:
        model = Utilisateur
        fields = "__all__"


class ProfilIndividuelInline(admin.StackedInline):
    model = ProfilIndividuel
    can_delete = False
    extra = 0
    max_num = 1


class ProfilOrganisationInline(admin.StackedInline):
    model = ProfilOrganisation
    can_delete = False
    extra = 0
    max_num = 1


@admin.register(Utilisateur)
class UtilisateurAdmin(AdminYCM, UserAdmin):
    titre_liste = "Comptes et connexions"
    description_liste = "Tous les comptes : membres, organisations et équipe d'administration."
    libelle_ajout = "Créer un compte"
    titre_ajout = "Créer un compte"
    libelle_tous = "Tous"
    onglets = (
        ("Membres", {"is_staff__exact": "0"}),
        ("Équipe d'administration", {"is_staff__exact": "1"}),
        ("E-mail à confirmer", {"email_confirme__exact": "0"}),
        ("Désactivés", {"is_active__exact": "0"}),
    )
    filtres_select = {"type_compte": "Tous les types de compte"}
    nom_export = "comptes-youthconnekt-mali"
    add_form = UtilisateurCreationForm
    form = UtilisateurChangeForm
    model = Utilisateur

    colonnes_csv = ("email", "type_compte", "email_confirme", "is_active", "is_staff", "date_joined")
    list_display = ("compte", "type_compte", "etat_email", "etat_compte", "inscription", "actions_ligne")
    list_display_links = ("compte",)
    list_filter = ("type_compte", "email_confirme", "is_active", "is_staff", "groups")
    search_fields = (
        "email",
        "profil_individuel__prenom",
        "profil_individuel__nom",
        "profil_organisation__nom_structure",
    )
    ordering = ("-date_joined",)
    actions = [
        "renvoyer_confirmation",
        "confirmer_email",
        "desactiver_comptes",
        "reactiver_comptes",
    ]
    list_per_page = 50

    fieldsets = (
        (None, {"fields": ("email", "password", "type_compte")}),
        ("État du compte", {"classes": ("cote",), "fields": ("is_active", "email_confirme")}),
        (
            "Accès à l'administration",
            {
                "classes": ("collapse",),
                "description": "Pour donner accès à l'administration : cocher « statut équipe » "
                               "puis choisir un rôle dans « groupes » (Éditeur de contenus, Modération des communautés, "
                               "Gestion des membres). Réserver « super-utilisateur » aux responsables du site.",
                "fields": ("is_staff", "is_superuser", "groups", "user_permissions"),
            },
        ),
        ("Dates", {"classes": ("cote",), "fields": ("last_login", "date_joined")}),
    )
    add_fieldsets = (
        (
            None,
            {
                "classes": ("wide",),
                "fields": ("email", "type_compte", "password1", "password2"),
            },
        ),
    )
    readonly_fields = ("last_login", "date_joined")

    def get_readonly_fields(self, request, obj=None):
        # Seul un super-administrateur peut donner des droits d'administration :
        # un membre de l'équipe ne peut pas s'en attribuer davantage.
        champs = list(super().get_readonly_fields(request, obj))
        if not request.user.is_superuser:
            champs += ["is_staff", "is_superuser", "groups", "user_permissions"]
        return champs

    def get_inlines(self, request, obj):
        if obj is None:
            return []
        return [ProfilOrganisationInline] if obj.est_organisation else [ProfilIndividuelInline]

    @admin.display(description="compte", ordering="email")
    def compte(self, obj):
        return nom_et_detail(obj.nom_affiche, obj.email, avatar(obj.initiales, obj.pk))

    @admin.display(description="inscription", ordering="date_joined")
    def inscription(self, obj):
        return obj.date_joined.strftime("%d/%m/%Y")

    @admin.display(description="e-mail", ordering="email_confirme")
    def etat_email(self, obj):
        return badge("Confirmé", "vert") if obj.email_confirme else badge("À confirmer", "orange")

    @admin.display(description="compte", ordering="is_active")
    def etat_compte(self, obj):
        if not obj.is_active:
            return badge("Désactivé", "rouge")
        if obj.is_superuser:
            return badge("Super-admin", "vert")
        if obj.is_staff:
            return badge("Administrateur", "vert")
        return badge("Membre", "gris")

    @admin.action(description="Renvoyer l'e-mail de confirmation")
    def renvoyer_confirmation(self, request, queryset):
        from .views import envoyer_confirmation

        total = 0
        for utilisateur in queryset.filter(email_confirme=False, is_active=True):
            envoyer_confirmation(request, utilisateur)
            total += 1
        self.message_user(request, f"E-mail de confirmation renvoyé à {total} compte(s).", messages.SUCCESS)

    @admin.action(description="Marquer l'e-mail comme confirmé")
    def confirmer_email(self, request, queryset):
        total = queryset.update(email_confirme=True)
        self.message_user(request, f"{total} adresse(s) marquée(s) comme confirmée(s).", messages.SUCCESS)

    @admin.action(description="Désactiver les comptes sélectionnés")
    def desactiver_comptes(self, request, queryset):
        # Un administrateur ne peut pas se désactiver lui-même par erreur.
        total = queryset.exclude(pk=request.user.pk).update(is_active=False)
        self.message_user(request, f"{total} compte(s) désactivé(s).", messages.SUCCESS)

    @admin.action(description="Réactiver les comptes sélectionnés")
    def reactiver_comptes(self, request, queryset):
        total = queryset.update(is_active=True)
        self.message_user(request, f"{total} compte(s) réactivé(s).", messages.SUCCESS)


class ProfilAdminBase(AdminYCM):
    """Base des listes « Membres individuels » et « Organisations et entreprises » (maquette)."""

    def has_add_permission(self, request):
        # Les profils sont créés par les membres eux-mêmes, à l'inscription.
        return False

    libelle_tous = "Tous"
    autocomplete_fields = ("utilisateur",)
    list_select_related = ("utilisateur",)
    filtres_select = {"region": "Toutes les régions"}

    def get_queryset(self, request):
        return super().get_queryset(request).annotate(
            nb_communautes=Count(
                "utilisateur__demandes_adhesion",
                filter=Q(utilisateur__demandes_adhesion__statut=DemandeAdhesion.Statut.ACCEPTEE),
                distinct=True,
            )
        )

    @admin.display(description="région", ordering="region")
    def region_affichee(self, obj):
        return obj.get_region_display() if obj.region else "—"

    @admin.display(description="communautés", ordering="nb_communautes")
    def communautes(self, obj):
        n = obj.nb_communautes
        return f"{n} communauté{'s' if n > 1 else ''}"

    @admin.display(description="inscription", ordering="utilisateur__date_joined")
    def inscription(self, obj):
        return obj.utilisateur.date_joined.strftime("%d/%m/%Y")

    @admin.display(description="statut", ordering="utilisateur__is_active")
    def statut(self, obj):
        return badge(self.libelle_actif, "vert") if obj.utilisateur.is_active else badge(self.libelle_inactif, "gris")

    @admin.display(description="actions")
    def actions_ligne(self, obj):
        modifier = reverse(f"admin:comptes_{obj._meta.model_name}_change", args=[obj.pk])
        if obj.visible and obj.utilisateur.is_active:
            return format_html(
                '<span class="ycm-actions-ligne"><a href="{}" target="_blank" rel="noopener">Voir</a><a href="{}">Modifier</a></span>',
                obj.get_absolute_url(),
                modifier,
            )
        return format_html('<span class="ycm-actions-ligne"><a href="{}">Modifier</a></span>', modifier)

    @admin.display(description="compte")
    def lien_compte(self, obj):
        return format_html(
            '<a class="ycm-lien" href="{}">{}</a>',
            reverse("admin:comptes_utilisateur_change", args=[obj.utilisateur_id]),
            obj.utilisateur.email,
        )


@admin.register(ProfilIndividuel)
class ProfilIndividuelAdmin(ProfilAdminBase):
    titre_liste = "Membres individuels"
    description_liste = ("Jeunes, étudiants, jeunes professionnels, entrepreneurs, porteurs de projets, "
                         "innovateurs et leaders d'associations inscrits.")
    libelle_ajout = "Ajouter un membre"
    libelle_actif = "Actif"
    libelle_inactif = "Inactif"
    onglets = (("Actifs", {"utilisateur__is_active__exact": "1"}), ("Inactifs", {"utilisateur__is_active__exact": "0"}))
    filtres_select = {"region": "Toutes les régions", "categorie": "Toutes catégories"}
    colonnes_csv = ("prenom", "nom", "utilisateur", "categorie", "region", "ville", "domaine", "communautes", "inscription")
    list_display = ("membre", "categorie", "region_affichee", "communautes", "inscription", "statut", "actions_ligne")
    list_display_links = ("membre",)
    list_filter = ("categorie", "region", "visible", "utilisateur__is_active")
    search_fields = ("prenom", "nom", "utilisateur__email", "domaine", "ville")
    ordering = ("-utilisateur__date_joined",)
    nom_export = "membres-individuels"
    readonly_fields = ("lien_compte",)
    fieldsets = (
        ("Identité", {"fields": (("prenom", "nom"), ("categorie", "domaine"), "photo")}),
        ("Localisation et contact", {"fields": (("region", "ville"), "telephone")}),
        ("Présentation", {"fields": ("presentation", ("site_web", "lien_linkedin"))}),
        ("Compte", {"classes": ("cote",), "fields": ("lien_compte", "utilisateur", "visible")}),
    )

    @admin.display(description="membre", ordering="nom")
    def membre(self, obj):
        return nom_et_detail(f"{obj.prenom} {obj.nom}", obj.utilisateur.email, avatar(obj.initiales, obj.pk, obj.photo))


@admin.register(ProfilOrganisation)
class ProfilOrganisationAdmin(ProfilAdminBase):
    titre_liste = "Organisations et entreprises"
    description_liste = "Entreprises, startups, associations et ONG inscrites."
    libelle_ajout = "Ajouter une organisation"
    libelle_tous = "Toutes"
    libelle_actif = "Active"
    libelle_inactif = "Inactive"
    colonnes_csv = ("nom_structure", "type_structure", "secteur", "utilisateur", "personne_contact", "region", "ville", "inscription")
    list_display = ("organisation", "region_affichee", "personne_contact", "communautes", "statut", "actions_ligne")
    list_display_links = ("organisation",)
    list_filter = ("type_structure", "region", "visible", "utilisateur__is_active")
    search_fields = ("nom_structure", "utilisateur__email", "secteur", "personne_contact")
    ordering = ("-utilisateur__date_joined",)
    nom_export = "organisations"
    readonly_fields = ("lien_compte",)
    fieldsets = (
        ("Structure", {"fields": ("nom_structure", ("type_structure", "secteur"), "logo")}),
        ("Contact", {"fields": (("personne_contact", "email_contact_public"), ("region", "ville"))}),
        ("Présentation", {"fields": ("presentation", ("site_web", "lien_linkedin"))}),
        ("Compte", {"classes": ("cote",), "fields": ("lien_compte", "utilisateur", "visible")}),
    )

    def get_onglets(self, request):
        noms = {"entreprise": "Entreprises", "startup": "Startups", "association": "Associations", "ong": "ONG"}
        return tuple((libelle, {"type_structure__exact": cle}) for cle, libelle in noms.items())

    @admin.display(description="organisation", ordering="nom_structure")
    def organisation(self, obj):
        return nom_et_detail(obj.nom_structure, obj.get_type_structure_display(), avatar(obj.initiales, obj.pk, obj.logo))
