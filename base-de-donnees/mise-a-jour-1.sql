-- =====================================================================
-- YouthConnekt Mali : mise à jour 1 (2 novembre 2026)
-- À importer une fois dans phpMyAdmin, sur la base « youthconnekt » déjà installée.
--  - les comptes d'administration deviennent distincts des comptes membres ;
--  - la demande d'intégration à une communauté contient une motivation.
-- =====================================================================

USE youthconnekt;

ALTER TABLE utilisateurs
  MODIFY type_compte ENUM('individuel','organisation','administration') NOT NULL DEFAULT 'individuel';

UPDATE utilisateurs SET type_compte = 'administration' WHERE role_admin IS NOT NULL;

-- Un compte d'administration ne fait partie d'aucune communauté
DELETE d FROM demandes_adhesion d JOIN utilisateurs u ON u.id = d.utilisateur_id WHERE u.type_compte = 'administration';

ALTER TABLE demandes_adhesion
  ADD COLUMN motivation TEXT NULL AFTER statut,
  ADD COLUMN apport TEXT NULL AFTER motivation;
