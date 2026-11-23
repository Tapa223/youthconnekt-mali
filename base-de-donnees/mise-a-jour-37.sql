-- YouthConnekt Mali, mise à jour 37 : inscription courte et rappels de profil.
-- La région n'est plus « Bamako » par défaut (elle est demandée après l'inscription),
-- et chaque compte garde le nombre de rappels de profil reçus par e-mail. Sans risque si on l'importe deux fois.
SET NAMES utf8mb4;

ALTER TABLE profils_individuels ALTER COLUMN region SET DEFAULT '';
ALTER TABLE profils_organisations ALTER COLUMN region SET DEFAULT '';
ALTER TABLE profils_individuels ALTER COLUMN categorie SET DEFAULT '';
ALTER TABLE profils_organisations ALTER COLUMN type_structure SET DEFAULT '';

SET @existe = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'utilisateurs' AND COLUMN_NAME = 'rappel_profil_nb');
SET @sql = IF(@existe = 0, 'ALTER TABLE utilisateurs ADD COLUMN rappel_profil_nb TINYINT UNSIGNED NOT NULL DEFAULT 0, ADD COLUMN rappel_profil_le DATETIME NULL', 'SELECT 1');
PREPARE etape FROM @sql; EXECUTE etape; DEALLOCATE PREPARE etape;

-- Les comptes déjà inscrits ne reçoivent pas de rappel : seuls les nouveaux, inscrits avec le formulaire court, sont concernés
UPDATE utilisateurs SET rappel_profil_nb = 2 WHERE @existe = 0;
