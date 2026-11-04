-- =====================================================================
-- YouthConnekt Mali : mise à jour 11 (4 novembre 2026)
-- À importer une fois dans phpMyAdmin, sur la base « youthconnekt ».
--  - demande d'intégration à une communauté : nouvelle question sur l'initiative
--    ou le projet que porte déjà le membre.
-- =====================================================================

USE youthconnekt;
SET NAMES utf8mb4;

ALTER TABLE demandes_adhesion ADD COLUMN projet TEXT NULL AFTER apport;
