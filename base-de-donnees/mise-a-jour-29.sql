-- YouthConnekt Mali, mise à jour 29 : vidéo bilan de l'année (Administration > Site > Bilan de l'année).
-- À importer dans phpMyAdmin après mise-a-jour-28.sql.
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS bilans_annuels (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  annee SMALLINT UNSIGNED NOT NULL,
  titre VARCHAR(150) NOT NULL DEFAULT '',
  texte VARCHAR(300) NOT NULL DEFAULT '',
  video VARCHAR(255) NOT NULL DEFAULT '',
  lien_video VARCHAR(255) NOT NULL DEFAULT '',
  image VARCHAR(255) NOT NULL DEFAULT '',
  publie TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  UNIQUE KEY uq_bilans_annee (annee)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
