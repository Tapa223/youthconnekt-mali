import csv

from django.contrib import admin, messages
from django.http import HttpResponse

from .models import AbonneNewsletter, MessageContact, ParametresSite, SectionAPropos

admin.site.site_header = "Administration YouthConnekt Mali"
admin.site.site_title = "YouthConnekt Mali"
admin.site.index_title = "Gestion du site"


@admin.register(ParametresSite)
class ParametresSiteAdmin(admin.ModelAdmin):
    fieldsets = (
        ("Accueil", {"fields": ("accroche_accueil", "presentation_accueil", "image_accueil")}),
        ("Coordonnées", {"fields": ("adresse", "telephone", "email_contact")}),
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
    list_display = ("objet", "nom", "email", "recu_le", "traite")
    list_filter = ("traite", "recu_le")
    search_fields = ("nom", "email", "objet", "message")
    readonly_fields = ("nom", "email", "objet", "message", "recu_le")
    actions = ["marquer_traites"]

    def has_add_permission(self, request):
        return False

    @admin.action(description="Marquer comme traités")
    def marquer_traites(self, request, queryset):
        n = queryset.update(traite=True)
        self.message_user(request, f"{n} message(s) marqué(s) comme traité(s).", messages.SUCCESS)


@admin.register(AbonneNewsletter)
class AbonneNewsletterAdmin(admin.ModelAdmin):
    list_display = ("email", "inscrit_le", "actif")
    list_filter = ("actif",)
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

    @admin.action(description="Désabonner")
    def desabonner(self, request, queryset):
        n = queryset.update(actif=False)
        self.message_user(request, f"{n} abonné(s) désactivé(s).", messages.SUCCESS)
