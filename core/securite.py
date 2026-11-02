from django.utils.http import url_has_allowed_host_and_scheme


def adresse_de_retour(request, defaut="/", champ="suivant"):
    """Adresse de retour fournie par le formulaire, seulement si elle reste sur ce site."""
    adresse = request.POST.get(champ) or request.GET.get(champ) or ""
    if adresse and url_has_allowed_host_and_scheme(
        adresse, allowed_hosts={request.get_host()}, require_https=request.is_secure()
    ):
        return adresse
    return defaut
