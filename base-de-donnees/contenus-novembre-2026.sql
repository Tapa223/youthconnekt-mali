-- YouthConnekt Mali : nouvelles initiatives et actualité (novembre 2026).
-- À importer dans phpMyAdmin APRÈS mise-a-jour-29.sql. Textes complets, publiés ; les photos s'ajoutent depuis l'administration.
-- Sans risque si on l'importe deux fois : un contenu déjà présent (même adresse) n'est pas recréé.
SET NAMES utf8mb4;

-- Bootcamp YouthConnekt Mali 2022
INSERT INTO initiatives (titre, slug, statut, publie_le, mis_en_avant, axe_id, resume, contenu, image, etat, date_libelle, date_debut, date_fin, lieu, participants, format, objectifs, programme, chiffres, citation, citation_auteur, participations_ouvertes, cree_le, modifie_le)
SELECT 'Bootcamp YouthConnekt Mali 2022', 'bootcamp-youthconnekt-mali-2022', 'publie', NOW() - INTERVAL 10 MINUTE, 0, (SELECT id FROM axes WHERE slug = 'croissance-environnement'), 'En 2022, 26 jeunes entrepreneurs de Bamako, Mopti et Ségou ont suivi le Bootcamp YouthConnekt Mali, organisé avec le PNUD Mali et l''incubateur DoniLab pour structurer leurs projets et renforcer leurs compétences en gestion d''entreprise.',
  'Le Bootcamp YouthConnekt Mali 2022 a réuni 26 jeunes entrepreneurs : 20 jeunes du programme PNUD Mali-TEF et 6 jeunes de la société civile venus de Mopti et de Ségou. Organisé avec l''appui du PNUD Mali et de l''incubateur DoniLab, il leur a offert un temps de formation intensive, de travail en groupe et d''échanges entre pairs, une étape importante dans leur parcours d''entrepreneurs.
Une ouverture sous le signe de l''engagement
La première journée s''est ouverte sur le discours du Dr Doudou Ben Bechir Niang, représentant du ministère de la Jeunesse et des Sports, chargé de l''Instruction civique et de la Construction citoyenne, suivi des interventions des représentants du PNUD et de DoniLab.
Les participants ont ensuite travaillé sur la structuration de leur projet et sur le Business Model Canvas, avant des travaux de groupe et une séance de sensibilisation nourrie de retours d''expérience. Abdoulaye Sow, chargé des programmes à DoniLab, les a accompagnés et formés tout au long de la journée.
Gérer son entreprise au quotidien
La deuxième journée a été consacrée à la gestion d''entreprise et à la comptabilité, avec une formation animée par Youssouf Coulibaly.
Un atelier de codéveloppement a permis aux entrepreneurs de se conseiller mutuellement et de partager leurs bonnes pratiques pour mieux gérer leur activité. Aly Kouma et Abdoulaye Sow, responsables des programmes à DoniLab, ont ensuite proposé des cas pratiques sur la négociation commerciale et la gestion de la trésorerie.
Des entrepreneurs déterminés
Au-delà des formations, le Bootcamp a été un lieu de belles rencontres. Les jeunes de Bamako, Mopti et Ségou se sont montrés à la hauteur de l''exigence de ces journées, avec une détermination qui promet pour la suite de leurs projets.',
  '', 'terminee', '2022', NULL, NULL, '', '26 jeunes entrepreneurs', 'Présentiel',
  'Aider les jeunes entrepreneurs à structurer leur projet
Maîtriser des outils de modélisation comme le Business Model Canvas
Renforcer leurs compétences en gestion d''entreprise, comptabilité et trésorerie
Favoriser l''échange de pratiques entre entrepreneurs de différentes régions',
  'Jour 1 | Lancement et structuration de projet | Ouverture officielle, atelier sur la structuration de projet, Business Model Canvas, travaux de groupe, sensibilisation et retours d''expérience
Jour 2 | Gestion d''entreprise et codéveloppement | Formation en gestion d''entreprise et comptabilité, atelier de codéveloppement, cas pratiques sur la négociation commerciale et la gestion de la trésorerie',
  '26 | jeunes entrepreneurs formés
20 | jeunes du programme PNUD Mali-TEF
6 | jeunes de la société civile
3 | villes représentées',
  '', '', 0, NOW(), NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM initiatives WHERE slug = 'bootcamp-youthconnekt-mali-2022');

-- Les jeunes du CNJ mobilisés pendant la crise du carburant
INSERT INTO initiatives (titre, slug, statut, publie_le, mis_en_avant, axe_id, resume, contenu, image, etat, date_libelle, date_debut, date_fin, lieu, participants, format, objectifs, programme, chiffres, citation, citation_auteur, participations_ouvertes, cree_le, modifie_le)
SELECT 'Les jeunes du CNJ mobilisés pendant la crise du carburant', 'cnj-mobilisation-crise-carburant', 'publie', NOW() - INTERVAL 8 MINUTE, 0, (SELECT id FROM axes WHERE slug = 'gouvernance-participation'), 'Pendant la pénurie de carburant de l''automne 2025, le Conseil national de la jeunesse a mobilisé des jeunes bénévoles pour maintenir l''ordre dans les stations-service et informer la population. YouthConnekt Mali a soutenu cette mobilisation.',
  'À partir de septembre 2025, le Mali a connu une grave pénurie de carburant, qui a atteint Bamako début octobre. Devant les stations-service, les files d''attente s''allongeaient parfois sur des centaines de mètres, jour et nuit, avec des risques de tensions.
La jeunesse en renfort
Face à cette situation, le Conseil national de la jeunesse du Mali (CNJ) a mobilisé des jeunes bénévoles pour assurer l''ordre et la discipline au niveau des stations-service, en appui aux mesures prises par les autorités. Le CNJ a aussi publié chaque jour la liste des stations disposant de carburant, pour orienter les usagers, désengorger les stations et éviter les tensions dans les files.
YouthConnekt Mali a soutenu cette mobilisation citoyenne de la jeunesse.
Une mobilisation reconnue
Le 16 octobre 2025, reçu en audience par le Président de la Transition, le président du CNJ, Sory Ibrahim Cissé, a présenté cet engagement des jeunes dans la gestion citoyenne de la crise. Il l''a de nouveau cité parmi les grandes mobilisations de l''année lors des vœux du CNJ au chef de l''État, le 16 février 2026.',
  '', 'terminee', 'Automne 2025', NULL, NULL, 'Bamako', 'Jeunes bénévoles du CNJ', 'Sur le terrain',
  'Maintenir l''ordre et la discipline dans les files d''attente des stations-service
Informer chaque jour la population sur les stations approvisionnées
Montrer l''engagement citoyen de la jeunesse en temps de crise',
  '',
  '',
  '', '', 0, NOW(), NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM initiatives WHERE slug = 'cnj-mobilisation-crise-carburant');

-- École de la citoyenneté, 5e cohorte
INSERT INTO initiatives (titre, slug, statut, publie_le, mis_en_avant, axe_id, resume, contenu, image, etat, date_libelle, date_debut, date_fin, lieu, participants, format, objectifs, programme, chiffres, citation, citation_auteur, participations_ouvertes, cree_le, modifie_le)
SELECT 'École de la citoyenneté, 5e cohorte', 'ecole-de-la-citoyennete-5e-cohorte', 'publie', NOW() - INTERVAL 6 MINUTE, 0, (SELECT id FROM axes WHERE slug = 'gouvernance-participation'), 'Programme du ministère de la Jeunesse et des Sports soutenu par YouthConnekt Mali, l''École de la citoyenneté a accueilli en avril 2026 sa 5e cohorte : 1 000 jeunes formés au civisme, à la citoyenneté et au patriotisme au Palais des Pionniers.',
  'L''École de la citoyenneté est un programme du ministère de la Jeunesse et des Sports, chargé de l''Instruction civique et de la Construction citoyenne. Elle forme les jeunes au civisme, à la citoyenneté et au patriotisme, pour en faire des acteurs engagés de la construction nationale. Depuis son lancement, elle a formé plus de 4 000 jeunes. YouthConnekt Mali contribue à ce programme.
Une 5e cohorte de 1 000 jeunes
La 5e cohorte a été lancée le vendredi 17 avril 2026 au Palais des Pionniers de Bamako par le ministre d''État chargé de la Réconciliation, de la Paix et de la Cohésion nationale, le Général de Corps d''Armée Ismaël Wagué, au nom du Premier ministre. Elle a réuni 1 000 jeunes venus du district de Bamako, de la diaspora et des pays de l''Alliance des États du Sahel, ainsi que des jeunes déplacés internes.
Des échanges directs avec le Gouvernement
Au fil de la formation, des membres du Gouvernement sont venus échanger avec les jeunes. Le 18 avril 2026, les ministres chargés des Transports et des Infrastructures et de l''Urbanisme ont ainsi animé un panel interactif, notamment sur la sécurité routière.
Des relais de la citoyenneté
À l''issue du programme, les participants deviennent des ambassadeurs de la citoyenneté, de la paix et de la cohésion sociale dans leurs communautés.',
  '', 'terminee', 'Avril 2026', '2026-04-17', NULL, 'Palais des Pionniers, Bamako', '1 000 jeunes', 'Présentiel',
  'Former les jeunes au civisme, à la citoyenneté et au patriotisme
Rapprocher les jeunes des institutions et des membres du Gouvernement
Faire des participants des relais de la paix et de la cohésion sociale dans leurs communautés',
  '',
  '1 000 | jeunes dans la 5e cohorte
5 | cohortes depuis le lancement du programme',
  '', '', 0, NOW(), NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM initiatives WHERE slug = 'ecole-de-la-citoyennete-5e-cohorte');

-- DONIYA-LAB, l'innovation des jeunes au service de la santé communautaire
INSERT INTO initiatives (titre, slug, statut, publie_le, mis_en_avant, axe_id, resume, contenu, image, etat, date_libelle, date_debut, date_fin, lieu, participants, format, objectifs, programme, chiffres, citation, citation_auteur, participations_ouvertes, cree_le, modifie_le)
SELECT 'DONIYA-LAB, l''innovation des jeunes au service de la santé communautaire', 'doniya-lab-innovation-sante-communautaire', 'publie', NOW() - INTERVAL 4 MINUTE, 0, (SELECT id FROM axes WHERE slug = 'croissance-environnement'), 'Avec le soutien de YouthConnekt Mali, le DONIYA-LAB a réuni en juin 2026 au Palais des Pionniers 20 jeunes porteurs de projets pour un bootcamp de cinq jours. Cinq projets ont été primés.',
  'Le DONIYA-LAB est une initiative du Gouvernement du Mali, à travers le ministère de la Jeunesse et des Sports, chargé de l''Instruction civique et de la Construction citoyenne, et de l''UNICEF, avec l''appui de la Fondation Mastercard. Il fait émerger et accompagne les idées des jeunes au service du développement communautaire, notamment dans le domaine de la santé. YouthConnekt Mali a soutenu cette initiative.
De 450 candidatures à 20 projets
L''appel à projets a suscité plus de 450 candidatures dans les régions de Kayes, Nioro, Kita, Sikasso, Bougouni, Koutiala et Koulikoro, ainsi que dans le district sanitaire de Bamako. Vingt projets ont été retenus pour la phase nationale, organisée en juin 2026 au Palais des Pionniers de Bamako.
Cinq jours pour passer de l''idée au projet
La phase finale a pris la forme d''un bootcamp intensif de cinq jours, fondé sur la pratique, le mentorat et la co-création. Diagnostic des projets, travaux de groupe, coaching par des experts et simulations de pitch ont permis à chaque porteur de transformer son idée en un projet structuré, crédible et défendable. Le témoignage d''une entrepreneure ayant réussi son projet a été l''un des moments forts de la semaine.
L''ouverture technique a réuni le chef de cabinet du ministère de la Jeunesse et des Sports et la spécialiste ADAP de l''UNICEF, chargée de coordonner l''activité.
Cinq lauréats récompensés
Au terme d''un pitch final devant un jury multisectoriel, cinq projets ont été retenus pour leur caractère innovant, leur faisabilité et leur impact : trois dans la transformation agroalimentaire (farine enrichie, conservation et vente de fruits et légumes) et deux dans l''élevage (ferme avicole et coopérative apicole).
La cérémonie de clôture s''est tenue en présence du ministre de la Jeunesse et des Sports et du représentant de l''UNICEF au Mali. Tous les participants ont reçu une attestation, et les cinq lauréats ont été récompensés par des ordinateurs pour poursuivre leurs projets.
Et après
Les recommandations prévoient un suivi technique des projets lauréats avec l''APEJ et le ministère de l''Entrepreneuriat national, de l''Emploi et de la Formation professionnelle, la création d''un réseau des anciens du DONIYA-LAB et, à terme, l''élargissement de l''initiative à d''autres régions.',
  '', 'terminee', 'Juin 2026', NULL, NULL, 'Palais des Pionniers, Bamako', '20 jeunes porteurs de projets', 'Bootcamp en présentiel',
  'Renforcer les capacités des jeunes porteurs de projets
Promouvoir des solutions innovantes répondant aux besoins des communautés
Sélectionner et récompenser les projets les plus prometteurs
Encourager les jeunes filles à porter des projets entrepreneuriaux
Créer un espace de partage et de mise en réseau avec des mentors',
  'Étape 1 | Ouverture et diagnostic | Présentation des projets et diagnostic avec les experts
Étape 2 | Travail en groupe et coaching | Amélioration collective des projets et mentorat
Étape 3 | Simulations de pitch | Mise en situation face à des experts, avec des retours directs
Étape 4 | Pitch final et clôture | Présentation devant un jury multisectoriel, remise des attestations et des prix',
  '450 | candidatures
20 | jeunes porteurs de projets formés
5 | projets lauréats
5 | jours de bootcamp',
  '', '', 0, NOW(), NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM initiatives WHERE slug = 'doniya-lab-innovation-sante-communautaire');

-- Semaine nationale de la jeunesse 2026
INSERT INTO initiatives (titre, slug, statut, publie_le, mis_en_avant, axe_id, resume, contenu, image, etat, date_libelle, date_debut, date_fin, lieu, participants, format, objectifs, programme, chiffres, citation, citation_auteur, participations_ouvertes, cree_le, modifie_le)
SELECT 'Semaine nationale de la jeunesse 2026', 'semaine-nationale-de-la-jeunesse-2026', 'publie', NOW() - INTERVAL 2 MINUTE, 0, (SELECT id FROM axes WHERE slug = 'gouvernance-participation'), 'Du 11 au 16 août 2026, la Semaine nationale de la jeunesse a mobilisé les jeunes de Bamako et des régions autour du thème « Jeunesse, paix et sécurité ». YouthConnekt Mali a soutenu cette mobilisation portée par le Conseil national de la jeunesse.',
  'Couplée à la Journée internationale de la jeunesse du 12 août, la Semaine nationale de la jeunesse 2026 s''est tenue du 11 au 16 août sous le thème « Jeunesse, paix et sécurité : prévenir la désinformation, lutter contre la radicalisation et promouvoir la paix ». Portée par le Conseil national de la jeunesse du Mali (CNJ) et le ministère de la Jeunesse et des Sports, chargé de l''Instruction civique et de la Construction citoyenne, elle a placé les jeunes au cœur des enjeux de paix, de cohésion sociale et de développement. YouthConnekt Mali a soutenu cette initiative.
Lancement officiel au CICB
Le mercredi 12 août 2026, le ministre d''État chargé de la Réconciliation nationale et de la Cohésion sociale, le Général de Corps d''Armée Ismaël Wagué, a lancé les activités au Centre international de conférences de Bamako, au nom du Président de la Transition. Le ministre Abdoul Kassim Ibrahim Fomba y a salué une jeunesse prête à rapprocher les communautés, et le président du CNJ, Sory Ibrahim Cissé, a porté la volonté des jeunes de contribuer activement à la stabilité du pays.
Une semaine d''échanges et de formation
Au programme : un atelier sur l''agenda Jeunesse, paix et sécurité, une formation sur le développement conduit par les communautés, une conférence sur l''engagement civique et le patriotisme, un forum Jeunesse-Diaspora et des panels régionaux sur les priorités de développement.
Dans les régions aussi
La Semaine a été relayée dans les régions par les conseils régionaux de la jeunesse, comme à Nioro du Sahel dès le 12 août et à Bandiagara du 22 au 24 août.',
  '', 'terminee', '11 au 16 août 2026', '2026-08-11', '2026-08-16', 'CICB, Bamako, et régions', '', 'Présentiel',
  'Renforcer la participation des jeunes à la consolidation de la paix
Prévenir la désinformation et la radicalisation chez les jeunes
Promouvoir la citoyenneté et la cohésion sociale
Renforcer les capacités des jeunes et la gouvernance associative',
  '',
  '',
  '', '', 0, NOW(), NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM initiatives WHERE slug = 'semaine-nationale-de-la-jeunesse-2026');

-- Housseyni Diallo au Forum YouthConnekt Sahel 2026
INSERT INTO actualites (titre, slug, statut, publie_le, mis_en_avant, categorie, chapeau, contenu, image, date_evenement, date_fin, lieu, cree_le, modifie_le)
SELECT 'Housseyni Diallo au Forum YouthConnekt Sahel 2026', 'housseyni-diallo-forum-youthconnekt-sahel-2026', 'publie', NOW(), 0, 'actualite', 'Au Forum YouthConnekt Sahel 2026 à Niamey, Housseyni Diallo, de U-Report Mali, a représenté la jeunesse malienne comme panéliste, partagé l''expérience malienne de mobilisation numérique des jeunes et animé le stand du Mali au Sahel Marketplace.',
  'Du 10 au 12 août 2026, Housseyni Diallo faisait partie des jeunes Maliens présents à Niamey pour la 3e édition du Forum YouthConnekt Sahel. Sa participation s''est articulée autour de trois engagements : contribuer aux travaux techniques et aux échanges du forum, intervenir comme panéliste et animer le stand du Mali au Sahel Marketplace.
Panéliste sur l''économie numérique
Le 11 août, il est intervenu lors de la session « Économie numérique et innovation ancrées dans les territoires : mobiliser les compétences numériques pour transformer les défis locaux en solutions génératrices d''emplois et de revenus ». Il a échangé aux côtés du ministre de la Communication et des Nouvelles Technologies de l''Information du Niger et d''acteurs du secteur privé, de la transformation digitale, de l''intelligence artificielle et de la jeunesse numérique, sous la modération du Dr Rachid Awal, de l''UNFPA.
La session portait sur les obstacles que rencontrent les jeunes innovateurs du Sahel (accès aux infrastructures, au financement, aux compétences, aux incubateurs et aux marchés, inégalités entre territoires) et sur les moyens de bâtir une économie numérique inclusive et souveraine.
L''exemple de la campagne contre la polio à Sikasso
Housseyni Diallo a ouvert son intervention par la projection d''une vidéo réalisée pendant la campagne de sensibilisation et de vaccination contre la polio menée en novembre 2024 dans la région de Sikasso. Il y a montré comment l''action communautaire et le numérique, portés par et pour les jeunes, peuvent produire des résultats concrets. Selon les données présentées, le taux de vaccination a augmenté de 3 %, plusieurs milliers de rumeurs ont été repérées et traitées, et le Mali n''a enregistré aucun cas de polio l''année suivante. Ces résultats ont retenu l''attention de nombreux jeunes et organisations présents.
## Ses messages clés
- Rendre visibles les innovations locales, dans les médias et en ligne, pour inspirer d''autres jeunes et attirer des partenaires ;
- S''appuyer sur les plateformes numériques et les réseaux de jeunes pour repérer, documenter et faire connaître les solutions locales, y compris loin des grandes villes ;
- Associer la valorisation des initiatives à un accompagnement concret : compétences, mentorat, information, mise en réseau, financement et accès aux marchés ;
- Documenter et raconter les initiatives de jeunes, et les relier aux institutions, entreprises et partenaires capables de les aider à changer d''échelle.
## Ce qu''il retient du panel
- Le numérique n''est pas seulement un secteur d''activité, c''est une infrastructure qui transforme toute l''économie et la société ;
- Les jeunes ruraux et les femmes doivent être pris en compte dans toute stratégie numérique ;
- La souveraineté numérique passe par les talents et solutions locales, la maîtrise des données et des technologies adaptées aux réalités du Sahel ;
- Les liens entre jeunes innovateurs, pouvoirs publics, secteur privé, universités et incubateurs restent essentiels.
Le Made in Mali à l''honneur
Au Sahel Marketplace, il a animé le stand du Mali. Le stand présentait des produits « Made in Mali » ainsi que des réalisations et initiatives portées ou soutenues par le ministère de la Jeunesse et des Sports, chargé de l''Instruction civique et de la Construction citoyenne (CNPV, SNV, APEJ), le PNUD et l''UNICEF. Il a été un lieu de rencontres avec des jeunes, des institutions, des organisations internationales, des innovateurs et des partenaires de toute la région.',
  '', '2026-08-10', '2026-08-12', 'Niamey, Niger', NOW(), NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM actualites WHERE slug = 'housseyni-diallo-forum-youthconnekt-sahel-2026');

-- L'actualité apparaît aussi sur la page de la communauté Tech et numérique
INSERT INTO actualite_communaute (actualite_id, communaute_id)
SELECT a.id, c.id FROM actualites a JOIN communautes c ON c.slug = 'tech-et-numerique'
WHERE a.slug = 'housseyni-diallo-forum-youthconnekt-sahel-2026'
  AND NOT EXISTS (SELECT 1 FROM actualite_communaute x WHERE x.actualite_id = a.id AND x.communaute_id = c.id);
