-- =====================================================================
-- YouthConnekt Mali : mise à jour 4 (3 novembre 2026)
-- À importer une fois dans phpMyAdmin, sur la base « youthconnekt ».
-- Remet les trois témoignages de la maquette sur l'accueil
-- (seulement si aucun témoignage n'existe encore).
-- =====================================================================

USE youthconnekt;

-- Témoignages de la maquette (exemples, modifiables dans Administration > Témoignages)
INSERT INTO temoignages (nom, role, texte, ordre, publie)
SELECT * FROM (
  SELECT 'Aminata K.' AS nom, 'Étudiante en agronomie, Sikasso · Jeune' AS role, 'Le Bootcamp m''a aidée à transformer mon idée de transformation de mangues en vrai projet. J''y ai aussi trouvé des mentors.' AS texte, 1 AS ordre, 1 AS publie
  UNION ALL SELECT 'Moussa T.', 'Jeune entrepreneur, Ségou · Jeune', 'J''ai découvert ici un appel à candidatures que je n''aurais jamais vu ailleurs. Aujourd''hui, mon projet est accompagné.', 2, 1
  UNION ALL SELECT 'Organisation partenaire', 'Responsable de programmes · Partenaire', 'Travailler avec YouthConnekt Mali nous permet de toucher des jeunes motivés partout dans le pays.', 3, 1
) AS exemples
WHERE NOT EXISTS (SELECT 1 FROM temoignages);
