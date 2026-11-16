-- YouthConnekt Mali : École de la citoyenneté et Semaine nationale de la jeunesse.
-- Le chapitre a accompagné et soutenu ces initiatives, sans prise de parole : textes corrigés en ce sens.
-- À importer dans phpMyAdmin. Les photos et les autres champs ne sont pas touchés.
SET NAMES utf8mb4;

UPDATE actualites SET titre = 'École de la citoyenneté : YouthConnekt Mali aux côtés de la 5e cohorte', chapeau = 'En avril 2026, YouthConnekt Mali a accompagné et soutenu la 5e cohorte de l''École de la citoyenneté, programme du ministère de la Jeunesse et des Sports qui a réuni 1 000 jeunes au Palais des Pionniers de Bamako.',
  contenu = 'L''École de la citoyenneté est un programme du ministère de la Jeunesse et des Sports, chargé de l''Instruction civique et de la Construction citoyenne. Elle forme les jeunes au civisme, à la citoyenneté et au patriotisme, à travers des sessions de formation et des rencontres directes avec les décideurs.
Une 5e cohorte de 1 000 jeunes
Lancée le vendredi 17 avril 2026 au Palais des Pionniers par le ministre d''État chargé de la Réconciliation, de la Paix et de la Cohésion nationale, le Général de Corps d''Armée Ismaël Wagué, au nom du Premier ministre, la 5e cohorte a réuni 1 000 jeunes du district de Bamako, de la diaspora et des pays de l''Alliance des États du Sahel, ainsi que des jeunes déplacés internes.
Au fil de la formation, des membres du Gouvernement sont venus échanger directement avec eux. Le 18 avril, les ministres chargés des Transports et des Infrastructures et de l''Urbanisme ont ainsi animé un panel interactif, notamment sur la sécurité routière.
Le soutien de YouthConnekt Mali
YouthConnekt Mali a accompagné cette cohorte et soutient l''École de la citoyenneté, qui prépare les jeunes à devenir des relais de la citoyenneté, de la paix et de la cohésion sociale dans leurs communautés.',
  modifie_le = NOW()
WHERE slug = 'youthconnekt-mali-ecole-de-la-citoyennete-2026';

UPDATE actualites SET chapeau = 'Du 11 au 16 août 2026, YouthConnekt Mali a accompagné et soutenu le Conseil national de la jeunesse lors de la Semaine nationale de la jeunesse, de la cérémonie d''ouverture au CICB jusqu''aux activités de la semaine.',
  contenu = 'Couplée à la Journée internationale de la jeunesse du 12 août, la Semaine nationale de la jeunesse 2026 s''est tenue du 11 au 16 août sur le thème « Jeunesse, paix et sécurité : prévenir la désinformation, lutter contre la radicalisation et promouvoir la paix ». Portée par le Conseil national de la jeunesse du Mali (CNJ) et le ministère de la Jeunesse et des Sports, chargé de l''Instruction civique et de la Construction citoyenne, elle a mis en avant des jeunes acteurs de la paix et de la cohésion sociale.
YouthConnekt Mali à la cérémonie d''ouverture
Le mercredi 12 août, YouthConnekt Mali était présent au Centre international de conférences de Bamako pour la cérémonie de lancement, présidée par le ministre d''État chargé de la Réconciliation nationale et de la Cohésion sociale, le Général de Corps d''Armée Ismaël Wagué, au nom du Président de la Transition.
Le ministre Abdoul Kassim Ibrahim Fomba y a salué une jeunesse prête à rapprocher les communautés, tandis que le président du CNJ, Sory Ibrahim Cissé, a exprimé la volonté des jeunes de contribuer activement à la stabilité du pays.
Aux côtés du CNJ toute la semaine
Le chapitre a soutenu le CNJ dans l''organisation de la Semaine et mobilisé les jeunes de son réseau pour qu''ils prennent part à la cérémonie comme aux activités qui ont suivi.
Au programme : un atelier sur l''agenda Jeunesse, paix et sécurité, une formation au développement conduit par les communautés, une conférence sur l''engagement civique et le patriotisme, un forum Jeunesse-Diaspora et des panels régionaux sur les priorités de développement. La Semaine s''est aussi prolongée dans les régions, portée par les conseils régionaux de la jeunesse, comme à Nioro du Sahel dès le 12 août et à Bandiagara du 22 au 24 août.',
  modifie_le = NOW()
WHERE slug = 'youthconnekt-mali-semaine-nationale-jeunesse-2026';
