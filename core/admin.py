from urllib.parse import quote

from django.contrib import admin, messages
from django.contrib.auth import get_user_model
from django.http import HttpResponseRedirect
from django.shortcuts import get_object_or_404
from django.urls import path, reverse
from django.utils.html import format_html

from .admin_outils import AdminYCM, FicheUniqueYCM, badge
from .admin_site import initiales
from .models import AbonneNewsletter, MessageContact, ParametresSite, SectionAPropos


@admin.register(ParametresSite)
class ParametresSiteAdmin(FicheUniqueYCM):
    titre_liste = "Paramètres"
    description_formulaire = "Coordonnées, réseaux sociaux et comptes d'administration."
    change_form_template = "admin/core/parametressite/change_form.html"
    fieldsets = (
        ("Coordonnées du chapitre", {"fields": (("email_contact", "telephone"), ("adresse", "horaires"))}),
        ("Réseaux sociaux", {"fields": (
            ("lien_facebook", "lien_instagram"), ("lien_linkedin", "lien_x"), ("lien_youtube", "lien_tiktok"),
        )}),
        ("Textes de l'accueil", {
            "classes": ("collapse",),
            "description": "Laisser vide pour garder les textes validés de la maquette.",
            "fields": ("accroche_accueil", "presentation_accueil"),
        }),
    )

    def changeform_view(self, request, object_id=None, form_url="", extra_context=None):
        Utilisateur = get_user_model()
        equipe = []
        if request.user.has_perm("comptes.view_utilisateur") or request.user.is_superuser:
            for i, u in enumerate(Utilisateur.objects.filter(is_staff=True, is_active=True).order_by("-is_superuser", "email")):
                groupe = u.groups.order_by("name").first()
                equipe.append({
                    "nom": u.nom_affiche,
                    "initiales": initiales(u.nom_affiche),
                    "role": "Administrateur principal" if u.is_superuser else (groupe.name if groupe else "Administrateur"),
                    "url": reverse("admin:comptes_utilisateur_change", args=[u.pk]),
                    "couleur": ["orange", "turquoise", "bleu", "vert"][i % 4],
                })
        extra_context = {
            **(extra_context or {}),
            "equipe_admin": equipe,
            "peut_ajouter_admin": request.user.is_superuser,
        }
        return super().changeform_view(request, object_id, form_url, extra_context)


@admin.register(SectionAPropos)
class SectionAProposAdmin(AdminYCM):
    titre_liste = "Page À propos"
    description_liste = "Les blocs de texte de la page À propos, dans l'ordre d'affichage."
    libelle_ajout = "Ajouter un bloc"
    list_display = ("titre", "ordre", "etat", "actions_ligne")
    list_display_links = ("titre",)
    list_editable = ("ordre",)
    search_fields = ("titre", "contenu")
    export_csv = False
    fieldsets = (
        (None, {"fields": ("titre", "contenu")}),
        ("Image", {"fields": ("image",)}),
        ("Affichage", {"classes": ("cote",), "fields": ("publiee", "ordre")}),
    )

    @admin.display(description="statut", ordering="publiee")
    def etat(self, obj):
        return badge("Publiée", "vert") if obj.publiee else badge("Masquée", "gris")


@admin.register(MessageContact)
class MessageContactAdmin(AdminYCM):
    """Boîte de réception : la liste à gauche, le message ouvert à droite (maquette)."""

    titre_liste = "Messages de contact"
    description_liste = "Les messages envoyés depuis le formulaire de contact."
    change_list_template = "admin/core/messagecontact/change_list.html"
    libelle_tous = "Tous"
    onglets = (("Non lus", {"traite__exact": "0"}), ("Traités", {"traite__exact": "1"}))
    filtres_select = {"profil": "Tous les profils"}
    colonnes_csv = ("recu_le", "nom", "email", "profil", "objet", "message", "traite")
    list_display = ("objet", "nom", "profil", "email", "recu_le", "etat")
    list_filter = ("traite", "profil")
    search_fields = ("nom", "email", "objet", "message")
    readonly_fields = ("nom", "profil", "email", "objet", "message_lisible", "repondre", "recu_le")
    fields = ("recu_le", "nom", "profil", "email", "objet", "message_lisible", "repondre", "traite")
    actions = ["marquer_traites", "marquer_a_traiter"]
    list_per_page = 50

    def has_add_permission(self, request):
        return False

    def get_urls(self):
        return [
            path("<int:pk>/traiter/", self.admin_site.admin_view(self.traiter), name="core_messagecontact_traiter"),
        ] + super().get_urls()

    def changelist_view(self, request, extra_context=None):
        ouvert = None
        identifiant = request.GET.get("ouvert")
        if identifiant and identifiant.isdigit():
            ouvert = MessageContact.objects.filter(pk=identifiant).first()
        extra_context = {**(extra_context or {}), "message_ouvert": ouvert}
        return super().changelist_view(request, extra_context)

    def traiter(self, request, pk):
        """Bouton « Marquer comme traité » (ou « Remettre à traiter ») de la boîte de réception."""
        message = get_object_or_404(MessageContact, pk=pk)
        if request.method == "POST" and self.has_change_permission(request, message):
            message.traite = request.POST.get("traite") == "1"
            message.save(update_fields=["traite"])
            messages.success(request, "Message marqué comme traité." if message.traite else "Message remis à traiter.")
        retour = request.POST.get("retour") or reverse("admin:core_messagecontact_changelist")
        if not retour.startswith("/"):
            retour = reverse("admin:core_messagecontact_changelist")
        return HttpResponseRedirect(retour)

    @admin.display(description="état", ordering="traite")
    def etat(self, obj):
        return badge("Traité", "vert") if obj.traite else badge("À traiter", "orange")

    @admin.display(description="message")
    def message_lisible(self, obj):
        return format_html('<div class="ycm-message">{}</div>', obj.message)

    @admin.display(description="répondre")
    def repondre(self, obj):
        return format_html(
            '<a class="ycm-bouton-vert" href="mailto:{}?subject={}">Répondre par e-mail</a>',
            obj.email,
            quote(f"Re : {obj.objet}"),
        )

    @admin.action(description="Marquer comme traités")
    def marquer_traites(self, request, queryset):
        n = queryset.update(traite=True)
        self.message_user(request, f"{n} message(s) marqué(s) comme traité(s).", messages.SUCCESS)

    @admin.action(description="Remettre à traiter")
    def marquer_a_traiter(self, request, queryset):
        n = queryset.update(traite=False)
        self.message_user(request, f"{n} message(s) remis à traiter.", messages.SUCCESS)


@admin.register(AbonneNewsletter)
class AbonneNewsletterAdmin(AdminYCM):
    titre_liste = "Abonnés newsletter"
    description_liste = "Les personnes qui reçoivent les opportunités et actualités par e-mail."
    libelle_ajout = "Ajouter un abonné"
    libelle_tous = "Tous"
    onglets = (("Abonnés", {"actif__exact": "1"}), ("Désabonnés", {"actif__exact": "0"}))
    colonnes_csv = ("email", "inscrit_le", "actif")
    list_display = ("email", "inscrit_le_court", "etat", "action_abonnement")
    list_display_links = ("email",)
    list_filter = ("actif",)
    search_fields = ("email",)
    actions = ["desabonner", "reabonner"]
    nom_export = "abonnes-newsletter"
    list_per_page = 50
    fields = ("email", "actif")

    def get_urls(self):
        return [
            path("<int:pk>/basculer/", self.admin_site.admin_view(self.basculer), name="core_abonnenewsletter_basculer"),
        ] + super().get_urls()

    def basculer(self, request, pk):
        abonne = get_object_or_404(AbonneNewsletter, pk=pk)
        if request.method == "POST" and self.has_change_permission(request, abonne):
            abonne.actif = not abonne.actif
            abonne.save(update_fields=["actif"])
            messages.success(request, f"{abonne.email} : {'réabonné' if abonne.actif else 'désabonné'}.")
        return HttpResponseRedirect(request.POST.get("retour", "") if str(request.POST.get("retour", "")).startswith("/")
                                    else reverse("admin:core_abonnenewsletter_changelist"))

    @admin.display(description="inscription", ordering="inscrit_le")
    def inscrit_le_court(self, obj):
        return obj.inscrit_le.strftime("%d/%m/%Y")

    @admin.display(description="statut", ordering="actif")
    def etat(self, obj):
        return badge("Abonné", "vert") if obj.actif else badge("Désabonné", "gris")

    @admin.display(description="action")
    def action_abonnement(self, obj):
        return format_html(
            '<button type="submit" form="basculer-{}" class="ycm-bouton-lien">{}</button>',
            obj.pk,
            "Désabonner" if obj.actif else "Réabonner",
        )

    @admin.action(description="Désabonner")
    def desabonner(self, request, queryset):
        n = queryset.update(actif=False)
        self.message_user(request, f"{n} abonné(s) désactivé(s).", messages.SUCCESS)

    @admin.action(description="Réabonner")
    def reabonner(self, request, queryset):
        n = queryset.update(actif=True)
        self.message_user(request, f"{n} abonné(s) réactivé(s).", messages.SUCCESS)
