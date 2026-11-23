-- YouthConnekt Mali, mise à jour 38 : nombre de membres des communautés masquable (masqué au départ).
-- Sans risque si on l'importe deux fois.
SET NAMES utf8mb4;
SET @existe = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'parametres' AND COLUMN_NAME = 'membres_communautes');
SET @sql = IF(@existe = 0, 'ALTER TABLE parametres ADD COLUMN membres_communautes TINYINT(1) NOT NULL DEFAULT 0', 'SELECT 1');
PREPARE etape FROM @sql; EXECUTE etape; DEALLOCATE PREPARE etape;
