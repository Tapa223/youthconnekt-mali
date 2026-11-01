"""
Configuration du site YouthConnekt Mali.

Les valeurs sensibles (clé secrète, accès à la base, mode debug) ne sont
jamais écrites ici : elles sont lues depuis le fichier .env, qui n'est pas
envoyé sur GitHub. Voir .env.example pour la liste des variables.
"""

import os
from pathlib import Path

from dotenv import load_dotenv

BASE_DIR = Path(__file__).resolve().parent.parent

load_dotenv(BASE_DIR / ".env")


def env_bool(nom, defaut=False):
    """Lit une variable d'environnement de type oui/non."""
    valeur = os.getenv(nom)
    if valeur is None:
        return defaut
    return valeur.strip().lower() in ("1", "true", "oui", "yes", "on")


def env_liste(nom, defaut=""):
    """Lit une liste séparée par des virgules."""
    return [v.strip() for v in os.getenv(nom, defaut).split(",") if v.strip()]


# --------------------------------------------------------------------------
# Sécurité de base
# --------------------------------------------------------------------------

DEBUG = env_bool("DEBUG", False)

SECRET_KEY = os.getenv("SECRET_KEY", "")
if not SECRET_KEY:
    if DEBUG:
        # Clé de secours réservée au développement local.
        SECRET_KEY = "dev-uniquement-cle-non-secrete-a-ne-jamais-utiliser-en-production"
    else:
        raise RuntimeError(
            "SECRET_KEY manquante : renseignez-la dans le fichier .env."
        )

ALLOWED_HOSTS = env_liste("ALLOWED_HOSTS", "127.0.0.1,localhost")
CSRF_TRUSTED_ORIGINS = env_liste("CSRF_TRUSTED_ORIGINS", "")


# --------------------------------------------------------------------------
# Applications
# --------------------------------------------------------------------------

INSTALLED_APPS = [
    "django.contrib.admin",
    "django.contrib.auth",
    "django.contrib.contenttypes",
    "django.contrib.sessions",
    "django.contrib.messages",
    "django.contrib.staticfiles",
    # Applications du projet
    "core",
    "comptes",
    "communautes",
    "contenus",
]

MIDDLEWARE = [
    "django.middleware.security.SecurityMiddleware",
    "django.contrib.sessions.middleware.SessionMiddleware",
    "django.middleware.common.CommonMiddleware",
    "django.middleware.csrf.CsrfViewMiddleware",
    "django.contrib.auth.middleware.AuthenticationMiddleware",
    "django.contrib.messages.middleware.MessageMiddleware",
    "django.middleware.clickjacking.XFrameOptionsMiddleware",
]

ROOT_URLCONF = "config.urls"

TEMPLATES = [
    {
        "BACKEND": "django.template.backends.django.DjangoTemplates",
        "DIRS": [BASE_DIR / "templates"],
        "APP_DIRS": True,
        "OPTIONS": {
            "context_processors": [
                "django.template.context_processors.request",
                "django.contrib.auth.context_processors.auth",
                "django.contrib.messages.context_processors.messages",
                "core.context_processors.site",
            ],
        },
    },
]

WSGI_APPLICATION = "config.wsgi.application"


# --------------------------------------------------------------------------
# Base de données
# Si DB_NAME est renseigné dans .env, on utilise MySQL ; sinon SQLite.
# --------------------------------------------------------------------------

if os.getenv("DB_NAME"):
    DATABASES = {
        "default": {
            "ENGINE": "django.db.backends.mysql",
            "NAME": os.getenv("DB_NAME"),
            "USER": os.getenv("DB_USER", ""),
            "PASSWORD": os.getenv("DB_PASSWORD", ""),
            "HOST": os.getenv("DB_HOST", "127.0.0.1"),
            "PORT": os.getenv("DB_PORT", "3306"),
            "OPTIONS": {
                "charset": "utf8mb4",
                "init_command": "SET sql_mode='STRICT_TRANS_TABLES'",
            },
        }
    }
else:
    DATABASES = {
        "default": {
            "ENGINE": "django.db.backends.sqlite3",
            "NAME": BASE_DIR / "db.sqlite3",
        }
    }

DEFAULT_AUTO_FIELD = "django.db.models.BigAutoField"


# --------------------------------------------------------------------------
# Comptes et authentification
# --------------------------------------------------------------------------

AUTH_USER_MODEL = "comptes.Utilisateur"

LOGIN_URL = "comptes:connexion"
LOGIN_REDIRECT_URL = "comptes:espace_membre"
LOGOUT_REDIRECT_URL = "core:accueil"

AUTH_PASSWORD_VALIDATORS = [
    {"NAME": "django.contrib.auth.password_validation.UserAttributeSimilarityValidator"},
    {
        "NAME": "django.contrib.auth.password_validation.MinimumLengthValidator",
        "OPTIONS": {"min_length": 8},
    },
    {"NAME": "django.contrib.auth.password_validation.CommonPasswordValidator"},
    {"NAME": "django.contrib.auth.password_validation.NumericPasswordValidator"},
]

# Règle d'adhésion aux communautés thématiques (à confirmer par l'équipe) :
# False = chaque demande est validée par un administrateur ;
# True  = la demande est acceptée automatiquement.
COMMUNAUTES_ACCEPTATION_AUTOMATIQUE = env_bool(
    "COMMUNAUTES_ACCEPTATION_AUTOMATIQUE", False
)


# --------------------------------------------------------------------------
# Langue et fuseau horaire
# --------------------------------------------------------------------------

LANGUAGE_CODE = "fr-fr"
TIME_ZONE = "Africa/Bamako"
USE_I18N = True
USE_TZ = True


# --------------------------------------------------------------------------
# Fichiers statiques (CSS, JS, images de l'interface)
# et fichiers envoyés (photos, logos, images des contenus)
# --------------------------------------------------------------------------

STATIC_URL = "static/"
STATICFILES_DIRS = [BASE_DIR / "static"]
STATIC_ROOT = BASE_DIR / "staticfiles"

MEDIA_URL = "media/"
MEDIA_ROOT = BASE_DIR / "media"

# Taille maximale d'un envoi traité en mémoire (5 Mo).
DATA_UPLOAD_MAX_MEMORY_SIZE = 5 * 1024 * 1024
FILE_UPLOAD_MAX_MEMORY_SIZE = 5 * 1024 * 1024


# --------------------------------------------------------------------------
# E-mails
# En développement, les e-mails s'affichent dans le terminal.
# --------------------------------------------------------------------------

MAILERS = {
    "default": {
        "BACKEND": "django.core.mail.backends.console.EmailBackend",
    },
}
DEFAULT_FROM_EMAIL = os.getenv("DEFAULT_FROM_EMAIL", "YouthConnekt Mali <noreply@localhost>")


# --------------------------------------------------------------------------
# Durcissement en production (actif dès que DEBUG=False)
# --------------------------------------------------------------------------

SESSION_COOKIE_HTTPONLY = True
SESSION_COOKIE_AGE = 60 * 60 * 24 * 14  # deux semaines
X_FRAME_OPTIONS = "DENY"
SECURE_CONTENT_TYPE_NOSNIFF = True
SECURE_REFERRER_POLICY = "same-origin"

if not DEBUG:
    SECURE_SSL_REDIRECT = env_bool("SECURE_SSL_REDIRECT", True)
    SECURE_PROXY_SSL_HEADER = ("HTTP_X_FORWARDED_PROTO", "https")
    SESSION_COOKIE_SECURE = True
    CSRF_COOKIE_SECURE = True
    SECURE_HSTS_SECONDS = int(os.getenv("SECURE_HSTS_SECONDS", "3600"))
    SECURE_HSTS_INCLUDE_SUBDOMAINS = False
