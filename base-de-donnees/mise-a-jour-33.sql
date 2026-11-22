-- YouthConnekt Mali, mise à jour 33 : tranche d'âge (au lieu de la date de naissance) et profil « Autre » précisé.
-- À importer dans phpMyAdmin (en ligne ET sur WAMP). Sans risque si on l'importe deux fois.
SET NAMES utf8mb4;

SET @existe = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'profils_individuels' AND COLUMN_NAME = 'tranche_age');
SET @sql = IF(@existe = 0, "ALTER TABLE profils_individuels ADD COLUMN tranche_age VARCHAR(20) NOT NULL DEFAULT '' AFTER date_naissance", 'SELECT 1');
PREPARE etape FROM @sql; EXECUTE etape; DEALLOCATE PREPARE etape;

SET @existe = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'profils_individuels' AND COLUMN_NAME = 'categorie_autre');
SET @sql = IF(@existe = 0, "ALTER TABLE profils_individuels ADD COLUMN categorie_autre VARCHAR(80) NOT NULL DEFAULT '' AFTER categorie", 'SELECT 1');
PREPARE etape FROM @sql; EXECUTE etape; DEALLOCATE PREPARE etape;

-- Les membres déjà inscrits gardent leur âge : la tranche est calculée depuis leur date de naissance.
UPDATE profils_individuels SET tranche_age = CASE
    WHEN TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) < 18 THEN 'moins18'
    WHEN TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) <= 24 THEN '18_24'
    WHEN TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) <= 29 THEN '25_29'
    WHEN TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) <= 35 THEN '30_35'
    ELSE 'plus35' END
WHERE tranche_age = '' AND date_naissance IS NOT NULL;

-- Chiffres du réseau sur la page À propos : masqués, à réactiver dans Paramètres quand ils seront parlants.
SET @existe = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'parametres' AND COLUMN_NAME = 'chiffres_reseau');
SET @sql = IF(@existe = 0, "ALTER TABLE parametres ADD COLUMN chiffres_reseau TINYINT(1) NOT NULL DEFAULT 0 AFTER acceptation_automatique", 'SELECT 1');
PREPARE etape FROM @sql; EXECUTE etape; DEALLOCATE PREPARE etape;
