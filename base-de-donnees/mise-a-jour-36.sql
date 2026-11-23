-- YouthConnekt Mali, mise à jour 36 : corbeille des messages de contact (30 jours avant suppression définitive).
-- À importer APRÈS les mises à jour 34 et 35. Sans risque si on l'importe deux fois.
SET NAMES utf8mb4;

SET @existe = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'messages_contact' AND COLUMN_NAME = 'supprime_le');
SET @sql = IF(@existe = 0, 'ALTER TABLE messages_contact ADD COLUMN supprime_le DATETIME NULL', 'SELECT 1');
PREPARE etape FROM @sql; EXECUTE etape; DEALLOCATE PREPARE etape;
