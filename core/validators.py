from pathlib import Path

from django.core.exceptions import ValidationError

EXTENSIONS_IMAGES = {".jpg", ".jpeg", ".png", ".webp"}
TAILLE_MAX_IMAGE = 3 * 1024 * 1024  # 3 Mo


def valider_image(fichier):
    """
    Refuse les images trop lourdes ou d'un format non prévu.
    Le contenu lui-même est vérifié par Pillow via ImageField.
    """
    extension = Path(fichier.name).suffix.lower()
    if extension not in EXTENSIONS_IMAGES:
        raise ValidationError(
            "Format non accepté. Utilisez une image JPG, PNG ou WebP."
        )
    if fichier.size > TAILLE_MAX_IMAGE:
        raise ValidationError(
            "Image trop lourde : 3 Mo maximum. Compressez-la avant de l'envoyer."
        )
