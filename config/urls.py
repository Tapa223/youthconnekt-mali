from django.conf import settings
from django.conf.urls.static import static
from django.contrib import admin
from django.urls import include, path

urlpatterns = [
    path("gestion/", admin.site.urls),
    path("", include("core.urls")),
    path("", include("contenus.urls")),
    path("communaute/", include("communautes.urls")),
    path("compte/", include("comptes.urls")),
]

if settings.DEBUG:
    # En développement uniquement : Django sert les images envoyées.
    # En production, c'est le serveur web qui s'en charge.
    urlpatterns += static(settings.MEDIA_URL, document_root=settings.MEDIA_ROOT)
