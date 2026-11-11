-- Mise à jour 21 : photos du premier écran de l'accueil choisies depuis Paramètres
ALTER TABLE parametres
  ADD COLUMN image_accueil_1 VARCHAR(255) NULL AFTER presentation_accueil,
  ADD COLUMN image_accueil_2 VARCHAR(255) NULL AFTER image_accueil_1,
  ADD COLUMN image_accueil_3 VARCHAR(255) NULL AFTER image_accueil_2;

-- Dates précises des initiatives : pour « Ajouter à mon agenda » et le billet
ALTER TABLE initiatives
  ADD COLUMN date_debut DATE NULL AFTER date_libelle,
  ADD COLUMN date_fin DATE NULL AFTER date_debut;
UPDATE initiatives SET date_debut = '2027-02-15', date_fin = '2027-02-28' WHERE slug = 'exemple-caravane-de-la-citoyennete';
UPDATE initiatives SET date_debut = '2027-03-20', date_fin = '2027-03-20' WHERE slug = 'exemple-journee-youthconnekt-mali';
