from .forms import NewsletterForm
from .models import ParametresSite

NAVIGATION = [
    ("core:accueil", "Accueil", ""),
    ("core:a_propos", "À propos", "a-propos"),
    ("contenus:initiatives", "Initiatives", "initiatives"),
    ("contenus:opportunites", "Opportunités", "opportunites"),
    ("contenus:actualites", "Actualités", "actualites"),
    ("communautes:liste", "Communauté", "communaute"),
    ("core:contact", "Contact", "contact"),
]


def site(request):
    """Données communes à toutes les pages : paramètres, menu, newsletter."""
    section = request.path.strip("/").split("/")[0]
    navigation = [
        {"url": nom_url, "libelle": libelle, "actif": section == prefixe}
        for nom_url, libelle, prefixe in NAVIGATION
    ]
    return {
        "parametres_site": ParametresSite.charger(),
        "navigation": navigation,
        "newsletter_form": NewsletterForm(),
    }
