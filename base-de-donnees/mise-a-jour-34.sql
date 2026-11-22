-- YouthConnekt Mali, mise à jour 34 : messages lus / non lus, réponses envoyées depuis l'administration,
-- et journal des e-mails envoyés par le site. À importer dans phpMyAdmin (en ligne ET sur WAMP). Sans risque si on l'importe deux fois.
SET NAMES utf8mb4;

SET @existe = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'messages_contact' AND COLUMN_NAME = 'lu_le');
SET @sql = IF(@existe = 0, 'ALTER TABLE messages_contact ADD COLUMN lu_le DATETIME NULL AFTER traite', 'SELECT 1');
PREPARE etape FROM @sql; EXECUTE etape; DEALLOCATE PREPARE etape;
-- Les messages déjà traités comptent comme lus
UPDATE messages_contact SET lu_le = recu_le WHERE traite = 1 AND lu_le IS NULL;

CREATE TABLE IF NOT EXISTS reponses_messages (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  message_id INT UNSIGNED NOT NULL,
  admin_id INT UNSIGNED NULL,
  texte MEDIUMTEXT NOT NULL,
  envoyee TINYINT(1) NOT NULL DEFAULT 0,
  envoyee_le DATETIME NOT NULL,
  INDEX (message_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS emails_envoyes (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  destinataire VARCHAR(190) NOT NULL,
  objet VARCHAR(255) NOT NULL DEFAULT '',
  categorie VARCHAR(60) NOT NULL DEFAULT '',
  ok TINYINT(1) NOT NULL DEFAULT 1,
  erreur VARCHAR(300) NOT NULL DEFAULT '',
  mode VARCHAR(20) NOT NULL DEFAULT '',
  envoye_le DATETIME NOT NULL,
  INDEX (envoye_le)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
