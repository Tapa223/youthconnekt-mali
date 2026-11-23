-- YouthConnekt Mali, mise à jour 35 : e-mails reçus à l'adresse de contact rangés dans l'administration,
-- bouton « Ajouter à mon agenda » au choix de l'admin, adresse de contact corrigée (.ml).
-- À importer dans phpMyAdmin (en ligne ET sur WAMP). Sans risque si on l'importe deux fois.
SET NAMES utf8mb4;

SET @existe = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'messages_contact' AND COLUMN_NAME = 'source');
SET @sql = IF(@existe = 0, "ALTER TABLE messages_contact ADD COLUMN source VARCHAR(10) NOT NULL DEFAULT 'site', ADD COLUMN message_id_email VARCHAR(255) NOT NULL DEFAULT '' AFTER source, ADD INDEX ix_messages_id_email (message_id_email(100))", 'SELECT 1');
PREPARE etape FROM @sql; EXECUTE etape; DEALLOCATE PREPARE etape;

SET @existe = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'actualites' AND COLUMN_NAME = 'bouton_agenda');
SET @sql = IF(@existe = 0, 'ALTER TABLE actualites ADD COLUMN bouton_agenda TINYINT(1) NOT NULL DEFAULT 0 AFTER date_fin', 'SELECT 1');
PREPARE etape FROM @sql; EXECUTE etape; DEALLOCATE PREPARE etape;

SET @existe = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'initiatives' AND COLUMN_NAME = 'bouton_agenda');
SET @sql = IF(@existe = 0, 'ALTER TABLE initiatives ADD COLUMN bouton_agenda TINYINT(1) NOT NULL DEFAULT 0 AFTER date_fin', 'SELECT 1');
PREPARE etape FROM @sql; EXECUTE etape; DEALLOCATE PREPARE etape;

-- L'ancienne adresse provisoire (.org) remplacée par la vraie
UPDATE parametres SET email_contact = 'contact@youthconnekt.ml' WHERE email_contact LIKE '%youthconnektmali.org';
