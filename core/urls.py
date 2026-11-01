from django.urls import path

from . import views

app_name = "core"

urlpatterns = [
    path("", views.page_provisoire, {"titre": "Accueil"}, name="accueil"),
    path("a-propos/", views.page_provisoire, {"titre": "À propos"}, name="a_propos"),
    path("contact/", views.page_provisoire, {"titre": "Contact"}, name="contact"),
]
