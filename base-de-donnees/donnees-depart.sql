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
  ('Gouvernance inclusive et participation des jeunes', 'gouvernance-participation',
   'Leadership, participation citoyenne et place des jeunes dans la gouvernance et la cohésion sociale.',
   'Dialogues entre les jeunes et les décideurs\nLeadership et participation citoyenne\nVoix des jeunes dans les politiques jeunesse et la cohésion sociale',
   'turquoise', 1),
  ('Croissance inclusive et environnement', 'croissance-environnement',
   'Compétences, emploi et entrepreneuriat des jeunes, et solutions locales face aux défis de l''environnement.',
   'Renforcement des compétences et employabilité\nEntrepreneuriat et accès au financement\nInnovation et solutions locales pour l''environnement',
   'orange', 2);

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


-- Chiffres clés du chapitre malien (les trois premiers publiés s'affichent sur l'accueil)
INSERT INTO chiffres_cles (valeur, libelle, ordre, publie) VALUES
  ('2021', 'lancement du chapitre YouthConnekt Mali', 1, 1),
  ('2', 'axes d''intervention du chapitre', 2, 1),
  ('18', 'délégués maliens au Sommet YouthConnekt Africa 2024', 3, 1),
  ('6', 'jeunes délégués maliens au Sommet de Kigali en 2024', 4, 0),
  ('40', 'contacts noués dans 19 pays africains par nos jeunes délégués', 5, 0),
  ('2', 'éditions du Forum YouthConnekt Sahel avec le Mali (Dakar 2021, N''Djamena 2025)', 6, 0);

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
    'Branche de YouthConnekt Africa, le Forum YouthConnekt Sahel donne la parole aux jeunes du Sahel pour réfléchir ensemble aux opportunités et aux solutions locales pour la paix et la prospérité.

La 3e édition s''est tenue à Niamey, au Niger, du 10 au 12 août 2026, sous le thème « Jeunesse sahélienne et Refondation : bâtir un Sahel souverain, résilient et prospère ».

## Une édition charnière
Cette édition devait poser les bases de la gouvernance du Forum : constitution d''un comité de pilotage réunissant les ministres en charge de la jeunesse des pays du Sahel, et validation de la Charte du Forum.',
    'Niamey, Niger', 'publie', '2026-08-10', 0, NOW() - INTERVAL 1 DAY, '', NOW(), NOW()
) AS a
WHERE NOT EXISTS (SELECT 1 FROM actualites x WHERE x.slug = a.slug);

-- 3. Partenaires institutionnels du chapitre
INSERT INTO partenaires (nom, categorie, logo, lien, ordre, actif)
SELECT * FROM (
  SELECT 'Ministère de la Jeunesse et des Sports, chargé de l''Instruction civique et de la Construction citoyenne' AS nom, 'institutionnel' AS categorie, '' AS logo, '' AS lien, 1 AS ordre, 1 AS actif
  UNION ALL SELECT 'PNUD Mali', 'institutionnel', '', '', 2, 1
  UNION ALL SELECT 'UNFPA Mali', 'institutionnel', '', '', 3, 1
  UNION ALL SELECT 'Bureau du Coordonnateur résident des Nations unies au Mali', 'institutionnel', '', '', 4, 1
  UNION ALL SELECT 'YouthConnekt Africa Hub', 'institutionnel', '', '', 5, 1
) AS p
WHERE NOT EXISTS (SELECT 1 FROM partenaires x WHERE x.nom = p.nom);


-- Histoire du chapitre
INSERT INTO actualites (titre, slug, categorie, chapeau, contenu, lieu, statut, date_evenement, mis_en_avant, publie_le, image, cree_le, modifie_le)
SELECT * FROM (
  SELECT 'Lancement du chapitre YouthConnekt Mali' AS titre, 'lancement-chapitre-youthconnekt-mali' AS slug, 'actualite' AS categorie,
    'Le 7 octobre 2021, le Mali a lancé son chapitre national YouthConnekt, avec l''appui du PNUD.' AS chapeau,
    'Le Gouvernement du Mali, à travers le ministère de la Jeunesse et des Sports, chargé de l''Instruction civique et de la Construction citoyenne, a officiellement endossé l''initiative YouthConnekt en octobre 2020. Le chapitre national a été lancé le 7 octobre 2021, avec l''appui du PNUD.

## Trois objectifs
- Connecter les jeunes et les faire participer à la transformation socio-économique du pays.
- Élargir l''accès des jeunes aux opportunités de leadership économique, social et civique, à travers des initiatives nationales.
- Soutenir les initiatives d''autonomisation des jeunes qui existent déjà, en favorisant la synergie entre tous les acteurs.

## Deux axes d''intervention
Le chapitre agit selon deux axes : la gouvernance inclusive et la participation des jeunes, d''une part ; la croissance inclusive et l''environnement, d''autre part. Ils sont alignés sur la politique nationale de la jeunesse, sur le cadre de coopération entre le Gouvernement du Mali et les Nations unies et sur la Stratégie des Nations unies pour la jeunesse.' AS contenu,
    'Bamako' AS lieu, 'publie' AS statut, '2021-10-07' AS date_evenement, 0 AS mis_en_avant, '2021-10-07 10:00:00' AS publie_le, '' AS image, NOW() AS cree_le, NOW() AS modifie_le
  UNION ALL
  SELECT 'Un comité technique pour YouthConnekt Mali', 'comite-technique-youthconnekt-mali', 'actualite',
    'Créé par décision du ministre de la Jeunesse, le Comité technique réunit les ministères sectoriels et les agences des Nations unies.',
    'La décision n° 0011/2022 du ministre de la Jeunesse a créé le Comité technique de YouthConnekt au Mali. Ce cadre de partenariat associe les ministères sectoriels et les agences, fonds et programmes des Nations unies.

Le Comité est co-présidé par le ministère chargé de la Jeunesse et par le PNUD. Il oriente les actions du chapitre et veille à la synergie entre les programmes en faveur des jeunes.',
    'Bamako', 'publie', NULL, 0, '2022-03-01 10:00:00', '', NOW(), NOW()
) AS a
WHERE NOT EXISTS (SELECT 1 FROM actualites x WHERE x.slug = a.slug);


-- 5. Initiatives réelles du chapitre (axe 1 : gouvernance inclusive et participation des jeunes)
INSERT INTO initiatives (titre, slug, statut, publie_le, mis_en_avant, axe_id, resume, contenu, image, etat, date_libelle, lieu, participants, format, objectifs, programme, chiffres, citation, citation_auteur, cree_le, modifie_le)
SELECT i.titre, i.slug, 'publie', i.publie_le, i.mis_en_avant, (SELECT id FROM axes WHERE slug = 'gouvernance-participation'), i.resume, i.contenu, '', 'terminee', i.date_libelle, i.lieu, i.participants, i.format, i.objectifs, '', i.chiffres, '', '', NOW(), NOW()
FROM (
  SELECT 'Délégation malienne au Sommet YouthConnekt Africa 2024' AS titre, 'delegation-sommet-youthconnekt-africa-2024' AS slug, '2024-11-15 10:00:00' AS publie_le, 1 AS mis_en_avant,
    'Dix-huit personnes, dont six jeunes délégués, ont porté la voix du Mali à la 7e édition du Sommet, à Kigali.' AS resume,
    'Du 8 au 11 novembre 2024, la 7e édition du Sommet YouthConnekt Africa a réuni à Kigali plus de 4 000 participants venus de 33 pays, autour du thème « Des emplois pour les jeunes grâce à des compétences innovantes ».

Coordonnée par le PNUD, la délégation malienne réunissait 7 représentants du Gouvernement, 5 représentants du Système des Nations unies au Mali et 6 jeunes délégués : entrepreneurs, innovateurs et responsables d''organisations de jeunesse.

Les actions et les perspectives de YouthConnekt Mali ont été présentées au comité technique de YouthConnekt Africa. Les jeunes délégués ont tenu un stand pour présenter des initiatives maliennes, pris part aux panels et noué une quarantaine de contacts dans 19 pays africains. Une jeune déléguée a prononcé le mot de clôture du panel sur la santé des jeunes.' AS contenu,
    '8 au 11 novembre 2024' AS date_libelle, 'Kigali, Rwanda' AS lieu, '18 délégués, dont 6 jeunes' AS participants, 'Sommet continental' AS format,
    'Présenter les actions du chapitre YouthConnekt Mali\nPorter la voix des jeunes maliens dans les panels\nNouer des contacts et des partenariats en Afrique\nPartager les initiatives et les produits des jeunes du Mali' AS objectifs,
    '18 | membres de la délégation\n6 | jeunes délégués\n40 | contacts noués dans 19 pays' AS chiffres
  UNION ALL
  SELECT 'Le Mali au Forum YouthConnekt Sahel', 'mali-forum-youthconnekt-sahel', '2025-10-16 10:00:00', 0,
    'Dakar en 2021, N''Djamena en 2025 : les jeunes maliens prennent part au rendez-vous de la jeunesse sahélienne.',
    'Le Forum YouthConnekt Sahel est la plateforme où les jeunes du Sahel réfléchissent ensemble aux opportunités et aux solutions locales pour la paix et la prospérité.

Le Mali a activement pris part à la première édition, à Dakar du 30 novembre au 3 décembre 2021 (« Connecter les jeunes du Sahel pour la paix et la prospérité »), puis à la deuxième, à N''Djamena du 13 au 15 octobre 2025 (« Jeunesse sahélienne et Souveraineté »).',
    'Dakar 2021 · N''Djamena 2025', 'Dakar et N''Djamena', '', 'Forum régional',
    'Porter la voix des jeunes maliens à l''échelle du Sahel\nPartager les solutions locales des jeunes\nRenforcer les liens entre les jeunes de la région',
    '2 | éditions avec le Mali'
) AS i
WHERE NOT EXISTS (SELECT 1 FROM initiatives x WHERE x.slug = i.slug);
