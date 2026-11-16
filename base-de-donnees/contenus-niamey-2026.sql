-- YouthConnekt Mali : l'intervention de Housseyni Diallo et le stand du Mali intégrés à l'article
-- « Le Mali au Forum YouthConnekt Sahel 2026 à Niamey » (avant la partie « Et maintenant »).
-- L'actualité séparée sur Housseyni Diallo passe en brouillon (gardée en attente, non visible sur le site).
-- À importer dans phpMyAdmin. Sans risque si on l'importe deux fois.
SET NAMES utf8mb4;

SET @section = _utf8mb4'La voix des jeunes Maliens dans les panels
Le 11 août, Housseyni Diallo, de U-Report Mali, est intervenu comme panéliste lors de la session consacrée à l''économie numérique et à l''innovation ancrées dans les territoires, aux côtés du ministre nigérien de la Communication et des Nouvelles Technologies de l''Information, sous la modération du Dr Rachid Awal, de l''UNFPA. Il y a présenté la campagne de sensibilisation et de vaccination contre la polio menée en novembre 2024 dans la région de Sikasso, exemple d''action communautaire appuyée par le numérique, portée par et pour les jeunes. Selon les données présentées, le taux de vaccination a augmenté de 3 %, plusieurs milliers de rumeurs ont été repérées et traitées, et le Mali n''a enregistré aucun cas de polio l''année suivante.
Il a plaidé pour que la valorisation des initiatives de jeunes s''accompagne d''un soutien concret : compétences, mentorat, information, mise en réseau, financement et accès aux marchés.
Le stand du Mali au Marché du Sahel
Au Marché du Sahel (Sahel Marketplace), le stand du Mali a présenté des produits « Made in Mali » ainsi que des réalisations et initiatives portées ou soutenues par le ministère de la Jeunesse et des Sports (CNPV, SNV, APEJ), le PNUD et l''UNICEF. Il a permis de nombreuses rencontres avec des jeunes, des institutions, des organisations internationales, des innovateurs et des partenaires de toute la région.' COLLATE utf8mb4_unicode_ci;
SET @saut = CONVERT(CHAR(10) USING utf8mb4) COLLATE utf8mb4_unicode_ci;
SET @repere = CONCAT(@saut, 'Et maintenant' COLLATE utf8mb4_unicode_ci, @saut);

UPDATE actualites
SET contenu = IF(LOCATE(@repere, contenu) > 0,
    REPLACE(contenu, @repere, CONCAT(@saut, @section, @repere)),
    CONCAT(contenu, @saut, @section)),
  modifie_le = NOW()
WHERE slug = 'mali-forum-youthconnekt-sahel-2026-niamey' AND contenu NOT LIKE '%Housseyni Diallo%';

UPDATE actualites SET statut = 'brouillon', modifie_le = NOW()
WHERE slug = 'housseyni-diallo-forum-youthconnekt-sahel-2026';
