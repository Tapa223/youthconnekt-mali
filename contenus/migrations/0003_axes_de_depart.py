from django.db import migrations

# Axes d'action validés dans les maquettes de l'accueil.
AXES = [
    ("Accompagner", "accompagner",
     "Formations, mentorat et accompagnement des jeunes dans leurs projets d'entrepreneuriat et d'emploi."),
    ("Engager", "engager",
     "Impliquer les jeunes dans la vie citoyenne et les rapprocher de celles et ceux qui décident."),
    ("Valoriser", "valoriser",
     "Faire connaître les initiatives, les talents et les réalisations des jeunes du Mali."),
]


def creer(apps, schema_editor):
    AxeAction = apps.get_model("contenus", "AxeAction")
    for ordre, (nom, slug, description) in enumerate(AXES, start=1):
        AxeAction.objects.get_or_create(
            slug=slug, defaults={"nom": nom, "description": description, "ordre": ordre}
        )


def supprimer(apps, schema_editor):
    apps.get_model("contenus", "AxeAction").objects.filter(
        slug__in=[a[1] for a in AXES]
    ).delete()


class Migration(migrations.Migration):
    dependencies = [("contenus", "0002_image_axe")]
    operations = [migrations.RunPython(creer, supprimer)]
