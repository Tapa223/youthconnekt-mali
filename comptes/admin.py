import csv

from django.contrib import admin, messages
from django.contrib.auth.admin import UserAdmin
from django.contrib.auth.forms import UserChangeForm, UserCreationForm
from django.http import HttpResponse

from core.admin_outils import badge

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

    list_display = ("email", "nom_affiche", "type_compte", "etat_email", "etat_compte", "date_joined")
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
        "exporter_csv",
        "desactiver_comptes",
        "reactiver_comptes",
    ]
    list_per_page = 50

    fieldsets = (
        (None, {"fields": ("email", "password", "type_compte")}),
        ("État du compte", {"fields": ("is_active", "email_confirme")}),
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

    @admin.display(description="nom affiché")
    def nom_affiche(self, obj):
        return obj.nom_affiche

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

    @admin.action(description="Exporter en CSV (Excel)")
    def exporter_csv(self, request, queryset):
        reponse = HttpResponse(content_type="text/csv; charset=utf-8")
        reponse["Content-Disposition"] = 'attachment; filename="membres-youthconnekt-mali.csv"'
        reponse.write("\ufeff")  # pour qu'Excel lise correctement les accents
        ecrivain = csv.writer(reponse, delimiter=";")
        ecrivain.writerow(["E-mail", "Nom", "Type de compte", "E-mail confirmé", "Actif", "Inscrit le"])
        for u in queryset:
            ecrivain.writerow([
                u.email,
                u.nom_affiche,
                u.get_type_compte_display(),
                "oui" if u.email_confirme else "non",
                "oui" if u.is_active else "non",
                u.date_joined.strftime("%d/%m/%Y"),
            ])
        return reponse

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
