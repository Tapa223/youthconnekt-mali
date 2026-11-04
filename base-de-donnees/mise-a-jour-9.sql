-- =====================================================================
-- YouthConnekt Mali : mise à jour 9 (4 novembre 2026)
-- À importer une fois dans phpMyAdmin, sur la base « youthconnekt ».
--  - notifications de l'espace membre : on retient quand le membre les a consultées.
-- =====================================================================

USE youthconnekt;
SET NAMES utf8mb4;

ALTER TABLE utilisateurs ADD COLUMN notifications_vues_le DATETIME NULL AFTER derniere_connexion;
