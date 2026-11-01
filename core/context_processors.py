from .models import ParametresSite


def site(request):
    """Rend les paramètres du site disponibles dans tous les gabarits."""
    return {"parametres_site": ParametresSite.charger()}
