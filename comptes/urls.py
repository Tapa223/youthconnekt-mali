from django.urls import path

from . import views

app_name = "comptes"

urlpatterns = [
    path("inscription/", views.inscription, name="inscription"),
    path("inscription/verification/", views.verification, name="verification"),
    path("inscription/renvoyer/", views.renvoyer_confirmation, name="renvoyer_confirmation"),
    path("inscription/<str:type_compte>/", views.inscription_formulaire, name="inscription_formulaire"),
    path("confirmer/<uidb64>/<jeton>/", views.confirmer, name="confirmer"),
    path("connexion/", views.Connexion.as_view(), name="connexion"),
    path("deconnexion/", views.deconnexion, name="deconnexion"),
    path("mot-de-passe-oublie/", views.MotDePasseOublie.as_view(), name="mdp_oublie"),
    path("mot-de-passe-oublie/envoye/", views.MotDePasseEnvoye.as_view(), name="mdp_envoye"),
    path("mot-de-passe/<uidb64>/<token>/", views.MotDePasseNouveau.as_view(), name="mdp_nouveau"),
    path("mot-de-passe/modifie/", views.MotDePasseModifie.as_view(), name="mdp_modifie"),
    path("mon-espace/", views.espace_membre, name="espace_membre"),
    path("mon-espace/communautes/", views.mes_communautes, name="mes_communautes"),
    path("mon-espace/opportunites/", views.mes_opportunites, name="mes_opportunites"),
    path("mon-espace/profil/", views.mon_profil, name="mon_profil"),
    path("mon-espace/parametres/", views.parametres, name="parametres"),
    path("membres/<int:pk>/", views.profil_public, name="profil_public"),
]
