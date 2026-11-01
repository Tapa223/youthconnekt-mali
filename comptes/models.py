from django.contrib.auth.models import AbstractUser, BaseUserManager
from django.db import models
from django.urls import reverse

from core.validators import valider_image


class UtilisateurManager(BaseUserManager):
    """Gestion des comptes : l'adresse e-mail sert d'identifiant."""

    use_in_migrations = True

    def _creer(self, email, password, **champs):
        if not email:
            raise ValueError("L'adresse e-mail est obligatoire.")
        email = self.normalize_email(email).lower()
        utilisateur = self.model(email=email, **champs)
        utilisateur.set_password(password)
        utilisateur.save(using=self._db)
        return utilisateur

    def create_user(self, email, password=None, **champs):
        champs.setdefault("is_staff", False)
        champs.setdefault("is_superuser", False)
        return self._creer(email, password, **champs)

    def create_superuser(self, email, password=None, **champs):
        champs.setdefault("is_staff", True)
        champs.setdefault("is_superuser", True)
        champs.setdefault("email_confirme", True)
        if champs.get("is_staff") is not True or champs.get("is_superuser") is not True:
            raise ValueError("Un super-administrateur doit avoir is_staff et is_superuser.")
        return self._creer(email, password, **champs)


class Utilisateur(AbstractUser):
    """
    Compte de connexion. Ne contient que les informations privées :
    elles ne sont jamais affichées publiquement.
    """

    class TypeCompte(models.TextChoices):
        INDIVIDUEL = "individuel", "Membre individuel"
        ORGANISATION = "organisation", "Organisation ou entreprise"

    username = None
    first_name = None
    last_name = None

    email = models.EmailField("adresse e-mail", unique=True)
    type_compte = models.CharField(
        "type de compte",
        max_length=20,
        choices=TypeCompte.choices,
        default=TypeCompte.INDIVIDUEL,
    )
    email_confirme = models.BooleanField("e-mail confirmé", default=False)

    USERNAME_FIELD = "email"
    REQUIRED_FIELDS = []

    objects = UtilisateurManager()

    class Meta:
        verbose_name = "compte"
        verbose_name_plural = "comptes"
        ordering = ["-date_joined"]

    def __str__(self):
        return self.email

    @property
    def est_organisation(self):
        return self.type_compte == self.TypeCompte.ORGANISATION

    @property
    def profil(self):
        """Renvoie le profil public correspondant au type de compte, s'il existe."""
        nom = "profil_organisation" if self.est_organisation else "profil_individuel"
        return getattr(self, nom, None)

    @property
    def nom_affiche(self):
        profil = self.profil
        return str(profil) if profil else self.email


class ProfilBase(models.Model):
    """Champs communs aux deux types de profils publics."""

    ville = models.CharField("ville", max_length=100, blank=True)
    presentation = models.TextField("présentation", max_length=1500, blank=True)
    site_web = models.URLField("site web", blank=True)
    lien_linkedin = models.URLField("LinkedIn", blank=True)
    visible = models.BooleanField(
        "visible dans la communauté",
        default=True,
        help_text="Décocher pour masquer ce profil des pages publiques.",
    )
    cree_le = models.DateTimeField(auto_now_add=True)
    modifie_le = models.DateTimeField(auto_now=True)

    class Meta:
        abstract = True


class ProfilIndividuel(ProfilBase):
    class Categorie(models.TextChoices):
        JEUNE = "jeune", "Jeune"
        ETUDIANT = "etudiant", "Étudiant"
        PROFESSIONNEL = "professionnel", "Jeune professionnel"
        ENTREPRENEUR = "entrepreneur", "Entrepreneur"
        PORTEUR_PROJET = "porteur_projet", "Porteur de projet"
        INNOVATEUR = "innovateur", "Innovateur"
        LEADER_ASSOCIATIF = "leader_associatif", "Jeune leader d'association"

    utilisateur = models.OneToOneField(
        Utilisateur,
        on_delete=models.CASCADE,
        related_name="profil_individuel",
        verbose_name="compte",
    )
    prenom = models.CharField("prénom", max_length=80)
    nom = models.CharField("nom", max_length=80)
    photo = models.ImageField(
        "photo", upload_to="profils/", blank=True, validators=[valider_image]
    )
    categorie = models.CharField("profil", max_length=30, choices=Categorie.choices)
    domaine = models.CharField("domaine d'activité ou d'études", max_length=150, blank=True)

    class Meta:
        verbose_name = "profil individuel"
        verbose_name_plural = "profils individuels"
        ordering = ["nom", "prenom"]

    def __str__(self):
        return f"{self.prenom} {self.nom}"

    def get_absolute_url(self):
        return reverse("comptes:profil_public", args=[self.utilisateur_id])


class ProfilOrganisation(ProfilBase):
    class TypeStructure(models.TextChoices):
        ENTREPRISE = "entreprise", "Entreprise"
        STARTUP = "startup", "Startup"
        ASSOCIATION = "association", "Association"
        ONG = "ong", "ONG"
        AUTRE = "autre", "Autre structure"

    utilisateur = models.OneToOneField(
        Utilisateur,
        on_delete=models.CASCADE,
        related_name="profil_organisation",
        verbose_name="compte",
    )
    nom_structure = models.CharField("nom de la structure", max_length=150)
    logo = models.ImageField(
        "logo", upload_to="organisations/", blank=True, validators=[valider_image]
    )
    type_structure = models.CharField(
        "type de structure", max_length=20, choices=TypeStructure.choices
    )
    secteur = models.CharField("secteur d'activité", max_length=150, blank=True)
    email_contact_public = models.EmailField(
        "e-mail de contact public",
        blank=True,
        help_text="Facultatif. Différent de l'adresse de connexion, qui reste privée.",
    )

    class Meta:
        verbose_name = "profil d'organisation"
        verbose_name_plural = "profils d'organisations"
        ordering = ["nom_structure"]

    def __str__(self):
        return self.nom_structure

    def get_absolute_url(self):
        return reverse("comptes:profil_public", args=[self.utilisateur_id])
