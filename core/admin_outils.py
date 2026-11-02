"""
Outils partagés par les écrans d'administration de YouthConnekt Mali.

- badge(), apercu()… : petits affichages réutilisés dans les listes ;
- AdminYCM : base commune des écrans, qui ajoute ce que montre la maquette :
  titre et phrase d'explication, bouton d'ajout orange, onglets de filtre
  (« Toutes (9) », « Publiées (7) »…), listes déroulantes, export CSV
  et liens « Voir / Modifier » sur chaque ligne.
"""

import csv

from django.contrib import admin
from django.contrib.admin.utils import label_for_field, lookup_field
from django.http import HttpResponse
from django.urls import reverse
from django.utils.html import format_html, strip_tags
from django.utils.http import urlencode
from django.utils.text import slugify


def badge(texte, couleur="gris"):
    """Pastille colorée : vert, orange, rouge, turquoise, bleu ou gris (voir static/css/admin.css)."""
    return format_html('<span class="ycm-badge ycm-badge--{}">{}</span>', couleur, texte)


def apercu(image, hauteur=48):
    """Miniature d'une image envoyée, ou un tiret s'il n'y en a pas."""
    if not image:
        return "—"
    return format_html('<img src="{}" alt="" class="ycm-miniature" style="height:{}px">', image.url, hauteur)


def apercu_grand(image):
    """Aperçu plus grand, affiché dans le formulaire sous le champ image."""
    if not image:
        return "Aucune image pour l'instant. Après l'enregistrement, l'aperçu s'affichera ici."
    return format_html('<img src="{}" alt="" class="ycm-apercu">', image.url)


def avatar(texte_initiales, numero=0, image=None):
    """Rond de couleur avec des initiales (ou la photo) devant un nom."""
    if image:
        return format_html('<img src="{}" alt="" class="ycm-avatar ycm-avatar--photo">', image.url)
    couleurs = ["orange", "turquoise", "bleu", "vert"]
    return format_html(
        '<span class="ycm-avatar ycm-avatar--{}" aria-hidden="true">{}</span>',
        couleurs[numero % 4],
        texte_initiales,
    )


def nom_et_detail(nom, detail="", prefixe=""):
    """Deux lignes dans une cellule : le nom en gras, un détail en petit dessous."""
    return format_html(
        '<span class="ycm-personne">{}<span><strong>{}</strong>{}</span></span>',
        prefixe,
        nom,
        format_html("<small>{}</small>", detail) if detail else "",
    )


# Paramètres d'adresse qui ne sont pas des filtres
PARAMETRES_HORS_FILTRE = {"o", "p", "q", "e", "_export", "_popup", "_to_field", "ouvert"}


class AdminYCM(admin.ModelAdmin):
    """Base des écrans de liste et de formulaire, à l'allure de la maquette."""

    titre_liste = None          # « Opportunités »
    description_liste = ""      # phrase sous le titre
    libelle_ajout = None        # « Ajouter une opportunité »
    titre_ajout = None          # titre du formulaire d'ajout
    description_formulaire = ""
    onglets = ()                # (("Publiées", {"statut__exact": "publie"}), …)
    libelle_tous = "Tous"       # premier onglet
    filtres_select = {}         # {"type": "Tous les types"}
    export_csv = True
    nom_export = None
    list_per_page = 25

    # ------------------------------------------------------------------
    # Liste
    # ------------------------------------------------------------------
    def get_onglets(self, request):
        return self.onglets

    def _parametres_filtre(self, request):
        return {k: v for k, v in request.GET.items() if k not in PARAMETRES_HORS_FILTRE}

    def changelist_view(self, request, extra_context=None):
        if request.GET.get("_export") == "csv" and self.export_csv:
            request.GET = request.GET.copy()
            request.GET.pop("_export")
            return self.exporter_csv_liste(request)

        actuels = self._parametres_filtre(request)
        base = self.get_queryset(request)
        onglets = []
        if self.get_onglets(request):
            onglets.append({
                "libelle": self.libelle_tous,
                "nombre": base.count(),
                "url": "?",
                "actif": not actuels,
            })
            for libelle, parametres in self.get_onglets(request):
                parametres = {k: str(v) for k, v in parametres.items()}
                onglets.append({
                    "libelle": libelle,
                    "nombre": base.filter(**parametres).count(),
                    "url": "?" + urlencode(parametres),
                    "actif": actuels == parametres,
                })

        params_export = request.GET.copy()
        params_export["_export"] = "csv"
        extra_context = {
            **(extra_context or {}),
            "ycm_onglets": onglets,
            "ycm_export_url": "?" + params_export.urlencode() if self.export_csv else None,
            "ycm_titre": self.titre_liste or self.model._meta.verbose_name_plural.capitalize(),
            "ycm_description": self.description_liste,
            "ycm_libelle_ajout": self.libelle_ajout or f"Ajouter : {self.model._meta.verbose_name}",
        }
        return super().changelist_view(request, extra_context)

    colonnes_csv = None         # colonnes du fichier CSV (par défaut : celles de la liste)

    def colonnes_export(self, request):
        if self.colonnes_csv:
            return list(self.colonnes_csv)
        ignorer = {"action_checkbox", "actions_ligne"}
        return [c for c in self.get_list_display(request) if c not in ignorer and not str(c).startswith("apercu")]

    def exporter_csv_liste(self, request):
        changelist = self.get_changelist_instance(request)
        colonnes = self.colonnes_export(request)
        nom = self.nom_export or slugify(self.model._meta.verbose_name_plural)
        reponse = HttpResponse(content_type="text/csv; charset=utf-8")
        reponse["Content-Disposition"] = f'attachment; filename="{nom}.csv"'
        reponse.write("﻿")  # pour qu'Excel lise correctement les accents
        ecrivain = csv.writer(reponse, delimiter=";")
        ecrivain.writerow([strip_tags(str(label_for_field(c, self.model, self))).capitalize() for c in colonnes])
        for obj in changelist.get_queryset(request):
            ligne = []
            for c in colonnes:
                try:
                    champ, _, valeur = lookup_field(c, obj, self)
                except Exception:
                    champ, valeur = None, ""
                if champ is not None and getattr(champ, "flatchoices", None):
                    valeur = dict(champ.flatchoices).get(valeur, valeur)
                if hasattr(valeur, "strftime"):
                    valeur = valeur.strftime("%d/%m/%Y")
                elif isinstance(valeur, bool):
                    valeur = "oui" if valeur else "non"
                ligne.append(strip_tags(str(valeur if valeur is not None else "")).strip())
            ecrivain.writerow(ligne)
        return reponse

    @admin.display(description="actions")
    def actions_ligne(self, obj):
        modifier = reverse(
            f"admin:{obj._meta.app_label}_{obj._meta.model_name}_change", args=[obj.pk]
        )
        voir = obj.get_absolute_url() if hasattr(obj, "get_absolute_url") else None
        if voir and getattr(obj, "statut", "publie") == "publie":
            return format_html(
                '<span class="ycm-actions-ligne"><a href="{}" target="_blank" rel="noopener">Voir</a>'
                '<a href="{}">Modifier</a></span>',
                voir,
                modifier,
            )
        return format_html('<span class="ycm-actions-ligne"><a href="{}">Modifier</a></span>', modifier)

    # ------------------------------------------------------------------
    # Formulaire
    # ------------------------------------------------------------------
    def formfield_for_manytomany(self, db_field, request, **kwargs):
        # Cases à cocher plutôt que la double liste de Django : plus simple, surtout au téléphone.
        if db_field.name not in ("groups", "user_permissions", "permissions"):
            from django import forms

            kwargs["widget"] = forms.CheckboxSelectMultiple(attrs={"class": "ycm-cases"})
        return super().formfield_for_manytomany(db_field, request, **kwargs)

    def changeform_view(self, request, object_id=None, form_url="", extra_context=None):
        ajout = object_id is None
        nom = self.model._meta.verbose_name
        extra_context = {
            **(extra_context or {}),
            "ycm_titre_formulaire": (self.titre_ajout or f"Ajouter : {nom}") if ajout else None,
            "ycm_description": self.description_formulaire,
            "ycm_url_liste": reverse(f"admin:{self.model._meta.app_label}_{self.model._meta.model_name}_changelist"),
            "ycm_titre_liste": self.titre_liste or self.model._meta.verbose_name_plural.capitalize(),
        }
        return super().changeform_view(request, object_id, form_url, extra_context)


class FicheUniqueYCM(AdminYCM):
    """
    Fiche qui n'existe qu'en un seul exemplaire (mot du ministre, paramètres du site) :
    la « liste » ouvre directement le formulaire, sans ajout ni suppression.
    """

    export_csv = False

    def has_add_permission(self, request):
        return not self.model.objects.exists()

    def has_delete_permission(self, request, obj=None):
        return False

    def changelist_view(self, request, extra_context=None):
        from django.shortcuts import redirect

        fiche = self.model.charger()
        return redirect(f"admin:{self.model._meta.app_label}_{self.model._meta.model_name}_change", fiche.pk)

    def changeform_view(self, request, object_id=None, form_url="", extra_context=None):
        extra_context = {**(extra_context or {}), "ycm_fiche_unique": True}
        return super().changeform_view(request, object_id, form_url, extra_context)

    def response_change(self, request, obj):
        # Après « Enregistrer », on reste sur la fiche (il n'y a pas de liste).
        from django.contrib import messages
        from django.http import HttpResponseRedirect

        if "_continue" in request.POST or "_save" in request.POST:
            messages.success(request, "Modifications enregistrées.")
            return HttpResponseRedirect(request.path)
        return super().response_change(request, obj)
