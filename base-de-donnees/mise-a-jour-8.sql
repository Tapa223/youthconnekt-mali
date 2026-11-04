-- =====================================================================
-- YouthConnekt Mali : mise à jour 8 (4 novembre 2026)
-- À importer une fois dans phpMyAdmin, sur la base « youthconnekt ».
--  - un membre peut ajouter plusieurs liens à son profil (Facebook, LinkedIn,
--    Instagram, site web…) ; les liens LinkedIn et site web déjà saisis sont repris.
-- =====================================================================

USE youthconnekt;
SET NAMES utf8mb4;

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

-- Reprise des liens déjà saisis
INSERT INTO liens_profil (utilisateur_id, reseau, url, ordre)
  SELECT utilisateur_id, 'linkedin', lien_linkedin, 1 FROM profils_individuels WHERE lien_linkedin <> ''
  UNION ALL SELECT utilisateur_id, 'site', site_web, 2 FROM profils_individuels WHERE site_web <> ''
  UNION ALL SELECT utilisateur_id, 'linkedin', lien_linkedin, 1 FROM profils_organisations WHERE lien_linkedin <> ''
  UNION ALL SELECT utilisateur_id, 'site', site_web, 2 FROM profils_organisations WHERE site_web <> '';
