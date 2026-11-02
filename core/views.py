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

from .forms import ContactForm, NewsletterForm
from .models import MessageContact


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
        "nb_communautes": Communaute.objects.actives().count(),
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


OBSTACLES = [
    ("L'information est dispersée",
     "Formations, bourses et appels à candidatures existent, mais les jeunes ne savent pas toujours où les trouver."),
    ("Les réseaux sont difficiles d'accès",
     "Rencontrer un mentor, un partenaire ou un décideur reste rare pour beaucoup de jeunes."),
    ("Les talents restent invisibles",
     "De nombreux projets portés par des jeunes avancent sans que personne ne les connaisse."),
]
PUBLICS = [
    ("Pour les jeunes",
     "Trouver une formation, une bourse ou un stage, rencontrer des mentors, faire connaître son projet et prendre part aux décisions qui les concernent."),
    ("Pour les organisations",
     "Toucher des jeunes motivés partout dans le pays, partager leurs opportunités et monter des initiatives avec le chapitre."),
    ("Avec les institutions",
     "Faire remonter la voix des jeunes, contribuer aux politiques jeunesse et relier les programmes publics aux jeunes qui en ont besoin."),
]
APPROCHE = [
    ("Repérer", "Nous recherchons formations, bourses, stages et appels utiles aux jeunes."),
    ("Partager", "Nous les relayons sur la plateforme, par e-mail et dans les communautés."),
    ("Accompagner", "Ateliers, mentorat et événements pour aider chacun à saisir sa chance."),
    ("Mettre en lumière", "Nous faisons connaître les réussites et les projets des jeunes."),
]
PARCOURS = [
    ("2024", "Lancement du chapitre", "Naissance de YouthConnekt Mali comme chapitre national."),
    ("2025", "Premières formations", "Premiers ateliers et programmes d'accompagnement."),
    ("2026", "La plateforme en ligne", "Un espace pour relier jeunes, organisations et opportunités."),
    ("Demain", "Toutes les régions", "Aller à la rencontre des jeunes partout au Mali."),
]


def a_propos(request):
    mot = MotDuMinistre.charger()
    return render(request, "core/a_propos.html", {
        "obstacles": OBSTACLES, "publics": PUBLICS, "approche": APPROCHE, "parcours": PARCOURS,
        "axes": AxeAction.objects.all(),
        "mot_ministre": mot if mot.publie and mot.message else None,
        "temoignages": Temoignage.objects.filter(publie=True)[:3],
    })


def contact(request):
    if request.method == "POST":
        form = ContactForm(request.POST)
        if form.is_valid():
            if not form.cleaned_data.get("site_web"):
                form.save()
            messages.success(request, "Merci, votre message a bien été envoyé. L'équipe vous répondra par e-mail.")
            return redirect("core:contact")
    else:
        sujet = request.GET.get("sujet")
        form = ContactForm(initial={"sujet": sujet} if sujet in dict(MessageContact.Sujet.choices) else None)
    return render(request, "core/contact.html", {"form": form})


def page_provisoire(request, *args, titre="Page", **kwargs):
    """
    Vue temporaire : chaque page sera remplacée par son intégration
    définitive à partir des maquettes.
    """
    return render(request, "provisoire.html", {"titre": titre})
