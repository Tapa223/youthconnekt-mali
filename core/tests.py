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


class AdministrationTests(TestCase):
    """Tableau de bord, rubriques et rôles de l'administration."""

    def setUp(self):
        from django.core.management import call_command

        call_command("creer_roles", stdout=open("/dev/null", "w"))
        self.admin = Utilisateur.objects.create_superuser("chef@test.ml", "MotDePasse!2026")

    def test_tableau_de_bord_et_rubriques(self):
        from .models import MessageContact

        MessageContact.objects.create(nom="Awa", email="awa@test.ml", objet="Partenariat", message="Bonjour")
        self.client.force_login(self.admin)
        reponse = self.client.get(reverse("admin:index"))
        self.assertContains(reponse, "À traiter")
        self.assertContains(reponse, "Messages de contact à traiter")
        self.assertContains(reponse, "Partenariat")
        for rubrique in ["Publications", "Accueil et pages", "Communautés", "Membres", "Messages et newsletter"]:
            self.assertContains(reponse, rubrique)

    def test_redaction_ne_voit_pas_les_messages(self):
        from django.contrib.auth.models import Group

        redac = Utilisateur.objects.create_user("redac@test.ml", "MotDePasse!2026", is_staff=True)
        redac.groups.add(Group.objects.get(name="Éditeur de contenus"))
        self.client.force_login(redac)
        reponse = self.client.get(reverse("admin:index"))
        self.assertContains(reponse, "Publier une actualité")
        self.assertNotContains(reponse, "Messages de contact à traiter")
        self.assertEqual(self.client.get(reverse("admin:core_messagecontact_changelist")).status_code, 403)
        self.assertEqual(self.client.get(reverse("admin:contenus_actualite_add")).status_code, 200)

    def test_gestion_des_membres_ne_peut_pas_se_donner_plus_de_droits(self):
        from django.contrib.auth.models import Group

        gestion = Utilisateur.objects.create_user("gestion@test.ml", "MotDePasse!2026", is_staff=True)
        gestion.groups.add(Group.objects.get(name="Gestion des membres"))
        self.client.force_login(gestion)
        url = reverse("admin:comptes_utilisateur_change", args=[gestion.pk])
        reponse = self.client.get(url)
        self.assertEqual(reponse.status_code, 200)
        self.assertNotContains(reponse, 'name="is_superuser"')
        self.assertNotContains(reponse, 'name="groups"')
