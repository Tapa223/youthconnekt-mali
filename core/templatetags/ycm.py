"""Filtres et balises de gabarits propres au site YouthConnekt Mali."""

from django import template
from django.utils.html import escape, format_html, format_html_join
from django.utils.safestring import mark_safe

register = template.Library()

COULEURS = {
    # couleur : (fond plein, texte sur fond plein, bordure, texte coloré, fond doux)
    "orange": ("bg-orange", "text-white", "border-orange", "text-orange", "bg-orange/10"),
    "turquoise": ("bg-turquoise", "text-encre", "border-turquoise", "text-turquoise", "bg-turquoise/10"),
    "bleu": ("bg-bleu", "text-white", "border-bleu", "text-bleu", "bg-bleu/10"),
    "vert": ("bg-vert", "text-white", "border-vert", "text-vert", "bg-vert/10"),
    "rouge": ("bg-rouge", "text-white", "border-rouge", "text-rouge", "bg-rouge/10"),
}


@register.filter
def fond(couleur):
    c = COULEURS.get(couleur, COULEURS["vert"])
    return f"{c[0]} {c[1]}"


@register.filter
def bordure(couleur):
    return COULEURS.get(couleur, COULEURS["vert"])[2]


@register.filter
def texte_couleur(couleur):
    return COULEURS.get(couleur, COULEURS["vert"])[3]


@register.filter
def paragraphes(texte):
    """Transforme un texte en paragraphes (séparés par une ligne vide), sans HTML autorisé."""
    blocs = [b.strip() for b in (texte or "").replace("\r\n", "\n").split("\n\n") if b.strip()]
    return format_html_join("", "<p>{}</p>", ((b.replace("\n", " "),) for b in blocs))


@register.simple_tag
def corps_article(texte, galerie=""):
    """
    Mise en forme simple et sûre du corps d'un article :
    « ## » intertitre, « - » liste, « > » citation suivie de « — auteur »,
    « [photos] » emplacement de la galerie. Tout le reste est échappé.
    """
    lignes = (texte or "").replace("\r\n", "\n").split("\n")
    html, paragraphe, liste, citation, galerie_placee = [], [], [], None, False

    def vider_paragraphe():
        if paragraphe:
            html.append(format_html("<p>{}</p>", " ".join(paragraphe)))
            paragraphe.clear()

    def vider_liste():
        if liste:
            html.append("<ul>" + "".join(format_html("<li>{}</li>", e) for e in liste) + "</ul>")
            liste.clear()

    def vider_citation(auteur=""):
        nonlocal citation
        if citation is not None:
            pied = format_html("<footer>{}</footer>", auteur) if auteur else ""
            html.append(format_html("<blockquote><p>{}</p>{}</blockquote>", " ".join(citation), pied))
            citation = None

    for brute in lignes:
        ligne = brute.strip()
        if citation is not None and not ligne.startswith(">"):
            if ligne.startswith(("—", "--")):
                vider_citation(ligne.lstrip("—-").strip())
                continue
            vider_citation()
        if not ligne:
            vider_paragraphe(); vider_liste()
        elif ligne.startswith("## "):
            vider_paragraphe(); vider_liste()
            html.append(format_html("<h2>{}</h2>", ligne[3:].strip()))
        elif ligne.startswith(("- ", "• ")):
            vider_paragraphe()
            liste.append(ligne[2:].strip())
        elif ligne.startswith(">"):
            vider_paragraphe(); vider_liste()
            citation = (citation or []) + [ligne.lstrip(">").strip()]
        elif ligne.lower() == "[photos]":
            vider_paragraphe(); vider_liste()
            if galerie:
                html.append(str(galerie)); galerie_placee = True
        else:
            vider_liste()
            paragraphe.append(ligne)
    vider_citation(); vider_paragraphe(); vider_liste()
    if galerie and not galerie_placee:
        html.append(str(galerie))
    return mark_safe("".join(html))


@register.filter
def jours_label(n):
    if n is None:
        return ""
    if n <= 0:
        return "Dernier jour"
    return f"Plus que {n} jour{'s' if n > 1 else ''}"


@register.simple_tag
def capture_galerie(photos):
    """Galerie de photos insérée dans le corps d'un article."""
    elements = format_html_join(
        "",
        '<li class="aspect-[4/3] overflow-hidden rounded-2xl"><img src="{}" alt="{}" class="h-full w-full object-cover" loading="lazy"></li>',
        ((p.image.url, p.legende) for p in photos),
    )
    return format_html('<ul class="!my-8 grid grid-cols-2 gap-3 !pl-0 [&>li]:!pl-0 [&>li]:before:!hidden">{}</ul>', elements)


@register.filter
def champ_html(champ, classes="champ"):
    """Affiche le widget d'un champ de formulaire avec les classes et l'état d'erreur."""
    attrs = {"class": classes}
    if champ.errors:
        attrs["aria-invalid"] = "true"
        attrs["aria-describedby"] = f"{champ.id_for_label}-erreur"
    elif champ.help_text:
        attrs["aria-describedby"] = f"{champ.id_for_label}-aide"
    return champ.as_widget(attrs=attrs)
