from django.urls import path

from . import views

app_name = "contenus"

urlpatterns = [
    path("initiatives/", views.initiatives, name="initiatives"),
    path("initiatives/<slug:slug>/", views.initiative, name="initiative"),
    path("opportunites/", views.opportunites, name="opportunites"),
    path("opportunites/<slug:slug>/", views.opportunite, name="opportunite"),
    path("actualites/", views.actualites, name="actualites"),
    path("actualites/<slug:slug>/", views.actualite, name="actualite"),
]
