from django.db import IntegrityError
from django.test import TestCase

from comptes.models import Utilisateur

from .models import Communaute, DemandeAdhesion


class AdhesionTests(TestCase):
    def test_communautes_de_depart_chargees(self):
        self.assertEqual(Communaute.objects.actives().count(), 9)

    def test_une_seule_demande_par_communaute(self):
        membre = Utilisateur.objects.create_user("m@b.ml", "x")
        communaute = Communaute.objects.first()
        DemandeAdhesion.objects.create(membre=membre, communaute=communaute)
        with self.assertRaises(IntegrityError):
            DemandeAdhesion.objects.create(membre=membre, communaute=communaute)

    def test_traitement_par_administrateur(self):
        admin = Utilisateur.objects.create_superuser("admin@b.ml", "x")
        membre = Utilisateur.objects.create_user("m@b.ml", "x")
        demande = DemandeAdhesion.objects.create(membre=membre, communaute=Communaute.objects.first())
        demande.marquer(DemandeAdhesion.Statut.ACCEPTEE, admin)
        demande.refresh_from_db()
        self.assertEqual(demande.traitee_par, admin)
        self.assertIsNotNone(demande.traitee_le)
        self.assertEqual(demande.communaute.nombre_membres(), 1)
