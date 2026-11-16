-- YouthConnekt Mali, mise à jour 31 : photos de la participation du Mali à YouthConnekt Africa et YouthConnekt Sahel
-- (Administration > Site > Photos Africa et Sahel ; affichées sur la page À propos).
-- À importer dans phpMyAdmin après mise-a-jour-30.sql.
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS albums_reseau (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  slug VARCHAR(40) NOT NULL,
  nom VARCHAR(120) NOT NULL,
  titre VARCHAR(120) NOT NULL DEFAULT '',
  PRIMARY KEY (id),
  UNIQUE KEY uq_albums_reseau_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS photos_reseau (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  album_id INT UNSIGNED NOT NULL,
  image VARCHAR(255) NOT NULL,
  legende VARCHAR(200) NOT NULL DEFAULT '',
  ordre SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  KEY fk_photos_reseau (album_id),
  CONSTRAINT fk_photos_reseau FOREIGN KEY (album_id) REFERENCES albums_reseau (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO albums_reseau (slug, nom, titre) VALUES
  ('africa', 'YouthConnekt Africa', 'Le Mali aux Sommets YouthConnekt Africa'),
  ('sahel', 'YouthConnekt Sahel', 'Le Mali aux Forums YouthConnekt Sahel');
