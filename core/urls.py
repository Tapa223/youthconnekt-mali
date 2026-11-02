from django.urls import path

from . import views

app_name = "core"

urlpatterns = [
    path("", views.accueil, name="accueil"),
    path("a-propos/", views.a_propos, name="a_propos"),
    path("contact/", views.contact, name="contact"),
    path("confidentialite/", views.page_provisoire, {"titre": "Politique de confidentialité"}, name="confidentialite"),
    path("newsletter/", views.newsletter_inscription, name="newsletter"),
]
