import csv

from django.contrib import admin, messages
from django.http import HttpResponse
from django.utils.html import format_html
from urllib.parse import quote

from .admin_outils import badge

from .models import AbonneNewsletter, MessageContact, ParametresSite, SectionAPropos



@admin.register(ParametresSite)
class ParametresSiteAdmin(admin.ModelAdmin):
    fieldsets = (
        ("Accueil", {"fields": ("accroche_accueil", "presentation_accueil")}),
        ("Coordonnées", {"fields": ("adresse", "telephone", "email_contact", "horaires")}),
        (
            "Réseaux sociaux officiels",
            {
                "fields": (
                    "lien_facebook",
                    "lien_instagram",
                    "lien_linkedin",
                    "lien_x",
                    "lien_youtube",
                    "lien_tiktok",
                )
            },
        ),
    )

    def has_add_permission(self, request):
        return not ParametresSite.objects.exists()

    def has_delete_permission(self, request, obj=None):
        return False


@admin.register(SectionAPropos)
class SectionAProposAdmin(admin.ModelAdmin):
    list_display = ("titre", "ordre", "publiee")
    list_editable = ("ordre", "publiee")
    search_fields = ("titre", "contenu")


@admin.register(MessageContact)
class MessageContactAdmin(admin.ModelAdmin):
    list_display = ("objet", "nom", "profil", "email", "recu_le", "etat")
    list_display_links = ("objet", "nom")
    list_filter = ("traite", "profil", "recu_le")
    search_fields = ("nom", "email", "objet", "message")
    readonly_fields = ("nom", "profil", "email", "objet", "message_lisible", "repondre", "recu_le")
    fields = ("recu_le", "nom", "profil", "email", "objet", "message_lisible", "repondre", "traite")
    actions = ["marquer_traites", "marquer_a_traiter"]
    date_hierarchy = "recu_le"

    def has_add_permission(self, request):
        return False

    @admin.display(description="état", ordering="traite")
    def etat(self, obj):
        return badge("Traité", "vert") if obj.traite else badge("À traiter", "orange")

    @admin.display(description="message")
    def message_lisible(self, obj):
        return format_html('<div class="ycm-message">{}</div>', obj.message)

    @admin.display(description="répondre")
    def repondre(self, obj):
        sujet = quote(f"Re : {obj.objet}")
        return format_html(
            '<a class="button" href="mailto:{}?subject={}">Répondre par e-mail</a>'
            '<p class="help">Ouvre votre messagerie. Pensez ensuite à cocher « traité » et à enregistrer.</p>',
            obj.email,
            sujet,
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
class AbonneNewsletterAdmin(admin.ModelAdmin):
    list_display = ("email", "inscrit_le", "etat")
    list_filter = ("actif", "inscrit_le")
    date_hierarchy = "inscrit_le"
    search_fields = ("email",)
    actions = ["exporter_csv", "desabonner"]

    @admin.action(description="Exporter en CSV (Excel)")
    def exporter_csv(self, request, queryset):
        reponse = HttpResponse(content_type="text/csv; charset=utf-8")
        reponse["Content-Disposition"] = 'attachment; filename="abonnes-newsletter.csv"'
        reponse.write("﻿")  # pour qu'Excel lise correctement les accents
        ecrivain = csv.writer(reponse, delimiter=";")
        ecrivain.writerow(["E-mail", "Inscrit le", "Actif"])
        for abonne in queryset:
            ecrivain.writerow(
                [abonne.email, abonne.inscrit_le.strftime("%d/%m/%Y"), "oui" if abonne.actif else "non"]
            )
        return reponse

    @admin.display(description="état", ordering="actif")
    def etat(self, obj):
        return badge("Abonné", "vert") if obj.actif else badge("Désabonné", "gris")

    @admin.action(description="Désabonner")
    def desabonner(self, request, queryset):
        n = queryset.update(actif=False)
        self.message_user(request, f"{n} abonné(s) désactivé(s).", messages.SUCCESS)
