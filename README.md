# Site web de YouthConnekt Mali

Site officiel du chapitre YouthConnekt Mali : portail institutionnel, initiatives, opportunités, actualités, communautés thématiques, espace membre et administration.

PHP 8.1 ou plus récent, sans framework · MySQL ou MariaDB · Tailwind CSS (déjà compilé).

## Installer sur WAMP

1. Copier le dossier du projet dans `C:\wamp64\www\youthconnekt-mali`.
2. Dans WAMP, vérifier que le module Apache **rewrite_module** est coché (icône WAMP > Apache > Modules Apache).
3. Dans phpMyAdmin, créer une base nommée `youthconnekt` (interclassement `utf8mb4_unicode_ci`).
4. Copier `config.exemple.php` sous le nom `config.php`, à côté. Les valeurs par défaut conviennent à WAMP (utilisateur `root`, mot de passe vide).
5. Ouvrir http://localhost/youthconnekt-mali : la page d'installation crée les tables et le compte de l'administrateur principal. Cocher « Charger les contenus d'exemple » pour voir le site rempli.

Sur WAMP, les e-mails ne partent pas (mode `journal`) : le lien de confirmation d'inscription ou de mot de passe oublié s'affiche directement à l'écran, et tous les e-mails sont écrits dans `stockage/emails.log`.

## Mettre en ligne (cPanel)

1. **Gestionnaire de fichiers** : envoyer tout le dossier dans `public_html` (ou un sous-dossier), sauf `node_modules`.
2. **Bases de données MySQL** : créer une base et un utilisateur, lui donner tous les droits sur la base.
3. Créer `config.php` à partir de `config.exemple.php` : adresse du site en `https://`, `'developpement' => false`, informations de la base, et `'mode' => 'smtp'` avec un compte e-mail créé dans cPanel (Comptes e-mail > Se connecter aux appareils donne l'hôte et le port).
4. Ouvrir l'adresse du site : la page d'installation s'affiche une seule fois. Autre possibilité : importer `base-de-donnees/schema.sql` puis `base-de-donnees/donnees-depart.sql` dans phpMyAdmin, puis ouvrir le site.
5. Vérifier que les dossiers `uploads` et `stockage` sont accessibles en écriture (permissions 755).
6. Activer HTTPS (cPanel > SSL/TLS Status) puis, dans l'administration > Paramètres, retirer les contenus d'exemple avant l'ouverture publique.

Changer d'hébergeur : copier les fichiers, exporter la base depuis phpMyAdmin et l'importer chez le nouvel hébergeur, puis adapter `config.php`.

## Organisation du code

| Dossier | Contenu |
|---|---|
| `index.php` | Point d'entrée unique (toutes les adresses passent par lui, voir `.htaccess`) |
| `app/routes.php` | Liste des adresses du site et fonction qui y répond |
| `app/controleurs/` | Une fonction par page : `site`, `contenus`, `communautes`, `comptes`, `espace`, `admin` |
| `app/ressources.php` | Rubriques de l'administration : colonnes, onglets, filtres, champs des formulaires |
| `app/noyau.php` | Base de données, sécurité, dates en français, mise en forme des textes, images |
| `app/listes.php` | Listes de choix (régions, profils, types d'opportunités, rôles…) |
| `vues/` | Pages HTML : `pages/` (site public), `comptes/`, `espace/`, `admin/`, `partials/` (morceaux réutilisés) |
| `assets/` | Styles, scripts et images de la maquette |
| `uploads/` | Images envoyées depuis le site (non versionnées) |
| `base-de-donnees/` | Structure SQL et données de départ (axes, 9 communautés) |

## Administration

Adresse : `/gestion`. Rôles, choisis dans la fiche d'un compte (Comptes et connexions) :

- **Administrateur principal** : tout, y compris les comptes de l'équipe, les paramètres et les contenus d'exemple ;
- **Éditeur de contenus** : opportunités, actualités, initiatives, témoignages, partenaires, chiffres clés, mot du ministre ;
- **Modération des communautés** : communautés et demandes d'intégration ;
- **Gestion des membres** : comptes, profils, messages de contact, newsletter, paramètres.

La règle d'intégration aux communautés (validation par un administrateur ou acceptation automatique) se règle dans Paramètres. La mention « Photo provisoire » se retire au même endroit, une fois les vraies photos en place.

## Remplacer les photos de la maquette

Les photos de `assets/img/` sont provisoires (prises hors du Mali). Pour en remplacer une, déposer la nouvelle avec exactement le même nom de fichier (voir `assets/img/LISEZMOI.md`). Les photos des contenus (initiatives, opportunités, actualités, ministre, partenaires) se gèrent depuis l'administration.

## Modifier les styles

Le fichier `assets/css/site.css` est généré par Tailwind à partir de `assets/src/site.css`. Après une modification des classes dans les vues :

```
npm install      (une seule fois, nécessite Node.js)
npm run css
```

`assets/css/admin.css` s'écrit directement, sans compilation.

## Sécurité

Mots de passe chiffrés (`password_hash`), protection des formulaires (jeton CSRF), requêtes préparées, blocage après 5 connexions échouées, liens par e-mail à usage unique, images vérifiées et réenregistrées, aucun script exécutable dans `uploads/`, dossiers internes inaccessibles depuis le navigateur.
