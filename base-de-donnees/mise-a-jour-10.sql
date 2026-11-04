-- =====================================================================
-- YouthConnekt Mali : mise à jour 10 (4 novembre 2026)
-- À importer une fois dans phpMyAdmin, sur la base « youthconnekt ».
--  - les membres peuvent envoyer leur témoignage depuis leur espace ;
--    l'administration le relit puis l'affiche sur le site.
-- =====================================================================

USE youthconnekt;
SET NAMES utf8mb4;

ALTER TABLE temoignages
  ADD COLUMN utilisateur_id INT UNSIGNED NULL AFTER id,
  ADD COLUMN envoye_le DATETIME NULL AFTER publie,
  ADD CONSTRAINT fk_temoignages_utilisateur FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs (id) ON DELETE SET NULL;
