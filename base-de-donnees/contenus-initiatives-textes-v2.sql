-- YouthConnekt Mali : textes des 5 initiatives réécrits (rôle de YouthConnekt Mali précisé pour chacune).
-- À importer dans phpMyAdmin APRÈS contenus-novembre-2026.sql. Les photos, l'état et les autres champs ne sont pas touchés.
SET NAMES utf8mb4;

UPDATE initiatives SET titre = 'Bootcamp YouthConnekt Mali 2022', resume = 'Deux jours de formation intensive pour 26 jeunes entrepreneurs de Bamako, Mopti et Ségou : structurer son projet, maîtriser ses chiffres et apprendre des autres. Un bootcamp organisé avec le PNUD Mali et l''incubateur DoniLab.',
  contenu = 'En 2022, YouthConnekt Mali a réuni 26 jeunes entrepreneurs pour un bootcamp pensé comme un accélérateur : vingt jeunes accompagnés par le programme PNUD Mali-TEF et six jeunes de la société civile venus de Mopti et de Ségou. Pendant deux jours, avec l''incubateur DoniLab, ils ont travaillé sur ce qui fait tenir une entreprise : un projet clair, des comptes maîtrisés et un réseau sur lequel s''appuyer.
Jour 1 : donner une structure à son idée
Après l''ouverture officielle, marquée par les discours du Dr Doudou Ben Bechir Niang, représentant du ministère de la Jeunesse et des Sports, et des représentants du PNUD et de DoniLab, les participants sont passés à la pratique. Atelier de structuration de projet, prise en main du Business Model Canvas, travaux en groupe : chacun est reparti avec une vision plus nette de son modèle. Abdoulaye Sow, chargé des programmes à DoniLab, a accompagné la journée, nourrie de retours d''expérience.
Jour 2 : gérer, compter, négocier
La deuxième journée a porté sur la gestion d''entreprise et la comptabilité, avec Youssouf Coulibaly. Aly Kouma et Abdoulaye Sow, de DoniLab, ont ensuite placé les entrepreneurs face à des cas pratiques de négociation commerciale et de gestion de trésorerie.
Temps fort du bootcamp, l''atelier de codéveloppement a permis à chacun de soumettre ses difficultés au groupe et de repartir avec des conseils concrets, venus d''entrepreneurs qui vivent les mêmes réalités.',
  modifie_le = NOW()
WHERE slug = 'bootcamp-youthconnekt-mali-2022';

UPDATE initiatives SET titre = 'Crise du carburant : la jeunesse mobilisée aux stations-service', resume = 'À l''automne 2025, quand les files d''attente s''étiraient devant les stations-service, les bénévoles du Conseil national de la jeunesse ont aidé à garder le calme et à informer les usagers. YouthConnekt Mali a soutenu cette mobilisation et salué leur engagement.',
  contenu = 'À partir de septembre 2025, le Mali a traversé une grave pénurie de carburant, ressentie à Bamako dès le début du mois d''octobre. Devant les stations encore approvisionnées, les files s''étiraient sur des centaines de mètres, de jour comme de nuit, et la tension montait vite.
Des bénévoles pour garder le calme
Le Conseil national de la jeunesse du Mali (CNJ) a alors déployé des jeunes bénévoles dans les stations-service pour organiser les files et maintenir l''ordre. Chaque jour, il publiait aussi la liste des stations qui disposaient de carburant : un geste simple, qui évitait aux usagers de tourner en ville et désengorgeait les points de vente.
Le soutien de YouthConnekt Mali
YouthConnekt Mali s''est tenu aux côtés du CNJ : le chapitre a soutenu l''initiative, mobilisé des jeunes de son réseau et félicité les bénévoles pour leur sens des responsabilités dans un moment difficile pour le pays.
Une mobilisation reconnue
Reçu en audience par le Président de la Transition le 16 octobre 2025, le président du CNJ, Sory Ibrahim Cissé, a présenté cet engagement de la jeunesse. Il l''a de nouveau cité parmi les grandes mobilisations de l''année lors des vœux du CNJ au chef de l''État, le 16 février 2026.',
  modifie_le = NOW()
WHERE slug = 'cnj-mobilisation-crise-carburant';

UPDATE initiatives SET titre = 'École de la citoyenneté, 5e cohorte', resume = 'En avril 2026, 1 000 jeunes ont rejoint au Palais des Pionniers la 5e cohorte de l''École de la citoyenneté, programme du ministère de la Jeunesse et des Sports. YouthConnekt Mali a soutenu la cohorte et y est intervenu auprès des participants.',
  contenu = 'Former des jeunes qui connaissent leurs droits, leurs devoirs et les institutions de leur pays : c''est l''ambition de l''École de la citoyenneté, programme du ministère de la Jeunesse et des Sports, chargé de l''Instruction civique et de la Construction citoyenne. Civisme, citoyenneté et patriotisme y sont abordés à travers des sessions de formation et des rencontres directes avec les décideurs.
Une 5e cohorte de 1 000 jeunes
Le vendredi 17 avril 2026, le ministre d''État chargé de la Réconciliation, de la Paix et de la Cohésion nationale, le Général de Corps d''Armée Ismaël Wagué, a lancé la 5e cohorte au Palais des Pionniers de Bamako, au nom du Premier ministre. Elle a réuni 1 000 jeunes du district de Bamako, de la diaspora et des pays de l''Alliance des États du Sahel, ainsi que des jeunes déplacés internes.
Face aux membres du Gouvernement
Tout au long de la formation, des ministres sont venus échanger directement avec les participants. Le 18 avril, les ministres chargés des Transports et des Infrastructures et de l''Urbanisme ont ainsi animé un panel interactif, notamment sur la sécurité routière.
L''apport de YouthConnekt Mali
YouthConnekt Mali a apporté son soutien à cette cohorte et y a pris la parole : des intervenants du chapitre sont venus à la rencontre des participants pour échanger avec eux.
Des relais dans les communautés
À l''issue du programme, les participants repartent avec une mission : porter les valeurs de citoyenneté, de paix et de cohésion sociale dans leurs communautés.',
  modifie_le = NOW()
WHERE slug = 'ecole-de-la-citoyennete-5e-cohorte';

UPDATE initiatives SET titre = 'DONIYA-LAB, l''innovation des jeunes au service de la santé communautaire', resume = 'Vingt porteurs de projets, cinq jours de bootcamp et cinq lauréats : en juin 2026, le DONIYA-LAB a fait mûrir des solutions de jeunes pour la santé et le développement des communautés, avec YouthConnekt Mali à l''organisation et à l''accompagnement.',
  contenu = 'Porté par le ministère de la Jeunesse et des Sports, chargé de l''Instruction civique et de la Construction citoyenne, et par l''UNICEF, avec l''appui de la Fondation Mastercard, le DONIYA-LAB repère les idées de jeunes qui répondent aux besoins des communautés, notamment en matière de santé, et les aide à devenir de vrais projets.
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
  modifie_le = NOW()
WHERE slug = 'doniya-lab-innovation-sante-communautaire';

UPDATE initiatives SET titre = 'Semaine nationale de la jeunesse 2026', resume = 'Du 11 au 16 août 2026, la Semaine nationale de la jeunesse a réuni les jeunes de Bamako et des régions autour de la paix, de la sécurité et de la lutte contre la désinformation. YouthConnekt Mali a contribué à son organisation et à la mobilisation des jeunes.',
  contenu = 'Couplée à la Journée internationale de la jeunesse du 12 août, la Semaine nationale de la jeunesse 2026 s''est tenue du 11 au 16 août, sur un thème qui parle aux jeunes Maliens d''aujourd''hui : « Jeunesse, paix et sécurité : prévenir la désinformation, lutter contre la radicalisation et promouvoir la paix ». Portée par le Conseil national de la jeunesse du Mali (CNJ) et le ministère de la Jeunesse et des Sports, chargé de l''Instruction civique et de la Construction citoyenne, elle a mis en avant des jeunes acteurs de la paix et de la cohésion sociale.
Lancement officiel au CICB
Le mercredi 12 août, le ministre d''État chargé de la Réconciliation nationale et de la Cohésion sociale, le Général de Corps d''Armée Ismaël Wagué, a lancé les activités au Centre international de conférences de Bamako, au nom du Président de la Transition. Le ministre Abdoul Kassim Ibrahim Fomba a salué une jeunesse prête à rapprocher les communautés, tandis que le président du CNJ, Sory Ibrahim Cissé, a exprimé la volonté des jeunes de contribuer activement à la stabilité du pays.
Six jours d''échanges et de formation
Atelier sur l''agenda Jeunesse, paix et sécurité, formation au développement conduit par les communautés, conférence sur l''engagement civique et le patriotisme, forum Jeunesse-Diaspora, panels régionaux sur les priorités de développement : la semaine a alterné réflexion et formation. Elle s''est aussi prolongée dans les régions, portée par les conseils régionaux de la jeunesse, comme à Nioro du Sahel dès le 12 août et à Bandiagara du 22 au 24 août.
La part de YouthConnekt Mali
YouthConnekt Mali a appuyé le CNJ dans l''organisation de la Semaine et mobilisé les jeunes de son réseau pour qu''ils prennent part aux activités.',
  modifie_le = NOW()
WHERE slug = 'semaine-nationale-de-la-jeunesse-2026';
