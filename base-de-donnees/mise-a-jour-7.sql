-- =====================================================================
-- YouthConnekt Mali : mise à jour 7 (3 novembre 2026)
-- À importer une fois dans phpMyAdmin, sur la base « youthconnekt ».
--  - les communautés ont un type (géré dans l'administration),
--    qui sert au tri sur la page Communauté.
-- =====================================================================

USE youthconnekt;
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS types_communaute (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nom VARCHAR(80) NOT NULL,
  slug VARCHAR(60) NOT NULL,
  couleur VARCHAR(20) NOT NULL DEFAULT 'vert',
  ordre SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  UNIQUE KEY uk_types_communaute_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE communautes ADD COLUMN type VARCHAR(60) NOT NULL DEFAULT '' AFTER slug;

-- Types de départ (modifiables dans Administration > Types de communautés)
INSERT IGNORE INTO types_communaute (nom, slug, couleur, ordre) VALUES
  ('Secteurs d''activité', 'secteurs', 'turquoise', 1),
  ('Entreprendre et innover', 'entreprendre', 'orange', 2),
  ('Engagement citoyen', 'engagement', 'vert', 3),
  ('Études et carrière', 'etudes-carriere', 'bleu', 4);

-- Classement des communautés de départ (modifiable sur la fiche de chaque communauté)
UPDATE communautes SET type = 'secteurs' WHERE slug IN ('tech-et-numerique', 'agriculture-et-environnement', 'arts-culture-et-medias', 'sante-et-bien-etre') AND type = '';
UPDATE communautes SET type = 'entreprendre' WHERE slug IN ('entrepreneuriat-et-startups', 'innovation-et-porteurs-de-projets') AND type = '';
UPDATE communautes SET type = 'engagement' WHERE slug IN ('leadership-et-citoyennete') AND type = '';
UPDATE communautes SET type = 'etudes-carriere' WHERE slug IN ('etudiants', 'jeunes-professionnels') AND type = '';
