-- YouthConnekt Mali, mise à jour 25 : l'inscription ne demande plus de confirmer l'adresse e-mail.
-- Les comptes qui attendaient encore cette confirmation deviennent utilisables tout de suite.
SET NAMES utf8mb4;
UPDATE utilisateurs SET email_confirme = 1 WHERE email_confirme = 0;
DELETE FROM jetons WHERE type = 'confirmation';
