from django.db import migrations
from django.utils.text import slugify

COMMUNAUTES = [
    ("Tech et numérique", "Développeurs, créateurs de contenu, passionnés du numérique"),
    ("Entrepreneuriat et startups", "Jeunes entrepreneurs, jeunes entreprises et startups"),
    ("Agriculture et environnement", "Jeunes agriculteurs, transformateurs et acteurs du climat"),
    ("Leadership et citoyenneté", "Jeunes leaders d'associations et jeunes engagés dans la vie publique"),
    ("Arts, culture et médias", "Artistes, journalistes, créatifs et vidéastes"),
    ("Santé et bien-être", "Étudiants et jeunes professionnels de la santé"),
    ("Innovation et porteurs de projets", "Innovateurs et porteurs de projets à la recherche d'accompagnement"),
    ("Étudiants", "Étudiants de toutes filières"),
    ("Jeunes professionnels", "Jeunes en début de carrière"),
]


def creer(apps, schema_editor):
    Communaute = apps.get_model("communautes", "Communaute")
    for ordre, (nom, public) in enumerate(COMMUNAUTES, start=1):
        Communaute.objects.get_or_create(
            slug=slugify(nom),
            defaults={"nom": nom, "public_vise": public, "ordre": ordre},
        )


def supprimer(apps, schema_editor):
    Communaute = apps.get_model("communautes", "Communaute")
    Communaute.objects.filter(slug__in=[slugify(n) for n, _ in COMMUNAUTES]).delete()


class Migration(migrations.Migration):
    dependencies = [("communautes", "0002_initial")]
    operations = [migrations.RunPython(creer, supprimer)]
