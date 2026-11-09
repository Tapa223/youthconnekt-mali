-- =====================================================================
-- YouthConnekt Mali : mise à jour 18 (9 novembre 2026)
-- À importer une fois dans phpMyAdmin, sur la base « youthconnekt ».
--  Participations aux initiatives : l'administrateur ouvre une initiative aux
--  participations, crée les rôles (bénévole, intervenant…) et les champs du
--  formulaire ; les membres se proposent, l'équipe retient ou non.
-- =====================================================================

USE youthconnekt;
SET NAMES utf8mb4;

ALTER TABLE initiatives
  ADD COLUMN participations_ouvertes TINYINT(1) NOT NULL DEFAULT 0 AFTER citation_auteur,
  ADD COLUMN participations_fin DATE NULL AFTER participations_ouvertes,
  ADD COLUMN participations_intro TEXT NULL AFTER participations_fin;

CREATE TABLE IF NOT EXISTS initiative_roles (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  initiative_id INT UNSIGNED NOT NULL,
  nom VARCHAR(120) NOT NULL,
  description VARCHAR(300) NOT NULL DEFAULT '',
  places SMALLINT UNSIGNED NULL,
  ordre SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  KEY idx_roles_initiative (initiative_id),
  CONSTRAINT fk_roles_initiative FOREIGN KEY (initiative_id) REFERENCES initiatives (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS initiative_champs (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  initiative_id INT UNSIGNED NOT NULL,
  libelle VARCHAR(200) NOT NULL,
  type VARCHAR(20) NOT NULL DEFAULT 'texte',
  options TEXT NULL,
  aide VARCHAR(300) NOT NULL DEFAULT '',
  requis TINYINT(1) NOT NULL DEFAULT 0,
  ordre SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  KEY idx_champs_initiative (initiative_id),
  CONSTRAINT fk_champs_initiative FOREIGN KEY (initiative_id) REFERENCES initiatives (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS participations (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  initiative_id INT UNSIGNED NOT NULL,
  role_id INT UNSIGNED NULL,
  role_nom VARCHAR(120) NOT NULL DEFAULT '',
  utilisateur_id INT UNSIGNED NOT NULL,
  reponses MEDIUMTEXT NULL,
  statut VARCHAR(20) NOT NULL DEFAULT 'en_attente',
  cree_le DATETIME NOT NULL,
  traitee_le DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uk_participation (initiative_id, utilisateur_id),
  KEY idx_participations_statut (statut),
  CONSTRAINT fk_participations_initiative FOREIGN KEY (initiative_id) REFERENCES initiatives (id) ON DELETE CASCADE,
  CONSTRAINT fk_participations_role FOREIGN KEY (role_id) REFERENCES initiative_roles (id) ON DELETE SET NULL,
  CONSTRAINT fk_participations_utilisateur FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
