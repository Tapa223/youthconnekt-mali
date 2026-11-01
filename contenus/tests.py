from datetime import timedelta

from django.test import TestCase
from django.utils import timezone

from .models import Actualite, MotDuMinistre, Opportunite, Statut


class OpportuniteTests(TestCase):
    def creer(self, **champs):
        valeurs = dict(titre="Bourse", type="appel", resume="r", description="d", statut=Statut.PUBLIE)
        valeurs.update(champs)
        return Opportunite.objects.create(**valeurs)

    def test_seules_les_opportunites_ouvertes_sont_listees(self):
        aujourd_hui = timezone.localdate()
        ouverte = self.creer(titre="Ouverte", date_limite=aujourd_hui)
        sans_date = self.creer(titre="Sans date")
        self.creer(titre="Expirée", date_limite=aujourd_hui - timedelta(days=1))
        self.creer(titre="Brouillon", statut=Statut.BROUILLON)
        self.assertEqual(set(Opportunite.objects.ouvertes()), {ouverte, sans_date})

    def test_adresse_creee_et_unique(self):
        a = self.creer(titre="Appel à projets jeunes")
        b = self.creer(titre="Appel à projets jeunes")
        self.assertEqual(a.slug, "appel-a-projets-jeunes")
        self.assertEqual(b.slug, "appel-a-projets-jeunes-2")


class PublicationTests(TestCase):
    def test_publication_programmee_non_visible(self):
        Actualite.objects.create(
            titre="Futur", categorie="annonce", chapeau="c", contenu="c",
            statut=Statut.PUBLIE, publie_le=timezone.now() + timedelta(days=2),
        )
        self.assertEqual(Actualite.objects.publies().count(), 0)

    def test_mot_du_ministre_fiche_unique(self):
        MotDuMinistre(nom="A").save()
        MotDuMinistre(nom="B").save()
        self.assertEqual(MotDuMinistre.objects.count(), 1)
        self.assertEqual(MotDuMinistre.charger().nom, "B")
