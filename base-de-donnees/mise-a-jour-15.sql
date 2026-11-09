-- =====================================================================
-- YouthConnekt Mali : mise à jour 15 (9 novembre 2026)
-- À importer une fois dans phpMyAdmin, sur la base « youthconnekt ».
-- Contenus officiels du chapitre (note technique YouthConnekt de juin 2026,
-- débriefing du Sommet YouthConnekt Africa 2024) :
--  - les deux axes d'intervention officiels remplacent les trois axes provisoires ;
--  - chiffres clés propres au chapitre malien ;
--  - partenaires institutionnels ;
--  - actualités et initiatives réelles du chapitre.
-- Tout reste modifiable ensuite dans l'administration.
-- =====================================================================

USE youthconnekt;
SET NAMES utf8mb4;

-- 1. Les deux axes officiels. « Engager » devient l'axe 1, « Accompagner » l'axe 2 ;
--    les initiatives de « Valoriser » rejoignent l'axe 2, puis « Valoriser » est retiré.
UPDATE axes SET nom = 'Gouvernance inclusive et participation des jeunes', slug = 'gouvernance-participation',
  description = 'Leadership, participation citoyenne et place des jeunes dans la gouvernance et la cohésion sociale.',
  elements = 'Dialogues entre les jeunes et les décideurs\nLeadership et participation citoyenne\nVoix des jeunes dans les politiques jeunesse et la cohésion sociale',
  couleur = 'turquoise', ordre = 1
WHERE slug = 'engager';

UPDATE axes SET nom = 'Croissance inclusive et environnement', slug = 'croissance-environnement',
  description = 'Compétences, emploi et entrepreneuriat des jeunes, et solutions locales face aux défis de l''environnement.',
  elements = 'Renforcement des compétences et employabilité\nEntrepreneuriat et accès au financement\nInnovation et solutions locales pour l''environnement',
  couleur = 'orange', ordre = 2
WHERE slug = 'accompagner';

UPDATE initiatives SET axe_id = (SELECT id FROM (SELECT id FROM axes WHERE slug = 'croissance-environnement') AS a)
WHERE axe_id = (SELECT id FROM (SELECT id FROM axes WHERE slug = 'valoriser') AS v);
DELETE FROM axes WHERE slug = 'valoriser';

-- 2. Chiffres clés du chapitre malien (les trois premiers publiés s'affichent sur l'accueil)
DELETE FROM chiffres_cles WHERE libelle IN ('lancement du chapitre Mali', 'pays dans le réseau YouthConnekt Africa', 'axes d''intervention du chapitre');
INSERT INTO chiffres_cles (valeur, libelle, ordre, publie) VALUES
  ('2021', 'lancement du chapitre YouthConnekt Mali', 1, 1),
  ('2', 'axes d''intervention du chapitre', 2, 1),
  ('18', 'délégués maliens au Sommet YouthConnekt Africa 2024', 3, 1),
  ('6', 'jeunes délégués maliens au Sommet de Kigali en 2024', 4, 0),
  ('40', 'contacts noués dans 19 pays africains par nos jeunes délégués', 5, 0),
  ('2', 'éditions du Forum YouthConnekt Sahel avec le Mali (Dakar 2021, N''Djamena 2025)', 6, 0);

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

-- 4. Actualités : dates réelles, et trois nouvelles pages d'histoire du chapitre
UPDATE actualites SET publie_le = '2024-11-15 10:00:00' WHERE slug = 'mali-sommet-youthconnekt-africa-2024';
UPDATE actualites SET contenu = REPLACE(contenu, 'Le Mali a pris part à chacune des éditions.', 'Le Mali a activement pris part aux éditions de Dakar et de N''Djamena.')
WHERE slug = 'forum-youthconnekt-sahel-2026-niamey';

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
  UNION ALL
  SELECT 'Le Mali au Forum YouthConnekt Sahel 2025 à N''Djamena', 'mali-forum-youthconnekt-sahel-2025', 'evenement',
    'Du 13 au 15 octobre 2025, la 2e édition du Forum a réuni les jeunes du Sahel autour de la souveraineté, de la sécurité, de l''environnement et de la justice sociale.',
    'La 2e édition du Forum YouthConnekt Sahel s''est tenue à N''Djamena, au Tchad, du 13 au 15 octobre 2025, sous le thème « Jeunesse sahélienne et Souveraineté : Construire l''avenir face aux défis sécuritaires, environnementaux et de justice sociale ».

Branche de YouthConnekt Africa, le Forum permet aux jeunes du Sahel de s''exprimer, de réfléchir aux opportunités et de proposer des solutions locales pour la paix et la prospérité. Le Mali, qui avait déjà participé à la première édition à Dakar en 2021, y a activement pris part.',
    'N''Djamena, Tchad', 'publie', '2025-10-13', 0, '2025-10-16 10:00:00', '', NOW(), NOW()
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
