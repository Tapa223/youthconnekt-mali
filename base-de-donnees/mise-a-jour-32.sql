-- YouthConnekt Mali, mise à jour 32 : téléphone et e-mail de la personne de contact des organisations (privés).
-- À importer dans phpMyAdmin (sur le site en ligne ET sur WAMP). Sans risque si on l'importe deux fois.
SET NAMES utf8mb4;

SET @existe = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'profils_organisations' AND COLUMN_NAME = 'telephone_contact');
SET @sql = IF(@existe = 0,
  "ALTER TABLE profils_organisations ADD COLUMN telephone_contact VARCHAR(30) NOT NULL DEFAULT '' AFTER personne_contact",
  'SELECT 1');
PREPARE etape FROM @sql; EXECUTE etape; DEALLOCATE PREPARE etape;

SET @existe = (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'profils_organisations' AND COLUMN_NAME = 'email_personne_contact');
SET @sql = IF(@existe = 0,
  "ALTER TABLE profils_organisations ADD COLUMN email_personne_contact VARCHAR(190) NOT NULL DEFAULT '' AFTER telephone_contact",
  'SELECT 1');
PREPARE etape FROM @sql; EXECUTE etape; DEALLOCATE PREPARE etape;
