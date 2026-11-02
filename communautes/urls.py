from django.urls import path

from . import views

app_name = "communautes"

urlpatterns = [
    path("", views.liste, name="liste"),
    path("<slug:slug>/", views.detail, name="detail"),
    path("<slug:slug>/rejoindre/", views.rejoindre, name="rejoindre"),
    path("<slug:slug>/quitter/", views.quitter, name="quitter"),
]
