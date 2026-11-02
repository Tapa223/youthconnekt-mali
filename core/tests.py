from django.test import TestCase
from django.urls import reverse

from comptes.models import Utilisateur

from .models import AbonneNewsletter


class AccueilTests(TestCase):
    def test_accueil_vide_sans_contenus_ni_crochets_pour_visiteur(self):
        reponse = self.client.get(reverse("core:accueil"))
        self.assertEqual(reponse.status_code, 200)
        self.assertContains(reponse, "La jeunesse malienne se connecte ici.")
        # Les mentions « à fournir » sont réservées aux administrateurs.
        self.assertNotContains(reponse, "[Chiffres")
        self.assertNotContains(reponse, "Aucune initiative publiée")

    def test_administrateur_voit_les_emplacements_a_remplir(self):
        admin = Utilisateur.objects.create_superuser("admin@test.ml", "x")
        self.client.force_login(admin)
        reponse = self.client.get(reverse("core:accueil"))
        self.assertContains(reponse, "Aucune initiative publiée")
        self.assertContains(reponse, "Aucune opportunité ouverte")

    def test_mon_espace_remplace_connexion_une_fois_connecte(self):
        membre = Utilisateur.objects.create_user("m@test.ml", "x")
        self.client.force_login(membre)
        reponse = self.client.get(reverse("core:accueil"))
        self.assertContains(reponse, "Mon espace")
        self.assertNotContains(reponse, "Se connecter")


class NewsletterTests(TestCase):
    def test_inscription(self):
        self.client.post(reverse("core:newsletter"), {"email": "Awa@Exemple.ML"})
        self.assertTrue(AbonneNewsletter.objects.filter(email="awa@exemple.ml").exists())

    def test_robot_ignore(self):
        self.client.post(reverse("core:newsletter"), {"email": "robot@spam.ml", "site_web": "http://spam"})
        self.assertFalse(AbonneNewsletter.objects.exists())

    def test_adresse_invalide_refusee(self):
        self.client.post(reverse("core:newsletter"), {"email": "pas-une-adresse"})
        self.assertFalse(AbonneNewsletter.objects.exists())

    def test_redirection_externe_bloquee(self):
        reponse = self.client.post(
            reverse("core:newsletter"), {"email": "a@b.ml", "suivant": "https://site-malveillant.example"}
        )
        self.assertEqual(reponse["Location"], "/#newsletter")

    def test_get_refuse(self):
        self.assertEqual(self.client.get(reverse("core:newsletter")).status_code, 405)


class PagesTests(TestCase):
    def test_pages_publiques(self):
        for nom in ["core:a_propos", "core:contact", "contenus:initiatives", "contenus:opportunites",
                    "contenus:actualites", "communautes:liste", "comptes:connexion", "comptes:inscription"]:
            self.assertEqual(self.client.get(reverse(nom)).status_code, 200, nom)

    def test_formulaire_contact(self):
        from .models import MessageContact
        r = self.client.post(reverse("core:contact"), {"nom": "Awa", "email": "a@b.ml", "profil": "jeune", "sujet": "partenariat",
                                                      "message": "Bonjour, je souhaite en savoir plus.", "consentement": "on"})
        self.assertRedirects(r, reverse("core:contact"))
        self.assertEqual(MessageContact.objects.get().objet, "Partenariat")
        r = self.client.post(reverse("core:contact"), {"nom": "Awa", "email": "a@b.ml", "profil": "jeune", "sujet": "partenariat", "message": "Bonjour à tous"})
        self.assertContains(r, "Cochez cette case")
