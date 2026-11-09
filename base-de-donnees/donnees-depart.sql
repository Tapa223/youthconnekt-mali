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

-- Catégories d'actualités de départ (modifiables dans Administration > Catégories d'actualités)
INSERT IGNORE INTO categories_actualite (nom, slug, couleur, ordre) VALUES
  ('Événement', 'evenement', 'orange', 1),
  ('Actualité', 'actualite', 'turquoise', 2);

-- Types de communautés de départ (modifiables dans Administration > Types de communautés)
INSERT IGNORE INTO types_communaute (nom, slug, couleur, ordre) VALUES
  ('Secteurs d''activité', 'secteurs', 'turquoise', 1),
  ('Entreprendre et innover', 'entreprendre', 'orange', 2),
  ('Engagement citoyen', 'engagement', 'vert', 3),
  ('Études et carrière', 'etudes-carriere', 'bleu', 4);

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
UPDATE communautes SET type = 'secteurs' WHERE slug IN ('tech-et-numerique', 'agriculture-et-environnement', 'arts-culture-et-medias', 'sante-et-bien-etre') AND type = '';
UPDATE communautes SET type = 'entreprendre' WHERE slug IN ('entrepreneuriat-et-startups', 'innovation-et-porteurs-de-projets') AND type = '';
UPDATE communautes SET type = 'engagement' WHERE slug IN ('leadership-et-citoyennete') AND type = '';
UPDATE communautes SET type = 'etudes-carriere' WHERE slug IN ('etudiants', 'jeunes-professionnels') AND type = '';

-- Témoignages de la maquette (exemples, modifiables dans Administration > Témoignages)
INSERT INTO temoignages (nom, role, texte, ordre, publie)
SELECT * FROM (
  SELECT 'Aminata K.' AS nom, 'Étudiante en agronomie, Sikasso · Jeune' AS role, 'Le Bootcamp m''a aidée à transformer mon idée de transformation de mangues en vrai projet. J''y ai aussi trouvé des mentors.' AS texte, 1 AS ordre, 1 AS publie
  UNION ALL SELECT 'Moussa T.', 'Jeune entrepreneur, Ségou · Jeune', 'J''ai découvert ici un appel à candidatures que je n''aurais jamais vu ailleurs. Aujourd''hui, mon projet est accompagné.', 2, 1
  UNION ALL SELECT 'Organisation partenaire', 'Responsable de programmes · Partenaire', 'Travailler avec YouthConnekt Mali nous permet de toucher des jeunes motivés partout dans le pays.', 3, 1
) AS exemples
WHERE NOT EXISTS (SELECT 1 FROM temoignages);

-- Chiffres clés de l'accueil (sources : note technique YouthConnekt, juin 2026)
INSERT INTO chiffres_cles (valeur, libelle, ordre, publie) VALUES
  ('2021', 'lancement du chapitre Mali', 1, 1),
  ('37', 'pays dans le réseau YouthConnekt Africa', 2, 1),
  ('2', 'axes d''intervention du chapitre', 3, 1);

-- Actualités réelles du réseau (sources : note technique YouthConnekt, juin 2026 ; débriefing du Sommet 2024).
-- Ajoutées une seule fois ; modifiables ou supprimables ensuite dans Administration > Actualités et événements.
INSERT INTO actualites (titre, slug, categorie, chapeau, contenu, lieu, statut, date_evenement, mis_en_avant, publie_le, image, cree_le, modifie_le)
SELECT * FROM (
  SELECT 'Sommet YouthConnekt Africa 2026 à Kigali' AS titre, 'sommet-youthconnekt-africa-2026-kigali' AS slug, 'evenement' AS categorie,
    'La 8e édition du Sommet se tient du 25 au 27 novembre 2026, sous le thème « Façonner la mission de notre génération ».' AS chapeau,
    'Le Sommet YouthConnekt Africa est le plus grand rendez-vous annuel du continent consacré à la jeunesse. Il réunit chaque année en moyenne 5 000 jeunes leaders, entrepreneurs, professionnels, membres de la diaspora, décideurs et partenaires de développement.

Pour sa 8e édition, le Sommet se tient à Kigali, au Rwanda, du 25 au 27 novembre 2026, sous la co-présidence du Président de la République du Rwanda et de la Directrice régionale du PNUD pour l''Afrique. Le thème retenu : « Façonner la mission de notre génération ».

## Une plateforme pour les jeunes du continent
Renforcement des capacités, réseautage, innovation et plaidoyer : le Sommet accélère l''autonomisation des jeunes Africains et leur rôle dans la réalisation des Objectifs de développement durable et de l''Agenda 2063.' AS contenu,
    'Kigali, Rwanda' AS lieu, 'publie' AS statut, '2026-11-25' AS date_evenement, 1 AS mis_en_avant, NOW() AS publie_le, '' AS image, NOW() AS cree_le, NOW() AS modifie_le
  UNION ALL
  SELECT 'Forum YouthConnekt Sahel 2026 à Niamey', 'forum-youthconnekt-sahel-2026-niamey', 'evenement',
    'La 3e édition du Forum s''est tenue du 10 au 12 août 2026, sous le thème « Jeunesse sahélienne et Refondation : bâtir un Sahel souverain, résilient et prospère ».',
    'Branche de YouthConnekt Africa, le Forum YouthConnekt Sahel donne la parole aux jeunes du Sahel pour réfléchir ensemble aux opportunités et aux solutions locales pour la paix et la prospérité. Il veut changer l''image du Sahel, en tant que terre d''opportunités et d''investissements.

Après Dakar en 2021 (« Connecter les jeunes du Sahel pour la paix et la prospérité ») et N''Djamena en 2025 (« Jeunesse sahélienne et Souveraineté »), la 3e édition s''est tenue à Niamey, au Niger, du 10 au 12 août 2026.

## Une édition charnière
Cette édition a posé les bases de la gouvernance du Forum : constitution d''un comité de pilotage réunissant les ministres en charge de la jeunesse des pays du Sahel, et validation de la Charte du Forum. Le Mali a pris part à chacune des éditions.',
    'Niamey, Niger', 'publie', '2026-08-10', 0, NOW() - INTERVAL 1 DAY, '', NOW(), NOW()
  UNION ALL
  SELECT 'Le Mali au Sommet YouthConnekt Africa 2024', 'mali-sommet-youthconnekt-africa-2024', 'actualite',
    'Une délégation de 18 personnes, dont 6 jeunes délégués, a représenté le Mali à la 7e édition du Sommet, à Kigali.',
    'La 7e édition du Sommet YouthConnekt Africa s''est tenue à Kigali, au Rwanda, du 8 au 11 novembre 2024, sous le thème « Des emplois pour les jeunes grâce à des compétences innovantes ». Elle a rassemblé plus de 4 000 participants venus de 33 pays africains.

## Une délégation malienne de 18 personnes
Coordonnée par le PNUD, la délégation réunissait 7 représentants du Gouvernement, 5 représentants du Système des Nations unies au Mali et 6 jeunes délégués : entrepreneurs, innovateurs et responsables d''organisations de jeunesse.

## Ce que le Mali en retient
- Les actions et les perspectives de YouthConnekt Mali ont été présentées au comité technique de YouthConnekt Africa.
- Les jeunes délégués ont tenu un stand pour présenter des initiatives maliennes et noué une quarantaine de contacts dans 19 pays africains.
- Une jeune déléguée a prononcé le mot de clôture du panel sur la santé des jeunes, en plaidant pour un meilleur accès aux services de santé mentale et à l''éducation à la santé sexuelle et reproductive.
- Les échanges d''affaires, notamment autour du textile malien, ont ouvert la voie à des collaborations avec des entrepreneurs rwandais.',
    'Kigali, Rwanda', 'publie', '2024-11-08', 0, NOW() - INTERVAL 2 DAY, '', NOW(), NOW()
) AS a
WHERE NOT EXISTS (SELECT 1 FROM actualites x WHERE x.slug = a.slug);
