-- =====================================================================
-- YouthConnekt Mali : mise à jour 14 (9 novembre 2026)
-- À importer une fois dans phpMyAdmin, sur la base « youthconnekt ».
--  - membres individuels : genre et date de naissance (privés, pour les statistiques) ;
--  - témoignages : profil tiré du compte (anciens libellés convertis) ;
--  - newsletters : rédaction, ciblage et historique des envois.
-- =====================================================================

USE youthconnekt;
SET NAMES utf8mb4;

ALTER TABLE profils_individuels
  ADD COLUMN genre VARCHAR(10) NOT NULL DEFAULT '' AFTER nom,
  ADD COLUMN date_naissance DATE NULL AFTER genre;

UPDATE temoignages SET categorie = 'femme' WHERE categorie = 'jeune_femme';
UPDATE temoignages SET categorie = 'homme' WHERE categorie = 'jeune_homme';
UPDATE temoignages SET categorie = 'organisation' WHERE categorie = 'partenaire';

-- Chiffres clés de l'accueil (section « Le chapitre ») : pré-remplis s'il n'y en a aucun.
-- Sources : note technique YouthConnekt (juin 2026). À ajuster dans Administration > Chiffres clés.
INSERT INTO chiffres_cles (valeur, libelle, ordre, publie)
SELECT * FROM (SELECT '2021' AS valeur, 'lancement du chapitre Mali' AS libelle, 1 AS ordre, 1 AS publie
  UNION ALL SELECT '37', 'pays dans le réseau YouthConnekt Africa', 2, 1
  UNION ALL SELECT '2', "axes d'intervention du chapitre", 3, 1) AS c
WHERE NOT EXISTS (SELECT 1 FROM chiffres_cles);

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
Cette édition a posé les bases de la gouvernance du Forum : constitution d''un comité de pilotage réunissant les ministres en charge de la jeunesse des pays du Sahel, et validation de la Charte du Forum. Le Mali a activement pris part aux éditions de Dakar et de N''Djamena.',
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

-- Newsletters rédigées dans l'administration (brouillons et historique des envois)
CREATE TABLE IF NOT EXISTS newsletters (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  objet VARCHAR(200) NOT NULL,
  titre VARCHAR(200) NOT NULL DEFAULT '',
  message MEDIUMTEXT NULL,
  image VARCHAR(255) NOT NULL DEFAULT '',
  cible VARCHAR(20) NOT NULL DEFAULT 'tous',
  communautes TEXT NULL,
  contenus TEXT NULL,
  statut VARCHAR(20) NOT NULL DEFAULT 'brouillon',
  nb_destinataires INT UNSIGNED NOT NULL DEFAULT 0,
  mode_envoi VARCHAR(20) NOT NULL DEFAULT '',
  cree_par INT UNSIGNED NULL,
  cree_le DATETIME NOT NULL,
  modifie_le DATETIME NOT NULL,
  envoyee_le DATETIME NULL,
  PRIMARY KEY (id),
  KEY idx_newsletters_statut (statut),
  CONSTRAINT fk_newsletters_auteur FOREIGN KEY (cree_par) REFERENCES utilisateurs (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
