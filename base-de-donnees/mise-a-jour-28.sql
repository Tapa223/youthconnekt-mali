-- YouthConnekt Mali, mise à jour 28 : précision « Autre » dans le formulaire de contact, et quelques corrections de contenu.
SET NAMES utf8mb4;

-- Formulaire de contact : quand la personne choisit « Autre », elle précise qui elle est
ALTER TABLE messages_contact ADD COLUMN profil_autre VARCHAR(120) NULL AFTER profil;

-- Pas encore de numéro : le numéro d'exemple disparaît (il ne s'affiche plus nulle part tant que le champ est vide)
UPDATE parametres SET telephone = '' WHERE telephone LIKE '%00 00 00 00%';

-- Partenaires : « PNUD » faisait doublon avec « PNUD Mali »
DELETE FROM partenaires WHERE nom = 'PNUD'
  AND (SELECT n FROM (SELECT COUNT(*) AS n FROM partenaires WHERE nom = 'PNUD Mali') AS x) > 0;

-- Chiffres clés : trois éditions du Forum YouthConnekt Sahel avec le Mali (Niamey 2026 ajoutée)
UPDATE chiffres_cles SET valeur = '3', libelle = 'éditions du Forum YouthConnekt Sahel avec le Mali (Dakar 2021, N''Djamena 2025, Niamey 2026)'
  WHERE libelle LIKE 'éditions du Forum YouthConnekt Sahel%';
