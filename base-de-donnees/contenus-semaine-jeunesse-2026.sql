-- YouthConnekt Mali : la Semaine nationale de la jeunesse 2026 n'est pas une initiative du chapitre.
-- Elle devient un événement dans Actualités (participation de YouthConnekt Mali, notamment à la cérémonie d'ouverture).
-- L'image principale et les photos déjà ajoutées à l'ancienne initiative sont reprises, puis l'initiative est supprimée.
-- À importer dans phpMyAdmin. Sans risque si on l'importe deux fois.
SET NAMES utf8mb4;

INSERT INTO actualites (titre, slug, statut, publie_le, mis_en_avant, categorie, chapeau, contenu, image, date_evenement, date_fin, lieu, cree_le, modifie_le)
SELECT 'YouthConnekt Mali à la Semaine nationale de la jeunesse 2026', 'youthconnekt-mali-semaine-nationale-jeunesse-2026', 'publie', NOW(), 0, 'evenement', 'Du 11 au 16 août 2026, YouthConnekt Mali a pris part à la Semaine nationale de la jeunesse, de la cérémonie d''ouverture au CICB jusqu''aux activités de la semaine, aux côtés du Conseil national de la jeunesse.',
  'Couplée à la Journée internationale de la jeunesse du 12 août, la Semaine nationale de la jeunesse 2026 s''est tenue du 11 au 16 août sur le thème « Jeunesse, paix et sécurité : prévenir la désinformation, lutter contre la radicalisation et promouvoir la paix ». Portée par le Conseil national de la jeunesse du Mali (CNJ) et le ministère de la Jeunesse et des Sports, chargé de l''Instruction civique et de la Construction citoyenne, elle a mis en avant des jeunes acteurs de la paix et de la cohésion sociale.
YouthConnekt Mali à la cérémonie d''ouverture
Le mercredi 12 août, YouthConnekt Mali était présent au Centre international de conférences de Bamako pour la cérémonie de lancement, présidée par le ministre d''État chargé de la Réconciliation nationale et de la Cohésion sociale, le Général de Corps d''Armée Ismaël Wagué, au nom du Président de la Transition.
Le ministre Abdoul Kassim Ibrahim Fomba y a salué une jeunesse prête à rapprocher les communautés, tandis que le président du CNJ, Sory Ibrahim Cissé, a exprimé la volonté des jeunes de contribuer activement à la stabilité du pays.
Aux côtés du CNJ toute la semaine
Le chapitre a appuyé le CNJ dans l''organisation de la Semaine et mobilisé les jeunes de son réseau pour qu''ils prennent part à la cérémonie comme aux activités qui ont suivi.
Au programme : un atelier sur l''agenda Jeunesse, paix et sécurité, une formation au développement conduit par les communautés, une conférence sur l''engagement civique et le patriotisme, un forum Jeunesse-Diaspora et des panels régionaux sur les priorités de développement. La Semaine s''est aussi prolongée dans les régions, portée par les conseils régionaux de la jeunesse, comme à Nioro du Sahel dès le 12 août et à Bandiagara du 22 au 24 août.',
  COALESCE((SELECT image FROM initiatives WHERE slug = 'semaine-nationale-de-la-jeunesse-2026'), ''), '2026-08-11', '2026-08-16', 'CICB, Bamako', NOW(), NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM actualites WHERE slug = 'youthconnekt-mali-semaine-nationale-jeunesse-2026');

INSERT INTO photos_actualites (actualite_id, image, legende, ordre)
SELECT a.id, p.image, p.legende, p.ordre
FROM photos_initiatives p
JOIN initiatives i ON i.id = p.initiative_id AND i.slug = 'semaine-nationale-de-la-jeunesse-2026'
JOIN actualites a ON a.slug = 'youthconnekt-mali-semaine-nationale-jeunesse-2026'
WHERE NOT EXISTS (SELECT 1 FROM photos_actualites x WHERE x.actualite_id = a.id AND x.image = p.image);

-- L'actualité apparaît aussi sur la page de la communauté Leadership et citoyenneté
INSERT INTO actualite_communaute (actualite_id, communaute_id)
SELECT a.id, c.id FROM actualites a JOIN communautes c ON c.slug = 'leadership-et-citoyennete'
WHERE a.slug = 'youthconnekt-mali-semaine-nationale-jeunesse-2026'
  AND NOT EXISTS (SELECT 1 FROM actualite_communaute x WHERE x.actualite_id = a.id AND x.communaute_id = c.id);

DELETE FROM initiatives WHERE slug = 'semaine-nationale-de-la-jeunesse-2026';
