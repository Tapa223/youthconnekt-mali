-- YouthConnekt Mali, mise à jour 30 : une initiative terminée n'a plus de participations ouvertes.
-- À importer dans phpMyAdmin après mise-a-jour-29.sql (désormais, l'administration le fait toute seule à l'enregistrement).
SET NAMES utf8mb4;

UPDATE initiatives SET participations_ouvertes = 0 WHERE etat = 'terminee';
