-- Adminer 5.4.1 MySQL 8.0.44 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `address`;
CREATE TABLE `address` (
  `id` int NOT NULL AUTO_INCREMENT,
  `line1` varchar(180) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `line2` varchar(180) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lat` double DEFAULT NULL,
  `lng` double DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `address` (`id`, `line1`, `line2`, `city`, `postal_code`, `country`, `lat`, `lng`) VALUES
(1,	'Marcory, Rue Laurraine',	'Face à l\'Église Ste-Thérèse',	'Marcory',	NULL,	'Côte d\'Ivoire',	5.2981167,	-3.9749771),
(2,	'Cocody Riviera M\'badon',	'Riviéra 6',	'Cocody',	NULL,	'Côte d\'Ivoire',	5.331585,	-3.941902),
(3,	'Cocody, 13 rue Lepic',	'1ère Villa Bosson',	'Cocody',	NULL,	'Côte d\'Ivoire',	NULL,	NULL),
(4,	'Riviera Golf, Cité Elias 1',	'Immeuble Makoré RDC, Appt 1701',	'Cocody',	NULL,	'Côte d\'Ivoire',	NULL,	NULL),
(5,	'Plateau Angré, 8ème tranche',	NULL,	'Plateau',	NULL,	'Côte d\'Ivoire',	NULL,	NULL),
(6,	'Treichville zone 3',	'26, rue des carrossiers, Immeuble Francisco 3ème étage',	'Treichville',	NULL,	'Côte d\'Ivoire',	NULL,	NULL),
(7,	'2 Plateaux, 7ème tranche',	'Quartier Zinsou, près de l\'hôtel Le Verseau',	'Cocody',	NULL,	'Côte d\'Ivoire',	NULL,	NULL),
(8,	'Cocody Ambassades',	'Rue Sainte Marie',	'Cocody',	NULL,	'Côte d\'Ivoire',	NULL,	NULL),
(9,	'Zone 4C',	'Rue Pierre et Marie Curie',	'Marcory',	NULL,	'Côte d\'Ivoire',	NULL,	NULL),
(10,	'2 Plateaux Aghien',	'Immeuble Marie Djaha, rue L139, face clinique Salpêtrière',	'Cocody',	NULL,	'Côte d\'Ivoire',	NULL,	NULL),
(11,	'Route d\'Azuretti',	'Quartier Phare',	'Commune de Grand-Bassam',	NULL,	'Côte d\'Ivoire',	5.215622,	-3.826117),
(12,	'Rue du Château d\'Eau',	'Habitat',	'Bingerville',	NULL,	'Côte d\'Ivoire',	6.820007,	-5.277603),
(13,	'Rue L130',	'Angré',	'Bingerville',	NULL,	'Côte d\'Ivoire',	5.352979,	-3.881689),
(14,	'Route de Béoumi',	NULL,	'Bouaké',	NULL,	'Côte d\'Ivoire',	7.67641,	-5.077236),
(15,	'Avenue Jean-Paul II',	'Le Plateau',	'Abidjan',	NULL,	'Côte d\'Ivoire',	5.332973,	-4.020064),
(16,	'Route de la Côtière',	NULL,	'Dabou',	NULL,	'Côte d\'Ivoire',	5.323999,	-4.372298),
(17,	'Route d\'Azuretti',	'Quartier Phare',	'Commune de Grand-Bassam',	NULL,	'Côte d\'Ivoire',	5.222706,	-3.825769);

DROP TABLE IF EXISTS `cabinet`;
CREATE TABLE `cabinet` (
  `id` int NOT NULL AUTO_INCREMENT,
  `type_id` int DEFAULT NULL,
  `managing_partner_id` int DEFAULT NULL,
  `address_id` int DEFAULT NULL,
  `name` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `logo_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `old_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lat` double DEFAULT NULL,
  `lng` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_4CED05B0989D9B62` (`slug`),
  KEY `IDX_4CED05B0C54C8C93` (`type_id`),
  KEY `IDX_4CED05B0E3EADEC0` (`managing_partner_id`),
  KEY `IDX_4CED05B0F5B7AF75` (`address_id`),
  CONSTRAINT `FK_4CED05B0C54C8C93` FOREIGN KEY (`type_id`) REFERENCES `cabinet_type` (`id`),
  CONSTRAINT `FK_4CED05B0E3EADEC0` FOREIGN KEY (`managing_partner_id`) REFERENCES `lawyer` (`id`),
  CONSTRAINT `FK_4CED05B0F5B7AF75` FOREIGN KEY (`address_id`) REFERENCES `address` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `cabinet` (`id`, `type_id`, `managing_partner_id`, `address_id`, `name`, `slug`, `website`, `description`, `logo_url`, `is_active`, `type`, `email`, `phone`, `old_address`, `city`, `lat`, `lng`) VALUES
(1,	1,	16,	1,	'Cabinet de Conseils Juridiques Me DJÉ KOURI',	'cabinet-conseils-juridiques-me-dje-kouri',	NULL,	NULL,	'https://cncj-ci.ci/wp-content/uploads/2024/11/LOGO-SITE-INTERNET-copie.png',	1,	'Cabinet',	'arthurdjekouri@yahoo.com',	'0707919679',	NULL,	NULL,	NULL,	NULL),
(2,	1,	13,	2,	'CABINET Alpha Jurist Conseil',	'cabinet-alpha-jurist-conseil',	NULL,	NULL,	'http://localhost:9002/uploads/cabinets/3238369-44129-6912d6139098d.jpg',	1,	'Cabinet',	'abgkablan@gmail.com',	'0708442442',	NULL,	NULL,	NULL,	NULL),
(3,	1,	15,	3,	'Cabinet de Conseil Juridique GUILE Dabloa Dagobert',	'cabinet-juridique-guile-dabloa-dagobert',	NULL,	NULL,	'http://localhost:9002/uploads/cabinets/1159312-3003-6912ea7a3276a.jpg',	1,	'Cabinet',	'blebloa@gmail.com',	'0708089731',	NULL,	NULL,	NULL,	NULL),
(4,	1,	13,	11,	'YAPI & ASSOCIE',	'yapi-and-associe',	'http://stephenjlo.xyz',	',opokpo\r\n,k,;p,m',	'https://cncj-ci.ci/wp-content/uploads/2024/11/LOGO-SITE-INTERNET-copie.png',	1,	'Cabinet',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL);

DROP TABLE IF EXISTS `cabinet_type`;
CREATE TABLE `cabinet_type` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_B149CEE5E237E06` (`name`),
  UNIQUE KEY `UNIQ_B149CEE989D9B62` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `cabinet_type` (`id`, `name`, `slug`) VALUES
(1,	'Cabinet',	'type-cabinet'),
(2,	'SCP',	'type-scp'),
(3,	'SCPA',	'type-scpa');

DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20251003ModelExtension',	'2025-10-07 09:55:36',	1581),
('DoctrineMigrations\\Version20251106205414',	NULL,	NULL),
('DoctrineMigrations\\Version20251107003000',	NULL,	NULL),
('DoctrineMigrations\\Version20251110130319',	NULL,	NULL);

DROP TABLE IF EXISTS `email_address`;
CREATE TABLE `email_address` (
  `id` int NOT NULL AUTO_INCREMENT,
  `lawyer_id` int DEFAULT NULL,
  `cabinet_id` int DEFAULT NULL,
  `label` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `position` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `IDX_B08E074E4C19F89F` (`lawyer_id`),
  KEY `IDX_B08E074ED351EC` (`cabinet_id`),
  CONSTRAINT `FK_B08E074E4C19F89F` FOREIGN KEY (`lawyer_id`) REFERENCES `lawyer` (`id`),
  CONSTRAINT `FK_B08E074ED351EC` FOREIGN KEY (`cabinet_id`) REFERENCES `cabinet` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `email_address` (`id`, `lawyer_id`, `cabinet_id`, `label`, `email`, `is_primary`, `position`) VALUES
(1,	NULL,	1,	'Contact',	'arthurdjekouri@yahoo.com',	1,	0),
(2,	NULL,	2,	'Contact',	'abgkablan@gmail.com',	1,	0),
(3,	NULL,	2,	'Contact',	'abgconseilj@gmail.com',	0,	1),
(4,	NULL,	3,	'Contact',	'blebloa@gmail.coms',	1,	0),
(5,	NULL,	4,	'Contact',	'toto@toto.fr',	1,	0),
(6,	15,	NULL,	'Contact',	'stephu.jl@yahoo.fr',	1,	0),
(7,	16,	NULL,	'Professionnel',	'contact@aaaaar.frssss',	1,	0),
(8,	17,	NULL,	'Professionnel',	'test@cabiet.ci',	1,	0),
(9,	18,	NULL,	'Professionnel',	'stephy.jlo@gmail.com',	1,	0),
(10,	14,	NULL,	'Professionnel',	'email@toto.fr',	1,	0);

DROP TABLE IF EXISTS `image`;
CREATE TABLE `image` (
  `id` int NOT NULL AUTO_INCREMENT,
  `entity_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_id` int NOT NULL,
  `filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `filepath` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `mime_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_size` int NOT NULL,
  `position` int NOT NULL DEFAULT '0',
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `uploaded_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `idx_entity` (`entity_type`,`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `lawyer`;
CREATE TABLE `lawyer` (
  `id` int NOT NULL AUTO_INCREMENT,
  `address_id` int DEFAULT NULL,
  `cabinet_id` int DEFAULT NULL,
  `first_name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bar_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `biography` longtext COLLATE utf8mb4_unicode_ci,
  `photo_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_61EF7477989D9B62` (`slug`),
  KEY `IDX_61EF7477F5B7AF75` (`address_id`),
  KEY `IDX_61EF7477D351EC` (`cabinet_id`),
  CONSTRAINT `FK_61EF7477D351EC` FOREIGN KEY (`cabinet_id`) REFERENCES `cabinet` (`id`),
  CONSTRAINT `FK_61EF7477F5B7AF75` FOREIGN KEY (`address_id`) REFERENCES `address` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `lawyer` (`id`, `address_id`, `cabinet_id`, `first_name`, `last_name`, `slug`, `bar_number`, `biography`, `photo_url`, `email`, `phone`, `city`) VALUES
(13,	12,	2,	'JEAN KOUASSI',	'KOFFI',	'jean-kouassi-koffi',	'LJK-985-JNNK',	'N°1 des cabinets',	NULL,	NULL,	NULL,	NULL),
(14,	13,	NULL,	'Cristophe',	'KONE',	'cristophe-kone',	'ci-bar-oh4+89+',	'Excellent',	'http://localhost:9002/uploads/lawyers/Gemini-Generated-Image-1uqj6t1uqj6t1uqj-6912d648a90dc.png',	NULL,	NULL,	NULL),
(15,	14,	3,	'MOBIO JEAN LOUIS',	'YAPI',	'mobio-jean-louis-yapi',	'CI-BAR-0002',	'Diplome de la FAC UFHB',	'http://localhost:9002/uploads/lawyers/3238369-44129-6912e4866fc55.jpg',	NULL,	NULL,	NULL),
(16,	15,	1,	'ZZZZZZ',	'ZZZZZ',	'zzzzzz-zzzzz',	'Z-5',	'JOĴ',	'http://localhost:9002/uploads/lawyers/bit-ly-44BnSBF-1-6912ec8c3538b.png',	NULL,	NULL,	NULL),
(17,	16,	1,	'ahiba',	'jean',	'ahiba-jean',	NULL,	NULL,	'http://localhost:9002/uploads/lawyers/Gemini-Generated-Image-m3qz0lm3qz0lm3qz-6912f36cb07e8.png',	NULL,	NULL,	NULL),
(18,	17,	1,	'STEPHENJLO',	'HALE',	'stephenjlo-hale',	'VUYBIUN',	'OK',	'http://localhost:9002/uploads/lawyers/3238369-44129-6912f4c160298.jpg',	NULL,	NULL,	NULL);

DROP TABLE IF EXISTS `lawyer_specialty`;
CREATE TABLE `lawyer_specialty` (
  `lawyer_id` int NOT NULL,
  `specialty_id` int NOT NULL,
  PRIMARY KEY (`lawyer_id`,`specialty_id`),
  KEY `IDX_63117B554C19F89F` (`lawyer_id`),
  KEY `IDX_63117B559A353316` (`specialty_id`),
  CONSTRAINT `FK_63117B554C19F89F` FOREIGN KEY (`lawyer_id`) REFERENCES `lawyer` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_63117B559A353316` FOREIGN KEY (`specialty_id`) REFERENCES `specialty` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `lawyer_specialty` (`lawyer_id`, `specialty_id`) VALUES
(13,	1),
(13,	3),
(14,	2),
(14,	4),
(15,	2),
(15,	5),
(16,	2),
(16,	4),
(17,	3),
(17,	5),
(18,	5);

DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `news` (`id`, `title`, `description`, `url`, `is_active`, `created_at`, `updated_at`) VALUES
(1,	'Journée mondiale des avocat',	'ok',	'http://www.gdghd',	1,	'2025-11-11 05:48:37',	'2025-11-11 05:48:48');

DROP TABLE IF EXISTS `phone`;
CREATE TABLE `phone` (
  `id` int NOT NULL AUTO_INCREMENT,
  `lawyer_id` int DEFAULT NULL,
  `cabinet_id` int DEFAULT NULL,
  `label` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `position` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `IDX_444F97DD4C19F89F` (`lawyer_id`),
  KEY `IDX_444F97DDD351EC` (`cabinet_id`),
  CONSTRAINT `FK_444F97DD4C19F89F` FOREIGN KEY (`lawyer_id`) REFERENCES `lawyer` (`id`),
  CONSTRAINT `FK_444F97DDD351EC` FOREIGN KEY (`cabinet_id`) REFERENCES `cabinet` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `phone` (`id`, `lawyer_id`, `cabinet_id`, `label`, `number`, `is_primary`, `position`) VALUES
(1,	NULL,	1,	'Standard',	'+225 0707919679',	1,	0),
(2,	NULL,	2,	'Standard',	'+225 0708442442',	1,	0),
(3,	NULL,	3,	'Standard',	'+225 0700000001',	1,	0),
(4,	NULL,	4,	'Standard',	'+225 07 09 45 60 86',	1,	0),
(5,	15,	NULL,	'Standard',	'+33 7 55 4455 00',	1,	0),
(6,	16,	NULL,	'Bureau',	'+5158 8551',	1,	0),
(7,	17,	NULL,	'Bureau',	'+88 555 566 55',	1,	0),
(8,	18,	NULL,	'Bureau',	'+225 07 09 45 60 86',	1,	0),
(9,	14,	NULL,	'Bureau',	'++255 66',	1,	0),
(10,	NULL,	2,	'Standard',	'+225 0500029898',	0,	1);

DROP TABLE IF EXISTS `specialty`;
CREATE TABLE `specialty` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_E066A6EC989D9B62` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `specialty` (`id`, `name`, `slug`, `description`) VALUES
(1,	'Droit Fiscal',	'droit-fiscal',	'Fiscalité des entreprises et des particuliers'),
(2,	'Droit des Affaires',	'droit-affaires',	'Droit commercial, contrats, sociétés'),
(3,	'Droit Pénal',	'droit-penal',	'Droit pénal des affaires et droit pénal général'),
(4,	'Droit Social',	'droit-social',	'Droit du travail et protection sociale'),
(5,	'Droit Immobilier',	'droit-immobilier',	'Transactions immobilières et urbanisme');

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `lawyer_id` int DEFAULT NULL,
  `cabinet_id` int DEFAULT NULL,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `must_change_password` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`),
  UNIQUE KEY `UNIQ_8D93D6494C19F89F` (`lawyer_id`),
  KEY `IDX_8D93D649D351EC` (`cabinet_id`),
  CONSTRAINT `FK_8D93D6494C19F89F` FOREIGN KEY (`lawyer_id`) REFERENCES `lawyer` (`id`),
  CONSTRAINT `FK_8D93D649D351EC` FOREIGN KEY (`cabinet_id`) REFERENCES `cabinet` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `user` (`id`, `lawyer_id`, `cabinet_id`, `email`, `roles`, `password`, `first_name`, `last_name`, `is_active`, `must_change_password`) VALUES
(1,	NULL,	NULL,	'admin@cncjci.ci',	'[\"ROLE_SUPER_ADMIN\"]',	'$2y$13$rQZJvHMGk7xN4YHU5tQjO.YWZl5g3xT8qY4Wp9mKlN0vR6sE8vU7W',	'Administrateur',	'CNCJCI',	1,	1),
(2,	15,	NULL,	'admin@example.com',	'[\"ROLE_SUPER_ADMIN\"]',	'$2y$13$wN8Or6AqwNkcdUGW76pFp.Nj625AhxS0lT31TjfYOVEM2nXWXS7l2',	'Mobio Jlo',	'Yapi',	1,	0),
(3,	16,	1,	'contact@aaaaar.fr',	'[\"ROLE_LAWYER\", \"ROLE_USER\", \"ROLE_RESPO_CABINET\"]',	'$2y$13$y6zDnoLMDXt7KzYt3TOj/edkFy3/d9c3rj10wYzA4Q94/y2HCFwra',	'ZZZZZZ',	'ZZZZZ',	1,	0),
(4,	17,	1,	'test@cabiet.ci',	'[\"ROLE_LAWYER\"]',	'$2y$13$ahzyQSnWfxAiMtnkoRxrqOrGa97E.LH6PzwN5412pdSFP0306zghS',	'ahiba',	'jean',	1,	1),
(5,	18,	1,	'stephy.jlo@gmail.com',	'[\"ROLE_LAWYER\"]',	'$2y$13$2oHQJuRz5r37KZViuN7LDO52jRbYhUzQ.98Ho3FWeMqQc463kz/pa',	'STEPHENJLO',	'HALE',	1,	1);

-- 2025-11-11 09:59:18 UTC
