from django.db import migrations

ELEMENTS = {
    "accompagner": ("orange", "Formations pratiques et ateliers\nMentorat par des professionnels\nRelais d'appels à candidatures et de bourses"),
    "engager": ("turquoise", "Dialogues entre jeunes et institutions\nActions citoyennes dans les régions\nParticipation aux politiques jeunesse"),
    "valoriser": ("bleu", "Mise en avant de projets portés par des jeunes\nPrix et distinctions\nVisibilité lors des événements du réseau"),
}


def convertir(apps, schema_editor):
    AxeAction = apps.get_model("contenus", "AxeAction")
    for slug, (couleur, elements) in ELEMENTS.items():
        AxeAction.objects.filter(slug=slug).update(couleur=couleur, elements=elements)
    # Anciennes valeurs de type et de catégorie, si des contenus existaient déjà
    Opportunite = apps.get_model("contenus", "Opportunite")
    Opportunite.objects.filter(type="offre").update(type="emploi")
    Opportunite.objects.filter(type="autre").update(type="appel")
    Actualite = apps.get_model("contenus", "Actualite")
    Actualite.objects.exclude(categorie__in=["actualite", "evenement"]).update(categorie="actualite")


class Migration(migrations.Migration):
    dependencies = [("contenus", "0004_actualite_communautes_actualite_date_evenement_and_more")]
    operations = [migrations.RunPython(convertir, migrations.RunPython.noop)]
