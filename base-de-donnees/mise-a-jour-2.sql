-- =====================================================================
-- YouthConnekt Mali : mise à jour 2 (2 novembre 2026)
-- À importer une fois dans phpMyAdmin, sur la base « youthconnekt ».
-- Retire des contenus d'exemple les mentions provisoires entre crochets
-- et les phrases « à remplacer ». Sans effet sur les contenus saisis.
-- =====================================================================

USE youthconnekt;

UPDATE opportunites SET organisme = '' WHERE organisme IN ('[Organisation]', '[Entreprise]', '[Organisme]', '[Organisateur]');
UPDATE actualites SET lieu = '' WHERE lieu = '[Lieu à confirmer]';
UPDATE actualites SET contenu = TRIM(REPLACE(contenu, 'Contenu d''exemple, à remplacer.', '')) WHERE contenu LIKE '%Contenu d''exemple, à remplacer.%';
