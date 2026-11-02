"""Petits outils d'affichage partagés par les écrans d'administration."""

from django.utils.html import format_html


def badge(texte, couleur="gris"):
    """Pastille colorée : couleur parmi vert, orange, rouge, gris (voir static/css/admin.css)."""
    return format_html('<span class="ycm-badge ycm-badge--{}">{}</span>', couleur, texte)


def apercu(image, hauteur=48):
    """Miniature d'une image envoyée, ou un tiret s'il n'y en a pas."""
    if not image:
        return "—"
    return format_html(
        '<img src="{}" alt="" style="height:{}px;width:auto;border-radius:6px;object-fit:cover">',
        image.url,
        hauteur,
    )


def apercu_grand(image):
    """Aperçu plus grand, affiché dans le formulaire sous le champ image."""
    if not image:
        return "Aucune image pour l'instant. Après l'enregistrement, l'aperçu s'affichera ici."
    return format_html('<img src="{}" alt="" class="ycm-apercu">', image.url)
