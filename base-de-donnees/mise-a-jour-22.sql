-- Mise à jour 22 : logos du pied de page (sceau de l'État et PNUD) déposés depuis Paramètres
ALTER TABLE parametres
  ADD COLUMN logo_ministere VARCHAR(255) NULL AFTER image_accueil_3,
  ADD COLUMN logo_pnud VARCHAR(255) NULL AFTER logo_ministere;
