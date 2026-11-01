from django.urls import path

from . import views

app_name = "core"

urlpatterns = [
    path("", views.accueil, name="accueil"),
    path("a-propos/", views.page_provisoire, {"titre": "À propos"}, name="a_propos"),
    path("contact/", views.page_provisoire, {"titre": "Contact"}, name="contact"),
    path("confidentialite/", views.page_provisoire, {"titre": "Politique de confidentialité"}, name="confidentialite"),
    path("newsletter/", views.newsletter_inscription, name="newsletter"),
]
