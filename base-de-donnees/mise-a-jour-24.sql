-- Mise à jour 24 : premier écran de l'accueil, un titre et un texte pour chaque photo
-- Photo 1 : le titre principal et la présentation de l'accueil ; photos 2 et 3 : les champs ci-dessous
SET NAMES utf8mb4;
ALTER TABLE parametres
  ADD COLUMN titre_accueil_2 VARCHAR(200) NULL AFTER texte_accueil_1,
  ADD COLUMN titre_accueil_3 VARCHAR(200) NULL AFTER titre_accueil_2,
  MODIFY texte_accueil_2 VARCHAR(400) NULL,
  MODIFY texte_accueil_3 VARCHAR(400) NULL;
UPDATE parametres SET
  texte_accueil_1 = NULL,
  titre_accueil_2 = 'La jeunesse malienne se forme',
  texte_accueil_2 = 'Formations, ateliers et mentorat : le chapitre relaie les opportunités qui aident les jeunes à développer leurs compétences et à réussir leurs projets.',
  titre_accueil_3 = 'La jeunesse malienne s''engage',
  texte_accueil_3 = 'Initiatives citoyennes, bénévolat et actions dans les régions : les jeunes du chapitre agissent pour la paix, la citoyenneté et le développement du Mali.';
