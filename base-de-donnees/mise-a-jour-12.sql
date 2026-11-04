-- =====================================================================
-- YouthConnekt Mali : mise à jour 12 (5 novembre 2026)
-- À importer une fois dans phpMyAdmin, sur la base « youthconnekt ».
--  - témoignages : profil de la personne (tri) et choix des trois de l'accueil.
-- =====================================================================

USE youthconnekt;
SET NAMES utf8mb4;

ALTER TABLE temoignages
  ADD COLUMN categorie VARCHAR(20) NOT NULL DEFAULT '' AFTER role,
  ADD COLUMN a_la_une TINYINT(1) NOT NULL DEFAULT 0 AFTER publie;

-- Les témoignages déjà affichés restent sur l'accueil
UPDATE temoignages SET a_la_une = 1 WHERE publie = 1;
