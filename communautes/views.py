from django.conf import settings
from django.contrib import messages
from django.contrib.auth.decorators import login_required
from django.db.models import Count, Q
from django.shortcuts import get_object_or_404, redirect, render
from django.views.decorators.http import require_POST

from contenus.models import Actualite, Opportunite
from core.securite import adresse_de_retour

from .models import Communaute, DemandeAdhesion

ACCEPTEE = DemandeAdhesion.Statut.ACCEPTEE
EN_ATTENTE = DemandeAdhesion.Statut.EN_ATTENTE


def avec_nombre_membres(queryset):
    return queryset.annotate(nb_membres=Count("demandes", filter=Q(demandes__statut=ACCEPTEE)))


def statuts_du_membre(user):
    """Dictionnaire {id de communauté: statut de la demande} pour un membre connecté."""
    if not user.is_authenticated:
        return {}
    return dict(DemandeAdhesion.objects.filter(membre=user).values_list("communaute_id", "statut"))


def liste(request):
    communautes = avec_nombre_membres(Communaute.objects.actives())
    statuts = statuts_du_membre(request.user)
    for c in communautes:
        c.statut_membre = statuts.get(c.pk)
    return render(request, "communautes/liste.html", {"communautes": communautes})


@login_required
def detail(request, slug):
    communaute = get_object_or_404(avec_nombre_membres(Communaute.objects.actives()), slug=slug)
    demande = DemandeAdhesion.objects.filter(membre=request.user, communaute=communaute).first()
    return render(request, "communautes/detail.html", {
        "c": communaute,
        "demande": demande,
        "opportunites": Opportunite.objects.ouvertes().filter(communautes=communaute)[:4],
        "activites_recentes": Actualite.objects.publies().filter(communautes=communaute)[:4],
    })


@login_required
@require_POST
def rejoindre(request, slug):
    communaute = get_object_or_404(Communaute.objects.actives(), slug=slug)
    automatique = settings.COMMUNAUTES_ACCEPTATION_AUTOMATIQUE
    demande, cree = DemandeAdhesion.objects.get_or_create(
        membre=request.user, communaute=communaute,
        defaults={"statut": ACCEPTEE if automatique else EN_ATTENTE},
    )
    if not cree and demande.statut == DemandeAdhesion.Statut.REFUSEE:
        demande.statut = ACCEPTEE if automatique else EN_ATTENTE
        demande.traitee_le = None
        demande.traitee_par = None
        demande.save()
    if demande.statut == ACCEPTEE:
        messages.success(request, f"Bienvenue dans la communauté {communaute.nom}.")
    else:
        messages.success(request, f"Votre demande pour rejoindre {communaute.nom} a bien été envoyée. Vous la suivez dans votre espace.")
    return redirect(adresse_de_retour(request, communaute.get_absolute_url()))


@login_required
@require_POST
def quitter(request, slug):
    communaute = get_object_or_404(Communaute, slug=slug)
    supprimees, _ = DemandeAdhesion.objects.filter(
        membre=request.user, communaute=communaute, statut__in=[ACCEPTEE, EN_ATTENTE]
    ).delete()
    if supprimees:
        messages.success(request, f"Vous ne faites plus partie de la communauté {communaute.nom}.")
    return redirect(adresse_de_retour(request, communaute.get_absolute_url()))
