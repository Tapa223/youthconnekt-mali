from django.db import models
from django.db.models import Q
from django.urls import reverse
from django.utils import timezone
from django.utils.text import slugify

from core.models import ModeleUnique
from core.validators import valider_image


def slug_unique(instance, valeur, champ="slug", longueur=180):
    """Fabrique une adresse lisible et unique à partir d'un titre."""
    base = slugify(valeur)[: longueur - 10] or "element"
    slug = base
    modele = instance.__class__
    n = 2
    while modele.objects.filter(**{champ: slug}).exclude(pk=instance.pk).exists():
        slug = f"{base}-{n}"
        n += 1
    return slug


class Statut(models.TextChoices):
    BROUILLON = "brouillon", "Brouillon"
    PUBLIE = "publie", "Publié"


class PubliableQuerySet(models.QuerySet):
    def publies(self):
        return self.filter(statut=Statut.PUBLIE, publie_le__lte=timezone.now())

    def en_avant(self):
        return self.publies().filter(mis_en_avant=True)


class Publiable(models.Model):
    """Champs communs aux contenus publiés par l'équipe."""

    titre = models.CharField("titre", max_length=200)
    slug = models.SlugField(
        "adresse (URL)",
        max_length=200,
        unique=True,
        blank=True,
        help_text="Laisser vide : elle est créée automatiquement à partir du titre.",
    )
    statut = models.CharField(
        "statut", max_length=20, choices=Statut.choices, default=Statut.BROUILLON
    )
    publie_le = models.DateTimeField(
        "date de publication",
        default=timezone.now,
        help_text="Une date future programme la publication.",
    )
    mis_en_avant = models.BooleanField("mis en avant sur l'accueil", default=False)
    cree_le = models.DateTimeField(auto_now_add=True)
    modifie_le = models.DateTimeField(auto_now=True)

    objects = PubliableQuerySet.as_manager()

    class Meta:
        abstract = True

    def __str__(self):
        return self.titre

    def save(self, *args, **kwargs):
        if not self.slug:
            self.slug = slug_unique(self, self.titre)
        super().save(*args, **kwargs)


# --------------------------------------------------------------------------
# Initiatives
# --------------------------------------------------------------------------


class AxeAction(models.Model):
    nom = models.CharField("nom", max_length=120, unique=True)
    slug = models.SlugField("adresse (URL)", max_length=140, unique=True)
    description = models.TextField("description", blank=True)
    image = models.ImageField(
        "image", upload_to="axes/", blank=True, validators=[valider_image]
    )
    ordre = models.PositiveSmallIntegerField("ordre d'affichage", default=0)

    class Meta:
        verbose_name = "axe d'action"
        verbose_name_plural = "axes d'action"
        ordering = ["ordre", "nom"]

    def __str__(self):
        return self.nom


class Initiative(Publiable):
    axe = models.ForeignKey(
        AxeAction,
        on_delete=models.SET_NULL,
        null=True,
        blank=True,
        related_name="initiatives",
        verbose_name="axe d'action",
    )
    resume = models.CharField("résumé", max_length=300)
    contenu = models.TextField("présentation détaillée")
    image = models.ImageField(
        "image principale", upload_to="initiatives/", blank=True, validators=[valider_image]
    )

    class Meta:
        verbose_name = "initiative"
        verbose_name_plural = "initiatives"
        ordering = ["-publie_le"]

    def get_absolute_url(self):
        return reverse("contenus:initiative", args=[self.slug])


class PhotoInitiative(models.Model):
    initiative = models.ForeignKey(
        Initiative, on_delete=models.CASCADE, related_name="photos"
    )
    image = models.ImageField("photo", upload_to="initiatives/galerie/", validators=[valider_image])
    legende = models.CharField("légende", max_length=200, blank=True)
    ordre = models.PositiveSmallIntegerField("ordre", default=0)

    class Meta:
        verbose_name = "photo"
        verbose_name_plural = "galerie de photos"
        ordering = ["ordre", "id"]

    def __str__(self):
        return self.legende or f"Photo {self.pk}"


# --------------------------------------------------------------------------
# Opportunités
# --------------------------------------------------------------------------


class OpportuniteQuerySet(PubliableQuerySet):
    def ouvertes(self):
        """Publiées et dont la date limite n'est pas dépassée (ou sans date limite)."""
        aujourd_hui = timezone.localdate()
        return self.publies().filter(
            Q(date_limite__isnull=True) | Q(date_limite__gte=aujourd_hui)
        )


class Opportunite(Publiable):
    class Type(models.TextChoices):
        OFFRE = "offre", "Offre"
        APPEL = "appel", "Appel à candidatures"
        FORMATION = "formation", "Formation"
        EVENEMENT = "evenement", "Événement"
        AUTRE = "autre", "Autre opportunité"

    type = models.CharField("type", max_length=20, choices=Type.choices)
    organisme = models.CharField("organisme", max_length=150, blank=True)
    resume = models.CharField("résumé", max_length=300)
    description = models.TextField("description complète")
    criteres = models.TextField(
        "conditions ou critères",
        blank=True,
        help_text="À remplir uniquement si les critères sont connus.",
    )
    date_limite = models.DateField("date limite", null=True, blank=True)
    lien_candidature = models.URLField("lien de candidature", blank=True)
    image = models.ImageField(
        "image", upload_to="opportunites/", blank=True, validators=[valider_image]
    )
    communautes = models.ManyToManyField(
        "communautes.Communaute",
        blank=True,
        related_name="opportunites",
        verbose_name="communautés concernées",
    )

    objects = OpportuniteQuerySet.as_manager()

    class Meta:
        verbose_name = "opportunité"
        verbose_name_plural = "opportunités"
        ordering = ["date_limite", "-publie_le"]

    def get_absolute_url(self):
        return reverse("contenus:opportunite", args=[self.slug])

    @property
    def est_expiree(self):
        return bool(self.date_limite and self.date_limite < timezone.localdate())

    @property
    def jours_restants(self):
        if not self.date_limite:
            return None
        return (self.date_limite - timezone.localdate()).days

    # Couleur associée à chaque type (classes Tailwind de la charte)
    COULEURS = {
        "formation": ("bg-turquoise text-encre", "bg-turquoise"),
        "appel": ("bg-orange text-white", "bg-orange"),
        "evenement": ("bg-vert text-white", "bg-vert"),
        "offre": ("bg-bleu text-white", "bg-bleu"),
        "autre": ("bg-rouge text-white", "bg-rouge"),
    }

    @property
    def classe_etiquette(self):
        return self.COULEURS.get(self.type, self.COULEURS["autre"])[0]

    @property
    def classe_bandeau(self):
        return self.COULEURS.get(self.type, self.COULEURS["autre"])[1]


# --------------------------------------------------------------------------
# Actualités
# --------------------------------------------------------------------------


class Actualite(Publiable):
    class Categorie(models.TextChoices):
        NOUVELLE = "nouvelle", "Nouvelle"
        ARTICLE = "article", "Article"
        ANNONCE = "annonce", "Annonce"
        COMPTE_RENDU = "compte_rendu", "Compte rendu"
        EVENEMENT = "evenement", "Événement"

    categorie = models.CharField("catégorie", max_length=20, choices=Categorie.choices)
    chapeau = models.CharField("chapeau (introduction)", max_length=400)
    contenu = models.TextField("corps de l'article")
    image = models.ImageField(
        "image principale", upload_to="actualites/", blank=True, validators=[valider_image]
    )

    class Meta:
        verbose_name = "actualité"
        verbose_name_plural = "actualités"
        ordering = ["-publie_le"]

    def get_absolute_url(self):
        return reverse("contenus:actualite", args=[self.slug])


# --------------------------------------------------------------------------
# Éléments de l'accueil
# --------------------------------------------------------------------------


class MotDuMinistre(ModeleUnique):
    nom = models.CharField("nom du ministre", max_length=150, blank=True)
    titre_officiel = models.CharField("titre officiel", max_length=255, blank=True)
    photo = models.ImageField(
        "photo officielle", upload_to="ministre/", blank=True, validators=[valider_image]
    )
    extrait = models.TextField("extrait affiché sur l'accueil", max_length=600, blank=True)
    message = models.TextField("message complet", blank=True)
    publie = models.BooleanField(
        "publié",
        default=False,
        help_text="Ne cocher qu'après validation officielle du texte et de la photo.",
    )

    class Meta:
        verbose_name = "mot du ministre"
        verbose_name_plural = "mot du ministre"

    def __str__(self):
        return "Mot du ministre"


class Partenaire(models.Model):
    nom = models.CharField("nom", max_length=150)
    logo = models.ImageField("logo", upload_to="partenaires/", validators=[valider_image])
    lien = models.URLField("site web", blank=True)
    ordre = models.PositiveSmallIntegerField("ordre d'affichage", default=0)
    actif = models.BooleanField("affiché", default=True)

    class Meta:
        verbose_name = "partenaire"
        verbose_name_plural = "partenaires"
        ordering = ["ordre", "nom"]

    def __str__(self):
        return self.nom


class Temoignage(models.Model):
    nom = models.CharField("nom", max_length=120)
    role = models.CharField("rôle ou parcours", max_length=150, blank=True)
    photo = models.ImageField(
        "photo", upload_to="temoignages/", blank=True, validators=[valider_image]
    )
    texte = models.TextField("témoignage", max_length=800)
    ordre = models.PositiveSmallIntegerField("ordre d'affichage", default=0)
    publie = models.BooleanField("publié", default=False)

    class Meta:
        verbose_name = "témoignage"
        verbose_name_plural = "témoignages"
        ordering = ["ordre", "-id"]

    def __str__(self):
        return f"{self.nom}"


class ChiffreCle(models.Model):
    valeur = models.CharField("valeur", max_length=30, help_text="Exemple : 1 200 ou 15+")
    libelle = models.CharField("libellé", max_length=120)
    ordre = models.PositiveSmallIntegerField("ordre d'affichage", default=0)
    publie = models.BooleanField(
        "publié", default=False, help_text="Ne publier qu'un chiffre vérifié."
    )

    class Meta:
        verbose_name = "chiffre clé"
        verbose_name_plural = "chiffres clés"
        ordering = ["ordre"]

    def __str__(self):
        return f"{self.valeur} {self.libelle}"
