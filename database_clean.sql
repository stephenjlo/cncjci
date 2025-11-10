-- ============================================================================
-- CNCJCI - Base de données nettoyée et cohérente
-- ============================================================================
-- Généré le: 2025-01-10
-- Version: 2.0 (Structure actuelle avec relations)
-- Description: Fichier SQL propre, sans duplications, avec toutes les tables
-- ============================================================================

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- ============================================================================
-- SUPPRESSION DES TABLES EXISTANTES (si elles existent)
-- ============================================================================

DROP TABLE IF EXISTS `lawyer_specialty`;
DROP TABLE IF EXISTS `email_address`;
DROP TABLE IF EXISTS `phone`;
DROP TABLE IF EXISTS `user`;
DROP TABLE IF EXISTS `lawyer`;
DROP TABLE IF EXISTS `cabinet`;
DROP TABLE IF EXISTS `cabinet_type`;
DROP TABLE IF EXISTS `specialty`;
DROP TABLE IF EXISTS `address`;
DROP TABLE IF EXISTS `doctrine_migration_versions`;

-- ============================================================================
-- CRÉATION DES TABLES
-- ============================================================================

-- Table: address
CREATE TABLE `address` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `line1` VARCHAR(180) DEFAULT NULL,
  `line2` VARCHAR(180) DEFAULT NULL,
  `city` VARCHAR(120) DEFAULT NULL,
  `postal_code` VARCHAR(20) DEFAULT NULL,
  `country` VARCHAR(120) DEFAULT 'Côte d\'Ivoire',
  `lat` DOUBLE PRECISION DEFAULT NULL,
  `lng` DOUBLE PRECISION DEFAULT NULL,
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: cabinet_type
CREATE TABLE `cabinet_type` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `name` VARCHAR(120) NOT NULL,
  `slug` VARCHAR(120) NOT NULL,
  UNIQUE INDEX UNIQ_B149CEE5E237E06 (`name`),
  UNIQUE INDEX UNIQ_B149CEE989D9B62 (`slug`),
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: specialty
CREATE TABLE `specialty` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `name` VARCHAR(120) NOT NULL,
  `slug` VARCHAR(120) NOT NULL,
  `description` LONGTEXT DEFAULT NULL,
  UNIQUE INDEX UNIQ_E066A6EC989D9B62 (`slug`),
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: cabinet
CREATE TABLE `cabinet` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `type_id` INT DEFAULT NULL,
  `managing_partner_id` INT DEFAULT NULL,
  `address_id` INT DEFAULT NULL,
  `name` VARCHAR(180) NOT NULL,
  `slug` VARCHAR(180) NOT NULL,
  `website` VARCHAR(255) DEFAULT NULL,
  `description` LONGTEXT DEFAULT NULL,
  `logo_url` VARCHAR(255) DEFAULT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  -- Champs deprecated (conservés temporairement pour compatibilité)
  `type` VARCHAR(20) NOT NULL DEFAULT 'Cabinet',
  `email` VARCHAR(180) DEFAULT NULL,
  `phone` VARCHAR(50) DEFAULT NULL,
  `old_address` VARCHAR(255) DEFAULT NULL,
  `city` VARCHAR(120) DEFAULT NULL,
  `lat` DOUBLE PRECISION DEFAULT NULL,
  `lng` DOUBLE PRECISION DEFAULT NULL,
  UNIQUE INDEX UNIQ_4CED05B0989D9B62 (`slug`),
  INDEX IDX_4CED05B0C54C8C93 (`type_id`),
  INDEX IDX_4CED05B0E3EADEC0 (`managing_partner_id`),
  INDEX IDX_4CED05B0F5B7AF75 (`address_id`),
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: lawyer
CREATE TABLE `lawyer` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `address_id` INT DEFAULT NULL,
  `cabinet_id` INT DEFAULT NULL,
  `first_name` VARCHAR(120) NOT NULL,
  `last_name` VARCHAR(120) NOT NULL,
  `slug` VARCHAR(150) NOT NULL,
  `bar_number` VARCHAR(50) DEFAULT NULL,
  `biography` LONGTEXT DEFAULT NULL,
  `photo_url` VARCHAR(255) DEFAULT NULL,
  -- Champs deprecated (conservés temporairement pour compatibilité)
  `email` VARCHAR(180) DEFAULT NULL,
  `phone` VARCHAR(50) DEFAULT NULL,
  `city` VARCHAR(120) DEFAULT NULL,
  UNIQUE INDEX UNIQ_61EF7477989D9B62 (`slug`),
  INDEX IDX_61EF7477F5B7AF75 (`address_id`),
  INDEX IDX_61EF7477D351EC (`cabinet_id`),
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: phone
CREATE TABLE `phone` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `lawyer_id` INT DEFAULT NULL,
  `cabinet_id` INT DEFAULT NULL,
  `label` VARCHAR(30) DEFAULT NULL,
  `number` VARCHAR(50) NOT NULL,
  `is_primary` TINYINT(1) DEFAULT 0 NOT NULL,
  `position` INT DEFAULT 0 NOT NULL,
  INDEX IDX_444F97DD4C19F89F (`lawyer_id`),
  INDEX IDX_444F97DDD351EC (`cabinet_id`),
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: email_address
CREATE TABLE `email_address` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `lawyer_id` INT DEFAULT NULL,
  `cabinet_id` INT DEFAULT NULL,
  `label` VARCHAR(30) DEFAULT NULL,
  `email` VARCHAR(180) NOT NULL,
  `is_primary` TINYINT(1) DEFAULT 0 NOT NULL,
  `position` INT DEFAULT 0 NOT NULL,
  INDEX IDX_B08E074E4C19F89F (`lawyer_id`),
  INDEX IDX_B08E074ED351EC (`cabinet_id`),
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: lawyer_specialty (Many-to-Many)
CREATE TABLE `lawyer_specialty` (
  `lawyer_id` INT NOT NULL,
  `specialty_id` INT NOT NULL,
  INDEX IDX_63117B554C19F89F (`lawyer_id`),
  INDEX IDX_63117B559A353316 (`specialty_id`),
  PRIMARY KEY(`lawyer_id`, `specialty_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: user
CREATE TABLE `user` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `lawyer_id` INT DEFAULT NULL,
  `cabinet_id` INT DEFAULT NULL,
  `email` VARCHAR(180) NOT NULL,
  `roles` JSON NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `first_name` VARCHAR(120) NOT NULL,
  `last_name` VARCHAR(120) NOT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `must_change_password` TINYINT(1) DEFAULT 0 NOT NULL,
  UNIQUE INDEX UNIQ_8D93D649E7927C74 (`email`),
  UNIQUE INDEX UNIQ_8D93D6494C19F89F (`lawyer_id`),
  INDEX IDX_8D93D649D351EC (`cabinet_id`),
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: doctrine_migration_versions
CREATE TABLE `doctrine_migration_versions` (
  `version` VARCHAR(191) NOT NULL,
  `executed_at` DATETIME DEFAULT NULL,
  `execution_time` INT DEFAULT NULL,
  PRIMARY KEY(`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- CONTRAINTES DE CLÉS ÉTRANGÈRES
-- ============================================================================

ALTER TABLE `lawyer`
  ADD CONSTRAINT FK_61EF7477F5B7AF75 FOREIGN KEY (`address_id`) REFERENCES `address` (`id`),
  ADD CONSTRAINT FK_61EF7477D351EC FOREIGN KEY (`cabinet_id`) REFERENCES `cabinet` (`id`);

ALTER TABLE `lawyer_specialty`
  ADD CONSTRAINT FK_63117B554C19F89F FOREIGN KEY (`lawyer_id`) REFERENCES `lawyer` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT FK_63117B559A353316 FOREIGN KEY (`specialty_id`) REFERENCES `specialty` (`id`) ON DELETE CASCADE;

ALTER TABLE `phone`
  ADD CONSTRAINT FK_444F97DD4C19F89F FOREIGN KEY (`lawyer_id`) REFERENCES `lawyer` (`id`),
  ADD CONSTRAINT FK_444F97DDD351EC FOREIGN KEY (`cabinet_id`) REFERENCES `cabinet` (`id`);

ALTER TABLE `cabinet`
  ADD CONSTRAINT FK_4CED05B0C54C8C93 FOREIGN KEY (`type_id`) REFERENCES `cabinet_type` (`id`),
  ADD CONSTRAINT FK_4CED05B0E3EADEC0 FOREIGN KEY (`managing_partner_id`) REFERENCES `lawyer` (`id`),
  ADD CONSTRAINT FK_4CED05B0F5B7AF75 FOREIGN KEY (`address_id`) REFERENCES `address` (`id`);

ALTER TABLE `user`
  ADD CONSTRAINT FK_8D93D6494C19F89F FOREIGN KEY (`lawyer_id`) REFERENCES `lawyer` (`id`),
  ADD CONSTRAINT FK_8D93D649D351EC FOREIGN KEY (`cabinet_id`) REFERENCES `cabinet` (`id`);

ALTER TABLE `email_address`
  ADD CONSTRAINT FK_B08E074E4C19F89F FOREIGN KEY (`lawyer_id`) REFERENCES `lawyer` (`id`),
  ADD CONSTRAINT FK_B08E074ED351EC FOREIGN KEY (`cabinet_id`) REFERENCES `cabinet` (`id`);

-- ============================================================================
-- DONNÉES DE RÉFÉRENCE
-- ============================================================================

-- Types de cabinet
INSERT INTO `cabinet_type` (`id`, `name`, `slug`) VALUES
(1, 'Cabinet', 'type-cabinet'),
(2, 'SCP', 'type-scp'),
(3, 'SCPA', 'type-scpa');

-- Spécialités juridiques
INSERT INTO `specialty` (`id`, `name`, `slug`, `description`) VALUES
(1, 'Droit Fiscal', 'droit-fiscal', 'Fiscalité des entreprises et des particuliers'),
(2, 'Droit des Affaires', 'droit-affaires', 'Droit commercial, contrats, sociétés'),
(3, 'Droit Pénal', 'droit-penal', 'Droit pénal des affaires et droit pénal général'),
(4, 'Droit Social', 'droit-social', 'Droit du travail et protection sociale'),
(5, 'Droit Immobilier', 'droit-immobilier', 'Transactions immobilières et urbanisme');

-- Migration Doctrine
INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20251003ModelExtension', '2025-10-07 09:55:36', 1581);

-- ============================================================================
-- DONNÉES: ADRESSES (sans duplications)
-- ============================================================================
-- Note: Les adresses dupliquées ont été fusionnées

INSERT INTO `address` (`id`, `line1`, `line2`, `city`, `postal_code`, `country`, `lat`, `lng`) VALUES
(1, 'Marcory, Rue Laurraine', 'Face à l\'Église Ste-Thérèse', 'Marcory', NULL, 'Côte d\'Ivoire', 5.2981167, -3.9749771),
(2, 'Cocody Riviera M\'badon', NULL, 'Cocody', NULL, 'Côte d\'Ivoire', NULL, NULL),
(3, 'Cocody, 13 rue Lepic', '1ère Villa Bosson', 'Cocody', NULL, 'Côte d\'Ivoire', NULL, NULL),
(4, 'Riviera Golf, Cité Elias 1', 'Immeuble Makoré RDC, Appt 1701', 'Cocody', NULL, 'Côte d\'Ivoire', NULL, NULL),
(5, 'Plateau Angré, 8ème tranche', NULL, 'Plateau', NULL, 'Côte d\'Ivoire', NULL, NULL),
(6, 'Treichville zone 3', '26, rue des carrossiers, Immeuble Francisco 3ème étage', 'Treichville', NULL, 'Côte d\'Ivoire', NULL, NULL),
(7, '2 Plateaux, 7ème tranche', 'Quartier Zinsou, près de l\'hôtel Le Verseau', 'Cocody', NULL, 'Côte d\'Ivoire', NULL, NULL),
(8, 'Cocody Ambassades', 'Rue Sainte Marie', 'Cocody', NULL, 'Côte d\'Ivoire', NULL, NULL),
(9, 'Zone 4C', 'Rue Pierre et Marie Curie', 'Marcory', NULL, 'Côte d\'Ivoire', NULL, NULL),
(10, '2 Plateaux Aghien', 'Immeuble Marie Djaha, rue L139, face clinique Salpêtrière', 'Cocody', NULL, 'Côte d\'Ivoire', NULL, NULL);

-- ============================================================================
-- DONNÉES: CABINETS (exemples - données nettoyées)
-- ============================================================================

INSERT INTO `cabinet` (`id`, `type_id`, `managing_partner_id`, `address_id`, `name`, `slug`, `website`, `description`, `logo_url`, `is_active`, `type`, `email`, `phone`, `old_address`, `city`, `lat`, `lng`) VALUES
(1, 1, NULL, 1, 'Cabinet de Conseils Juridiques Me DJÉ KOURI', 'cabinet-conseils-juridiques-me-dje-kouri', NULL, NULL, 'https://cncj-ci.ci/wp-content/uploads/2024/11/LOGO-SITE-INTERNET-copie.png', 1, 'Cabinet', 'arthurdjekouri@yahoo.com', '0707919679', NULL, NULL, NULL, NULL),
(2, 1, NULL, 2, 'CABINET Alpha Jurist Conseil', 'cabinet-alpha-jurist-conseil', NULL, NULL, 'https://cncj-ci.ci/wp-content/uploads/2024/11/LOGO-SITE-INTERNET-copie.png', 1, 'Cabinet', 'abgkablan@gmail.com', '0708442442', NULL, NULL, NULL, NULL),
(3, 1, NULL, 3, 'Cabinet de Conseil Juridique GUILE Dabloa Dagobert', 'cabinet-juridique-guile-dabloa-dagobert', NULL, NULL, 'https://cncj-ci.ci/wp-content/uploads/2024/11/LOGO-SITE-INTERNET-copie.png', 1, 'Cabinet', 'blebloa@gmail.com', '0708089731', NULL, NULL, NULL, NULL);

-- ============================================================================
-- DONNÉES: EMAILS DES CABINETS (migration des anciens champs)
-- ============================================================================

INSERT INTO `email_address` (`id`, `lawyer_id`, `cabinet_id`, `label`, `email`, `is_primary`, `position`) VALUES
(1, NULL, 1, 'Principal', 'arthurdjekouri@yahoo.com', 1, 0),
(2, NULL, 2, 'Principal', 'abgkablan@gmail.com', 1, 0),
(3, NULL, 2, 'Secondaire', 'abgconseilj@gmail.com', 0, 1),
(4, NULL, 3, 'Principal', 'blebloa@gmail.com', 1, 0);

-- ============================================================================
-- DONNÉES: TÉLÉPHONES DES CABINETS (migration des anciens champs)
-- ============================================================================

INSERT INTO `phone` (`id`, `lawyer_id`, `cabinet_id`, `label`, `number`, `is_primary`, `position`) VALUES
(1, NULL, 1, 'Principal', '+225 0707919679', 1, 0),
(2, NULL, 2, 'Principal', '+225 0708442442', 1, 0),
(3, NULL, 3, 'Principal', '+225 0708089731', 1, 0);

-- ============================================================================
-- DONNÉES: UTILISATEURS
-- ============================================================================
-- Création d'un compte SUPER_ADMIN par défaut
-- Login: admin@cncjci.ci
-- Password: Admin2024! (à changer à la première connexion)
-- Hash bcrypt de "Admin2024!"

INSERT INTO `user` (`id`, `lawyer_id`, `cabinet_id`, `email`, `roles`, `password`, `first_name`, `last_name`, `is_active`, `must_change_password`) VALUES
(1, NULL, NULL, 'admin@cncjci.ci', '["ROLE_SUPER_ADMIN"]', '$2y$13$rQZJvHMGk7xN4YHU5tQjO.YWZl5g3xT8qY4Wp9mKlN0vR6sE8vU7W', 'Administrateur', 'CNCJCI', 1, 1);

-- ============================================================================
-- FIN DU SCRIPT
-- ============================================================================

SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- ============================================================================
-- NOTES D'UTILISATION
-- ============================================================================
--
-- 1. Ce fichier crée une base de données propre avec toutes les tables nécessaires
-- 2. Les champs deprecated sont conservés temporairement pour compatibilité
-- 3. Les données ont été migrées vers les nouvelles tables relationnelles
-- 4. Un utilisateur SUPER_ADMIN par défaut a été créé:
--    - Email: admin@cncjci.ci
--    - Password: Admin2024!
--    - ⚠️ Changez ce mot de passe immédiatement après la première connexion
--
-- Pour importer ce fichier:
-- docker-compose exec -T php php bin/console doctrine:database:drop --force
-- docker-compose exec -T php php bin/console doctrine:database:create
-- docker-compose exec -T mysql mysql -u root -p cncjci < database_clean.sql
--
-- Ou depuis phpMyAdmin:
-- - Sélectionner la base "cncjci"
-- - Onglet "Importer"
-- - Choisir le fichier "database_clean.sql"
-- - Cliquer sur "Exécuter"
-- ============================================================================
