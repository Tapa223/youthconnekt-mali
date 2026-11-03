-- =====================================================================
-- YouthConnekt Mali : données de départ (cahier des charges v2 et maquettes validées)
-- Peut être réimporté sans risque : les lignes déjà présentes sont conservées.
-- =====================================================================

SET NAMES utf8mb4;

-- Fiches uniques
INSERT IGNORE INTO parametres (id) VALUES (1);
-- Coordonnées provisoires du chapitre, à confirmer et modifier dans Administration > Paramètres
UPDATE parametres SET
  email_contact = IF(email_contact = '', 'contact@youthconnektmali.org', email_contact),
  telephone = IF(telephone = '', '+223 00 00 00 00', telephone),
  adresse = IF(adresse = '', 'Bamako, Mali', adresse),
  horaires = IF(horaires = '', 'Du lundi au vendredi, de 8 h à 17 h', horaires)
WHERE id = 1;
INSERT IGNORE INTO mot_ministre (id, nom, titre_officiel) VALUES
  (1, 'M. Abdoul Kassim Ibrahim Fomba', 'Ministre de la Jeunesse et des Sports, chargé de l''Instruction civique et de la Construction citoyenne');

-- Axes d'action
INSERT IGNORE INTO axes (nom, slug, description, elements, couleur, ordre) VALUES
  ('Accompagner', 'accompagner',
   'Formations, mentorat et accompagnement des jeunes dans leurs projets d''entrepreneuriat et d''emploi.',
   'Formations pratiques et ateliers\nMentorat par des professionnels\nRelais d''appels à candidatures et de bourses',
   'orange', 1),
  ('Engager', 'engager',
   'Impliquer les jeunes dans la vie citoyenne et les rapprocher de celles et ceux qui décident.',
   'Dialogues entre jeunes et institutions\nActions citoyennes dans les régions\nParticipation aux politiques jeunesse',
   'turquoise', 2),
  ('Valoriser', 'valoriser',
   'Faire connaître les initiatives, les talents et les réalisations des jeunes du Mali.',
   'Mise en avant de projets portés par des jeunes\nPrix et distinctions\nVisibilité lors des événements du réseau',
   'bleu', 3);

-- Communautés thématiques de départ (cahier des charges v2, section 7.2)
-- Types d'opportunités de départ (modifiables dans Administration > Types d'opportunités)
INSERT IGNORE INTO types_opportunite (nom, slug, couleur, ordre) VALUES
  ('Formation', 'formation', 'turquoise', 1),
  ('Appel à candidatures', 'appel', 'orange', 2),
  ('Événement', 'evenement', 'vert', 3),
  ('Bourse', 'bourse', 'bleu', 4),
  ('Emploi et stage', 'emploi', 'rouge', 5);

INSERT IGNORE INTO communautes (nom, slug, public_vise, ordre) VALUES
  ('Tech et numérique', 'tech-et-numerique', 'Développeurs, créateurs de contenu, passionnés du numérique', 1),
  ('Entrepreneuriat et startups', 'entrepreneuriat-et-startups', 'Jeunes entrepreneurs, jeunes entreprises et startups', 2),
  ('Agriculture et environnement', 'agriculture-et-environnement', 'Jeunes agriculteurs, transformateurs et acteurs du climat', 3),
  ('Leadership et citoyenneté', 'leadership-et-citoyennete', 'Jeunes leaders d''associations et jeunes engagés dans la vie publique', 4),
  ('Arts, culture et médias', 'arts-culture-et-medias', 'Artistes, journalistes, créatifs et vidéastes', 5),
  ('Santé et bien-être', 'sante-et-bien-etre', 'Étudiants et jeunes professionnels de la santé', 6),
  ('Innovation et porteurs de projets', 'innovation-et-porteurs-de-projets', 'Innovateurs et porteurs de projets à la recherche d''accompagnement', 7),
  ('Étudiants', 'etudiants', 'Étudiants de toutes filières', 8),
  ('Jeunes professionnels', 'jeunes-professionnels', 'Jeunes en début de carrière', 9);

-- Témoignages de la maquette (exemples, modifiables dans Administration > Témoignages)
INSERT INTO temoignages (nom, role, texte, ordre, publie)
SELECT * FROM (
  SELECT 'Aminata K.' AS nom, 'Étudiante en agronomie, Sikasso · Jeune' AS role, 'Le Bootcamp m''a aidée à transformer mon idée de transformation de mangues en vrai projet. J''y ai aussi trouvé des mentors.' AS texte, 1 AS ordre, 1 AS publie
  UNION ALL SELECT 'Moussa T.', 'Jeune entrepreneur, Ségou · Jeune', 'J''ai découvert ici un appel à candidatures que je n''aurais jamais vu ailleurs. Aujourd''hui, mon projet est accompagné.', 2, 1
  UNION ALL SELECT 'Organisation partenaire', 'Responsable de programmes · Partenaire', 'Travailler avec YouthConnekt Mali nous permet de toucher des jeunes motivés partout dans le pays.', 3, 1
) AS exemples
WHERE NOT EXISTS (SELECT 1 FROM temoignages);
