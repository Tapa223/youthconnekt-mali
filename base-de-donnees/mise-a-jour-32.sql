-- YouthConnekt Mali, mise à jour 32 : téléphone et e-mail de l'organisation (privés, réservés à l'équipe).
-- À importer dans phpMyAdmin (en ligne ET sur WAMP). Sans risque si on l'importe deux fois,
-- y compris si la première version de ce fichier a déjà été importée.
SET NAMES utf8mb4;

SET @nouvelle = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'profils_organisations' AND COLUMN_NAME = 'telephone_organisation');
SET @ancienne = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'profils_organisations' AND COLUMN_NAME = 'telephone_contact');
SET @sql = IF(@nouvelle > 0, 'SELECT 1', IF(@ancienne > 0,
  "ALTER TABLE profils_organisations CHANGE COLUMN telephone_contact telephone_organisation VARCHAR(30) NOT NULL DEFAULT ''",
  "ALTER TABLE profils_organisations ADD COLUMN telephone_organisation VARCHAR(30) NOT NULL DEFAULT '' AFTER personne_contact"));
PREPARE etape FROM @sql; EXECUTE etape; DEALLOCATE PREPARE etape;

SET @nouvelle = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'profils_organisations' AND COLUMN_NAME = 'email_organisation');
SET @ancienne = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'profils_organisations' AND COLUMN_NAME = 'email_personne_contact');
SET @sql = IF(@nouvelle > 0, 'SELECT 1', IF(@ancienne > 0,
  "ALTER TABLE profils_organisations CHANGE COLUMN email_personne_contact email_organisation VARCHAR(190) NOT NULL DEFAULT ''",
  "ALTER TABLE profils_organisations ADD COLUMN email_organisation VARCHAR(190) NOT NULL DEFAULT '' AFTER telephone_organisation"));
PREPARE etape FROM @sql; EXECUTE etape; DEALLOCATE PREPARE etape;
