-- YouthConnekt Mali, mise à jour 26 : une actualité ou un événement peut durer plusieurs jours (dernier jour facultatif).
SET NAMES utf8mb4;
ALTER TABLE actualites ADD COLUMN date_fin DATE NULL AFTER date_evenement;
