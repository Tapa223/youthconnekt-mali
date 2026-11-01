from django.urls import path

from core.views import page_provisoire

app_name = "contenus"

urlpatterns = [
    path("initiatives/", page_provisoire, {"titre": "Initiatives"}, name="initiatives"),
    path("initiatives/<slug:slug>/", page_provisoire, {"titre": "Initiative"}, name="initiative"),
    path("opportunites/", page_provisoire, {"titre": "Opportunités"}, name="opportunites"),
    path("opportunites/<slug:slug>/", page_provisoire, {"titre": "Opportunité"}, name="opportunite"),
    path("actualites/", page_provisoire, {"titre": "Actualités"}, name="actualites"),
    path("actualites/<slug:slug>/", page_provisoire, {"titre": "Actualité"}, name="actualite"),
]
