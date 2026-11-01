from django.db import migrations

# Rôle limité : publie les contenus sans accès aux comptes ni aux réglages.
PERMISSIONS_EDITEUR = {
    "contenus": ["initiative", "photoinitiative", "opportunite", "actualite", "axeaction"],
}


def creer_groupe(apps, schema_editor):
    from django.contrib.auth.management import create_permissions

    for app_config in apps.get_app_configs():
        app_config.models_module = True
        create_permissions(app_config, apps=apps, verbosity=0)
        app_config.models_module = None

    Group = apps.get_model("auth", "Group")
    Permission = apps.get_model("auth", "Permission")
    groupe, _ = Group.objects.get_or_create(name="Éditeur de contenus")
    for app_label, modeles in PERMISSIONS_EDITEUR.items():
        for modele in modeles:
            perms = Permission.objects.filter(
                content_type__app_label=app_label, content_type__model=modele
            )
            groupe.permissions.add(*perms)


def supprimer_groupe(apps, schema_editor):
    apps.get_model("auth", "Group").objects.filter(name="Éditeur de contenus").delete()


class Migration(migrations.Migration):
    dependencies = [
        ("core", "0001_initial"),
        ("contenus", "0001_initial"),
        ("auth", "0012_alter_user_first_name_max_length"),
        ("contenttypes", "0002_remove_content_type_name"),
    ]
    operations = [migrations.RunPython(creer_groupe, supprimer_groupe)]
