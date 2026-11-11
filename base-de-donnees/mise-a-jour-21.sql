-- Mise à jour 21 : photos du premier écran de l'accueil choisies depuis Paramètres
ALTER TABLE parametres
  ADD COLUMN image_accueil_1 VARCHAR(255) NULL AFTER presentation_accueil,
  ADD COLUMN image_accueil_2 VARCHAR(255) NULL AFTER image_accueil_1,
  ADD COLUMN image_accueil_3 VARCHAR(255) NULL AFTER image_accueil_2;
