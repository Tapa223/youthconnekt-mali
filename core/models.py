from django.db import models

from core.validators import valider_image


class ModeleUnique(models.Model):
    """Modèle qui ne contient qu'une seule fiche (paramètres, mot du ministre...)."""

    class Meta:
        abstract = True

    def save(self, *args, **kwargs):
        self.pk = 1
        super().save(*args, **kwargs)

    def delete(self, *args, **kwargs):
        # La fiche unique ne se supprime pas : on la modifie.
        pass

    @classmethod
    def charger(cls):
        objet, _ = cls.objects.get_or_create(pk=1)
        return objet


class ParametresSite(ModeleUnique):
    """Coordonnées et textes généraux affichés sur tout le site."""

    accroche_accueil = models.CharField(
        "titre principal de l'accueil", max_length=200, blank=True
    )
    presentation_accueil = models.TextField(
        "présentation sur l'accueil", max_length=1200, blank=True
    )
    image_accueil = models.ImageField(
        "photo principale de l'accueil",
        upload_to="site/",
        blank=True,
        validators=[valider_image],
    )
    adresse = models.CharField("adresse", max_length=255, blank=True)
    telephone = models.CharField("téléphone", max_length=40, blank=True)
    email_contact = models.EmailField("e-mail de contact", blank=True)
    lien_facebook = models.URLField("Facebook", blank=True)
    lien_instagram = models.URLField("Instagram", blank=True)
    lien_linkedin = models.URLField("LinkedIn", blank=True)
    lien_x = models.URLField("X (Twitter)", blank=True)
    lien_youtube = models.URLField("YouTube", blank=True)
    lien_tiktok = models.URLField("TikTok", blank=True)

    class Meta:
        verbose_name = "paramètres du site"
        verbose_name_plural = "paramètres du site"

    def __str__(self):
        return "Paramètres du site"


class SectionAPropos(models.Model):
    """Sections modifiables de la page À propos (mission, vision, objectifs...)."""

    titre = models.CharField("titre", max_length=150)
    contenu = models.TextField("contenu")
    image = models.ImageField(
        "image", upload_to="a-propos/", blank=True, validators=[valider_image]
    )
    ordre = models.PositiveSmallIntegerField("ordre d'affichage", default=0)
    publiee = models.BooleanField("publiée", default=True)

    class Meta:
        verbose_name = "section de la page À propos"
        verbose_name_plural = "page À propos"
        ordering = ["ordre", "id"]

    def __str__(self):
        return self.titre


class MessageContact(models.Model):
    nom = models.CharField("nom", max_length=120)
    email = models.EmailField("e-mail")
    objet = models.CharField("objet", max_length=200)
    message = models.TextField("message", max_length=5000)
    recu_le = models.DateTimeField("reçu le", auto_now_add=True)
    traite = models.BooleanField("traité", default=False)

    class Meta:
        verbose_name = "message de contact"
        verbose_name_plural = "messages de contact"
        ordering = ["traite", "-recu_le"]

    def __str__(self):
        return f"{self.objet} ({self.nom})"


class AbonneNewsletter(models.Model):
    email = models.EmailField("e-mail", unique=True)
    inscrit_le = models.DateTimeField("inscrit le", auto_now_add=True)
    actif = models.BooleanField("actif", default=True)

    class Meta:
        verbose_name = "abonné à la newsletter"
        verbose_name_plural = "abonnés à la newsletter"
        ordering = ["-inscrit_le"]

    def __str__(self):
        return self.email
