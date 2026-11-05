-- =====================================================================
-- YouthConnekt Mali : mise à jour 13 (5 novembre 2026)
-- À importer une fois dans phpMyAdmin, sur la base « youthconnekt ».
--  - partenaires : deux familles affichées séparément sur le site
--    (institutionnels et techniques, secteur privé).
-- =====================================================================

USE youthconnekt;
SET NAMES utf8mb4;

ALTER TABLE partenaires
  ADD COLUMN categorie VARCHAR(20) NOT NULL DEFAULT 'institutionnel' AFTER nom;
