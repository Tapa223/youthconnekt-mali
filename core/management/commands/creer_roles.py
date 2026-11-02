"""
Crée (ou met à jour) les rôles de l'équipe d'administration.

    python manage.py creer_roles

Un rôle est un « groupe » Django : on l'attribue à un compte dans
Administration > Membres > Comptes > (le compte) > Accès à l'administration.
La commande peut être relancée sans risque : elle remet les droits à jour.
"""

from django.contrib.auth.models import Group, Permission
from django.core.management.base import BaseCommand

# Pour chaque rôle : les modèles concernés et les actions permises.
# add = ajouter, change = modifier, delete = supprimer, view = consulter
ROLES = {
    "Éditeur de contenus": {
        "description": "Publie les actualités, opportunités et initiatives, et tient à jour l'accueil.",
        "droits": {
            "contenus.actualite": ["add", "change", "delete", "view"],
            "contenus.photoactualite": ["add", "change", "delete", "view"],
            "contenus.opportunite": ["add", "change", "delete", "view"],
            "contenus.initiative": ["add", "change", "delete", "view"],
            "contenus.photoinitiative": ["add", "change", "delete", "view"],
            "contenus.axeaction": ["change", "view"],
            "contenus.chiffrecle": ["add", "change", "delete", "view"],
            "contenus.partenaire": ["add", "change", "delete", "view"],
            "contenus.temoignage": ["add", "change", "delete", "view"],
            "contenus.motduministre": ["change", "view"],
            "core.sectionapropos": ["add", "change", "view"],
            "communautes.communaute": ["view"],
        },
    },
    "Modération des communautés": {
        "description": "Accepte ou refuse les demandes d'adhésion et gère les communautés.",
        "droits": {
            "communautes.communaute": ["add", "change", "view"],
            "communautes.demandeadhesion": ["change", "view"],
            "comptes.utilisateur": ["view"],
            "comptes.profilindividuel": ["view"],
            "comptes.profilorganisation": ["view"],
        },
    },
    "Gestion des membres": {
        "description": "Suit les comptes, les profils, les messages de contact et la newsletter.",
        "droits": {
            "comptes.utilisateur": ["change", "view"],
            "comptes.profilindividuel": ["change", "view"],
            "comptes.profilorganisation": ["change", "view"],
            "core.messagecontact": ["change", "view"],
            "core.abonnenewsletter": ["change", "view"],
            "core.parametressite": ["change", "view"],
        },
    },
}


class Command(BaseCommand):
    help = "Crée ou met à jour les rôles de l'équipe d'administration."

    def handle(self, *args, **options):
        for nom, role in ROLES.items():
            groupe, cree = Group.objects.get_or_create(name=nom)
            permissions = []
            for cle, actions in role["droits"].items():
                app_label, modele = cle.split(".")
                for action in actions:
                    codename = f"{action}_{modele}"
                    try:
                        permissions.append(
                            Permission.objects.get(content_type__app_label=app_label, codename=codename)
                        )
                    except Permission.DoesNotExist:
                        self.stderr.write(f"  Droit introuvable : {app_label}.{codename}")
            groupe.permissions.set(permissions)
            etat = "créé" if cree else "mis à jour"
            self.stdout.write(self.style.SUCCESS(f"{nom} : {etat} ({len(permissions)} droits)"))
            self.stdout.write(f"  {role['description']}")
