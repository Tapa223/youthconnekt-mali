from django.contrib import admin, messages
from django.contrib.auth.admin import UserAdmin
from django.contrib.auth.forms import UserChangeForm, UserCreationForm

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
class UtilisateurAdmin(UserAdmin):
    add_form = UtilisateurCreationForm
    form = UtilisateurChangeForm
    model = Utilisateur

    list_display = ("email", "nom_affiche", "type_compte", "email_confirme", "is_active", "date_joined")
    list_filter = ("type_compte", "email_confirme", "is_active", "is_staff", "groups")
    search_fields = (
        "email",
        "profil_individuel__prenom",
        "profil_individuel__nom",
        "profil_organisation__nom_structure",
    )
    ordering = ("-date_joined",)
    actions = ["desactiver_comptes", "reactiver_comptes"]

    fieldsets = (
        (None, {"fields": ("email", "password", "type_compte")}),
        ("État du compte", {"fields": ("is_active", "email_confirme")}),
        (
            "Accès à l'administration",
            {
                "classes": ("collapse",),
                "fields": ("is_staff", "is_superuser", "groups", "user_permissions"),
            },
        ),
        ("Dates", {"fields": ("last_login", "date_joined")}),
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

    def get_inlines(self, request, obj):
        if obj is None:
            return []
        return [ProfilOrganisationInline] if obj.est_organisation else [ProfilIndividuelInline]

    @admin.display(description="nom affiché")
    def nom_affiche(self, obj):
        return obj.nom_affiche

    @admin.action(description="Désactiver les comptes sélectionnés")
    def desactiver_comptes(self, request, queryset):
        # Un administrateur ne peut pas se désactiver lui-même par erreur.
        total = queryset.exclude(pk=request.user.pk).update(is_active=False)
        self.message_user(request, f"{total} compte(s) désactivé(s).", messages.SUCCESS)

    @admin.action(description="Réactiver les comptes sélectionnés")
    def reactiver_comptes(self, request, queryset):
        total = queryset.update(is_active=True)
        self.message_user(request, f"{total} compte(s) réactivé(s).", messages.SUCCESS)


@admin.register(ProfilIndividuel)
class ProfilIndividuelAdmin(admin.ModelAdmin):
    list_display = ("__str__", "categorie", "ville", "visible")
    list_filter = ("categorie", "visible", "ville")
    search_fields = ("prenom", "nom", "utilisateur__email", "domaine")
    autocomplete_fields = ("utilisateur",)


@admin.register(ProfilOrganisation)
class ProfilOrganisationAdmin(admin.ModelAdmin):
    list_display = ("nom_structure", "type_structure", "secteur", "ville", "visible")
    list_filter = ("type_structure", "visible", "ville")
    search_fields = ("nom_structure", "utilisateur__email", "secteur")
    autocomplete_fields = ("utilisateur",)
