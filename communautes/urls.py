from django.urls import path

from core.views import page_provisoire

app_name = "communautes"

urlpatterns = [
    path("", page_provisoire, {"titre": "Communauté"}, name="liste"),
    path("<slug:slug>/", page_provisoire, {"titre": "Communauté thématique"}, name="detail"),
]
