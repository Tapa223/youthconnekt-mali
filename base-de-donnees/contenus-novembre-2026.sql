-- YouthConnekt Mali : nouvelles initiatives et actualité (novembre 2026).
-- À importer dans phpMyAdmin APRÈS mise-a-jour-29.sql. Textes complets, publiés ; les photos s'ajoutent depuis l'administration.
-- Sans risque si on l'importe deux fois : un contenu déjà présent (même adresse) n'est pas recréé.
SET NAMES utf8mb4;

-- Bootcamp YouthConnekt Mali 2022
INSERT INTO initiatives (titre, slug, statut, publie_le, mis_en_avant, axe_id, resume, contenu, image, etat, date_libelle, date_debut, date_fin, lieu, participants, format, objectifs, programme, chiffres, citation, citation_auteur, participations_ouvertes, cree_le, modifie_le)
SELECT 'Bootcamp YouthConnekt Mali 2022', 'bootcamp-youthconnekt-mali-2022', 'publie', NOW() - INTERVAL 10 MINUTE, 0, (SELECT id FROM axes WHERE slug = 'croissance-environnement'), 'Deux jours de formation intensive pour 26 jeunes entrepreneurs de Bamako, Mopti et Ségou : structurer son projet, maîtriser ses chiffres et apprendre des autres. Un bootcamp organisé avec le PNUD Mali et l''incubateur DoniLab.',
  'En 2022, YouthConnekt Mali a réuni 26 jeunes entrepreneurs pour un bootcamp pensé comme un accélérateur : vingt jeunes accompagnés par le programme PNUD Mali-TEF et six jeunes de la société civile venus de Mopti et de Ségou. Pendant deux jours, avec l''incubateur DoniLab, ils ont travaillé sur ce qui fait tenir une entreprise : un projet clair, des comptes maîtrisés et un réseau sur lequel s''appuyer.
Jour 1 : donner une structure à son idée
Après l''ouverture officielle, marquée par les discours du Dr Doudou Ben Bechir Niang, représentant du ministère de la Jeunesse et des Sports, et des représentants du PNUD et de DoniLab, les participants sont passés à la pratique. Atelier de structuration de projet, prise en main du Business Model Canvas, travaux en groupe : chacun est reparti avec une vision plus nette de son modèle. Abdoulaye Sow, chargé des programmes à DoniLab, a accompagné la journée, nourrie de retours d''expérience.
Jour 2 : gérer, compter, négocier
La deuxième journée a porté sur la gestion d''entreprise et la comptabilité, avec Youssouf Coulibaly. Aly Kouma et Abdoulaye Sow, de DoniLab, ont ensuite placé les entrepreneurs face à des cas pratiques de négociation commerciale et de gestion de trésorerie.
Temps fort du bootcamp, l''atelier de codéveloppement a permis à chacun de soumettre ses difficultés au groupe et de repartir avec des conseils concrets, venus d''entrepreneurs qui vivent les mêmes réalités.',
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

-- Crise du carburant : la jeunesse mobilisée aux stations-service
INSERT INTO initiatives (titre, slug, statut, publie_le, mis_en_avant, axe_id, resume, contenu, image, etat, date_libelle, date_debut, date_fin, lieu, participants, format, objectifs, programme, chiffres, citation, citation_auteur, participations_ouvertes, cree_le, modifie_le)
SELECT 'Crise du carburant : la jeunesse mobilisée aux stations-service', 'cnj-mobilisation-crise-carburant', 'publie', NOW() - INTERVAL 8 MINUTE, 0, (SELECT id FROM axes WHERE slug = 'gouvernance-participation'), 'À l''automne 2025, quand les files d''attente s''étiraient devant les stations-service, les bénévoles du Conseil national de la jeunesse ont aidé à garder le calme et à informer les usagers. YouthConnekt Mali a soutenu cette mobilisation et salué leur engagement.',
  'À partir de septembre 2025, le Mali a traversé une grave pénurie de carburant, ressentie à Bamako dès le début du mois d''octobre. Devant les stations encore approvisionnées, les files s''étiraient sur des centaines de mètres, de jour comme de nuit, et la tension montait vite.
Des bénévoles pour garder le calme
Le Conseil national de la jeunesse du Mali (CNJ) a alors déployé des jeunes bénévoles dans les stations-service pour organiser les files et maintenir l''ordre. Chaque jour, il publiait aussi la liste des stations qui disposaient de carburant : un geste simple, qui évitait aux usagers de tourner en ville et désengorgeait les points de vente.
Le soutien de YouthConnekt Mali
YouthConnekt Mali s''est tenu aux côtés du CNJ : le chapitre a soutenu l''initiative, mobilisé des jeunes de son réseau et félicité les bénévoles pour leur sens des responsabilités dans un moment difficile pour le pays.
Une mobilisation reconnue
Reçu en audience par le Président de la Transition le 16 octobre 2025, le président du CNJ, Sory Ibrahim Cissé, a présenté cet engagement de la jeunesse. Il l''a de nouveau cité parmi les grandes mobilisations de l''année lors des vœux du CNJ au chef de l''État, le 16 février 2026.',
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
SELECT 'École de la citoyenneté, 5e cohorte', 'ecole-de-la-citoyennete-5e-cohorte', 'publie', NOW() - INTERVAL 6 MINUTE, 0, (SELECT id FROM axes WHERE slug = 'gouvernance-participation'), 'En avril 2026, 1 000 jeunes ont rejoint au Palais des Pionniers la 5e cohorte de l''École de la citoyenneté, programme du ministère de la Jeunesse et des Sports. YouthConnekt Mali a soutenu la cohorte et y est intervenu auprès des participants.',
  'Former des jeunes qui connaissent leurs droits, leurs devoirs et les institutions de leur pays : c''est l''ambition de l''École de la citoyenneté, programme du ministère de la Jeunesse et des Sports, chargé de l''Instruction civique et de la Construction citoyenne. Civisme, citoyenneté et patriotisme y sont abordés à travers des sessions de formation et des rencontres directes avec les décideurs.
Une 5e cohorte de 1 000 jeunes
Le vendredi 17 avril 2026, le ministre d''État chargé de la Réconciliation, de la Paix et de la Cohésion nationale, le Général de Corps d''Armée Ismaël Wagué, a lancé la 5e cohorte au Palais des Pionniers de Bamako, au nom du Premier ministre. Elle a réuni 1 000 jeunes du district de Bamako, de la diaspora et des pays de l''Alliance des États du Sahel, ainsi que des jeunes déplacés internes.
Face aux membres du Gouvernement
Tout au long de la formation, des ministres sont venus échanger directement avec les participants. Le 18 avril, les ministres chargés des Transports et des Infrastructures et de l''Urbanisme ont ainsi animé un panel interactif, notamment sur la sécurité routière.
L''apport de YouthConnekt Mali
YouthConnekt Mali a apporté son soutien à cette cohorte et y a pris la parole : des intervenants du chapitre sont venus à la rencontre des participants pour échanger avec eux.
Des relais dans les communautés
À l''issue du programme, les participants repartent avec une mission : porter les valeurs de citoyenneté, de paix et de cohésion sociale dans leurs communautés.',
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
SELECT 'DONIYA-LAB, l''innovation des jeunes au service de la santé communautaire', 'doniya-lab-innovation-sante-communautaire', 'publie', NOW() - INTERVAL 4 MINUTE, 0, (SELECT id FROM axes WHERE slug = 'croissance-environnement'), 'Vingt porteurs de projets, cinq jours de bootcamp et cinq lauréats : en juin 2026, le DONIYA-LAB a fait mûrir des solutions de jeunes pour la santé et le développement des communautés, avec YouthConnekt Mali à l''organisation et à l''accompagnement.',
  'Porté par le ministère de la Jeunesse et des Sports, chargé de l''Instruction civique et de la Construction citoyenne, et par l''UNICEF, avec l''appui de la Fondation Mastercard, le DONIYA-LAB repère les idées de jeunes qui répondent aux besoins des communautés, notamment en matière de santé, et les aide à devenir de vrais projets.
De 450 candidatures à 20 projets
L''appel à projets a suscité plus de 450 candidatures dans les régions de Kayes, Nioro, Kita, Sikasso, Bougouni, Koutiala et Koulikoro, ainsi que dans le district sanitaire de Bamako. Vingt projets ont été retenus pour la phase nationale, en juin 2026, au Palais des Pionniers de Bamako.
Cinq jours pour passer de l''idée au projet
Le bootcamp a misé sur la pratique : diagnostic de chaque projet, travail en groupe, coaching par des experts, puis simulations de pitch face à des professionnels. Jour après jour, les idées se sont précisées jusqu''à devenir des projets structurés, crédibles et défendables. Le témoignage d''une entrepreneure ayant réussi a donné aux participants un repère concret, et beaucoup de motivation.
Cinq lauréats récompensés
Devant un jury multisectoriel, cinq projets se sont distingués par leur caractère innovant, leur faisabilité et leur impact : trois dans la transformation agroalimentaire (farine enrichie, conservation et vente de fruits et légumes), deux dans l''élevage (ferme avicole et coopérative apicole). À la clôture, en présence du ministre de la Jeunesse et des Sports et du représentant de l''UNICEF au Mali, tous les participants ont reçu une attestation et les lauréats ont été récompensés par des ordinateurs.
YouthConnekt Mali aux côtés des porteurs de projets
Le chapitre s''est engagé à chaque étape : il a relayé l''appel à projets dans son réseau pour mobiliser les candidats, appuyé l''organisation du bootcamp et assuré la couverture de l''événement. Des membres du chapitre ont aussi accompagné les porteurs de projets comme mentors ou membres du jury.
Et après
Les recommandations prévoient un suivi technique des lauréats avec l''APEJ et le ministère de l''Entrepreneuriat national, de l''Emploi et de la Formation professionnelle, la création d''un réseau des anciens du DONIYA-LAB et, à terme, l''extension du programme à d''autres régions.',
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
SELECT 'Semaine nationale de la jeunesse 2026', 'semaine-nationale-de-la-jeunesse-2026', 'publie', NOW() - INTERVAL 2 MINUTE, 0, (SELECT id FROM axes WHERE slug = 'gouvernance-participation'), 'Du 11 au 16 août 2026, la Semaine nationale de la jeunesse a réuni les jeunes de Bamako et des régions autour de la paix, de la sécurité et de la lutte contre la désinformation. YouthConnekt Mali a contribué à son organisation et à la mobilisation des jeunes.',
  'Couplée à la Journée internationale de la jeunesse du 12 août, la Semaine nationale de la jeunesse 2026 s''est tenue du 11 au 16 août, sur un thème qui parle aux jeunes Maliens d''aujourd''hui : « Jeunesse, paix et sécurité : prévenir la désinformation, lutter contre la radicalisation et promouvoir la paix ». Portée par le Conseil national de la jeunesse du Mali (CNJ) et le ministère de la Jeunesse et des Sports, chargé de l''Instruction civique et de la Construction citoyenne, elle a mis en avant des jeunes acteurs de la paix et de la cohésion sociale.
Lancement officiel au CICB
Le mercredi 12 août, le ministre d''État chargé de la Réconciliation nationale et de la Cohésion sociale, le Général de Corps d''Armée Ismaël Wagué, a lancé les activités au Centre international de conférences de Bamako, au nom du Président de la Transition. Le ministre Abdoul Kassim Ibrahim Fomba a salué une jeunesse prête à rapprocher les communautés, tandis que le président du CNJ, Sory Ibrahim Cissé, a exprimé la volonté des jeunes de contribuer activement à la stabilité du pays.
Six jours d''échanges et de formation
Atelier sur l''agenda Jeunesse, paix et sécurité, formation au développement conduit par les communautés, conférence sur l''engagement civique et le patriotisme, forum Jeunesse-Diaspora, panels régionaux sur les priorités de développement : la semaine a alterné réflexion et formation. Elle s''est aussi prolongée dans les régions, portée par les conseils régionaux de la jeunesse, comme à Nioro du Sahel dès le 12 août et à Bandiagara du 22 au 24 août.
La part de YouthConnekt Mali
YouthConnekt Mali a appuyé le CNJ dans l''organisation de la Semaine et mobilisé les jeunes de son réseau pour qu''ils prennent part aux activités.',
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
