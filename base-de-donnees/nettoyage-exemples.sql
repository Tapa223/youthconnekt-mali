-- YouthConnekt Mali : retirer le contenu d'exemple avant la mise en ligne.
-- À importer UNE fois, seulement quand vous êtes prête : la suppression est définitive.
-- Retire les 3 initiatives et les 2 opportunités dont l'adresse commence par « exemple- »,
-- avec leurs rôles, questions, photos et participations de test.
SET NAMES utf8mb4;
DELETE FROM initiatives WHERE slug LIKE 'exemple-%';
DELETE FROM opportunites WHERE slug LIKE 'exemple-%';
