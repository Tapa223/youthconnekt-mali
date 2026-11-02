from django.core.paginator import Paginator
from django.db.models import Q
from django.shortcuts import get_object_or_404, render
from django.utils import timezone

from .models import Actualite, AxeAction, Initiative, Opportunite


def initiatives(request):
    axes = AxeAction.objects.all()
    axe_actif = request.GET.get("axe", "")
    liste = Initiative.objects.publies().select_related("axe")
    if axe_actif:
        liste = liste.filter(axe__slug=axe_actif)
    une = liste.filter(mis_en_avant=True).first() or liste.first()
    page = Paginator(liste, 9).get_page(request.GET.get("page"))
    return render(request, "contenus/initiatives.html", {
        "axes": axes, "axe_actif": axe_actif, "une": une, "page": page,
    })


def initiative(request, slug):
    objet = get_object_or_404(Initiative.objects.publies().select_related("axe"), slug=slug)
    autres = Initiative.objects.publies().exclude(pk=objet.pk).select_related("axe")[:3]
    return render(request, "contenus/initiative.html", {
        "i": objet, "photos": objet.photos.all(), "autres": autres,
    })


def opportunites(request):
    types = Opportunite.Type.choices
    type_actif = request.GET.get("type", "")
    recherche = request.GET.get("q", "").strip()[:100]
    liste = Opportunite.objects.ouvertes()
    if type_actif in dict(types):
        liste = liste.filter(type=type_actif)
    else:
        type_actif = ""
    if recherche:
        liste = liste.filter(
            Q(titre__icontains=recherche) | Q(resume__icontains=recherche) | Q(organisme__icontains=recherche)
        )
    page = Paginator(liste.order_by("date_limite", "-publie_le"), 12).get_page(request.GET.get("page"))
    return render(request, "contenus/opportunites.html", {
        "types": types, "type_actif": type_actif, "recherche": recherche, "page": page,
        "total": page.paginator.count,
    })


def opportunite(request, slug):
    objet = get_object_or_404(Opportunite.objects.publies(), slug=slug)
    similaires = Opportunite.objects.ouvertes().exclude(pk=objet.pk)
    similaires = list(similaires.filter(type=objet.type)[:3]) or list(similaires[:3])
    return render(request, "contenus/opportunite.html", {"o": objet, "similaires": similaires})


def actualites(request):
    filtre = request.GET.get("filtre", "")
    publiees = Actualite.objects.publies()
    aujourd_hui = timezone.localdate()
    une = publiees.filter(mis_en_avant=True).first() or publiees.first()
    agenda = publiees.filter(categorie="evenement", date_evenement__gte=aujourd_hui).order_by("date_evenement")[:4]

    evenements = publiees.filter(categorie="evenement")
    a_venir = list(evenements.filter(date_evenement__gte=aujourd_hui).order_by("date_evenement")[:6])
    passes = list(evenements.exclude(date_evenement__gte=aujourd_hui).order_by("-date_evenement", "-publie_le")[:6])
    liste_actus = publiees.filter(categorie="actualite")

    contexte = {"filtre": filtre, "une": une, "agenda": agenda}
    if filtre != "actualites":
        contexte["evenements"] = (a_venir + passes)[:6]
    if filtre != "evenements":
        contexte["page"] = Paginator(liste_actus, 9).get_page(request.GET.get("page"))
    return render(request, "contenus/actualites.html", contexte)


def actualite(request, slug):
    objet = get_object_or_404(Actualite.objects.publies(), slug=slug)
    autres = Actualite.objects.publies().exclude(pk=objet.pk)[:3]
    return render(request, "contenus/actualite.html", {
        "a": objet, "photos": objet.photos.all(), "autres": autres,
    })
