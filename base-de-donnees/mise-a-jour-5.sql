-- =====================================================================
-- YouthConnekt Mali : mise à jour 5 (3 novembre 2026)
-- À importer une fois dans phpMyAdmin, sur la base « youthconnekt ».
--  - les types d'opportunités sont gérés dans l'administration ;
--  - les membres peuvent proposer une opportunité, validée par l'administration.
-- =====================================================================

USE youthconnekt;

CREATE TABLE IF NOT EXISTS types_opportunite (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nom VARCHAR(80) NOT NULL,
  slug VARCHAR(60) NOT NULL,
  couleur VARCHAR(20) NOT NULL DEFAULT 'vert',
  ordre SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  UNIQUE KEY uk_types_opportunite_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Types d'opportunités de départ (modifiables dans Administration > Types d'opportunités)
INSERT IGNORE INTO types_opportunite (nom, slug, couleur, ordre) VALUES
  ('Formation', 'formation', 'turquoise', 1),
  ('Appel à candidatures', 'appel', 'orange', 2),
  ('Événement', 'evenement', 'vert', 3),
  ('Bourse', 'bourse', 'bleu', 4),
  ('Emploi et stage', 'emploi', 'rouge', 5);

ALTER TABLE opportunites
  MODIFY statut ENUM('brouillon','propose','publie') NOT NULL DEFAULT 'brouillon',
  MODIFY type VARCHAR(60) NOT NULL,
  ADD COLUMN propose_par INT UNSIGNED NULL AFTER type,
  ADD CONSTRAINT fk_opportunites_propose_par FOREIGN KEY (propose_par) REFERENCES utilisateurs (id) ON DELETE SET NULL;
