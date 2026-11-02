from django.conf import settings
from django.db import models
from django.urls import reverse
from django.utils import timezone

from core.validators import valider_image


class CommunauteQuerySet(models.QuerySet):
    def actives(self):
        return self.filter(active=True)


class Communaute(models.Model):
    nom = models.CharField("nom", max_length=120, unique=True)
    slug = models.SlugField("adresse (URL)", max_length=140, unique=True)
    public_vise = models.CharField("public visé", max_length=255)
    description = models.TextField("description", blank=True)
    activites = models.TextField("ce qu'on y fait", blank=True, help_text="Une activité par ligne.")
    image = models.ImageField(
        "image", upload_to="communautes/", blank=True, validators=[valider_image]
    )
    ordre = models.PositiveSmallIntegerField("ordre d'affichage", default=0)
    active = models.BooleanField(
        "active",
        default=True,
        help_text="Une communauté désactivée n'apparaît plus sur le site, sans perte de données.",
    )

    objects = CommunauteQuerySet.as_manager()

    class Meta:
        verbose_name = "communauté thématique"
        verbose_name_plural = "communautés thématiques"
        ordering = ["ordre", "nom"]

    def __str__(self):
        return self.nom

    def get_absolute_url(self):
        return reverse("communautes:detail", args=[self.slug])

    @property
    def liste_activites(self):
        return [l.strip().lstrip("-•").strip() for l in self.activites.splitlines() if l.strip()]

    def nombre_membres(self):
        return self.demandes.filter(statut=DemandeAdhesion.Statut.ACCEPTEE).count()


class DemandeAdhesion(models.Model):
    class Statut(models.TextChoices):
        EN_ATTENTE = "en_attente", "En attente"
        ACCEPTEE = "acceptee", "Acceptée"
        REFUSEE = "refusee", "Refusée"

    membre = models.ForeignKey(
        settings.AUTH_USER_MODEL,
        on_delete=models.CASCADE,
        related_name="demandes_adhesion",
        verbose_name="membre",
    )
    communaute = models.ForeignKey(
        Communaute,
        on_delete=models.CASCADE,
        related_name="demandes",
        verbose_name="communauté",
    )
    statut = models.CharField(
        "statut", max_length=20, choices=Statut.choices, default=Statut.EN_ATTENTE
    )
    demandee_le = models.DateTimeField("demandée le", auto_now_add=True)
    traitee_le = models.DateTimeField("traitée le", null=True, blank=True)
    traitee_par = models.ForeignKey(
        settings.AUTH_USER_MODEL,
        on_delete=models.SET_NULL,
        null=True,
        blank=True,
        related_name="+",
        verbose_name="traitée par",
    )

    class Meta:
        verbose_name = "demande d'adhésion"
        verbose_name_plural = "demandes d'adhésion"
        ordering = ["statut", "-demandee_le"]
        constraints = [
            # Un membre ne peut avoir qu'une demande par communauté.
            models.UniqueConstraint(
                fields=["membre", "communaute"], name="une_demande_par_communaute"
            )
        ]

    def __str__(self):
        return f"{self.membre} → {self.communaute}"

    def marquer(self, statut, administrateur=None):
        self.statut = statut
        self.traitee_le = timezone.now()
        self.traitee_par = administrateur
        self.save(update_fields=["statut", "traitee_le", "traitee_par"])
