-- =====================================================================
-- YouthConnekt Mali : mise à jour 17 (9 novembre 2026)
-- À importer une fois dans phpMyAdmin, sur la base « youthconnekt ».
--  - chaque édition du Forum YouthConnekt Sahel a sa propre actualité
--    (Dakar 2021, N'Djamena 2025, Niamey 2026) ;
--  - à la une : la participation du Mali au Forum de Niamey (la plus récente) ;
--  - le Sommet 2026 de Kigali est annoncé comme à venir, sans être à la une ;
--  - la participation au Sommet 2024 passe dans les actualités : les initiatives
--    sont réservées aux projets du chapitre auxquels on peut prendre part.
-- =====================================================================

USE youthconnekt;
SET NAMES utf8mb4;

-- L'ancienne page qui regroupait plusieurs éditions du Forum
DELETE FROM initiatives WHERE slug = 'mali-forum-youthconnekt-sahel';

-- Sommet 2026 : à venir
UPDATE actualites SET mis_en_avant = 0,
  chapeau = 'La 8e édition du Sommet se tiendra à Kigali du 25 au 27 novembre 2026, sous le thème « Façonner la mission de notre génération ».',
  contenu = 'Le Sommet YouthConnekt Africa est le plus grand rendez-vous annuel du continent consacré à la jeunesse. Il réunit chaque année en moyenne 5 000 jeunes leaders, entrepreneurs, professionnels, membres de la diaspora, décideurs et partenaires de développement.

La 8e édition se tiendra à Kigali, au Rwanda, du 25 au 27 novembre 2026, sous la co-présidence du Président de la République du Rwanda et de la Directrice régionale du PNUD pour l''Afrique. Le thème retenu : « Façonner la mission de notre génération ».

## Une plateforme pour les jeunes du continent
Renforcement des capacités, réseautage, innovation et plaidoyer : le Sommet accélère l''autonomisation des jeunes Africains et leur rôle dans la réalisation des Objectifs de développement durable et de l''Agenda 2063.'
WHERE slug = 'sommet-youthconnekt-africa-2026-kigali';

-- Niamey 2026 : la participation du Mali, à la une
UPDATE actualites SET mis_en_avant = 1,
  titre = 'Le Mali au Forum YouthConnekt Sahel 2026 à Niamey',
  slug = 'mali-forum-youthconnekt-sahel-2026-niamey',
  chapeau = 'Du 10 au 12 août 2026, le Mali a pris part à la 3e édition du Forum, à Niamey, sous le thème « Jeunesse sahélienne et Refondation : bâtir un Sahel souverain, résilient et prospère ».',
  contenu = 'Branche de YouthConnekt Africa, le Forum YouthConnekt Sahel donne la parole aux jeunes du Sahel pour réfléchir ensemble aux opportunités et aux solutions locales pour la paix et la prospérité.

La 3e édition s''est tenue à Niamey, au Niger, du 10 au 12 août 2026, sous le thème « Jeunesse sahélienne et Refondation : bâtir un Sahel souverain, résilient et prospère ». Le Mali y a pris part.

## Une édition charnière
Cette édition devait poser les bases de la gouvernance du Forum : constitution d''un comité de pilotage réunissant les ministres en charge de la jeunesse des pays du Sahel, et validation de la Charte du Forum.',
  publie_le = '2026-08-14 10:00:00'
WHERE slug = 'forum-youthconnekt-sahel-2026-niamey';

-- Dakar 2021, N'Djamena 2025 et Sommet 2024 : une actualité chacun
INSERT INTO actualites (titre, slug, categorie, chapeau, contenu, lieu, statut, date_evenement, mis_en_avant, publie_le, image, cree_le, modifie_le)
SELECT * FROM (
  SELECT 'Le Mali au premier Forum YouthConnekt Sahel à Dakar' AS titre, 'mali-forum-youthconnekt-sahel-2021-dakar' AS slug, 'evenement' AS categorie,
    'Du 30 novembre au 3 décembre 2021, le Mali a pris part à la première édition du Forum, sous le thème « Connecter les jeunes du Sahel pour la paix et la prospérité ».' AS chapeau,
    'La première édition du Forum YouthConnekt Sahel s''est tenue à Dakar, au Sénégal, du 30 novembre au 3 décembre 2021, sous le thème « Connecter les jeunes du Sahel pour la paix et la prospérité ».

Ce forum sous-régional, branche de YouthConnekt Africa, est organisé dans un pays du Sahel sous le leadership du Gouvernement hôte, avec l''appui du Hub YouthConnekt Africa, du Hub régional du PNUD pour l''Afrique de l''Ouest et du Centre et du Bureau des Nations unies pour l''Afrique de l''Ouest et le Sahel. Son objectif : inspirer, connecter, engager et outiller les jeunes de la région, et changer l''image du Sahel en terre d''opportunités et d''investissements.

Le Mali a activement pris part à cette première édition.' AS contenu,
    'Dakar, Sénégal' AS lieu, 'publie' AS statut, '2021-11-30' AS date_evenement, 0 AS mis_en_avant, '2021-12-06 10:00:00' AS publie_le, '' AS image, NOW() AS cree_le, NOW() AS modifie_le
  UNION ALL
  SELECT 'Le Mali au Forum YouthConnekt Sahel 2025 à N''Djamena', 'mali-forum-youthconnekt-sahel-2025-ndjamena', 'evenement',
    'Du 13 au 15 octobre 2025, le Mali a pris part à la 2e édition du Forum, sous le thème « Jeunesse sahélienne et Souveraineté ».',
    'La 2e édition du Forum YouthConnekt Sahel s''est tenue à N''Djamena, au Tchad, du 13 au 15 octobre 2025, sous le thème « Jeunesse sahélienne et Souveraineté : Construire l''avenir face aux défis sécuritaires, environnementaux et de justice sociale ».

Les jeunes du Sahel y ont réfléchi ensemble aux opportunités et aux solutions locales pour la paix et la prospérité. Le Mali a activement pris part à cette édition.',
    'N''Djamena, Tchad', 'publie', '2025-10-13', 0, '2025-10-17 10:00:00', '', NOW(), NOW()
  UNION ALL
  SELECT 'Le Mali au Sommet YouthConnekt Africa 2024', 'mali-sommet-youthconnekt-africa-2024', 'evenement',
    'Du 8 au 11 novembre 2024, une délégation de 18 personnes, dont 6 jeunes délégués, a représenté le Mali à la 7e édition du Sommet, à Kigali.',
    'La 7e édition du Sommet YouthConnekt Africa s''est tenue à Kigali, au Rwanda, du 8 au 11 novembre 2024, sous le thème « Des emplois pour les jeunes grâce à des compétences innovantes ». Elle a rassemblé plus de 4 000 participants venus de 33 pays africains.

## Une délégation malienne de 18 personnes
Coordonnée par le PNUD, la délégation réunissait 7 représentants du Gouvernement, 5 représentants du Système des Nations unies au Mali et 6 jeunes délégués : entrepreneurs, innovateurs et responsables d''organisations de jeunesse.

## Ce que le Mali en retient
- Les actions et les perspectives de YouthConnekt Mali ont été présentées au comité technique de YouthConnekt Africa.
- Les jeunes délégués ont tenu un stand pour présenter des initiatives maliennes et noué une quarantaine de contacts dans 19 pays africains.
- Une jeune déléguée a prononcé le mot de clôture du panel sur la santé des jeunes, en plaidant pour un meilleur accès aux services de santé mentale et à l''éducation à la santé sexuelle et reproductive.
- Les échanges d''affaires, notamment autour du textile malien, ont ouvert la voie à des collaborations avec des entrepreneurs rwandais.',
    'Kigali, Rwanda', 'publie', '2024-11-08', 0, '2024-11-15 10:00:00', '', NOW(), NOW()
) AS a
WHERE NOT EXISTS (SELECT 1 FROM actualites x WHERE x.slug = a.slug);

DELETE FROM initiatives WHERE slug = 'delegation-sommet-youthconnekt-africa-2024';
