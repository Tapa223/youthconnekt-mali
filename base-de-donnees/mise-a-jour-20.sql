-- =====================================================================
-- YouthConnekt Mali : mise à jour 20 (9 novembre 2026)
-- À importer une fois dans phpMyAdmin, sur la base « youthconnekt ».
--  - chaque rôle est soit une inscription (validée tout de suite), soit une
--    candidature (étudiée par l'équipe) ;
--  - les questions sont propres à chaque rôle ;
--  - billet virtuel (code unique) et pointage à l'entrée.
-- =====================================================================

USE youthconnekt;
SET NAMES utf8mb4;

ALTER TABLE initiative_roles ADD COLUMN mode VARCHAR(20) NOT NULL DEFAULT 'candidature' AFTER nom;

ALTER TABLE initiative_champs
  ADD COLUMN role_id INT UNSIGNED NULL AFTER initiative_id,
  ADD CONSTRAINT fk_champs_role FOREIGN KEY (role_id) REFERENCES initiative_roles (id) ON DELETE CASCADE;

-- Les questions communes à toute une initiative deviennent les questions de chacun de ses rôles
INSERT INTO initiative_champs (initiative_id, role_id, libelle, type, options, aide, requis, ordre)
SELECT c.initiative_id, r.id, c.libelle, c.type, c.options, c.aide, c.requis, c.ordre
FROM initiative_champs c JOIN initiative_roles r ON r.initiative_id = c.initiative_id
WHERE c.role_id IS NULL;
DELETE FROM initiative_champs WHERE role_id IS NULL;

ALTER TABLE participations
  ADD COLUMN code VARCHAR(12) NULL AFTER statut,
  ADD COLUMN present_le DATETIME NULL AFTER traitee_le,
  ADD UNIQUE KEY uk_participations_code (code);

-- Exemples : la Journée YouthConnekt Mali montre l'inscription simple,
-- les intervenants de la Caravane proposent leur thématique.
UPDATE initiative_roles r JOIN initiatives i ON i.id = r.initiative_id
SET r.mode = 'inscription' WHERE i.slug = 'exemple-journee-youthconnekt-mali' AND r.nom = 'Participant';
DELETE c FROM initiative_champs c JOIN initiative_roles r ON r.id = c.role_id JOIN initiatives i ON i.id = r.initiative_id
WHERE i.slug = 'exemple-journee-youthconnekt-mali' AND r.nom = 'Participant';
DELETE c FROM initiative_champs c JOIN initiative_roles r ON r.id = c.role_id JOIN initiatives i ON i.id = r.initiative_id
WHERE i.slug = 'exemple-journee-youthconnekt-mali' AND r.nom = 'Exposant';
DELETE c FROM initiative_champs c JOIN initiative_roles r ON r.id = c.role_id JOIN initiatives i ON i.id = r.initiative_id
WHERE i.slug = 'exemple-caravane-de-la-citoyennete' AND r.nom = 'Intervenant';
DELETE c FROM initiative_champs c JOIN initiative_roles r ON r.id = c.role_id JOIN initiatives i ON i.id = r.initiative_id
WHERE i.slug = 'exemple-programme-de-mentorat' AND r.nom = 'Mentor' AND c.libelle IN ('Où en est le projet ?', 'Qu''attendez-vous du programme ?');

INSERT INTO initiative_champs (initiative_id, role_id, libelle, type, options, aide, requis, ordre)
SELECT r.initiative_id, r.id, q.libelle, q.type, q.options, q.aide, q.requis, q.ordre
FROM (
  SELECT 'exemple-journee-youthconnekt-mali' AS slug, 'Exposant' AS role, 'Nom du projet' AS libelle, 'texte' AS type, '' AS options, '' AS aide, 1 AS requis, 0 AS ordre
  UNION ALL SELECT 'exemple-journee-youthconnekt-mali', 'Exposant', 'Présentez votre projet en quelques lignes', 'zone', '', 'Ce qu''il fait, pour qui, où il en est.', 1, 1
  UNION ALL SELECT 'exemple-journee-youthconnekt-mali', 'Exposant', 'Domaine', 'choix', 'Agriculture\nNumérique\nArtisanat et mode\nSanté\nÉducation\nAutre', '', 1, 2
  UNION ALL SELECT 'exemple-caravane-de-la-citoyennete', 'Intervenant', 'Thématique proposée', 'texte', '', 'Exemple : le rôle des jeunes dans la vie locale', 1, 0
  UNION ALL SELECT 'exemple-caravane-de-la-citoyennete', 'Intervenant', 'Résumé de votre intervention', 'zone', '', '', 1, 1
  UNION ALL SELECT 'exemple-caravane-de-la-citoyennete', 'Intervenant', 'Format', 'choix', 'Atelier\nPanel\nPrésentation', '', 1, 2
  UNION ALL SELECT 'exemple-caravane-de-la-citoyennete', 'Intervenant', 'Durée souhaitée (en minutes)', 'nombre', '', '', 0, 3
  UNION ALL SELECT 'exemple-caravane-de-la-citoyennete', 'Intervenant', 'À quelles étapes pouvez-vous intervenir ?', 'cases', 'Ségou\nMopti\nSikasso', '', 1, 4
  UNION ALL SELECT 'exemple-programme-de-mentorat', 'Mentor', 'Votre parcours en quelques lignes', 'zone', '', '', 1, 2
  UNION ALL SELECT 'exemple-programme-de-mentorat', 'Mentor', 'Combien de temps pouvez-vous y consacrer par mois ?', 'choix', '2 heures\n4 heures\nPlus de 4 heures', '', 1, 3
) AS q
JOIN initiatives i ON i.slug = q.slug
JOIN initiative_roles r ON r.initiative_id = i.id AND r.nom = q.role
WHERE NOT EXISTS (SELECT 1 FROM initiative_champs x WHERE x.role_id = r.id AND x.libelle = q.libelle);

UPDATE initiative_champs SET options = 'Idée\nLancement\nEn activité depuis plus d''un an' WHERE libelle = 'Où en est le projet ?';
