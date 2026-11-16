-- YouthConnekt Mali : clarification Initiatives / Actualités.
-- Restent des initiatives les activités menées par le chapitre (Bootcamp 2022, DONIYA-LAB).
-- L'École de la citoyenneté et la crise du carburant deviennent des actualités sur la participation de YouthConnekt Mali :
-- l'image principale et les photos déjà ajoutées sont reprises, puis l'ancienne initiative est supprimée.
-- À importer dans phpMyAdmin. Sans risque si on l'importe deux fois.
SET NAMES utf8mb4;

-- DONIYA-LAB : présenté comme une activité de YouthConnekt Mali
UPDATE initiatives SET resume = 'Vingt porteurs de projets, cinq jours de bootcamp et cinq lauréats : en juin 2026, YouthConnekt Mali a mené le DONIYA-LAB avec le ministère de la Jeunesse et des Sports et l''UNICEF, pour faire mûrir des solutions de jeunes au service de la santé communautaire.',
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

-- YouthConnekt Mali à l'École de la citoyenneté
INSERT INTO actualites (titre, slug, statut, publie_le, mis_en_avant, categorie, chapeau, contenu, image, date_evenement, date_fin, lieu, cree_le, modifie_le)
SELECT 'YouthConnekt Mali à l''École de la citoyenneté', 'youthconnekt-mali-ecole-de-la-citoyennete-2026', 'publie', NOW(), 0, 'actualite', 'En avril 2026, des intervenants de YouthConnekt Mali sont allés à la rencontre des 1 000 jeunes de la 5e cohorte de l''École de la citoyenneté, programme du ministère de la Jeunesse et des Sports, au Palais des Pionniers de Bamako.',
  'L''École de la citoyenneté est un programme du ministère de la Jeunesse et des Sports, chargé de l''Instruction civique et de la Construction citoyenne. Elle forme les jeunes au civisme, à la citoyenneté et au patriotisme, à travers des sessions de formation et des rencontres directes avec les décideurs.
Une 5e cohorte de 1 000 jeunes
Lancée le vendredi 17 avril 2026 au Palais des Pionniers par le ministre d''État chargé de la Réconciliation, de la Paix et de la Cohésion nationale, le Général de Corps d''Armée Ismaël Wagué, au nom du Premier ministre, la 5e cohorte a réuni 1 000 jeunes du district de Bamako, de la diaspora et des pays de l''Alliance des États du Sahel, ainsi que des jeunes déplacés internes.
Au fil de la formation, des membres du Gouvernement sont venus échanger directement avec eux. Le 18 avril, les ministres chargés des Transports et des Infrastructures et de l''Urbanisme ont ainsi animé un panel interactif, notamment sur la sécurité routière.
YouthConnekt Mali parmi les intervenants
YouthConnekt Mali a soutenu cette cohorte et y a pris la parole : des intervenants du chapitre sont venus échanger avec les participants, appelés à devenir à leur tour des relais de la citoyenneté, de la paix et de la cohésion sociale dans leurs communautés.',
  COALESCE((SELECT image FROM initiatives WHERE slug = 'ecole-de-la-citoyennete-5e-cohorte'), ''), '2026-04-17', NULL, 'Palais des Pionniers, Bamako', NOW(), NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM actualites WHERE slug = 'youthconnekt-mali-ecole-de-la-citoyennete-2026');

INSERT INTO photos_actualites (actualite_id, image, legende, ordre)
SELECT x.id, p.image, p.legende, p.ordre
FROM photos_initiatives p
JOIN initiatives i ON i.id = p.initiative_id AND i.slug = 'ecole-de-la-citoyennete-5e-cohorte'
JOIN actualites x ON x.slug = 'youthconnekt-mali-ecole-de-la-citoyennete-2026'
WHERE NOT EXISTS (SELECT 1 FROM photos_actualites y WHERE y.actualite_id = x.id AND y.image = p.image);

INSERT INTO actualite_communaute (actualite_id, communaute_id)
SELECT x.id, c.id FROM actualites x JOIN communautes c ON c.slug = 'leadership-et-citoyennete'
WHERE x.slug = 'youthconnekt-mali-ecole-de-la-citoyennete-2026'
  AND NOT EXISTS (SELECT 1 FROM actualite_communaute y WHERE y.actualite_id = x.id AND y.communaute_id = c.id);

DELETE FROM initiatives WHERE slug = 'ecole-de-la-citoyennete-5e-cohorte';

-- Crise du carburant : YouthConnekt Mali aux côtés des bénévoles du CNJ
INSERT INTO actualites (titre, slug, statut, publie_le, mis_en_avant, categorie, chapeau, contenu, image, date_evenement, date_fin, lieu, cree_le, modifie_le)
SELECT 'Crise du carburant : YouthConnekt Mali aux côtés des bénévoles du CNJ', 'crise-carburant-youthconnekt-mali-benevoles-cnj', 'publie', '2025-10-16 12:00:00', 0, 'actualite', 'À l''automne 2025, pendant la pénurie de carburant, les bénévoles du Conseil national de la jeunesse ont aidé à garder le calme dans les stations-service. YouthConnekt Mali a soutenu leur action, mobilisé des jeunes de son réseau et salué leur engagement.',
  'À partir de septembre 2025, le Mali a traversé une grave pénurie de carburant, ressentie à Bamako dès le début du mois d''octobre. Devant les stations encore approvisionnées, les files s''étiraient sur des centaines de mètres, de jour comme de nuit, et la tension montait vite.
Des bénévoles pour garder le calme
Le Conseil national de la jeunesse du Mali (CNJ) a alors déployé des jeunes bénévoles dans les stations-service pour organiser les files et maintenir l''ordre. Chaque jour, il publiait aussi la liste des stations qui disposaient de carburant : un geste simple, qui évitait aux usagers de tourner en ville et désengorgeait les points de vente.
Le soutien de YouthConnekt Mali
YouthConnekt Mali s''est tenu aux côtés du CNJ : le chapitre a soutenu l''initiative, mobilisé des jeunes de son réseau et félicité les bénévoles pour leur sens des responsabilités dans un moment difficile pour le pays.
Une mobilisation reconnue
Reçu en audience par le Président de la Transition le 16 octobre 2025, le président du CNJ, Sory Ibrahim Cissé, a présenté cet engagement de la jeunesse. Il l''a de nouveau cité parmi les grandes mobilisations de l''année lors des vœux du CNJ au chef de l''État, le 16 février 2026.',
  COALESCE((SELECT image FROM initiatives WHERE slug = 'cnj-mobilisation-crise-carburant'), ''), NULL, NULL, 'Bamako', NOW(), NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM actualites WHERE slug = 'crise-carburant-youthconnekt-mali-benevoles-cnj');

INSERT INTO photos_actualites (actualite_id, image, legende, ordre)
SELECT x.id, p.image, p.legende, p.ordre
FROM photos_initiatives p
JOIN initiatives i ON i.id = p.initiative_id AND i.slug = 'cnj-mobilisation-crise-carburant'
JOIN actualites x ON x.slug = 'crise-carburant-youthconnekt-mali-benevoles-cnj'
WHERE NOT EXISTS (SELECT 1 FROM photos_actualites y WHERE y.actualite_id = x.id AND y.image = p.image);

INSERT INTO actualite_communaute (actualite_id, communaute_id)
SELECT x.id, c.id FROM actualites x JOIN communautes c ON c.slug = 'leadership-et-citoyennete'
WHERE x.slug = 'crise-carburant-youthconnekt-mali-benevoles-cnj'
  AND NOT EXISTS (SELECT 1 FROM actualite_communaute y WHERE y.actualite_id = x.id AND y.communaute_id = c.id);

DELETE FROM initiatives WHERE slug = 'cnj-mobilisation-crise-carburant';
