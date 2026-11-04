-- =====================================================================
-- YouthConnekt Mali : structure de la base de données (MySQL 5.7+ / MariaDB 10.3+)
-- À importer une seule fois, par la page /installation ou par phpMyAdmin.
-- =====================================================================

SET NAMES utf8mb4;

-- ---------------------------------------------------------------------
-- Comptes
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS utilisateurs (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  email VARCHAR(190) NOT NULL,
  mot_de_passe VARCHAR(255) NOT NULL,
  -- « administration » : compte de l'équipe, distinct des comptes membres
  type_compte ENUM('individuel','organisation','administration') NOT NULL DEFAULT 'individuel',
  email_confirme TINYINT(1) NOT NULL DEFAULT 0,
  actif TINYINT(1) NOT NULL DEFAULT 1,
  -- Accès à l'administration : NULL = membre, sinon super / editeur / moderation / membres
  role_admin VARCHAR(20) NULL DEFAULT NULL,
  prenom_admin VARCHAR(80) NOT NULL DEFAULT '',
  nom_admin VARCHAR(80) NOT NULL DEFAULT '',
  derniere_connexion DATETIME NULL,
  notifications_vues_le DATETIME NULL,
  inscrit_le DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uk_utilisateurs_email (email),
  KEY ix_utilisateurs_role (role_admin)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS profils_individuels (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  utilisateur_id INT UNSIGNED NOT NULL,
  prenom VARCHAR(80) NOT NULL,
  nom VARCHAR(80) NOT NULL,
  photo VARCHAR(255) NOT NULL DEFAULT '',
  categorie VARCHAR(30) NOT NULL,
  domaine VARCHAR(150) NOT NULL DEFAULT '',
  telephone VARCHAR(30) NOT NULL DEFAULT '',
  region VARCHAR(30) NOT NULL DEFAULT 'bamako',
  ville VARCHAR(100) NOT NULL DEFAULT '',
  presentation TEXT NULL,
  site_web VARCHAR(255) NOT NULL DEFAULT '',
  lien_linkedin VARCHAR(255) NOT NULL DEFAULT '',
  visible TINYINT(1) NOT NULL DEFAULT 1,
  cree_le DATETIME NOT NULL,
  modifie_le DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uk_profil_ind_utilisateur (utilisateur_id),
  CONSTRAINT fk_profil_ind_utilisateur FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS profils_organisations (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  utilisateur_id INT UNSIGNED NOT NULL,
  nom_structure VARCHAR(150) NOT NULL,
  logo VARCHAR(255) NOT NULL DEFAULT '',
  type_structure VARCHAR(20) NOT NULL,
  secteur VARCHAR(150) NOT NULL DEFAULT '',
  personne_contact VARCHAR(120) NOT NULL DEFAULT '',
  email_contact_public VARCHAR(190) NOT NULL DEFAULT '',
  region VARCHAR(30) NOT NULL DEFAULT 'bamako',
  ville VARCHAR(100) NOT NULL DEFAULT '',
  presentation TEXT NULL,
  site_web VARCHAR(255) NOT NULL DEFAULT '',
  lien_linkedin VARCHAR(255) NOT NULL DEFAULT '',
  visible TINYINT(1) NOT NULL DEFAULT 1,
  cree_le DATETIME NOT NULL,
  modifie_le DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uk_profil_org_utilisateur (utilisateur_id),
  CONSTRAINT fk_profil_org_utilisateur FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Liens publics d'un membre (réseaux sociaux, site…), affichés sur son profil
CREATE TABLE IF NOT EXISTS liens_profil (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  utilisateur_id INT UNSIGNED NOT NULL,
  reseau VARCHAR(20) NOT NULL,
  url VARCHAR(255) NOT NULL,
  ordre SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  KEY ix_liens_profil_utilisateur (utilisateur_id),
  CONSTRAINT fk_liens_profil_utilisateur FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Liens à usage unique : confirmation d'e-mail et nouveau mot de passe
CREATE TABLE IF NOT EXISTS jetons (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  utilisateur_id INT UNSIGNED NOT NULL,
  type ENUM('confirmation','mot_de_passe') NOT NULL,
  empreinte CHAR(64) NOT NULL,
  expire_le DATETIME NOT NULL,
  cree_le DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uk_jetons_empreinte (empreinte),
  KEY ix_jetons_utilisateur (utilisateur_id, type),
  CONSTRAINT fk_jetons_utilisateur FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tentatives de connexion échouées (protection contre les essais en série)
CREATE TABLE IF NOT EXISTS tentatives_connexion (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  cle CHAR(64) NOT NULL,
  moment DATETIME NOT NULL,
  PRIMARY KEY (id),
  KEY ix_tentatives_cle (cle, moment)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Communautés thématiques
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS types_communaute (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nom VARCHAR(80) NOT NULL,
  slug VARCHAR(60) NOT NULL,
  couleur VARCHAR(20) NOT NULL DEFAULT 'vert',
  ordre SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  UNIQUE KEY uk_types_communaute_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS communautes (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nom VARCHAR(120) NOT NULL,
  slug VARCHAR(140) NOT NULL,
  type VARCHAR(60) NOT NULL DEFAULT '',
  public_vise VARCHAR(255) NOT NULL,
  description TEXT NULL,
  activites TEXT NULL,
  image VARCHAR(255) NOT NULL DEFAULT '',
  ordre SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  active TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  UNIQUE KEY uk_communautes_nom (nom),
  UNIQUE KEY uk_communautes_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS demandes_adhesion (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  utilisateur_id INT UNSIGNED NOT NULL,
  communaute_id INT UNSIGNED NOT NULL,
  statut ENUM('en_attente','acceptee','refusee') NOT NULL DEFAULT 'en_attente',
  motivation TEXT NULL,
  apport TEXT NULL,
  projet TEXT NULL,
  demandee_le DATETIME NOT NULL,
  traitee_le DATETIME NULL,
  traitee_par INT UNSIGNED NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uk_une_demande_par_communaute (utilisateur_id, communaute_id),
  KEY ix_demandes_statut (statut),
  CONSTRAINT fk_demandes_utilisateur FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs (id) ON DELETE CASCADE,
  CONSTRAINT fk_demandes_communaute FOREIGN KEY (communaute_id) REFERENCES communautes (id) ON DELETE CASCADE,
  CONSTRAINT fk_demandes_traitee_par FOREIGN KEY (traitee_par) REFERENCES utilisateurs (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Contenus
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS axes (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nom VARCHAR(120) NOT NULL,
  slug VARCHAR(140) NOT NULL,
  description TEXT NULL,
  elements TEXT NULL,
  image VARCHAR(255) NOT NULL DEFAULT '',
  couleur VARCHAR(20) NOT NULL DEFAULT 'orange',
  ordre SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  UNIQUE KEY uk_axes_nom (nom),
  UNIQUE KEY uk_axes_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS initiatives (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  titre VARCHAR(200) NOT NULL,
  slug VARCHAR(200) NOT NULL,
  statut ENUM('brouillon','publie') NOT NULL DEFAULT 'brouillon',
  publie_le DATETIME NOT NULL,
  mis_en_avant TINYINT(1) NOT NULL DEFAULT 0,
  axe_id INT UNSIGNED NULL,
  resume VARCHAR(300) NOT NULL DEFAULT '',
  contenu TEXT NULL,
  image VARCHAR(255) NOT NULL DEFAULT '',
  etat ENUM('a_venir','en_cours','terminee') NOT NULL DEFAULT 'a_venir',
  date_libelle VARCHAR(80) NOT NULL DEFAULT '',
  lieu VARCHAR(120) NOT NULL DEFAULT '',
  participants VARCHAR(80) NOT NULL DEFAULT '',
  format VARCHAR(80) NOT NULL DEFAULT '',
  objectifs TEXT NULL,
  programme TEXT NULL,
  chiffres TEXT NULL,
  citation TEXT NULL,
  citation_auteur VARCHAR(120) NOT NULL DEFAULT '',
  cree_le DATETIME NOT NULL,
  modifie_le DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uk_initiatives_slug (slug),
  KEY ix_initiatives_publication (statut, publie_le),
  CONSTRAINT fk_initiatives_axe FOREIGN KEY (axe_id) REFERENCES axes (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS photos_initiatives (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  initiative_id INT UNSIGNED NOT NULL,
  image VARCHAR(255) NOT NULL,
  legende VARCHAR(200) NOT NULL DEFAULT '',
  ordre SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  CONSTRAINT fk_photos_initiatives FOREIGN KEY (initiative_id) REFERENCES initiatives (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS types_opportunite (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nom VARCHAR(80) NOT NULL,
  slug VARCHAR(60) NOT NULL,
  couleur VARCHAR(20) NOT NULL DEFAULT 'vert',
  ordre SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  UNIQUE KEY uk_types_opportunite_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS opportunites (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  titre VARCHAR(200) NOT NULL,
  slug VARCHAR(200) NOT NULL,
  statut ENUM('brouillon','propose','publie') NOT NULL DEFAULT 'brouillon',
  publie_le DATETIME NOT NULL,
  mis_en_avant TINYINT(1) NOT NULL DEFAULT 0,
  type VARCHAR(60) NOT NULL,
  propose_par INT UNSIGNED NULL,
  organisme VARCHAR(150) NOT NULL DEFAULT '',
  resume VARCHAR(300) NOT NULL DEFAULT '',
  description TEXT NULL,
  criteres TEXT NULL,
  avantages TEXT NULL,
  etapes TEXT NULL,
  lieu VARCHAR(120) NOT NULL DEFAULT '',
  duree VARCHAR(120) NOT NULL DEFAULT '',
  public VARCHAR(120) NOT NULL DEFAULT '',
  cout VARCHAR(80) NOT NULL DEFAULT '',
  date_limite DATE NULL,
  lien_candidature VARCHAR(255) NOT NULL DEFAULT '',
  image VARCHAR(255) NOT NULL DEFAULT '',
  cree_le DATETIME NOT NULL,
  modifie_le DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uk_opportunites_slug (slug),
  KEY ix_opportunites_publication (statut, publie_le),
  KEY ix_opportunites_limite (date_limite),
  CONSTRAINT fk_opportunites_propose_par FOREIGN KEY (propose_par) REFERENCES utilisateurs (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS opportunite_communaute (
  opportunite_id INT UNSIGNED NOT NULL,
  communaute_id INT UNSIGNED NOT NULL,
  PRIMARY KEY (opportunite_id, communaute_id),
  KEY ix_oc_communaute (communaute_id),
  CONSTRAINT fk_oc_opportunite FOREIGN KEY (opportunite_id) REFERENCES opportunites (id) ON DELETE CASCADE,
  CONSTRAINT fk_oc_communaute FOREIGN KEY (communaute_id) REFERENCES communautes (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS categories_actualite (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nom VARCHAR(80) NOT NULL,
  slug VARCHAR(60) NOT NULL,
  couleur VARCHAR(20) NOT NULL DEFAULT 'vert',
  ordre SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  UNIQUE KEY uk_categories_actualite_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS actualites (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  titre VARCHAR(200) NOT NULL,
  slug VARCHAR(200) NOT NULL,
  statut ENUM('brouillon','publie') NOT NULL DEFAULT 'brouillon',
  publie_le DATETIME NOT NULL,
  mis_en_avant TINYINT(1) NOT NULL DEFAULT 0,
  categorie VARCHAR(60) NOT NULL DEFAULT '',
  chapeau VARCHAR(400) NOT NULL DEFAULT '',
  contenu MEDIUMTEXT NULL,
  image VARCHAR(255) NOT NULL DEFAULT '',
  date_evenement DATE NULL,
  lieu VARCHAR(120) NOT NULL DEFAULT '',
  cree_le DATETIME NOT NULL,
  modifie_le DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uk_actualites_slug (slug),
  KEY ix_actualites_publication (statut, publie_le),
  KEY ix_actualites_evenement (categorie, date_evenement)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS photos_actualites (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  actualite_id INT UNSIGNED NOT NULL,
  image VARCHAR(255) NOT NULL,
  legende VARCHAR(200) NOT NULL DEFAULT '',
  ordre SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  CONSTRAINT fk_photos_actualites FOREIGN KEY (actualite_id) REFERENCES actualites (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS actualite_communaute (
  actualite_id INT UNSIGNED NOT NULL,
  communaute_id INT UNSIGNED NOT NULL,
  PRIMARY KEY (actualite_id, communaute_id),
  KEY ix_ac_communaute (communaute_id),
  CONSTRAINT fk_ac_actualite FOREIGN KEY (actualite_id) REFERENCES actualites (id) ON DELETE CASCADE,
  CONSTRAINT fk_ac_communaute FOREIGN KEY (communaute_id) REFERENCES communautes (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Éléments de l'accueil
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS mot_ministre (
  id TINYINT UNSIGNED NOT NULL,
  nom VARCHAR(150) NOT NULL DEFAULT '',
  titre_officiel VARCHAR(255) NOT NULL DEFAULT '',
  photo VARCHAR(255) NOT NULL DEFAULT '',
  extrait TEXT NULL,
  message TEXT NULL,
  publie TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS partenaires (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nom VARCHAR(150) NOT NULL,
  logo VARCHAR(255) NOT NULL DEFAULT '',
  lien VARCHAR(255) NOT NULL DEFAULT '',
  ordre SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  actif TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS temoignages (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  utilisateur_id INT UNSIGNED NULL,
  nom VARCHAR(120) NOT NULL,
  role VARCHAR(150) NOT NULL DEFAULT '',
  categorie VARCHAR(20) NOT NULL DEFAULT '',
  photo VARCHAR(255) NOT NULL DEFAULT '',
  texte TEXT NOT NULL,
  ordre SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  publie TINYINT(1) NOT NULL DEFAULT 0,
  a_la_une TINYINT(1) NOT NULL DEFAULT 0,
  envoye_le DATETIME NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_temoignages_utilisateur FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS chiffres_cles (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  valeur VARCHAR(30) NOT NULL,
  libelle VARCHAR(120) NOT NULL,
  ordre SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  publie TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Site, échanges
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS parametres (
  id TINYINT UNSIGNED NOT NULL,
  accroche_accueil VARCHAR(200) NOT NULL DEFAULT '',
  presentation_accueil TEXT NULL,
  adresse VARCHAR(255) NOT NULL DEFAULT '',
  horaires VARCHAR(200) NOT NULL DEFAULT '',
  telephone VARCHAR(40) NOT NULL DEFAULT '',
  email_contact VARCHAR(190) NOT NULL DEFAULT '',
  lien_facebook VARCHAR(255) NOT NULL DEFAULT '',
  lien_instagram VARCHAR(255) NOT NULL DEFAULT '',
  lien_linkedin VARCHAR(255) NOT NULL DEFAULT '',
  lien_x VARCHAR(255) NOT NULL DEFAULT '',
  lien_youtube VARCHAR(255) NOT NULL DEFAULT '',
  lien_tiktok VARCHAR(255) NOT NULL DEFAULT '',
  photos_provisoires TINYINT(1) NOT NULL DEFAULT 1,
  acceptation_automatique TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS messages_contact (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nom VARCHAR(120) NOT NULL,
  profil VARCHAR(20) NOT NULL DEFAULT 'jeune',
  email VARCHAR(190) NOT NULL,
  objet VARCHAR(200) NOT NULL,
  message TEXT NOT NULL,
  recu_le DATETIME NOT NULL,
  traite TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  KEY ix_messages_traite (traite, recu_le)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS abonnes_newsletter (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  email VARCHAR(190) NOT NULL,
  inscrit_le DATETIME NOT NULL,
  actif TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  UNIQUE KEY uk_abonnes_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
