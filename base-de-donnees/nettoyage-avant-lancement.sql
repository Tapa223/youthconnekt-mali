-- YouthConnekt Mali : retirer le contenu d'exemple et les comptes de test avant la mise en ligne.
-- À importer UNE fois dans phpMyAdmin (base youthconnekt), quand vous êtes prête : la suppression est définitive.
--
-- Supprime :
--   - toutes les actualités et événements (avec leurs photos et leurs liens aux communautés) ;
--   - toutes les initiatives (avec leurs rôles, leurs questions, leurs photos et les participations) ;
--   - toutes les opportunités (avec leurs liens aux communautés) ;
--   - tous les comptes membres et organisations (les comptes d'administration sont gardés),
--     avec leurs profils, liens, demandes d'adhésion, participations, jetons,
--     leurs abonnements à la newsletter et les messages de contact envoyés avec leur adresse.
-- Garde : paramètres, axes, chiffres clés, partenaires, communautés, mot du ministre, comptes d'administration.
-- Les fichiers des images restent dans le dossier uploads : vous pouvez le vider à la main si vous le souhaitez.

SET NAMES utf8mb4;
START TRANSACTION;

DELETE FROM actualites;
DELETE FROM initiatives;
DELETE FROM opportunites;

DELETE FROM abonnes_newsletter WHERE email IN (SELECT email FROM utilisateurs WHERE type_compte <> 'administration');
DELETE FROM messages_contact WHERE email IN (SELECT email FROM utilisateurs WHERE type_compte <> 'administration');
DELETE FROM utilisateurs WHERE type_compte <> 'administration';
DELETE FROM tentatives_connexion;

COMMIT;
