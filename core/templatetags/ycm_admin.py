"""Balises utilisées par les gabarits de l'administration."""

from django import template

register = template.Library()


@register.simple_tag
def ycm_filtres(cl):
    """Transforme certains filtres de la liste en listes déroulantes (maquette : « Tous les types »)."""
    voulus = getattr(cl.model_admin, "filtres_select", {}) or {}
    filtres = []
    for spec in cl.filter_specs:
        cle = getattr(spec, "field_path", None) or getattr(spec, "parameter_name", None)
        if cle not in voulus:
            continue
        options = []
        for i, choix in enumerate(spec.choices(cl)):
            libelle = voulus[cle] if i == 0 else choix["display"]
            options.append({"libelle": libelle, "url": choix["query_string"], "choisi": choix["selected"]})
        filtres.append({"titre": spec.title, "options": options})
    return filtres
