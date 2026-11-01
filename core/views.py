from django.shortcuts import render


def page_provisoire(request, *args, titre="Page", **kwargs):
    """
    Vue temporaire : chaque page sera remplacée par son intégration
    définitive à partir des maquettes.
    """
    return render(request, "provisoire.html", {"titre": titre})
