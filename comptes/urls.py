from django.urls import path

from core.views import page_provisoire

app_name = "comptes"

urlpatterns = [
    path("connexion/", page_provisoire, {"titre": "Connexion"}, name="connexion"),
    path("inscription/", page_provisoire, {"titre": "Créer un compte"}, name="inscription"),
    path("mon-compte/", page_provisoire, {"titre": "Mon compte"}, name="espace_membre"),
    path("membres/<int:pk>/", page_provisoire, {"titre": "Profil"}, name="profil_public"),
]
