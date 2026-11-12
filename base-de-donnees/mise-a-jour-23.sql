-- Mise à jour 23 : premier écran de l'accueil
-- un texte court pour chaque photo (il change avec elle) et les 4 photos fixes des petites bulles
SET NAMES utf8mb4;
ALTER TABLE parametres
  ADD COLUMN texte_accueil_1 VARCHAR(200) NULL AFTER image_accueil_3,
  ADD COLUMN texte_accueil_2 VARCHAR(200) NULL AFTER texte_accueil_1,
  ADD COLUMN texte_accueil_3 VARCHAR(200) NULL AFTER texte_accueil_2,
  ADD COLUMN bulle_accueil_1 VARCHAR(255) NULL AFTER texte_accueil_3,
  ADD COLUMN bulle_accueil_2 VARCHAR(255) NULL AFTER bulle_accueil_1,
  ADD COLUMN bulle_accueil_3 VARCHAR(255) NULL AFTER bulle_accueil_2,
  ADD COLUMN bulle_accueil_4 VARCHAR(255) NULL AFTER bulle_accueil_3;
UPDATE parametres SET
  texte_accueil_1 = 'Se former : des formations et des ateliers pour développer vos compétences',
  texte_accueil_2 = 'S''engager : des initiatives citoyennes dans les régions du Mali',
  texte_accueil_3 = 'Se connecter : un réseau de jeunes, d''organisations et de partenaires';
