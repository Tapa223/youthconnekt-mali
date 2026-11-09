-- =====================================================================
-- YouthConnekt Mali : mise à jour 19 (9 novembre 2026)
-- À importer une fois dans phpMyAdmin, sur la base « youthconnekt ».
--  - trois initiatives d'exemple, ouvertes aux participations (rôles et questions),
--    pour voir la présentation ; elles partent avec « Retirer les contenus d'exemple » ;
--  - photos provisoires sur les actualités (à remplacer par les vraies dans l'administration).
-- =====================================================================

USE youthconnekt;
SET NAMES utf8mb4;

-- Photos provisoires des actualités (Sommet YouthConnekt Africa, hors Mali)
UPDATE actualites SET image = 'assets/img/exemples/arena.jpg' WHERE slug = 'sommet-youthconnekt-africa-2026-kigali' AND image = '';
UPDATE actualites SET image = 'assets/img/exemples/panel.jpg' WHERE slug = 'mali-forum-youthconnekt-sahel-2026-niamey' AND image = '';
UPDATE actualites SET image = 'assets/img/exemples/ceremonie.jpg' WHERE slug = 'mali-forum-youthconnekt-sahel-2025-ndjamena' AND image = '';
UPDATE actualites SET image = 'assets/img/exemples/foule.jpg' WHERE slug = 'mali-forum-youthconnekt-sahel-2021-dakar' AND image = '';
UPDATE actualites SET image = 'assets/img/exemples/stand.jpg' WHERE slug = 'mali-sommet-youthconnekt-africa-2024' AND image = '';
UPDATE actualites SET image = 'assets/img/exemples/intervenant.jpg' WHERE slug = 'comite-technique-youthconnekt-mali' AND image = '';
UPDATE actualites SET image = 'assets/img/exemples/salle-bleue.jpg' WHERE slug = 'lancement-chapitre-youthconnekt-mali' AND image = '';

-- Initiatives d'exemple
INSERT INTO initiatives (titre, slug, statut, publie_le, mis_en_avant, axe_id, resume, contenu, image, etat, date_libelle, lieu, participants, format, objectifs, programme, chiffres, citation, citation_auteur, participations_ouvertes, participations_fin, participations_intro, cree_le, modifie_le)
SELECT i.titre, i.slug, 'publie', i.publie_le, i.une, (SELECT id FROM axes WHERE slug = i.axe), i.resume, i.contenu, i.image, i.etat, i.date_libelle, i.lieu, i.participants, i.format, i.objectifs, i.programme, '', '', '', 1, NULL, i.intro, NOW(), NOW()
FROM (
  SELECT 'Caravane de la citoyenneté' AS titre, 'exemple-caravane-de-la-citoyennete' AS slug, NOW() AS publie_le, 1 AS une, 'gouvernance-participation' AS axe,
    'Trois régions, trois étapes : aller à la rencontre des jeunes pour parler engagement, citoyenneté et vivre-ensemble.' AS resume,
    'La Caravane de la citoyenneté va à la rencontre des jeunes là où ils vivent. À chaque étape : un débat ouvert, des ateliers sur la participation citoyenne et un temps d''échange avec les autorités locales.

Pour réussir ces trois étapes, le chapitre a besoin de jeunes bénévoles pour l''animation, la logistique et la communication, et d''intervenants pour les ateliers.' AS contenu,
    'assets/img/exemples/foule.jpg' AS image, 'a_venir' AS etat, 'Février 2027' AS date_libelle, 'Ségou, Mopti, Sikasso' AS lieu, '300 jeunes attendus' AS participants, 'Présentiel' AS format,
    'Informer les jeunes sur leurs droits et leurs devoirs\nDonner la parole aux jeunes des régions\nRapprocher les jeunes des autorités locales' AS objectifs,
    'Étape 1 | Ségou | Débat ouvert et ateliers citoyenneté\nÉtape 2 | Mopti | Dialogue avec les autorités locales\nÉtape 3 | Sikasso | Restitution et engagements des jeunes' AS programme,
    'Nous recherchons des bénévoles motivés pour faire vivre la caravane dans les trois régions. Le transport et les repas sont pris en charge pendant les étapes.' AS intro
  UNION ALL
  SELECT 'Programme de mentorat des jeunes entrepreneurs', 'exemple-programme-de-mentorat', NOW() - INTERVAL 1 DAY, 0, 'croissance-environnement',
    'Six mois pour faire grandir son projet, accompagné par un entrepreneur ou une entrepreneuse expérimentée.',
    'Le programme associe chaque jeune porteur de projet à un mentor. Ensemble, ils se rencontrent deux fois par mois pour avancer sur le projet : modèle économique, financement, premiers clients.

Le chapitre recherche à la fois des mentors, professionnels prêts à donner un peu de leur temps, et des jeunes porteurs de projet à accompagner.',
    'assets/img/exemples/intervenant.jpg', 'en_cours', 'Novembre 2026 à avril 2027', 'Bamako', '20 binômes', 'Rencontres et ateliers',
    'Accompagner 20 jeunes porteurs de projet\nMobiliser des mentors expérimentés\nAider chaque projet à franchir une étape concrète',
    '', 'Mentors et porteurs de projet : présentez-vous, l''équipe forme les binômes selon les domaines et les besoins.'
  UNION ALL
  SELECT 'Journée YouthConnekt Mali', 'exemple-journee-youthconnekt-mali', NOW() - INTERVAL 2 DAY, 0, 'gouvernance-participation',
    'Une journée pour faire connaître le chapitre, recueillir l''avis des jeunes et mettre en avant leurs projets.',
    'La Journée YouthConnekt Mali réunit les jeunes, les organisations et les partenaires du chapitre : présentation de YouthConnekt Mali, panels, espace d''exposition des projets de jeunes et consultation sur les priorités de la jeunesse.

Venez y participer, présenter votre projet ou rejoindre l''équipe de bénévoles.',
    'assets/img/exemples/salle-bleue.jpg', 'a_venir', 'Mars 2027', 'Bamako', '500 participants attendus', 'Présentiel',
    'Faire connaître le chapitre aux jeunes\nRecueillir les avis et propositions des jeunes\nMettre en avant les projets portés par des jeunes',
    'Matin | Ouverture et panels | Présentation du chapitre et échanges avec les partenaires\nAprès-midi | Espace des projets | Exposition et présentation des projets de jeunes',
    ''
) AS i
WHERE NOT EXISTS (SELECT 1 FROM initiatives x WHERE x.slug = i.slug);

-- Rôles
INSERT INTO initiative_roles (initiative_id, nom, description, places, ordre)
SELECT (SELECT id FROM initiatives WHERE slug = r.slug), r.nom, r.description, r.places, r.ordre FROM (
  SELECT 'exemple-caravane-de-la-citoyennete' AS slug, 'Bénévole animation' AS nom, 'Animer les débats et les ateliers avec les jeunes' AS description, 10 AS places, 0 AS ordre
  UNION ALL SELECT 'exemple-caravane-de-la-citoyennete', 'Bénévole logistique', 'Accueil, installation des salles, matériel', 6, 1
  UNION ALL SELECT 'exemple-caravane-de-la-citoyennete', 'Intervenant', 'Animer un atelier sur la citoyenneté ou la participation des jeunes', NULL, 2
  UNION ALL SELECT 'exemple-programme-de-mentorat', 'Mentor', 'Accompagner un jeune porteur de projet pendant six mois', 5, 0
  UNION ALL SELECT 'exemple-programme-de-mentorat', 'Porteur de projet', 'Être accompagné pour faire avancer son projet', 20, 1
  UNION ALL SELECT 'exemple-journee-youthconnekt-mali', 'Participant', 'Assister aux panels et à la consultation', NULL, 0
  UNION ALL SELECT 'exemple-journee-youthconnekt-mali', 'Exposant', 'Présenter son projet dans l''espace des projets', 8, 1
  UNION ALL SELECT 'exemple-journee-youthconnekt-mali', 'Bénévole', 'Accueil, orientation et organisation de la journée', 15, 2
) AS r
WHERE NOT EXISTS (SELECT 1 FROM initiative_roles x JOIN initiatives i ON i.id = x.initiative_id WHERE i.slug = r.slug AND x.nom = r.nom);

-- Questions
INSERT INTO initiative_champs (initiative_id, libelle, type, options, aide, requis, ordre)
SELECT (SELECT id FROM initiatives WHERE slug = c.slug), c.libelle, c.type, c.options, c.aide, c.requis, c.ordre FROM (
  SELECT 'exemple-caravane-de-la-citoyennete' AS slug, 'À quelles étapes pouvez-vous participer ?' AS libelle, 'cases' AS type, 'Ségou\nMopti\nSikasso' AS options, '' AS aide, 1 AS requis, 0 AS ordre
  UNION ALL SELECT 'exemple-caravane-de-la-citoyennete', 'Langues parlées', 'texte', '', 'Exemple : bambara, peul, français', 0, 1
  UNION ALL SELECT 'exemple-caravane-de-la-citoyennete', 'Avez-vous déjà été bénévole ?', 'oui_non', '', '', 0, 2
  UNION ALL SELECT 'exemple-caravane-de-la-citoyennete', 'Taille de t-shirt', 'choix', 'S\nM\nL\nXL', '', 0, 3
  UNION ALL SELECT 'exemple-programme-de-mentorat', 'Votre domaine', 'texte', '', 'Exemple : agriculture, numérique, mode', 1, 0
  UNION ALL SELECT 'exemple-programme-de-mentorat', 'Où en est le projet ?', 'choix', 'Idée\nLancement\nEn activité depuis plus d''un an\nJe suis mentor', '', 1, 1
  UNION ALL SELECT 'exemple-programme-de-mentorat', 'Qu''attendez-vous du programme ?', 'zone', '', '', 0, 2
  UNION ALL SELECT 'exemple-journee-youthconnekt-mali', 'Comment avez-vous connu la journée ?', 'choix', 'Réseaux sociaux\nUn ami\nMon association ou mon école\nLe site YouthConnekt Mali', '', 0, 0
  UNION ALL SELECT 'exemple-journee-youthconnekt-mali', 'Avez-vous un projet à présenter ?', 'oui_non', '', '', 0, 1
) AS c
WHERE NOT EXISTS (SELECT 1 FROM initiative_champs x JOIN initiatives i ON i.id = x.initiative_id WHERE i.slug = c.slug AND x.libelle = c.libelle);
