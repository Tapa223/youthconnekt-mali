-- YouthConnekt Mali, mise à jour 27 : annuaire des organisations et « Soutenir le chapitre ».
SET NAMES utf8mb4;

-- Fiche d'organisation plus complète, annuaire public, badge vérifié, mention de partenaire
ALTER TABLE profils_organisations
  ADD COLUMN taille VARCHAR(20) NULL AFTER secteur,
  ADD COLUMN offre_jeunes TEXT NULL AFTER presentation,
  ADD COLUMN dans_annuaire TINYINT(1) NOT NULL DEFAULT 1 AFTER visible,
  ADD COLUMN verifiee TINYINT(1) NOT NULL DEFAULT 0 AFTER dans_annuaire,
  ADD COLUMN partenaire_chapitre TINYINT(1) NOT NULL DEFAULT 0 AFTER verifiee;

-- Propositions de soutien envoyées par les membres et les organisations depuis leur espace
CREATE TABLE IF NOT EXISTS soutiens (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  utilisateur_id INT UNSIGNED NOT NULL,
  formes VARCHAR(255) NOT NULL DEFAULT '',
  message TEXT NOT NULL,
  statut VARCHAR(20) NOT NULL DEFAULT 'nouveau',
  note_equipe TEXT NULL,
  cree_le DATETIME NOT NULL,
  modifie_le DATETIME NULL,
  KEY idx_soutiens_statut (statut),
  CONSTRAINT fk_soutiens_utilisateur FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
