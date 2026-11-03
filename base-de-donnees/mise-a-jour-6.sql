-- =====================================================================
-- YouthConnekt Mali : mise à jour 6 (3 novembre 2026)
-- À importer une fois dans phpMyAdmin, sur la base « youthconnekt ».
--  - les catégories d'actualités sont gérées dans l'administration ;
--  - une actualité peut n'avoir aucune catégorie.
-- =====================================================================

USE youthconnekt;
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS categories_actualite (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nom VARCHAR(80) NOT NULL,
  slug VARCHAR(60) NOT NULL,
  couleur VARCHAR(20) NOT NULL DEFAULT 'vert',
  ordre SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  UNIQUE KEY uk_categories_actualite_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les deux catégories existantes deviennent modifiables
INSERT IGNORE INTO categories_actualite (nom, slug, couleur, ordre) VALUES
  ('Événement', 'evenement', 'orange', 1),
  ('Actualité', 'actualite', 'turquoise', 2);

ALTER TABLE actualites
  MODIFY categorie VARCHAR(60) NOT NULL DEFAULT '';
