"""
Charge les contenus d'exemple des maquettes pour voir les pages remplies.

    python manage.py charger_exemples              # ajoute les exemples
    python manage.py charger_exemples --supprimer  # retire tous les exemples

Les exemples sont reconnaissables à leur adresse, qui commence par « exemple- ».
Ils ne doivent jamais rester en ligne : ce ne sont pas des informations officielles.
"""

from datetime import timedelta
from pathlib import Path

from django.conf import settings
from django.core.files import File
from django.core.management.base import BaseCommand
from django.utils import timezone

from communautes.models import Communaute
from contenus.models import Actualite, AxeAction, Initiative, Opportunite, PhotoActualite, PhotoInitiative

PHOTOS = Path(settings.BASE_DIR) / "static" / "img" / "exemples"


def photo(nom):
    return File(open(PHOTOS / f"{nom}.jpg", "rb"), name=f"exemple-{nom}.jpg")


INITIATIVES = [
    dict(slug="bootcamp-jeunes-entrepreneurs", titre="Bootcamp Jeunes Entrepreneurs", axe="accompagner", photo="stand",
         resume="Trois jours pour transformer une idée en projet, avec des mentors, des ateliers pratiques et une présentation finale devant un jury.",
         contenu="Beaucoup de jeunes ont une idée de projet mais ne savent pas par où commencer. Le Bootcamp Jeunes Entrepreneurs leur donne trois jours pour passer de l'idée à un projet solide, accompagnés par des entrepreneurs qui sont passés par là.\n\nLes participants travaillent en petites équipes, alternent ateliers et séances de mentorat, puis présentent leur projet devant un jury composé de partenaires du chapitre.",
         etat="terminee", date_libelle="12 au 14 mars 2026", lieu="Bamako", participants="60 jeunes", format="Présentiel",
         objectifs="Transformer une idée en projet structuré\nRencontrer des entrepreneurs et des mentors\nPrésenter son projet devant un jury\nRepartir avec un plan d'action concret",
         programme="Jour 1 | Trouver et tester son idée | Ateliers sur le problème à résoudre, le client et la valeur du projet.\nJour 2 | Construire son modèle | Coûts, revenus, partenaires : chaque équipe bâtit son modèle avec un mentor.\nJour 3 | Convaincre | Préparation du pitch et présentation finale devant le jury.",
         chiffres="60 | jeunes accompagnés\n12 | mentors mobilisés\n15 | projets présentés",
         citation="J'y suis arrivée avec une idée, je suis repartie avec un projet et des mentors.", citation_auteur="Une participante",
         galerie=["salle-bleue", "intervenant", "panel", "arena"], une=True),
    dict(slug="caravane-de-la-citoyennete", titre="Caravane de la citoyenneté", axe="engager", photo="panel",
         resume="À la rencontre des jeunes dans les régions pour parler engagement.", contenu="La caravane va à la rencontre des jeunes dans les régions pour parler engagement citoyen.",
         etat="terminee", date_libelle="Janvier 2026", lieu="Ségou, Mopti"),
    dict(slug="prix-des-jeunes-innovateurs", titre="Prix des jeunes innovateurs", axe="valoriser", photo="arena",
         resume="Récompenser les projets qui font bouger le pays.", contenu="Un prix pour mettre en lumière les projets innovants portés par des jeunes.",
         etat="terminee", date_libelle="Décembre 2025", lieu="Bamako"),
    dict(slug="atelier-leadership-des-jeunes-femmes", titre="Atelier leadership des jeunes femmes", axe="accompagner", photo="intervenant",
         resume="Prise de parole, confiance et réseau pour les jeunes femmes.", contenu="Un atelier pour renforcer la prise de parole, la confiance et le réseau des jeunes femmes.",
         etat="terminee", date_libelle="Novembre 2025", lieu="Bamako"),
    dict(slug="dialogue-jeunes-et-decideurs", titre="Dialogue jeunes et décideurs", axe="engager", photo="ceremonie",
         resume="Une journée d'échanges entre jeunes et institutions.", contenu="Une journée d'échanges directs entre jeunes et institutions.",
         etat="terminee", date_libelle="Octobre 2025", lieu="Bamako"),
    dict(slug="vitrine-des-talents", titre="Vitrine des talents", axe="valoriser", photo="salle-bleue",
         resume="Mettre en lumière les jeunes créateurs et artisans.", contenu="Une vitrine pour mettre en lumière les jeunes créateurs et artisans.",
         etat="terminee", date_libelle="Septembre 2025", lieu="Sikasso"),
]

# (slug, titre, type, jours avant la date limite, organisme, lieu, photo, communautés)
OPPORTUNITES = [
    ("stage-en-gestion-de-projet", "Stage en gestion de projet", "emploi", 19, "[Organisation]", "Bamako", "stand", ["jeunes-professionnels"]),
    ("atelier-monter-son-projet", "Atelier « Monter son projet »", "formation", 24, "YouthConnekt Mali", "Ségou · présentiel", "salle-bleue", ["entrepreneuriat-et-startups"]),
    ("stage-en-communication-numerique", "Stage en communication numérique", "emploi", 32, "[Entreprise]", "Bamako", "panel", ["tech-et-numerique"]),
    ("formation-en-marketing-digital", "Formation en marketing digital", "formation", 34, "YouthConnekt Mali", "Bamako, présentiel", "stand", ["tech-et-numerique", "entrepreneuriat-et-startups"]),
    ("bourse-detudes-en-entrepreneuriat", "Bourse d'études en entrepreneuriat", "bourse", 39, "[Organisme]", "International", "ceremonie", ["etudiants"]),
    ("concours-de-pitch-jeunes-innovateurs", "Concours de pitch « Jeunes innovateurs »", "evenement", 42, "YouthConnekt Mali", "Bamako", "ceremonie", ["innovation-et-porteurs-de-projets", "agriculture-et-environnement"]),
    ("programme-de-mentorat-2027", "Programme de mentorat 2027", "appel", 49, "YouthConnekt Mali et partenaires", "En ligne", "intervenant", ["leadership-et-citoyennete"]),
    ("forum-national-de-lemploi-des-jeunes", "Forum national de l'emploi des jeunes", "evenement", 75, "[Organisateur]", "Bamako · présentiel", "arena", ["jeunes-professionnels"]),
    ("bourse-de-leadership-feminin", "Bourse de leadership féminin", "bourse", 85, "[Organisme]", "En ligne", "intervenant", ["leadership-et-citoyennete"]),
]

DETAIL_MARKETING = dict(
    description="Cette formation pratique apprend à promouvoir un projet, une association ou une petite entreprise sur internet : réseaux sociaux, contenus, publicité en ligne et mesure des résultats.\n\nElle est animée par des professionnels du numérique et se termine par la présentation d'un plan de communication.",
    avantages="Des méthodes concrètes, applicables tout de suite\nUn accompagnement personnalisé sur votre projet\nUne attestation de participation\nUn réseau de jeunes et de professionnels",
    criteres="Avoir entre 18 et 35 ans\nPorter un projet ou une activité, même débutante\nÊtre disponible deux soirs par semaine",
    etapes="Remplissez le formulaire de candidature en ligne.\nJoignez une courte présentation de votre projet.\nLes candidats retenus sont contactés par e-mail.",
    duree="4 semaines, 2 soirs par semaine", public="Jeunes de 18 à 35 ans", cout="Gratuit",
)

# (slug, titre, catégorie, décalage en jours de la date d'événement, lieu, photo, chapeau)
ACTUALITES = [
    ("lancement-officiel-de-la-plateforme", "Lancement officiel de la plateforme YouthConnekt Mali", "evenement", 40, "Bamako", "ceremonie",
     "Une nouvelle porte d'entrée pour les jeunes et les organisations du pays."),
    ("atelier-monter-son-projet-segou", "Atelier « Monter son projet »", "evenement", 46, "Ségou", "salle-bleue", "Un atelier pratique pour structurer son projet."),
    ("forum-national-emploi", "Forum national de l'emploi des jeunes", "evenement", 69, "Bamako", "arena", "Rencontrer des recruteurs et découvrir des offres."),
    ("retour-atelier-leadership", "Atelier leadership des jeunes femmes", "evenement", -4, "Bamako", "intervenant", "Trois jours d'échanges, de prise de parole et de réseau."),
    ("caravane-a-segou", "La Caravane de la citoyenneté à Ségou", "evenement", -26, "Ségou", "panel", "Ce que les jeunes nous ont dit pendant la Caravane."),
    ("remise-prix-innovateurs", "Remise du Prix des jeunes innovateurs", "evenement", -49, "Bamako", "stand", "Les lauréats de l'édition ont été récompensés."),
    ("appel-a-benevoles-forum", "Appel à bénévoles pour le forum national", "actualite", None, "", "salle-bleue", "Le chapitre recherche des bénévoles pour l'organisation du forum."),
    ("cinq-jeunes-agriculture", "Cinq jeunes qui innovent dans l'agriculture", "actualite", None, "", "stand", "Portraits de jeunes qui réinventent les filières agricoles."),
    ("preparer-une-candidature", "Comment préparer une candidature solide", "actualite", None, "", "arena", "Nos conseils pour réussir vos dossiers de candidature."),
    ("retour-leadership-femmes", "Retour sur l'atelier leadership des jeunes femmes", "actualite", None, "", "intervenant", "Trois jours d'échanges, de prise de parole et de réseau."),
    ("rencontre-jeunes-segou", "Le chapitre à la rencontre des jeunes de Ségou", "actualite", None, "", "panel", "Ce que les jeunes nous ont dit pendant la Caravane."),
    ("formations-numerique", "Nouvelles opportunités de formation au numérique", "actualite", None, "", "ceremonie", "Le point sur les formations ouvertes ce trimestre."),
]

CORPS_LANCEMENT = """La plateforme YouthConnekt Mali a été présentée officiellement à Bamako, devant des jeunes venus de plusieurs régions, des représentants d'organisations et des partenaires du chapitre.

Pensée comme une porte d'entrée unique, elle permet de découvrir les opportunités du moment, de suivre la vie du chapitre et de rejoindre la communauté, à titre individuel ou en tant qu'organisation.

> Aujourd'hui, on n'a plus d'excuse : les opportunités sont là, il suffit de se connecter.
— Une participante

## Ce que la plateforme apporte

- Des opportunités mises à jour chaque semaine
- Un espace membre pour suivre ses communautés
- Une communauté de jeunes et d'organisations dans tout le pays

[photos]

Au programme de la journée : une présentation de la plateforme, des témoignages de jeunes accompagnés par le chapitre et des ateliers pratiques pour créer son profil et repérer les bonnes opportunités.

Article d'exemple, photos provisoires prises hors du Mali."""


class Command(BaseCommand):
    help = "Ajoute (ou retire avec --supprimer) les contenus d'exemple des maquettes."

    def add_arguments(self, parser):
        parser.add_argument("--supprimer", action="store_true", help="Retirer tous les contenus d'exemple.")

    def handle(self, *args, supprimer=False, **options):
        modeles = [Initiative, Opportunite, Actualite]
        if supprimer:
            for modele in modeles:
                n, _ = modele.objects.filter(slug__startswith="exemple-").delete()
                self.stdout.write(f"{modele._meta.verbose_name_plural} : {n} élément(s) supprimé(s)")
            Communaute.objects.filter(slug="agriculture-et-environnement", description__startswith="Jeunes agriculteurs, transformateurs, étudiants").update(description="", activites="")
            self.stdout.write(self.style.SUCCESS("Contenus d'exemple retirés."))
            return

        maintenant = timezone.now()
        aujourd_hui = timezone.localdate()
        axes = {a.slug: a for a in AxeAction.objects.all()}

        for i, d in enumerate(INITIATIVES):
            d = dict(d)
            galerie, une, slug, image = d.pop("galerie", []), d.pop("une", False), d.pop("slug"), d.pop("photo")
            axe = axes.get(d.pop("axe"))
            objet, cree = Initiative.objects.update_or_create(
                slug=f"exemple-{slug}",
                defaults=dict(d, axe=axe, statut="publie", mis_en_avant=une, publie_le=maintenant - timedelta(days=i)),
            )
            if cree:
                objet.image = photo(image)
                objet.save()
                for ordre, nom in enumerate(galerie):
                    PhotoInitiative.objects.create(initiative=objet, image=photo(nom), ordre=ordre)

        communautes = {c.slug: c for c in Communaute.objects.all()}
        for ordre, (slug, titre, type_, jours, organisme, lieu, image, coms) in enumerate(OPPORTUNITES):
            defauts = dict(
                titre=titre, type=type_, organisme=organisme, lieu=lieu, statut="publie",
                resume=f"{titre} relayée par le chapitre.", description="Description complète à fournir par l'organisateur.",
                date_limite=aujourd_hui + timedelta(days=jours), mis_en_avant=ordre in (3, 6, 7),
                publie_le=maintenant - timedelta(days=ordre + 1),
            )
            if slug == "formation-en-marketing-digital":
                defauts.update(DETAIL_MARKETING)
            objet, cree = Opportunite.objects.update_or_create(slug=f"exemple-{slug}", defaults=defauts)
            if cree:
                objet.image = photo(image)
                objet.save()
            objet.communautes.set([communautes[c] for c in coms if c in communautes])

        for ordre, (slug, titre, categorie, decalage, lieu, image, chapeau) in enumerate(ACTUALITES):
            defauts = dict(
                titre=titre, categorie=categorie, chapeau=chapeau, lieu=lieu, statut="publie",
                contenu=CORPS_LANCEMENT if ordre == 0 else f"{chapeau}\n\nContenu d'exemple, à remplacer.",
                date_evenement=(aujourd_hui + timedelta(days=decalage)) if decalage is not None else None,
                mis_en_avant=ordre == 0, publie_le=maintenant - timedelta(days=ordre * 3),
            )
            objet, cree = Actualite.objects.update_or_create(slug=f"exemple-{slug}", defaults=defauts)
            if cree:
                objet.image = photo(image)
                objet.save()
                if ordre == 0:
                    for o, nom in enumerate(["intervenant", "salle-bleue"]):
                        PhotoActualite.objects.create(actualite=objet, image=photo(nom), ordre=o)
            if slug == "cinq-jeunes-agriculture" and "agriculture-et-environnement" in communautes:
                objet.communautes.add(communautes["agriculture-et-environnement"])

        Communaute.objects.filter(slug="agriculture-et-environnement", description="").update(
            description="Jeunes agriculteurs, transformateurs, étudiants et acteurs du climat qui veulent faire de l'agriculture un métier d'avenir.",
            activites="Ateliers pratiques sur la transformation et la conservation\nVisites de fermes et d'unités de transformation\nPartage d'opportunités de financement agricole",
        )
        self.stdout.write(self.style.SUCCESS(
            "Contenus d'exemple chargés. Retirez-les avec : python manage.py charger_exemples --supprimer"
        ))
