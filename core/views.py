from django.contrib import messages
from django.shortcuts import redirect, render
from django.utils.http import url_has_allowed_host_and_scheme
from django.views.decorators.http import require_POST

from communautes.models import Communaute
from contenus.models import (
    Actualite,
    AxeAction,
    ChiffreCle,
    Initiative,
    MotDuMinistre,
    Opportunite,
    Partenaire,
    Temoignage,
)

from .forms import NewsletterForm


def accueil(request):
    initiatives = Initiative.objects.publies().select_related("axe")
    initiatives_en_avant = list(initiatives.filter(mis_en_avant=True)[:3])
    if len(initiatives_en_avant) < 3:
        # Compléter avec les plus récentes si moins de trois sont mises en avant.
        deja = [i.pk for i in initiatives_en_avant]
        initiatives_en_avant += list(initiatives.exclude(pk__in=deja)[: 3 - len(initiatives_en_avant)])

    opportunites = Opportunite.objects.ouvertes()
    opportunites_accueil = list(opportunites.filter(mis_en_avant=True)[:3]) or list(opportunites[:3])

    actualites = list(Actualite.objects.publies()[:4])

    mot = MotDuMinistre.charger()

    contexte = {
        "chiffres": ChiffreCle.objects.filter(publie=True)[:3],
        "axes": AxeAction.objects.all()[:3],
        "initiatives": initiatives_en_avant,
        "mot_ministre": mot if mot.publie else None,
        "mot_ministre_brouillon": mot,
        "communautes": Communaute.objects.actives()[:6],
        "opportunites": opportunites_accueil,
        "actualite_une": actualites[0] if actualites else None,
        "actualites": actualites[1:4],
        "partenaires": Partenaire.objects.filter(actif=True),
        "temoignages": Temoignage.objects.filter(publie=True)[:3],
    }
    return render(request, "core/accueil.html", contexte)


@require_POST
def newsletter_inscription(request):
    form = NewsletterForm(request.POST)
    if form.is_valid():
        if not form.est_un_robot():
            form.enregistrer()
        # Même message dans tous les cas, sans révéler si l'adresse était déjà inscrite.
        messages.success(request, "Merci, votre inscription à la newsletter est bien prise en compte.")
    else:
        messages.error(request, form.errors["email"][0] if "email" in form.errors else "Inscription impossible.")

    retour = request.POST.get("suivant") or request.META.get("HTTP_REFERER", "")
    if not url_has_allowed_host_and_scheme(retour, allowed_hosts={request.get_host()}, require_https=request.is_secure()):
        retour = "/"
    return redirect(f"{retour.split('#')[0]}#newsletter")


def page_provisoire(request, *args, titre="Page", **kwargs):
    """
    Vue temporaire : chaque page sera remplacée par son intégration
    définitive à partir des maquettes.
    """
    return render(request, "provisoire.html", {"titre": titre})
