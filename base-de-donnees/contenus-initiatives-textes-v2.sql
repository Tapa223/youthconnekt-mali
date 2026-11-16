-- YouthConnekt Mali : textes des initiatives réécrits (rôle de YouthConnekt Mali précisé).
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

UPDATE initiatives SET titre = 'DONIYA-LAB, l''innovation des jeunes au service de la santé communautaire', resume = 'Vingt porteurs de projets, cinq jours de bootcamp et cinq lauréats : en juin 2026, YouthConnekt Mali a mené le DONIYA-LAB avec le ministère de la Jeunesse et des Sports et l''UNICEF, pour faire mûrir des solutions de jeunes au service de la santé communautaire.',
  contenu = 'Le DONIYA-LAB est une activité menée par YouthConnekt Mali avec le ministère de la Jeunesse et des Sports, chargé de l''Instruction civique et de la Construction citoyenne, et l''UNICEF, avec l''appui de la Fondation Mastercard. Il repère les idées de jeunes qui répondent aux besoins des communautés, notamment en matière de santé, et les aide à devenir de vrais projets.
De 450 candidatures à 20 projets
L''appel à projets a suscité plus de 450 candidatures dans les régions de Kayes, Nioro, Kita, Sikasso, Bougouni, Koutiala et Koulikoro, ainsi que dans le district sanitaire de Bamako. Vingt projets ont été retenus pour la phase nationale, en juin 2026, au Palais des Pionniers de Bamako.
Cinq jours pour passer de l''idée au projet
Le bootcamp a misé sur la pratique : diagnostic de chaque projet, travail en groupe, coaching par des experts, puis simulations de pitch face à des professionnels. Jour après jour, les idées se sont précisées jusqu''à devenir des projets structurés, crédibles et défendables. Le témoignage d''une entrepreneure ayant réussi a donné aux participants un repère concret, et beaucoup de motivation.
Cinq lauréats récompensés
Devant un jury multisectoriel, cinq projets se sont distingués par leur caractère innovant, leur faisabilité et leur impact : trois dans la transformation agroalimentaire (farine enrichie, conservation et vente de fruits et légumes), deux dans l''élevage (ferme avicole et coopérative apicole). À la clôture, en présence du ministre de la Jeunesse et des Sports et du représentant de l''UNICEF au Mali, tous les participants ont reçu une attestation et les lauréats ont été récompensés par des ordinateurs.
Le rôle de YouthConnekt Mali
De l''appel à projets à la clôture, le chapitre a été présent à chaque étape : il a relayé l''appel dans son réseau pour mobiliser les candidats, organisé le bootcamp avec ses partenaires et assuré la couverture de l''événement. Des membres du chapitre ont aussi accompagné les porteurs de projets comme mentors ou membres du jury.
Et après
Les recommandations prévoient un suivi technique des lauréats avec l''APEJ et le ministère de l''Entrepreneuriat national, de l''Emploi et de la Formation professionnelle, la création d''un réseau des anciens du DONIYA-LAB et, à terme, l''extension du programme à d''autres régions.',
  modifie_le = NOW()
WHERE slug = 'doniya-lab-innovation-sante-communautaire';
