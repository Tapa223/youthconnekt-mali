import time

from django.conf import settings
from django.contrib import messages
from django.contrib.auth import login, logout, update_session_auth_hash
from django.contrib.auth import views as auth_views
from django.contrib.auth.decorators import login_required
from django.contrib.auth.tokens import PasswordResetTokenGenerator
from django.core.mail import send_mail
from django.db.models import Count, Q
from django.http import Http404
from django.shortcuts import get_object_or_404, redirect, render
from django.template.loader import render_to_string
from django.urls import reverse, reverse_lazy
from django.utils import timezone
from django.utils.encoding import force_bytes, force_str
from django.utils.http import urlsafe_base64_decode, urlsafe_base64_encode
from django.views.decorators.http import require_POST

from communautes.models import Communaute, DemandeAdhesion
from contenus.models import Actualite, Opportunite

from .forms import (
    ChangementMotDePasseForm,
    ConnexionForm,
    InscriptionIndividuelleForm,
    InscriptionOrganisationForm,
    ProfilIndividuelForm,
    ProfilOrganisationForm,
    SuppressionCompteForm,
)
from .models import ProfilIndividuel, ProfilOrganisation, Utilisateur


class JetonConfirmation(PasswordResetTokenGenerator):
    """Jeton de confirmation d'e-mail : il devient invalide une fois l'adresse confirmée."""

    key_salt = "comptes.JetonConfirmation"

    def _make_hash_value(self, user, timestamp):
        return f"{user.pk}{user.email}{user.email_confirme}{timestamp}"


jeton_confirmation = JetonConfirmation()
ACCEPTEE = DemandeAdhesion.Statut.ACCEPTEE


def envoyer_confirmation(request, utilisateur):
    lien = request.build_absolute_uri(reverse("comptes:confirmer", args=[
        urlsafe_base64_encode(force_bytes(utilisateur.pk)), jeton_confirmation.make_token(utilisateur),
    ]))
    contexte = {"utilisateur": utilisateur, "lien": lien}
    send_mail(
        "Confirmez votre adresse e-mail · YouthConnekt Mali",
        render_to_string("comptes/emails/confirmation.txt", contexte),
        settings.DEFAULT_FROM_EMAIL,
        [utilisateur.email],
    )
    request.session["confirmation_envoyee_a"] = time.time()


# --------------------------------------------------------------------------
# Inscription en trois étapes
# --------------------------------------------------------------------------

def inscription(request):
    """Étape 1 : choix du type de compte."""
    if request.user.is_authenticated:
        return redirect("comptes:espace_membre")
    return render(request, "comptes/inscription_type.html", {"etape": 1})


def inscription_formulaire(request, type_compte):
    """Étape 2 : informations du compte."""
    if request.user.is_authenticated:
        return redirect("comptes:espace_membre")
    formulaires = {"individuel": InscriptionIndividuelleForm, "organisation": InscriptionOrganisationForm}
    if type_compte not in formulaires:
        raise Http404
    form = formulaires[type_compte](request.POST or None)
    if request.method == "POST" and form.is_valid():
        utilisateur = form.save()
        envoyer_confirmation(request, utilisateur)
        request.session["inscription_utilisateur"] = utilisateur.pk
        return redirect("comptes:verification")
    return render(request, "comptes/inscription_formulaire.html", {"form": form, "type_compte": type_compte, "etape": 2})


def verification(request):
    """Étape 3 : invitation à confirmer l'adresse e-mail."""
    pk = request.session.get("inscription_utilisateur")
    utilisateur = Utilisateur.objects.filter(pk=pk, email_confirme=False).first() if pk else None
    if utilisateur is None:
        return redirect("comptes:connexion")
    return render(request, "comptes/verification.html", {"email": utilisateur.email, "etape": 3})


@require_POST
def renvoyer_confirmation(request):
    pk = request.session.get("inscription_utilisateur")
    utilisateur = Utilisateur.objects.filter(pk=pk, email_confirme=False).first() if pk else None
    if utilisateur is None:
        return redirect("comptes:connexion")
    dernier_envoi = request.session.get("confirmation_envoyee_a", 0)
    if time.time() - dernier_envoi < 60:
        messages.error(request, "Patientez une minute avant de demander un nouvel e-mail.")
    else:
        envoyer_confirmation(request, utilisateur)
        messages.success(request, "Un nouvel e-mail de confirmation vient de vous être envoyé.")
    return redirect("comptes:verification")


def confirmer(request, uidb64, jeton):
    try:
        utilisateur = Utilisateur.objects.get(pk=force_str(urlsafe_base64_decode(uidb64)))
    except (Utilisateur.DoesNotExist, ValueError, TypeError, OverflowError):
        utilisateur = None
    if utilisateur is None or not jeton_confirmation.check_token(utilisateur, jeton):
        return render(request, "comptes/lien_invalide.html", status=400)
    utilisateur.email_confirme = True
    utilisateur.save(update_fields=["email_confirme"])
    request.session.pop("inscription_utilisateur", None)
    login(request, utilisateur, backend="django.contrib.auth.backends.ModelBackend")
    messages.success(request, "Votre adresse e-mail est confirmée. Bienvenue dans votre espace.")
    return redirect("comptes:espace_membre")


# --------------------------------------------------------------------------
# Connexion et mot de passe
# --------------------------------------------------------------------------

class Connexion(auth_views.LoginView):
    template_name = "comptes/connexion.html"
    authentication_form = ConnexionForm
    redirect_authenticated_user = True

    def form_valid(self, form):
        if not form.cleaned_data.get("se_souvenir"):
            self.request.session.set_expiry(0)  # session fermée à la fermeture du navigateur
        return super().form_valid(form)

    def form_invalid(self, form):
        # Permettre de renvoyer l'e-mail de confirmation à un compte non confirmé
        if any(e.code == "non_confirme" for e in form.non_field_errors().as_data()):
            email = (form.data.get("username") or "").strip().lower()
            utilisateur = Utilisateur.objects.filter(email=email, email_confirme=False).first()
            if utilisateur:
                self.request.session["inscription_utilisateur"] = utilisateur.pk
        return super().form_invalid(form)


@require_POST
def deconnexion(request):
    logout(request)
    messages.success(request, "Vous êtes déconnecté. À bientôt.")
    return redirect("core:accueil")


class MotDePasseOublie(auth_views.PasswordResetView):
    template_name = "comptes/mdp_oublie.html"
    email_template_name = "comptes/emails/mdp_reinitialisation.txt"
    subject_template_name = "comptes/emails/mdp_sujet.txt"
    success_url = reverse_lazy("comptes:mdp_envoye")


class MotDePasseEnvoye(auth_views.PasswordResetDoneView):
    template_name = "comptes/mdp_envoye.html"


class MotDePasseNouveau(auth_views.PasswordResetConfirmView):
    template_name = "comptes/mdp_nouveau.html"
    success_url = reverse_lazy("comptes:mdp_modifie")


class MotDePasseModifie(auth_views.PasswordResetCompleteView):
    template_name = "comptes/mdp_modifie.html"


# --------------------------------------------------------------------------
# Espace membre
# --------------------------------------------------------------------------

def _donnees_membre(user):
    demandes = DemandeAdhesion.objects.filter(membre=user).select_related("communaute")
    communautes_ids = [d.communaute_id for d in demandes if d.statut == ACCEPTEE]
    opportunites = Opportunite.objects.ouvertes().filter(communautes__in=communautes_ids).distinct()
    return demandes, communautes_ids, opportunites


@login_required
def espace_membre(request):
    user = request.user
    demandes, communautes_ids, opportunites = _donnees_membre(user)
    mes_communautes = (Communaute.objects.filter(pk__in=communautes_ids)
                       .annotate(nb_membres=Count("demandes", filter=Q(demandes__statut=ACCEPTEE))))
    for c in mes_communautes:
        c.nb_opportunites = opportunites.filter(communautes=c).count()
    exclues = [d.communaute_id for d in demandes]
    suggestions = (Communaute.objects.actives().exclude(pk__in=exclues)
                   .annotate(nb_membres=Count("demandes", filter=Q(demandes__statut=ACCEPTEE)))
                   .order_by("-nb_membres", "ordre")[:3])
    evenements = (Actualite.objects.publies()
                  .filter(categorie="evenement", date_evenement__gte=timezone.localdate())
                  .order_by("date_evenement"))
    profil = user.profil
    return render(request, "comptes/espace/tableau_de_bord.html", {
        "rubrique": "tableau",
        "profil": profil,
        "completion": profil.taux_completion if profil else 0,
        "mes_communautes": mes_communautes,
        "demandes_en_attente": [d for d in demandes if d.statut == DemandeAdhesion.Statut.EN_ATTENTE],
        "opportunites": opportunites[:3],
        "nb_opportunites": opportunites.count(),
        "evenements": evenements[:3],
        "nb_evenements": evenements.count(),
        "suggestions": suggestions,
    })


@login_required
def mes_communautes(request):
    demandes, _, _ = _donnees_membre(request.user)
    return render(request, "comptes/espace/communautes.html", {
        "rubrique": "communautes", "profil": request.user.profil, "demandes": demandes,
    })


@login_required
def mes_opportunites(request):
    demandes, communautes_ids, opportunites = _donnees_membre(request.user)
    return render(request, "comptes/espace/opportunites.html", {
        "rubrique": "opportunites", "profil": request.user.profil,
        "opportunites": opportunites.prefetch_related("communautes"), "a_des_communautes": bool(communautes_ids),
    })


@login_required
def mon_profil(request):
    user = request.user
    profil = user.profil
    if profil is None:  # compte créé depuis l'administration sans profil
        modele = ProfilOrganisation if user.est_organisation else ProfilIndividuel
        profil = modele(utilisateur=user)
    classe = ProfilOrganisationForm if user.est_organisation else ProfilIndividuelForm
    form = classe(request.POST or None, request.FILES or None, instance=profil)
    if request.method == "POST" and form.is_valid():
        form.save()
        messages.success(request, "Votre profil a bien été mis à jour.")
        return redirect("comptes:mon_profil")
    return render(request, "comptes/espace/profil.html", {
        "rubrique": "profil", "profil": user.profil, "form": form,
    })


@login_required
def parametres(request):
    form_mdp = ChangementMotDePasseForm(request.user, request.POST if request.POST.get("action") == "mdp" else None)
    form_suppr = SuppressionCompteForm(request.user, request.POST if request.POST.get("action") == "supprimer" else None)
    if request.method == "POST":
        if request.POST.get("action") == "mdp" and form_mdp.is_valid():
            form_mdp.save()
            update_session_auth_hash(request, form_mdp.user)
            messages.success(request, "Votre mot de passe a bien été modifié.")
            return redirect("comptes:parametres")
        if request.POST.get("action") == "supprimer" and form_suppr.is_valid():
            utilisateur = request.user
            logout(request)
            utilisateur.delete()
            messages.success(request, "Votre compte et vos informations ont été supprimés.")
            return redirect("core:accueil")
    return render(request, "comptes/espace/parametres.html", {
        "rubrique": "parametres", "profil": request.user.profil, "form_mdp": form_mdp, "form_suppr": form_suppr,
    })


def profil_public(request, pk):
    """Profil public d'un membre : réservé aux membres connectés, et seulement s'il est visible."""
    if not request.user.is_authenticated:
        return redirect(f"{reverse('comptes:connexion')}?next={request.path}")
    utilisateur = get_object_or_404(Utilisateur, pk=pk, is_active=True)
    profil = utilisateur.profil
    if profil is None or (not profil.visible and utilisateur != request.user):
        raise Http404
    communautes = Communaute.objects.filter(demandes__membre=utilisateur, demandes__statut=ACCEPTEE)
    return render(request, "comptes/profil_public.html", {"membre": utilisateur, "p": profil, "communautes": communautes})
