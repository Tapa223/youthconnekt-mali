-- =====================================================================
-- YouthConnekt Mali : mise à jour 16 (9 novembre 2026)
-- À importer une fois dans phpMyAdmin, sur la base « youthconnekt ».
-- Le site ne garde que des contenus réels :
--  - initiatives, actualités et témoignages d'exemple retirés ;
--  - opportunités d'exemple retirées, sauf les formations ;
--  - doublons retirés : le Sommet 2024 et le Forum Sahel (Dakar, N'Djamena)
--    sont racontés une seule fois, dans les initiatives du chapitre.
-- =====================================================================

USE youthconnekt;
SET NAMES utf8mb4;

DELETE FROM initiatives WHERE slug LIKE 'exemple-%';
DELETE FROM actualites WHERE slug LIKE 'exemple-%';
DELETE FROM opportunites WHERE slug LIKE 'exemple-%' AND type <> 'formation';

DELETE FROM temoignages WHERE utilisateur_id IS NULL AND nom IN ('Aminata K.', 'Moussa T.', 'Organisation partenaire');

DELETE FROM actualites WHERE slug IN ('mali-sommet-youthconnekt-africa-2024', 'mali-forum-youthconnekt-sahel-2025');

-- Forum Sahel 2026 : l'historique des éditions précédentes est déjà raconté dans l'initiative « Le Mali au Forum YouthConnekt Sahel »
UPDATE actualites SET contenu = 'Branche de YouthConnekt Africa, le Forum YouthConnekt Sahel donne la parole aux jeunes du Sahel pour réfléchir ensemble aux opportunités et aux solutions locales pour la paix et la prospérité.

La 3e édition s''est tenue à Niamey, au Niger, du 10 au 12 août 2026, sous le thème « Jeunesse sahélienne et Refondation : bâtir un Sahel souverain, résilient et prospère ».

## Une édition charnière
Cette édition devait poser les bases de la gouvernance du Forum : constitution d''un comité de pilotage réunissant les ministres en charge de la jeunesse des pays du Sahel, et validation de la Charte du Forum.'
WHERE slug = 'forum-youthconnekt-sahel-2026-niamey';
