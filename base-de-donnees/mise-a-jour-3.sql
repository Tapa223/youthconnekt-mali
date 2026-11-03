-- =====================================================================
-- YouthConnekt Mali : mise à jour 3 (3 novembre 2026)
-- À importer une fois dans phpMyAdmin, sur la base « youthconnekt ».
-- Ajoute des coordonnées provisoires (seulement là où rien n'est saisi).
-- =====================================================================

USE youthconnekt;

-- Coordonnées provisoires du chapitre, à confirmer et modifier dans Administration > Paramètres
UPDATE parametres SET
  email_contact = IF(email_contact = '', 'contact@youthconnektmali.org', email_contact),
  telephone = IF(telephone = '', '+223 00 00 00 00', telephone),
  adresse = IF(adresse = '', 'Bamako, Mali', adresse),
  horaires = IF(horaires = '', 'Du lundi au vendredi, de 8 h à 17 h', horaires)
WHERE id = 1;
