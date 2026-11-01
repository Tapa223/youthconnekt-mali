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
