from django.core.exceptions import ValidationError
from django.core.files.uploadedfile import SimpleUploadedFile
from django.test import TestCase

from core.validators import valider_image

from .models import ProfilIndividuel, Utilisateur


class UtilisateurTests(TestCase):
    def test_email_identifiant_normalise_et_mot_de_passe_chiffre(self):
        u = Utilisateur.objects.create_user("Awa@Exemple.ML", "MotDePasse!2026")
        self.assertEqual(u.email, "awa@exemple.ml")
        self.assertNotEqual(u.password, "MotDePasse!2026")
        self.assertTrue(u.check_password("MotDePasse!2026"))
        self.assertFalse(u.is_staff)

    def test_profil_selon_type_de_compte(self):
        u = Utilisateur.objects.create_user("a@b.ml", "x")
        ProfilIndividuel.objects.create(utilisateur=u, prenom="Awa", nom="Traoré", categorie="etudiant")
        self.assertEqual(u.nom_affiche, "Awa Traoré")


class ValidationImageTests(TestCase):
    def test_refuse_format_non_prevu(self):
        with self.assertRaises(ValidationError):
            valider_image(SimpleUploadedFile("script.php", b"x"))

    def test_refuse_image_trop_lourde(self):
        with self.assertRaises(ValidationError):
            valider_image(SimpleUploadedFile("photo.jpg", b"0" * (3 * 1024 * 1024 + 1)))


import re

from django.core import mail
from django.test import override_settings
from django.urls import reverse

from communautes.models import Communaute, DemandeAdhesion
from core.models import AbonneNewsletter


class ParcoursInscriptionTests(TestCase):
    donnees = {
        "prenom": "Awa", "nom": "Traoré", "categorie": "etudiant", "region": "bamako",
        "email": "Awa@Test.ML", "telephone": "", "password1": "Bamako!Jeunesse26",
        "password2": "Bamako!Jeunesse26", "conditions": "on", "newsletter": "on",
    }

    def test_inscription_confirmation_puis_connexion(self):
        reponse = self.client.post(reverse("comptes:inscription_formulaire", args=["individuel"]), self.donnees)
        self.assertRedirects(reponse, reverse("comptes:verification"))
        u = Utilisateur.objects.get(email="awa@test.ml")
        self.assertFalse(u.email_confirme)
        self.assertEqual(u.profil.prenom, "Awa")
        self.assertTrue(AbonneNewsletter.objects.filter(email="awa@test.ml").exists())
        # Connexion refusée tant que l'e-mail n'est pas confirmé
        r = self.client.post(reverse("comptes:connexion"), {"username": "awa@test.ml", "password": "Bamako!Jeunesse26"})
        self.assertContains(r, "abord votre adresse e-mail")
        # Lien de confirmation reçu par e-mail
        self.assertEqual(len(mail.outbox), 1)
        lien = re.search(r"http://testserver(/compte/confirmer/\S+)", mail.outbox[0].body).group(1)
        r = self.client.get(lien)
        self.assertRedirects(r, reverse("comptes:espace_membre"))
        u.refresh_from_db()
        self.assertTrue(u.email_confirme)
        # Le lien ne sert qu'une fois
        self.client.logout()
        self.assertEqual(self.client.get(lien).status_code, 400)

    def test_conditions_obligatoires_et_mots_de_passe_identiques(self):
        donnees = dict(self.donnees, conditions="", password2="autre")
        r = self.client.post(reverse("comptes:inscription_formulaire", args=["individuel"]), donnees)
        self.assertContains(r, "Vous devez accepter")
        self.assertContains(r, "ne correspondent pas")
        self.assertFalse(Utilisateur.objects.exists())

    def test_email_deja_utilise(self):
        Utilisateur.objects.create_user("awa@test.ml", "x")
        r = self.client.post(reverse("comptes:inscription_formulaire", args=["individuel"]), self.donnees)
        self.assertContains(r, "Un compte existe déjà")

    def test_inscription_organisation(self):
        donnees = {"nom_structure": "Kalanso", "type_structure": "association", "personne_contact": "Moussa D.",
                   "region": "segou", "email": "contact@kalanso.ml", "site_web": "kalanso.ml",
                   "password1": "Segou!Jeunesse26", "password2": "Segou!Jeunesse26", "conditions": "on"}
        self.client.post(reverse("comptes:inscription_formulaire", args=["organisation"]), donnees)
        u = Utilisateur.objects.get(email="contact@kalanso.ml")
        self.assertTrue(u.est_organisation)
        self.assertEqual(u.profil.nom_structure, "Kalanso")


class EspaceMembreTests(TestCase):
    def setUp(self):
        self.u = Utilisateur.objects.create_user("m@test.ml", "Bamako!Jeunesse26", email_confirme=True)
        ProfilIndividuel.objects.create(utilisateur=self.u, prenom="Awa", nom="T", categorie="jeune")
        self.client.force_login(self.u)
        self.c = Communaute.objects.get(slug="tech-et-numerique")

    def test_espace_reserve_aux_membres(self):
        self.client.logout()
        r = self.client.get(reverse("comptes:espace_membre"))
        self.assertEqual(r.status_code, 302)

    def test_demande_puis_quitter(self):
        self.client.post(reverse("communautes:rejoindre", args=[self.c.slug]))
        d = DemandeAdhesion.objects.get(membre=self.u, communaute=self.c)
        self.assertEqual(d.statut, "en_attente")
        self.client.post(reverse("communautes:quitter", args=[self.c.slug]))
        self.assertFalse(DemandeAdhesion.objects.exists())

    @override_settings(COMMUNAUTES_ACCEPTATION_AUTOMATIQUE=True)
    def test_acceptation_automatique(self):
        self.client.post(reverse("communautes:rejoindre", args=[self.c.slug]))
        self.assertEqual(DemandeAdhesion.objects.get().statut, "acceptee")

    def test_redirection_externe_refusee(self):
        r = self.client.post(reverse("communautes:rejoindre", args=[self.c.slug]), {"suivant": "//site-malveillant.example"})
        self.assertEqual(r["Location"], self.c.get_absolute_url())

    def test_modifier_profil(self):
        r = self.client.post(reverse("comptes:mon_profil"), {"prenom": "Aminata", "nom": "Keïta", "categorie": "etudiant", "region": "sikasso", "visible": "on"})
        self.assertRedirects(r, reverse("comptes:mon_profil"))
        self.u.refresh_from_db()
        self.assertEqual(self.u.profil.prenom, "Aminata")

    def test_suppression_du_compte(self):
        r = self.client.post(reverse("comptes:parametres"), {"action": "supprimer", "mot_de_passe": "mauvais"})
        self.assertContains(r, "Mot de passe incorrect")
        self.client.post(reverse("comptes:parametres"), {"action": "supprimer", "mot_de_passe": "Bamako!Jeunesse26"})
        self.assertFalse(Utilisateur.objects.filter(pk=self.u.pk).exists())

    def test_profil_masque_invisible_aux_autres(self):
        self.u.profil.visible = False
        self.u.profil.save()
        autre = Utilisateur.objects.create_user("b@test.ml", "x", email_confirme=True)
        self.client.force_login(autre)
        self.assertEqual(self.client.get(reverse("comptes:profil_public", args=[self.u.pk])).status_code, 404)

    def test_aucune_donnee_privee_sur_le_profil_public(self):
        self.u.profil.telephone = "+223 70 00 00 00"
        self.u.profil.save()
        r = self.client.get(reverse("comptes:profil_public", args=[self.u.pk]))
        self.assertNotContains(r, "70 00 00 00")
        self.assertNotContains(r, "m@test.ml")
