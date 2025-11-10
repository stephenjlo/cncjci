-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 06 nov. 2025 à 18:34
-- Version du serveur : 11.8.3-MariaDB-log
-- Version de PHP : 7.2.34

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `u443003029_api`
--

-- --------------------------------------------------------

--
-- Structure de la table `address`
--

CREATE TABLE `address` (
  `id` int(11) NOT NULL,
  `line1` varchar(180) DEFAULT NULL,
  `line2` varchar(180) DEFAULT NULL,
  `city` varchar(120) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `country` varchar(120) DEFAULT NULL,
  `lat` double DEFAULT NULL,
  `lng` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `address`
--

INSERT INTO `address` (`id`, `line1`, `line2`, `city`, `postal_code`, `country`, `lat`, `lng`) VALUES
(2, 'Cocody Riviéra, M’badon', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(3, 'Abidjan Cocody,13 rue Lepic, 1ère Villa Bosson', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(4, 'Cabinet SCP-MOSSO, OHIN Conseil & Associés, Abidjan Riviera golf, Cité Elias 1, Immeuble Makoré RDC, Appt 1701', NULL, NULL, NULL, 'Côte d’Ivoire', NULL, NULL),
(5, '2 Plateau Angré,8ème tranche', NULL, 'Plateau', NULL, 'Côte d’Ivoire', NULL, NULL),
(6, 'Treichville zone 3, 26, rue des carrossiers Immeuble Francisco 3éme étage', NULL, NULL, NULL, 'Côte d’Ivoire', NULL, NULL),
(7, 'Abidjan 2 Plateaux,7ème tranche, quartier zinsou, non loin de l’hôtel le verseau', NULL, 'Plateau', NULL, 'Côte d’Ivoire', NULL, NULL),
(8, 'ADNA LAW Firm, rue Sainte Marie Cocody Ambassade', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(9, 'CIJ-Conseil, Cabinet de Me KRA Jacqueline Macory zone 4C, rue Pierre et Marie Curie', NULL, NULL, NULL, 'Côte d’Ivoire', NULL, NULL),
(10, '2 Plateau Aghien, immeuble Marie Djaha, rue L139, en face de la clinique Salpetrière', NULL, 'Plateau', NULL, 'Côte d’Ivoire', NULL, NULL),
(11, 'Cabinet NEC Consulting, Abidjan Cocody, Riviera les jardins', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(12, 'Bld de Marseille, Blétry immeuble Bleue cub, 3è étage porte A, face à la résidence Yacé', NULL, NULL, NULL, 'Côte d’Ivoire', NULL, NULL),
(13, 'Abidjan 2 Plateaux 7è tranche', NULL, 'Plateau', NULL, 'Côte d’Ivoire', NULL, NULL),
(14, 'Abidjan, Marcory groupement foncier', NULL, 'Marcory', NULL, 'Côte d’Ivoire', NULL, NULL),
(15, 'Cocody cité Prestige 1 Lot 31', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(16, 'SCPCJ Callaentis-Ekac « Associés », Abidjan riviera Palmeraie, non loin de Quick market, 2è étage P.6', NULL, NULL, NULL, 'Côte d’Ivoire', NULL, NULL),
(17, 'Cabinet BAHIE Conseil Abidjan Cocody riviera Palmeraie, rond-point ADO', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(18, 'Deloitte Côte d’Ivoire boulevard Hassan II, Abidjan Cocody', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(19, 'SCOGES Conseil (Zone 4 rue clément Ader)', NULL, NULL, NULL, 'Côte d’Ivoire', NULL, NULL),
(20, 'Cocody Faya, après le magasin bon prix', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(21, 'Cabinet PWC, Abidjan Cocody, Immeuble ITC, bâtiment D 3éme étage ; Angle rue booker washington, boulevard Hassan 2', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(22, 'Cocody Angré, 9è tranche, villa lot 477 ilot 22', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(23, 'Cocody 2 Plateaux vallon rue j75', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(24, 'Cocody , Cité des Arts en face de l’entrée principale de la cité BAD', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(25, 'Abidjan Cocody Angré, zone Bessikoi, non loin du nouveau CHU, Résidence Harlène Anayah, 1er étage, Appt n°6', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(26, 'Plateau, immeuble les anciens combattants, 1er étage, alignement 1er Arrondissement et nouveau bâtiment du Trésor public', NULL, 'Plateau', NULL, 'Côte d’Ivoire', NULL, NULL),
(27, 'Cocody cité des Arts villa n°1 face portail cité BAD', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(28, 'Cocody cité des Arts en face de l’entrée principale de la cité BAD', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(29, '2 Plateaux Vallons, 2è tranche, sortie rue des jardins, vers échangeur des 220 lgts, Immeuble contiguë à la station shell, 1er étage', NULL, 'Plateau', NULL, 'Côte d’Ivoire', NULL, NULL),
(30, 'Abidjan Cocody Faya', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(31, 'Cocody Riviera', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(32, 'Yamoussokro', NULL, NULL, NULL, 'Côte d’Ivoire', NULL, NULL),
(33, 'Riviéra Golf, les Caddies Tour Driver 1er étage Appartement 663', NULL, NULL, NULL, 'Côte d’Ivoire', NULL, NULL),
(34, 'Cocody,2 plateaux vallon, 2è Tranche', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(35, 'Abidjan Cocody Angré Château, Résidence Willy porte E1', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(36, 'Abidjan', NULL, NULL, NULL, 'Côte d’Ivoire', NULL, NULL),
(37, 'Abidjan Cocody 2 Plateaux rue k64, en face de la pharmacie des Grâces', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(38, 'Abidjan Cocody Vallon, rue des jardins, immeuble SAYEGH', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(39, 'Cabinet Fiduciaire Afrique de l’Ouest immeuble SIMO Treichville zone 2', NULL, NULL, NULL, 'Côte d’Ivoire', NULL, NULL),
(40, 'Abidjan 2 Plateaux, carrefour Las Palmas', NULL, 'Plateau', NULL, 'Côte d’Ivoire', NULL, NULL),
(41, 'Abidjan Cocody Angré, Pharmacie les Arcades', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(42, 'Abidjan Cocody, Val doyen, rue A30 face à UCAO, cabinet Orrick RCI', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(43, 'Cocody Angré, 7è tranche carrefour Bluetooth', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(44, 'Abidjan Plateau, immeuble Crozet sous-sol', NULL, 'Plateau', NULL, 'Côte d’Ivoire', NULL, NULL),
(45, 'Treichville zone 3,26, Rue des carrossiers immeuble Francisco 3ème étage', NULL, NULL, NULL, 'Côte d’Ivoire', NULL, NULL),
(46, 'Cabinet PWC, Abidjan Cocody, immeuble ITC, bât. D 3è étage Angle rue booker Washington, boulevard Hassan', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(47, 'Abidjan Marcory, zone 4', NULL, 'Marcory', NULL, 'Côte d’Ivoire', NULL, NULL),
(48, 'Abidjan, Cocody Val doyen, rue A30 face à UCAO, cabinet Orrick RCI', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(49, 'Abidjan Cocody, en face du siège PDCI immeuble SHAKESPEAR au 1er étage', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(50, 'Abidjan Plateau, immeuble Regus XXL', NULL, 'Plateau', NULL, 'Côte d’Ivoire', NULL, NULL),
(51, 'Treichville rue 12, imm. New York Shopping 1er', NULL, NULL, NULL, 'Côte d’Ivoire', NULL, NULL),
(52, 'Abidjan Plateau, immeuble les harmonies 5ème étage, Appartement 62, Escalier M2', NULL, 'Plateau', NULL, 'Côte d’Ivoire', NULL, NULL),
(53, 'Abidjan Marcory Sicogi, Espace la Madone', NULL, 'Marcory', NULL, 'Côte d’Ivoire', NULL, NULL),
(54, 'Plateau avenue Dr Crozet, immeuble Abidjan XL, 6ème étage, porte 610', NULL, 'Plateau', NULL, 'Côte d’Ivoire', NULL, NULL),
(55, 'Cocody Angré djibi, cité Arc-en-ciel', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(56, 'Abidjan Plateau', NULL, 'Plateau', NULL, 'Côte d’Ivoire', NULL, NULL),
(57, 'Riviera Bonoumin Zinsou 2, rue 180 résidence Tonian, RDC porte A6', NULL, NULL, NULL, 'Côte d’Ivoire', NULL, NULL),
(58, 'Grand-Bassam, quartier Mockeyville lot 26 ilot 76C', NULL, NULL, NULL, 'Côte d’Ivoire', NULL, NULL),
(59, 'Riviera Palmeraie rue Ministre immeuble PAX appartement C1', NULL, NULL, NULL, 'Côte d’Ivoire', NULL, NULL),
(60, 'Abidjan Cocody Riviera Golf, immeuble SAMBA, RDC', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(61, 'Cocody Angré château, Résidence Maëlle 4è étage', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(62, 'FlYTH Fiscal et Juridique. Abidjan Plateau, boulevard carde. Les Harmonies, 6è étage', NULL, 'Plateau', NULL, 'Côte d’Ivoire', NULL, NULL),
(63, 'Abidjan Plateau, Résidence du Banco', NULL, 'Plateau', NULL, 'Côte d’Ivoire', NULL, NULL),
(64, 'Abidjan Angré', NULL, NULL, NULL, 'Côte d’Ivoire', NULL, NULL),
(65, 'PKD conseil Abidjan Plateau, avenue Lamblin Résidence Eden 3è étage', NULL, 'Plateau', NULL, 'Côte d’Ivoire', NULL, NULL),
(66, 'Abidjan Cocody 2 Plateaux Angré, opération Bougainvilliers Appt.A02', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(67, 'Abidjan Cocody', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(68, 'Abidjan Yopougon Assonvon, immeuble Koné, 2è étage, près de l’église Baptiste', NULL, 'Yopougon', NULL, 'Côte d’Ivoire', NULL, NULL),
(69, 'Cabinet SCP-MOSSO, OHIN Conseils & Associés, Abidjan Riviera Golf, Cité Elias 1, immeuble Makoré RDC. Appt 1701', NULL, NULL, NULL, 'Côte d’Ivoire', NULL, NULL),
(70, 'Cocody route de Bingerville, Car.Abatta SIR, près de l’antenne de l’Université de Man, Imm, Résidence Holy Church, 1er Etage Porte 8', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(71, 'SCPCJ Callentis-Ekac « Associés» Abidjan Riviéra Palmeraie, non loin de Quick Market, 2è étage, P.A6', NULL, NULL, NULL, 'Côte d’Ivoire', NULL, NULL),
(72, 'Treichville, Rue des carrossiers zone 3', NULL, NULL, NULL, 'Côte d’Ivoire', NULL, NULL),
(73, 'Cabinet Grant Thornton Tax & légal CI, Abidjan plateau rue du commerce, résidences Nabil 1er étage', NULL, 'Plateau', NULL, 'Côte d’Ivoire', NULL, NULL),
(74, 'Cocody riviera 3, centre Saint Michel, rue E139', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(75, 'Abidjan Cocody 7ème tranche', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(76, 'Plateau, rue du commerce, immeuble NASSAR et GAGGAR, au dessus de la pharmacie Mazuet 3è étage, porte A23', NULL, 'Plateau', NULL, 'Côte d’Ivoire', NULL, NULL),
(77, 'Treichville, immeuble la Balance, 2è étage en face de la solibra', NULL, NULL, NULL, 'Côte d’Ivoire', NULL, NULL),
(78, 'FLYTH Fiscal et Juridique. Abidjan, Boulevard carde. Immeuble les Harmonies, 6è étage', NULL, NULL, NULL, 'Côte d’Ivoire', NULL, NULL),
(79, 'Plateau, Imm, les Anciens Combattants, 1er étage, alignement 1er Arrondissement et nouveau bâtiment du Trésor public', NULL, 'Plateau', NULL, 'Côte d’Ivoire', NULL, NULL),
(80, 'Abidjan Cocody, Saint Jean', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(81, 'Abidjan, Cocody Faya, résidence HOPE ? non loin de l’église Catholique Saint PAUL des lauriers 9', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(82, 'Riviéra Palmeraie', NULL, NULL, NULL, 'Côte d’Ivoire', NULL, NULL),
(83, 'Cabinet P2A,Abidjan 2 Plateaux Vallon, Carrefour Ambassade du Ghana, Immeuble Fougère, rue j14, bâtiment B23, 1er étage', NULL, 'Plateau', NULL, 'Côte d’Ivoire', NULL, NULL),
(84, 'Abidjan Treichville, immeuble la balance 2è étage', NULL, NULL, NULL, 'Côte d’Ivoire', NULL, NULL),
(85, 'ABATTA, à côté de la SICTA', NULL, NULL, NULL, 'Côte d’Ivoire', NULL, NULL),
(86, 'FFA conseil, 5 Avenue Marchand, Pateau', NULL, NULL, NULL, 'Côte d’Ivoire', NULL, NULL),
(87, 'Abidjan, Feh Kessé route de Bingerville', NULL, NULL, NULL, 'Côte d’Ivoire', NULL, NULL),
(88, 'Cocody 2 Plateaux, Boulevard des Martyres, résidence SICOGI latrille, près de la mosquée d’Aghien, bat N, 1er étage porte 161', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(89, 'AMDG CONSEILS FISCAUX, Cocody Cité Sicogi, Saint Jean, immeuble GAUGUIN 3ème étage, Appt 40', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(90, 'Abidjan Treichville, Avenue Christiani immeuble Berger Diop', NULL, NULL, NULL, 'Côte d’Ivoire', NULL, NULL),
(91, 'Cocody Angré, Star 9B, Villa 459', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(92, 'Cocody riviéra golf', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(93, 'Cabinet conseils Associés en Afrique C2A, 2Plateaux Vallon, 2è tranche, sortie rue des jardins vers échangeur des 220 Lts, imm. Contigë à la station shell, 1er étage', NULL, 'Plateau', NULL, 'Côte d’Ivoire', NULL, NULL),
(94, 'Abidjan Cocody Palmeraie, Laurier 9', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(95, 'Cocody Riviera Palmeraie, Immeuble AL JAWAD, 3ème étage porte C8', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(96, 'Koumassi, 6è Arrondissement, près des banques', NULL, NULL, NULL, 'Côte d’Ivoire', NULL, NULL),
(97, 'Abidjan Plateau, rue du commerce', NULL, 'Plateau', NULL, 'Côte d’Ivoire', NULL, NULL),
(98, 'FLYTH Fiscal et Juridique. Abidjan Plateau boulevard Carde, imm. Les Hamonies, 6è étage', NULL, 'Plateau', NULL, 'Côte d’Ivoire', NULL, NULL),
(99, 'Bouké', NULL, NULL, NULL, 'Côte d’Ivoire', NULL, NULL),
(100, 'Cocody Val. Doyen, rue A30, face UCAO', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(101, 'Cabinet SCP-MOSSO, OHIN conseil & Associés, Abidjan Riviera Golf, cité Elias 1, Immeuble Makoré RDC Appt 1701', NULL, NULL, NULL, 'Côte d’Ivoire', NULL, NULL),
(102, 'Riviera 3', NULL, NULL, NULL, 'Côte d’Ivoire', NULL, NULL),
(103, 'Riviera 3 attoban', NULL, NULL, NULL, 'Côte d’Ivoire', NULL, NULL),
(104, '2 Plateaux Boulevard Latrille, \nCité Saint Jacques, Rue K 36, lot n° 419', NULL, 'Plateau', NULL, 'Côte d’Ivoire', NULL, NULL),
(105, '2 Plateaux Boulevard Latrille,\nCité Saint Jacques, Rue K 36, lot n°419', NULL, 'Plateau', NULL, 'Côte d’Ivoire', NULL, NULL),
(106, 'Treichville, avenue Christiani, rue & » barrée (Plazzard)', NULL, NULL, NULL, 'Côte d’Ivoire', NULL, NULL),
(107, 'Angré château, Rue des Amoatrins', NULL, NULL, NULL, 'Côte d’Ivoire', NULL, NULL),
(108, 'Riviera Golf 4, Immeuble Brandon & Mcain, Appt 3', NULL, NULL, NULL, 'Côte d’Ivoire', NULL, NULL),
(109, 'ZONE 3,26, Rue des Carrossiers, Imm. Francisco', NULL, NULL, NULL, 'Côte d’Ivoire', NULL, NULL),
(110, 'Abidjan Yopougon, face FIGAYO', NULL, 'Yopougon', NULL, 'Côte d’Ivoire', NULL, NULL),
(111, 'Abidjan Treichville, Immeuble la balance, 2è étage', NULL, NULL, NULL, 'Côte d’Ivoire', NULL, NULL),
(112, 'Abidjan Cocody Angré, Terminus 81/ 82, face NSIA Banque, rue des Aigles 1er étage porte A4', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(113, 'Cocody 8ème tranche, rue ONOA SPA', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(114, 'Cocody 2Plateaux, face Pharmacie 7è tranche', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(115, 'Abidjan Zone, 3 boulevard VGE, face au collège Moderne Autoroute, Immeuble ex Drocolor 2è Etage', NULL, NULL, NULL, 'Côte d’Ivoire', NULL, NULL),
(116, 'Abidjan Cocody, Val doyen, rue A30, face à l’UCAO', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(117, 'Riviera Golf, cité Elias1, Imm. Tiama, RDC appt 1503', NULL, NULL, NULL, 'Côte d’Ivoire', NULL, NULL),
(118, 'Cocody, Cité des Arts, 7è tranche, rue L169', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(119, 'Angré 8è tranche, Immeuble SCI SAGED \n3è étage Appartement 305', NULL, NULL, NULL, 'Côte d’Ivoire', NULL, NULL),
(120, 'Cocody Angré 7è tranche, lot 3694 ilot 301', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(121, 'Cabinet SCP-MOSSO, OHIN Conseils & Associés. Abidjan Riviera Golf, Cité Elias 1, Immeuble Makoré RDC Appt 1701', NULL, NULL, NULL, 'Côte d’Ivoire', NULL, NULL),
(122, 'Cocody 2 Plateaux Angré, Cité les Papayers, Villa 96', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(123, 'Angré 7è tranche, face Groupe Scolaire Papillon', NULL, NULL, NULL, 'Côte d’Ivoire', NULL, NULL),
(124, 'Plateau, Cité Esculape', NULL, 'Plateau', NULL, 'Côte d’Ivoire', NULL, NULL),
(125, 'Abidjan Marcory, rue F 74 Dabou, derrière la Pharmacie du petit marché', NULL, 'Marcory', NULL, 'Côte d’Ivoire', NULL, NULL),
(126, 'Abidjan Cocody, Cité des arts, Rue de l’Impasse, Villa n°1', NULL, 'Cocody', NULL, 'Côte d’Ivoire', NULL, NULL),
(128, '225 0707919679', '225 0506101020', 'Abidjan Marcory, Rue Laurraine, face à l\'Église Ste-Thérèse.', '31 BP 865 Abidjan 31', 'Côte d\'ivoire', 5.2981167, -3.9749771),
(129, '0708442442', NULL, 'Treichville avenue christiani', NULL, 'Côte d\'ivoire', 5.3090307, 4.019538),
(130, '225 0708089731', NULL, 'Étude située à Cocody - Riviera FAYA, près de la société AL Sanogo, Immeuble Les Résidences Holly Church 2, Porte 4', '08 BP 1450 Abidjan 0', 'Côte d\'ivoire', 5.3711345, -3.929456),
(131, '+225 2722209325', NULL, 'Abidjan Cocody 9ème Tranche, Immeuble KLAS M en face de CGK', '', 'Côte d\'ivoire', 5.3833134, -3.9764082),
(132, '+225 0709857829', NULL, 'Bingerville Feh Kesse, Résidence Nehemie, Appt B5 (1er étage)', '', 'Côte d\'ivoire', 5.3834028, -4.0151241),
(133, '225 0707994800', NULL, 'Riviera 2,  face au Groupe Scolaire Sogefiha 2, non loin de la Cité Universitaire.', '25 BP 528 Abidjan 25', 'Côte d\'ivoire', 5.354444, -3.9918394),
(134, '+225 0708092041', NULL, 'Abidjan Cocody , Riviera Palmeraie, Rue Ministre près de l\'école Maci, Immeuble Supérette QUEEN CAYO', '28 BP 671 ABJ 28', 'Côte d\'ivoire', 5.3674958, -3.9592017),
(135, '+225 27 22 28 89 48 /+225 07 59 73 04 59', NULL, 'Cocody Faya, Quartier Génie 2000, Rue Cloud Stéroids, Résidence Fasma, Immeuble abritant la Supérette SUPMARKET, Escalie', '22 BPM 1591 Abidjan ', 'Côte d\'ivoire', 5.3789687, -3.937479);

-- --------------------------------------------------------

--
-- Structure de la table `cabinet`
--

CREATE TABLE `cabinet` (
  `id` int(11) NOT NULL,
  `name` varchar(180) NOT NULL,
  `slug` varchar(180) NOT NULL,
  `type` varchar(20) NOT NULL,
  `email` varchar(180) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(120) DEFAULT NULL,
  `lat` double DEFAULT NULL,
  `lng` double DEFAULT NULL,
  `logo_url` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL,
  `type_id` int(11) DEFAULT NULL,
  `managing_partner_id` int(11) DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `address_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `cabinet`
--

INSERT INTO `cabinet` (`id`, `name`, `slug`, `type`, `email`, `phone`, `website`, `address`, `city`, `lat`, `lng`, `logo_url`, `is_active`, `type_id`, `managing_partner_id`, `description`, `address_id`) VALUES
(1, 'Cabinet de Conseils Juridiques Me DJÉ KOURI', 'cabinet-conseils-juridiques-me-dje-kouri', '', 'arthurdjekouri@yahoo.com', '225 0707919679', NULL, 'Abidjan Marcory, Rue Laurraine, face à l\'Église Ste-Thérèse. ', 'Abidjan Marcory, Rue Laurraine, face à l\'Église Ste-Thérèse. ', 5.2981167, -3.9749771, 'https://cncj-ci.ci/wp-content/uploads/2024/11/LOGO-SITE-INTERNET-copie.png', 0, 1, 259, NULL, 128),
(3, 'CABINET Alpha jurist conseil', 'cabinet-Alpha-jurist-conseil ', '', 'abgkablan@gmail.com / abgconseilj@gmail.com', '+225 0708442442', NULL, 'Treichville avenue christiani', 'Treichville avenue christiani', 5.3090307, 4.019538, 'https://cncj-ci.ci/wp-content/uploads/2024/11/LOGO-SITE-INTERNET-copie.png', 1, 1, NULL, NULL, 129),
(4, 'Cabinet de Conseil Juridique GUILE Dabloa Dagobert', 'cabinet-juridique-guiledabloa-dagobert', '', 'blebloa@gmail.com', '+225 0708089731', NULL, 'Étude située à Cocody - Riviera FAYA, près de la société AL Sanogo, Immeuble Les Résidences Holly Church 2, Porte 4', 'Étude située à Cocody - Riviera FAYA, près de la société AL Sanogo, Immeuble Les Résidences Holly Church 2, Porte 4', 5.3711345, -3.929456, 'https://cncj-ci.ci/wp-content/uploads/2024/11/LOGO-SITE-INTERNET-copie.png', 1, 1, NULL, NULL, 130),
(5, 'Cabinet Nakhan & Co Consulting ', 'cabinet-nakhan-co-consulting', 'Cabinet', 'nakhanconsulting@gmail.com', '+225 2722209325', NULL, 'Abidjan Cocody 9ème Tranche, Immeuble KLAS M en face de CGK', 'Abidjan Cocody 9ème Tranche, Immeuble KLAS M en face de CGK', 5.3833134, -3.9764082, 'https://cncj-ci.ci/wp-content/uploads/2024/11/LOGO-SITE-INTERNET-copie.png', 0, 1, NULL, NULL, 131),
(6, 'MARK\'US LEGAL ADVISORS', 'markus-legal-advisors', '', 'info@markus-group.com', '+225 0709857829 / 0566018866', NULL, 'Bingerville Feh Kesse, Résidence Nehemie, Appt B5 (1er étage)', 'Bingerville Feh Kesse, Résidence Nehemie, Appt B5 (1er étage)', 5.3834028, -4.0151241, 'https://cncj-ci.ci/wp-content/uploads/2024/11/LOGO-SITE-INTERNET-copie.png', 1, 1, NULL, NULL, 132),
(7, 'Cabinet Conseils N\'KLO Sylvie (CCNS)', 'cabinet-conseils-nklo-sylvie', '', 'sylvienklo23@gmail.com', '+225 0707994800 / +225 0102763877', NULL, 'Riviera 2,  face au Groupe Scolaire Sogefiha 2, non loin de la Cité Universitaire', 'Riviera 2,  face au Groupe Scolaire Sogefiha 2, non loin de la Cité Universitaire', 5.354444, -3.9918394, 'https://cncj-ci.ci/wp-content/uploads/2024/11/LOGO-SITE-INTERNET-copie.png', 0, 1, 63, NULL, 133),
(8, 'Cabinet CALLENTIS-EKAC & ASSOCIÉS', 'cabinet-callentis-ekac-associes', '', 'cabinetekac@gmail.com', '+225 0708092041', NULL, 'Situé à Abidjan Cocody, Riviera Palmeraie,Rue Ministre près de l\'école Maci, Immeuble Supérette QUEEN CAYO', 'Situé à Abidjan Cocody ,Riviera Palmeraie, Rue Ministre près de l\'école Maci, Immeuble Supérette QUEEN CAYO', 5.3674958, -3.9592017, 'https://cncj-ci.ci/wp-content/uploads/2024/11/LOGO-SITE-INTERNET-copie.png', 0, 1, 108, NULL, 134),
(9, 'CABINET PRO JURIS', 'cabinet-pro-juris', '', 'maitre.attoungbre@pro-juris.ci', '+225 27 22 28 89 48/ +225 07 59 73 04 59', 'www. pro-juris.ci', 'Cocody Faya, Quartier Génie 2000, Rue Cloud Stéroids, Résidence Fasma, Immeuble abritant la Supérette SUPMARKET, Escalier Gauche, 1er étage, Porte A 102.', 'Cocody Faya, Quartier Génie 2000, Rue Cloud Stéroids, Résidence Fasma, Immeuble abritant la Supérette SUPMARKET, Escalie', 5.3789687, -3.937479, 'https://cncj-ci.ci/wp-content/uploads/2024/11/LOGO-SITE-INTERNET-copie.png', 1, 1, NULL, NULL, 135);

--
-- Déchargement des données de la table `cabinet_type`
--

INSERT INTO `cabinet_type` (`id`, `name`, `slug`) VALUES
(1, 'Cabinet', 'type-cabinet'),
(2, 'SCP', 'type-scp'),
(3, 'SCPA', 'type-scpa');

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20251003ModelExtension', '2025-10-07 09:55:36', 1581);

--
-- Déchargement des données de la table `email_address`
--

INSERT INTO `email_address` (`id`, `lawyer_id`, `cabinet_id`, `label`, `email`, `is_primary`, `position`) VALUES
(2, 5, NULL, 'principal', 'allali-louismarc@yahoo.fr', 1, 0),
(3, 6, NULL, 'principal', 'jucouloud712@gmail.com', 1, 0),
(4, 90, NULL, 'principal', 'poteywillys@gmail.com', 1, 0),
(5, 7, NULL, 'principal', 'lavoielegale@gmail.com', 1, 0),
(6, 8, NULL, 'principal', 'kikounfigespro.c@gmail.com', 1, 0),
(7, 92, NULL, 'principal', 'kadhysoumahoro@yahoo.fr', 1, 0),
(8, 10, NULL, 'principal', 'ladjiciss@yahoo.fr', 1, 0),
(9, 11, NULL, 'principal', 'alain_nda_ezoa@yahoo.fr', 1, 0),
(10, 93, NULL, 'principal', 'jean.enokou@altioparteners.com', 1, 0),
(11, 12, NULL, 'principal', 'passemien@mk-conseil.net', 1, 0),
(12, 13, NULL, 'principal', 'kabab75@gmail.com', 1, 0),
(13, 14, NULL, 'principal', 'stephanekoffi3@gmail.com', 1, 0),
(14, 15, NULL, 'principal', 'g.cabinetbahieconseil@gmail.com', 1, 0),
(15, 16, NULL, 'principal', 'micheline.koudou@gmail.com', 1, 0),
(16, 95, NULL, 'principal', 'kleoncio2002@gmail.com', 1, 0),
(17, 17, NULL, 'principal', 'g.sylvain@aphconseil.com', 1, 0),
(18, 18, NULL, 'principal', 'jcgnanmien707@gmail.com', 1, 0),
(19, 19, NULL, 'principal', 'bongamartial@gmail.com', 1, 0),
(20, 20, NULL, 'principal', 'www.adiomande@d2a-conseils.com', 1, 0),
(21, 21, NULL, 'principal', 'coulibalyfougniga@yahoo.fr', 1, 0),
(22, 22, NULL, 'principal', 'jobatibi79@gmail.com', 1, 0),
(23, 23, NULL, 'principal', 'info@maitredeaboa.com', 1, 0),
(24, 24, NULL, 'principal', 'antoinekouame@eti-ci.com', 1, 0),
(25, 25, NULL, 'principal', 'kmiez2002@yahoo.fr', 1, 0),
(26, 26, NULL, 'principal', 'ouattara_kimbefo@yahoo.fr', 1, 0),
(27, 27, NULL, 'principal', 'ahmedbere@gmail.com', 1, 0),
(28, 29, NULL, 'principal', 'gerad.bohui@outlook.fr', 1, 0),
(29, 96, NULL, 'principal', 'anojocelyne@gmail.com', 1, 0),
(30, 97, NULL, 'principal', 'kdagnoko.cja@gmail.com', 1, 0),
(31, 30, NULL, 'principal', 'pdjedje@c2a-ci.com', 1, 0),
(32, 31, NULL, 'principal', 'djire.ousmane@conseiljuridiqueci.org', 1, 0),
(33, 98, NULL, 'principal', 'dtmariatou@yahoo.fr', 1, 0),
(34, 32, NULL, 'principal', 'innocent.kouakou@gmail.com', 1, 0),
(35, 33, NULL, 'principal', 'kfaustinassemian@yahoo.fr', 1, 0),
(36, 99, NULL, 'principal', 'sfofana@faoconseil.com', 1, 0),
(37, 34, NULL, 'principal', 'dominiqueoswald@yahoo.fr', 1, 0),
(38, 35, NULL, 'principal', 'larissaehozi@gmail.com', 1, 0),
(39, 36, NULL, 'principal', 'jamal@jfconseils.com', 1, 0),
(40, 37, NULL, 'principal', 'icf.abidjan2@gmail.com', 1, 0),
(41, 38, NULL, 'principal', 'cesar.aloco@alocohein.com', 1, 0),
(42, 100, NULL, 'principal', 'cesar.aloco@alocohein.com', 1, 0),
(43, 28, NULL, 'principal', 'bkouassikof@gmail.com', 1, 0),
(44, 39, NULL, 'principal', 'gilleskouame@gmail.com', 1, 0),
(45, 40, NULL, 'principal', 'cabinet2csconseiljuridique@gmail.com', 1, 0),
(46, 41, NULL, 'principal', 'dbouadoux@asafo-rci.com', 1, 0),
(47, 42, NULL, 'principal', 'florindacat@hotmail.fr', 1, 0),
(48, 44, NULL, 'principal', 'gbakayoro.roger@gmail.com', 1, 0),
(49, 101, NULL, 'principal', 'gbane3salimata@gmail.com', 1, 0),
(50, 45, NULL, 'principal', 'ginaud2000@yahoo.fr', 1, 0),
(51, 43, NULL, 'principal', 'sothenekwassi@gmail.com', 1, 0),
(52, 47, NULL, 'principal', 'eric.megalou@cdi-counsel.com', 1, 0),
(53, 48, NULL, 'principal', 'g.tiavalery@gmail.com', 1, 0),
(54, 49, NULL, 'principal', 'c.doum@yahoo.fr', 1, 0),
(55, 102, NULL, 'principal', 'stephane.ahoussi@gmail.com', 1, 0),
(56, 103, NULL, 'principal', 'desireaka01@gmail.com', 1, 0),
(57, 104, NULL, 'principal', 'assamoua.stanislas@gmail.com', 1, 0),
(58, 50, NULL, 'principal', 'l.yace@icloud.com', 1, 0),
(59, 51, NULL, 'principal', 'adouigor@yahoo.fr', 1, 0),
(60, 52, NULL, 'principal', 'aboudou.sekou@flyth.com', 1, 0),
(61, 53, NULL, 'principal', 'nksergeoli@gmail.com', 1, 0),
(62, 105, NULL, 'principal', 'mouhikoffi@gmail.com', 1, 0),
(63, 54, NULL, 'principal', 'fahoussi@hotmail.fr', 1, 0),
(64, 55, NULL, 'principal', 'contact@cabinet-bayeron.com', 1, 0),
(65, 56, NULL, 'principal', 'ysiluey@gmail.com', 1, 0),
(66, 106, NULL, 'principal', 'kouyate.valy@yahoo.fr', 1, 0),
(67, 57, NULL, 'principal', 'adamaben@yahoo.fr', 1, 0),
(68, 107, NULL, 'principal', 'yaomariepriscilla.mca@gmail.com', 1, 0),
(69, 58, NULL, 'principal', 'blebloa@gmail.com', 1, 0),
(70, 108, NULL, 'principal', 'cabinetekac@gmail.com', 1, 0),
(71, 109, NULL, 'principal', 'isoualoco@gmail.com', 1, 0),
(72, 59, NULL, 'principal', 'jldattie@gmail.com', 1, 0),
(73, 60, NULL, 'principal', 'yves-auguste@hotmail.com', 1, 0),
(74, 61, NULL, 'principal', 'koffosephora@yahoo.fr', 1, 0),
(75, 62, NULL, 'principal', 'yvesalexandre1er@gmail.com', 1, 0),
(76, 110, NULL, 'principal', 'koneissouf1954@gmail.com', 1, 0),
(77, 111, NULL, 'principal', 'bintou.coulibaly-kouadio@flyth.com', 1, 0),
(78, 63, NULL, 'principal', 'sylvieklo23@gmail.com', 1, 0),
(79, 64, NULL, 'principal', 'samah53@hotmail.com', 1, 0),
(80, 65, NULL, 'principal', 'massibertsanogo@yahoo.fr', 1, 0),
(81, 112, NULL, 'principal', 'asmusayni@gmail.com', 1, 0),
(82, 66, NULL, 'principal', 'p.aman@p2aconseils.com', 1, 0),
(83, 113, NULL, 'principal', 'kafolodjeamadoukone@gmail.com', 1, 0),
(84, 114, NULL, 'principal', 'bamadhcorporate@gmail.com', 1, 0),
(85, 115, NULL, 'principal', 'eric.nguessan@ci.ey.com', 1, 0),
(86, 116, NULL, 'principal', 'charlesassale@gmail.com', 1, 0),
(87, 67, NULL, 'principal', 'kouassi24firmin@gmail.com', 1, 0),
(88, 68, NULL, 'principal', 'contact@amdgconseilsfiscaux.com', 1, 0),
(89, 117, NULL, 'principal', 'melanamoly@yahoo.fr', 1, 0),
(90, 69, NULL, 'principal', 'harmelconseil@gmail.com', 1, 0),
(91, 70, NULL, 'principal', 'nguessanfrancoise2007@yahoo.fr', 1, 0),
(92, 71, NULL, 'principal', 'linguessan@gmail.com', 1, 0),
(93, 118, NULL, 'principal', 'toussaint.sokoty@yahoo.fr', 1, 0),
(94, 72, NULL, 'principal', 'marie.kurayum@gmail.com', 1, 0),
(95, 119, NULL, 'principal', 'tiebleyves@gmail.com', 1, 0),
(96, 73, NULL, 'principal', 'jeanpaulwognin@yahoo.fr', 1, 0),
(97, 120, NULL, 'principal', 'zogbod116@gmail.com', 1, 0),
(98, 74, NULL, 'principal', 'brigitteyeboue2015@gmail.com', 1, 0),
(99, 75, NULL, 'principal', 'theodore.yonan@flyth.com', 1, 0),
(100, 121, NULL, 'principal', 'y.marthe0304@gmail.com', 1, 0),
(101, 76, NULL, 'principal', 'ycoulibaly@asafo-rci.com', 1, 0),
(102, 123, NULL, 'principal', 'assirifix.philipe@gmail.com', 1, 0),
(103, 124, NULL, 'principal', 'cabconsegue@yahoo.fr', 1, 0),
(104, 77, NULL, 'principal', 'ccjf2019@yahoo.fr', 1, 0),
(105, 78, NULL, 'principal', 'gdjessan@gmail.com', 1, 0),
(106, 125, NULL, 'principal', 'abgkablan@gmail.com', 1, 0),
(107, 126, NULL, 'principal', 'miange2010@yahoo.fr', 1, 0),
(108, 127, NULL, 'principal', 'any.raymond01@gmail.com', 1, 0),
(109, 128, NULL, 'principal', 'doubire.hien@alocohien.com', 1, 0),
(110, 79, NULL, 'principal', 'konankouadioetienne@yahoo.fr', 1, 0),
(111, 129, NULL, 'principal', 'ouattara.ahmed@hotmail.com', 1, 0),
(112, 80, NULL, 'principal', 'jeromekouakou@yahoo.fr', 1, 0),
(113, 81, NULL, 'principal', 'jkouadio@southinvestcab.com', 1, 0),
(114, 82, NULL, 'principal', 'mariecoulibaly1971@gmail.com', 1, 0),
(115, 130, NULL, 'principal', 'abeymarius0019@gmail.com', 1, 0),
(116, 83, NULL, 'principal', 'kfadiga@asafo-rci.com', 1, 0),
(117, 131, NULL, 'principal', 'cgogoua@alltaxci.com', 1, 0),
(118, 84, NULL, 'principal', 'maimounalzouma@gmail.com', 1, 0),
(119, 132, NULL, 'principal', 'wognlntaxlaw@gmail.com', 1, 0),
(120, 85, NULL, 'principal', 'dg@athena-conseil.com', 1, 0),
(121, 133, NULL, 'principal', 'mosso_julien@yahoo.fr', 1, 0),
(122, 134, NULL, 'principal', 'amaniconseil38@gmail.com', 1, 0),
(123, 135, NULL, 'principal', 'ksergeyves@gmail.com', 1, 0),
(124, 86, NULL, 'principal', 'akjd69@gmail.com', 1, 0),
(125, 136, NULL, 'principal', 'ouattara_hubert@yahoo.fr', 1, 0),
(126, 87, NULL, 'principal', 'fcamaragbalet@live.fr', 1, 0),
(127, 88, NULL, 'principal', 'mardoche1@gmail.com', 1, 0),
(128, 89, NULL, 'principal', 'renesiekoffi@etl-ci.com', 1, 0);

--
-- Déchargement des données de la table `lawyer`
--

INSERT INTO `lawyer` (`id`, `cabinet_id`, `first_name`, `last_name`, `slug`, `email`, `phone`, `city`, `bar_number`, `biography`, `photo_url`, `address_id`) VALUES
(5, NULL, 'Louis Marc', 'ALLALI', 'allali-louis-marc', 'allali-louismarc@yahoo.fr', '0506337347', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 2),
(6, NULL, 'AHOUTCHI Judith', 'COULOUD', 'couloud-ahoutchi-judith', 'jucouloud712@gmail.com', '0707039519', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 3),
(7, NULL, 'Aïchata Epouse ADEGBIDI', 'DIALLO', 'diallo-aïchata-epouse-adegbidi', 'lavoielegale@gmail.com', '0707012561', 'Plateau', NULL, 'Liste les conseillers juridiques de la commune du plateau.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 5),
(8, NULL, 'Kikoun', 'COULIBALY', 'coulibaly-kikoun', 'kikounfigespro.c@gmail.com', '0777430625', 'Plateau', NULL, 'Liste les conseillers juridiques de la commune du plateau.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 7),
(9, NULL, 'OPERI Sydney', 'DOMORAUD', 'domoraud-operi-sydney', NULL, '0748377773', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 8),
(10, NULL, 'Ladji', 'CISSE', 'cisse-ladji', 'ladjiciss@yahoo.fr', '0707968355', 'Plateau', NULL, 'Liste les conseillers juridiques de la commune du plateau.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 10),
(11, NULL, 'ain', 'N’DA-EZOA', 'n’da-ezoa-alain', 'alain_nda_ezoa@yahoo.fr', '0707931335', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 11),
(12, NULL, 'N’Galet Ebaw Pierre', 'ASSEMIEN', 'assemien-n’galet-ebaw-pierre', 'passemien@mk-conseil.net', '0101100662', 'Plateau', NULL, 'Liste les conseillers juridiques de la commune du plateau.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 13),
(13, NULL, 'Ka', 'BABAKAR', 'babakar-ka', 'kabab75@gmail.com', '0708659900', 'Marcory', NULL, 'Liste les conseillers juridiques de la commune de marcory.', NULL, 14),
(14, NULL, 'Stephane', 'KOFFI', 'koffi-stephane', 'stephanekoffi3@gmail.com', '0544766512', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 15),
(15, NULL, 'SOKO Gabriel', 'BAHIE', 'bahie-soko-gabriel', 'g.cabinetbahieconseil@gmail.com', '0708190677', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 17),
(16, NULL, 'Micheline Rose', 'KOUDOU', 'koudou-micheline-rose', 'micheline.koudou@gmail.com', '0506468624', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 18),
(17, NULL, 'SAHONNE Silvain Boris', 'GNANDJI', 'gnandji-sahonne-silvain-boris', 'g.sylvain@aphconseil.com', '0708904987', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 20),
(18, NULL, 'KOUAKOU Jean', 'GNANMIEN', 'gnanmien-kouakou-jean', 'jcgnanmien707@gmail.com', '0506312731', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 21),
(19, NULL, 'ANGE Martial', 'BONGA', 'bonga-ange-martial', 'bongamartial@gmail.com', '2722466971', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 22),
(20, NULL, 'ABDOUL Ahmara', 'DIOMANDE', 'diomande-abdoul-ahmara', 'www.adiomande@d2a-conseils.com', '0708085727', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 23),
(21, NULL, 'FOUGNIGA', 'COULIBALY', 'coulibaly-fougniga', 'coulibalyfougniga@yahoo.fr', '0707648107', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 24),
(22, NULL, 'HYRIET Joël Jean Silvere', 'BATI-BI', 'bati-bi-hyriet-joël-jean-silvere', 'jobatibi79@gmail.com', '0758742335', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 25),
(23, NULL, 'ANOH NARCISSE Anderson', 'ABOA', 'aboa-anoh-narcisse-anderson', 'info@maitredeaboa.com', '2520009149', 'Plateau', NULL, 'Liste les conseillers juridiques de la commune du plateau.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 26),
(24, NULL, 'KOUADIO Antoine', 'KOUAME', 'kouame-kouadio-antoine', 'antoinekouame@eti-ci.com', '0707905232', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 27),
(25, NULL, 'KOUADIO Dakoi', 'MEIZAN', 'meizan-kouadio-dakoi', 'kmiez2002@yahoo.fr', '0102503756', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 28),
(26, NULL, 'Kimbefo', 'OUATTARA', 'ouattara-kimbefo', 'ouattara_kimbefo@yahoo.fr', '0707058971', 'Plateau', NULL, 'Liste les conseillers juridiques de la commune du plateau.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 29),
(27, NULL, 'ELVIS Ahmed Wend-Nale', 'BERE', 'bere-elvis-ahmed-wend-nale', 'ahmedbere@gmail.com', '0747068393', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 30),
(28, NULL, 'KOUASSI KOFFI Bienvenue', 'BINI', 'bini-kouassi-koffi-bienvenue', 'bkouassikof@gmail.com', '0709387777', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 30),
(29, NULL, 'HUGUES Gerard Kurtus', 'BOHUI', 'bohui-hugues-gerard-kurtus', 'gerad.bohui@outlook.fr', '0748336368', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 31),
(30, NULL, 'PIERRE Claver', 'DJEDJE', 'djedje-pierre-claver', 'pdjedje@c2a-ci.com', '2722599270', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 34),
(31, NULL, 'Ousmane', 'DJIRE', 'djire-ousmane', 'djire.ousmane@conseiljuridiqueci.org', '2722016099', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 35),
(32, NULL, 'Innocent', 'KOUAKOU', 'kouakou-innocent', 'innocent.kouakou@gmail.com', '0101318180', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 37),
(33, NULL, 'ASSEMIAN Faustin', 'KOUAKOU', 'kouakou-assemian-faustin', 'kfaustinassemian@yahoo.fr', '0505089973', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 38),
(34, NULL, 'DOMINIQUE Oswald', 'DOUHOT', 'douhot-dominique-oswald', 'dominiqueoswald@yahoo.fr', '2722428823', 'Plateau', NULL, 'Liste les conseillers juridiques de la commune du plateau.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 40),
(35, NULL, 'EBA LARISSA Sandrine Muriel Stephanie', 'EHOZI', 'ehozi-eba-larissa-sandrine-muriel-stephanie', 'larissaehozi@gmail.com', '2722536971', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 41),
(36, NULL, 'MOHAMED Ibrahim Jamal', 'FOFANA', 'fofana-mohamed-ibrahim-jamal', 'jamal@jfconseils.com', '0748355483', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 42),
(37, NULL, 'MONPOHO Landry Pacôme', 'SOHOU', 'sohou-monpoho-landry-pacôme', 'icf.abidjan2@gmail.com', '2720355469', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 43),
(38, NULL, 'Boundy', 'SOULEYMANE', 'souleymane-boundy', 'cesar.aloco@alocohein.com', '0747230650', 'Plateau', NULL, 'Liste les conseillers juridiques de la commune du plateau.', 'https://cncj-ci.ci/wp-content/uploads/2025/10/Me-BOUNDY-SOULEYMANE-scaled.jpg', 44),
(39, NULL, 'AMANI Gilles-Hervé', 'KOUAME', 'kouame-amani-gilles-hervé', 'gilleskouame@gmail.com', '0708426364', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 46),
(40, NULL, 'Epouse CISSE Salimata', 'COULIBALY', 'coulibaly-epouse-cisse-salimata', 'cabinet2csconseiljuridique@gmail.com', '0707408852', 'Marcory', NULL, 'Liste les conseillers juridiques de la commune de marcory.', NULL, 47),
(41, NULL, 'DOUX Didier Charles', 'BOUA', 'boua-doux-didier-charles', 'dbouadoux@asafo-rci.com', '0759441154', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 48),
(42, NULL, 'Fabienne', 'CATTANEO', 'cattaneo-fabienne', 'florindacat@hotmail.fr', '0749266226', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 49),
(43, NULL, 'KONAN Sosthene', 'KOUASSI', 'kouassi-konan-sosthene', 'sothenekwassi@gmail.com', '0789825515', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 49),
(44, NULL, 'Soumwerin Roger', 'GBAKAYORO', 'gbakayoro-soumwerin-roger', 'gbakayoro.roger@gmail.com', '0708141351', 'Plateau', NULL, 'Liste les conseillers juridiques de la commune du plateau.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 50),
(45, NULL, 'BETA GNALY Honorat Jean Noël Daffot', 'GENY', 'geny-beta-gnaly-honorat-jean-noël-daffot', 'ginaud2000@yahoo.fr', '2720216007', 'Plateau', NULL, 'Liste les conseillers juridiques de la commune du plateau.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 52),
(46, NULL, 'LAMINE Dossé', 'TOURE', 'toure-lamine-dossé', NULL, '0707260699', 'Marcory', NULL, 'Liste les conseillers juridiques de la commune de marcory.', NULL, 53),
(47, NULL, 'Eric Maxime', 'MEGALOU', 'megalou-eric-maxime', 'eric.megalou@cdi-counsel.com', '0708303003', 'Plateau', NULL, 'Liste les conseillers juridiques de la commune du plateau.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 54),
(48, NULL, 'TIA Vlery', 'GOHOGBEU', 'gohogbeu-tia-vlery', 'g.tiavalery@gmail.com', '0708404049', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 55),
(49, NULL, 'Moussa', 'DOUMBIA', 'doumbia-moussa', 'c.doum@yahoo.fr', '0505481646', 'Plateau', NULL, 'Liste les conseillers juridiques de la commune du plateau.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 56),
(50, NULL, 'LEONCE Daniel Djeket', 'YACE', 'yace-leonce-daniel-djeket', 'l.yace@icloud.com', '0759777777', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 60),
(51, NULL, 'GNOLBEYCES Igor', 'ADOU', 'adou-gnolbeyces-igor', 'adouigor@yahoo.fr', '0101418665', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 61),
(52, NULL, 'Sekou', 'ABOUDOU', 'aboudou-sekou', 'aboudou.sekou@flyth.com', '2720236955', 'Plateau', NULL, 'Liste les conseillers juridiques de la commune du plateau.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 62),
(53, NULL, 'SERGE-OLIVIER N’da KOFFI', 'TANO', 'tano-serge-olivier-n’da-koffi', 'nksergeoli@gmail.com', '0748234552', 'Plateau', NULL, 'Liste les conseillers juridiques de la commune du plateau.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 63),
(54, NULL, 'JUNIOR Frank Nicaise', 'AHOUSSI', 'ahoussi-junior-frank-nicaise', 'fahoussi@hotmail.fr', '0789739644', 'Plateau', NULL, 'Liste les conseillers juridiques de la commune du plateau.', 'https://cncj-ci.ci/wp-content/uploads/2025/10/Me-AHOUSSI-JUNIOR-FRANCK-NICAISE-scaled.jpg', 65),
(55, NULL, 'Michel Ahoun', 'BAYERON', 'bayeron-michel-ahoun', 'contact@cabinet-bayeron.com', '0709571525', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 66),
(56, NULL, 'Yaraba', 'SILUE', 'silue-yaraba', 'ysiluey@gmail.com', '0747077030', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 67),
(57, NULL, 'Adama Ben', 'DIARRASSOUBA', 'diarrassouba-adama-ben', 'adamaben@yahoo.fr', '0708714373', 'Yopougon', NULL, NULL, 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 68),
(58, 4, 'DABLOA Dagobert', 'GUILE', 'guile-dabloa-dagobert', 'blebloa@gmail.com', '2722478395', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/10/Me-GUILE-DABLOA-DAGOBERT-scaled.jpg', 70),
(59, NULL, 'Jean Louis', 'DATTIE', 'dattie-jean-louis', 'jldattie@gmail.com', '0707900090', 'Plateau', NULL, 'Liste les conseillers juridiques de la commune du plateau.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 73),
(60, NULL, 'ANDROUDOUX  Clement Yves Augustin', 'KATTIE', 'kattie-androudoux--clement-yves-augustin', 'yves-auguste@hotmail.com', '0505282222', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 74),
(61, NULL, 'Lydie Sephora Epouse BAKAYOKO', 'KOFFO', 'koffo-lydie-sephora-epouse-bakayoko', 'koffosephora@yahoo.fr', '0707144999', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 75),
(62, NULL, 'MOHAMED Yves Alexandre', 'KONE', 'kone-mohamed-yves-alexandre', 'yvesalexandre1er@gmail.com', '2720327626', 'Plateau', NULL, 'Liste les conseillers juridiques de la commune du plateau.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 76),
(63, 7, 'Sylvie', 'N’KLO', 'n’klo-sylvie', 'sylvieklo23@gmail.com', '0707994800', 'Riviera 2,  face au Groupe Scolaire Sogefiha 2, non loin de la Cité Universitaire', NULL, 'Liste les conseillers juridiques de la commune du plateau.', 'https://cncj-ci.ci/wp-content/uploads/2025/10/Me-NKLO-SYLVIE-scaled.jpg', 133),
(64, NULL, 'Samah Aïda', 'TOURE', 'toure-samah-aïda', 'samah53@hotmail.com', '0707037402', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 80),
(65, NULL, 'Massandjé Epouse BERTE', 'SANAGO', 'sanago-massandjé-epouse-berte', 'massibertsanogo@yahoo.fr', '0709136464', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 81),
(66, NULL, 'Patricia Valerie Aimée', 'AMAND', 'amand-patricia-valerie-aimée', 'p.aman@p2aconseils.com', '0757531733', 'Plateau', NULL, 'Liste les conseillers juridiques de la commune du plateau.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 83),
(67, NULL, 'ABEMAN Firmin', 'KOUAKOU', 'kouakou-abeman-firmin', 'kouassi24firmin@gmail.com', '0506292000', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 88),
(68, NULL, 'Marcel', 'YAPI', 'yapi-marcel', 'contact@amdgconseilsfiscaux.com', '0707522561', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 89),
(69, NULL, 'MELLO Huberson Ares', 'MIANGNE', 'miangne-mello-huberson-ares', 'harmelconseil@gmail.com', '0709607525', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 91),
(70, NULL, 'A Françoise-Xavière', 'N’GUESSAN', 'n’guessan-aya-françoise-xavière', 'nguessanfrancoise2007@yahoo.fr', '0707565739', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 92),
(71, NULL, 'lianne Mauricine St LIN Eléonore', 'N’GUESSAN', 'n’guessan-lilianne-mauricine-st-lin-eléonore', 'linguessan@gmail.com', '0707058970', 'Plateau', NULL, 'Liste les conseillers juridiques de la commune du plateau.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 93),
(72, NULL, 'Marie Anne Epouse KURAYUM', 'SERY', 'sery-marie-anne-epouse-kurayum', 'marie.kurayum@gmail.com', '0707701582', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 94),
(73, NULL, 'AMAGOUA Jean Paul', 'WOGNIN', 'wognin-amagoua-jean-paul', 'jeanpaulwognin@yahoo.fr', '0707574026', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 95),
(74, NULL, 'Brigitte', 'YEBOUE', 'yeboue-brigitte', 'brigitteyeboue2015@gmail.com', '0506305192', 'Plateau', NULL, 'Liste les conseillers juridiques de la commune du plateau.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 97),
(75, NULL, 'Théodore', 'YONAN', 'yonan-théodore', 'theodore.yonan@flyth.com', '2720236955', 'Plateau', NULL, 'Liste les conseillers juridiques de la commune du plateau.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 98),
(76, NULL, 'Coulibaly', 'YACOUBA', 'yacouba-coulibaly', 'ycoulibaly@asafo-rci.com', '2722007728', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 100),
(77, NULL, 'Raphaël', 'THIEMON', 'thiemon-raphaël', 'ccjf2019@yahoo.fr', '0708977772', 'Plateau', NULL, 'Liste les conseillers juridiques de la commune du plateau.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 104),
(78, NULL, 'y Léonard', 'N’DJESSAN', 'n’djessan-guy-léonard', 'gdjessan@gmail.com', '0584333621', 'Plateau', NULL, 'Liste les conseillers juridiques de la commune du plateau.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 105),
(79, NULL, 'KOUADIO Etienne', 'KONAN', 'konan-kouadio-etienne', 'konankouadioetienne@yahoo.fr', '0505539983', 'Yopougon', NULL, NULL, 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 110),
(80, NULL, 'KOUAKOU Jérôme', 'KOUAKOU', 'kouakou-kouakou-jérôme', 'jeromekouakou@yahoo.fr', '2722422088', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 112),
(81, NULL, 'Yao Jacques Alphonse', 'KOUADIO', 'kouadio-yao-jacques-alphonse', 'jkouadio@southinvestcab.com', '0748154316', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 113),
(82, NULL, 'Lazeni', 'COULIBALY', 'coulibaly-lazeni', 'mariecoulibaly1971@gmail.com', '0708961616', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 114),
(83, NULL, 'Karamoko', 'FADIGA', 'fadiga-karamoko', 'kfadiga@asafo-rci.com', '0707330732', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 116),
(84, NULL, 'Maïmouna Hélène Epouse TOURE', 'ALZOUMA', 'alzouma-maïmouna-hélène-epouse-toure', 'maimounalzouma@gmail.com', '0707525389', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 118),
(85, NULL, 'Patricia Aicha', 'MYLONOYANNIS', 'mylonoyannis-patricia-aicha', 'dg@athena-conseil.com', '2722416053', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 120),
(86, NULL, 'KOUASSI Jean Daniel', 'APHING', 'aphing-kouassi-jean-daniel', 'akjd69@gmail.com', '0777430088', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 122),
(87, NULL, 'Epouse CAMARA Françoise', 'GBALET', 'gbalet-epouse-camara-françoise', 'fcamaragbalet@live.fr', '0707361523', 'Plateau', NULL, 'Liste les conseillers juridiques de la commune du plateau.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 124),
(88, NULL, 'BLEDJERO Alexis', 'ABOUGNAN', 'abougnan-bledjero-alexis', 'mardoche1@gmail.com', '0707084724', 'Marcory', NULL, 'Liste les conseillers juridiques de la commune de marcory.', NULL, 125),
(89, NULL, 'KOFFI René', 'SIE', 'sie-koffi-rené', 'renesiekoffi@etl-ci.com', '0708087878', 'Cocody', NULL, 'Liste les conseillers juridiques de la commune de Cocody.', 'https://cncj-ci.ci/wp-content/uploads/2025/03/pin.png', 126),
(90, NULL, 'POTEY Willys Franck', 'PAH', 'pah-potey-willys-franck', 'poteywillys@gmail.com', '0709392019', NULL, NULL, NULL, NULL, NULL),
(91, NULL, 'KOUASSI Arthur', 'ALOCO', 'aloco-kouassi-arthur', NULL, '2721230190', NULL, NULL, NULL, NULL, NULL),
(92, NULL, 'Maba', 'SOUMAHORO', 'soumahoro-maba', 'kadhysoumahoro@yahoo.fr', '2721241760', NULL, NULL, NULL, NULL, NULL),
(93, NULL, 'KANGAH Jean', 'ENOKOU', 'enokou-kangah-jean', 'jean.enokou@altioparteners.com', '0505090056', NULL, NULL, NULL, NULL, NULL),
(94, NULL, 'Augustin Eloi', 'KONE', 'kone-augustin-eloi', NULL, '0708390191', NULL, NULL, NULL, NULL, NULL),
(95, NULL, 'Globaye Kwadjo', 'KOBENAN', 'kobenan-globaye-kwadjo', 'kleoncio2002@gmail.com', '0505951232', NULL, NULL, NULL, NULL, NULL),
(96, NULL, 'Goorenan Jocelyne-Andre Epouse ANO', 'BOLOUZRAMON', 'bolouzramon-goorenan-jocelyne-andre-epouse-ano', 'anojocelyne@gmail.com', '0758421919', NULL, NULL, NULL, NULL, NULL),
(97, NULL, 'Karidja', 'DAGNOKO', 'dagnoko-karidja', 'kdagnoko.cja@gmail.com', '0759051771', NULL, NULL, NULL, NULL, NULL),
(98, NULL, 'MARIATOU Epouse DEMBELE', 'TRAORE', 'traore-mariatou-epouse-dembele', 'dtmariatou@yahoo.fr', '0101463847', NULL, NULL, NULL, NULL, NULL),
(99, NULL, 'MOULOUKOU Souleymane', 'FOFANA', 'fofana-mouloukou-souleymane', 'sfofana@faoconseil.com', '0707498282', NULL, NULL, NULL, NULL, NULL),
(100, NULL, 'KOUAME KOUASSI Cesar', 'ALOCO', 'aloco-kouame-kouassi-cesar', 'cesar.aloco@alocohein.com', '2721230190', NULL, NULL, NULL, NULL, NULL),
(101, NULL, 'Salimata Epouse OUATTARA', 'GBANE', 'gbane-salimata-epouse-ouattara', 'gbane3salimata@gmail.com', '0102744971', NULL, NULL, NULL, NULL, NULL),
(102, NULL, 'Stephane Sylvain', 'AHOUSSI', 'ahoussi-stephane-sylvain', 'stephane.ahoussi@gmail.com', '0708168410', NULL, NULL, NULL, NULL, NULL),
(103, NULL, 'Desiré', 'AKA', 'aka-desiré', 'desireaka01@gmail.com', '0707007981', NULL, NULL, NULL, NULL, NULL),
(104, NULL, 'Yao Stanislas', 'ASSAMOUA', 'assamoua-yao-stanislas', 'assamoua.stanislas@gmail.com', '0505714604', NULL, NULL, NULL, NULL, NULL),
(105, NULL, 'Mouhi', 'KOFFI', 'koffi-mouhi', 'mouhikoffi@gmail.com', '0708301270', NULL, NULL, NULL, NULL, NULL),
(106, NULL, 'Valy', 'KOUYATE', 'kouyate-valy', 'kouyate.valy@yahoo.fr', '0747505152', NULL, NULL, NULL, NULL, NULL),
(107, NULL, 'NATOGOMAN Marie Priscille Epouse YAO', 'FOFANA', 'fofana-natogoman-marie-priscille-epouse-yao', 'yaomariepriscilla.mca@gmail.com', '2722435890', NULL, NULL, NULL, NULL, NULL),
(108, 8, 'Awa Epouse COLLOT', 'COULIBALY', 'coulibaly-awa-epouse-collot', 'cabinetekac@gmail.com', '0708092041', NULL, NULL, NULL, 'https://cncj-ci.ci/wp-content/uploads/2025/10/Me-COULIBALY-AWA-EPOUSE-COLLOT-scaled.jpg', 134),
(109, NULL, 'Epouse ALOCO Isabelle Ouakou Constance', 'MOULARE', 'moulare-epouse-aloco-isabelle-ouakou-constance', 'isoualoco@gmail.com', '0747717171', NULL, NULL, NULL, NULL, NULL),
(110, NULL, 'Issouf', 'KONE', 'kone-issouf', 'koneissouf1954@gmail.com', '0707216021', NULL, NULL, NULL, NULL, NULL),
(111, NULL, 'Epouse KOUADIO Kafaga Yely Bintou', 'COULIBALY', 'coulibaly-epouse-kouadio-kafaga-yely-bintou', 'bintou.coulibaly-kouadio@flyth.com', '2720236955', NULL, NULL, NULL, NULL, NULL),
(112, NULL, 'ASSOH Marie-Claire Epouse ASSOUMOU', 'SAYNI', 'sayni-assoh-marie-claire-epouse-assoumou', 'asmusayni@gmail.com', '0101860036', NULL, NULL, NULL, NULL, NULL),
(113, NULL, 'Koné', 'AMADOU', 'amadou-koné', 'kafolodjeamadoukone@gmail.com', '2721245682', NULL, NULL, NULL, NULL, NULL),
(114, NULL, 'AKA Innocent', 'KOUACOU', 'kouacou-aka-innocent', 'bamadhcorporate@gmail.com', '2522004263', NULL, NULL, NULL, NULL, NULL),
(115, NULL, 'ERIC Lambert N’guessan', 'KOUAME', 'kouame-eric-lambert-n’guessan', 'eric.nguessan@ci.ey.com', '0708025038', NULL, NULL, NULL, NULL, NULL),
(116, NULL, 'ADJEI Charles', 'ASSALE', 'assale-adjei-charles', 'charlesassale@gmail.com', '0748313893', NULL, NULL, NULL, NULL, NULL),
(117, NULL, 'AMOLY Marie Odile Epouse N’TAYE', 'MELAN', 'melan-amoly-marie-odile-epouse-n’taye', 'melanamoly@yahoo.fr', '0709170477', NULL, NULL, NULL, NULL, NULL),
(118, NULL, 'KOFFI Toussaint', 'SOKOTY', 'sokoty-koffi-toussaint', 'toussaint.sokoty@yahoo.fr', '0789552164', NULL, NULL, NULL, NULL, NULL),
(119, NULL, 'Yves Didier', 'TIEBLEY', 'tiebley-yves-didier', 'tiebleyves@gmail.com', '0506372071', NULL, NULL, NULL, NULL, NULL),
(120, NULL, 'BAYERE Ignace Désiré', 'ZOGBO', 'zogbo-bayere-ignace-désiré', 'zogbod116@gmail.com', '0546107759', NULL, NULL, NULL, NULL, NULL),
(121, NULL, 'Marthe Epouse KARIDIOULA', 'YEO', 'yeo-marthe-epouse-karidioula', 'y.marthe0304@gmail.com', '0757108101', NULL, NULL, NULL, NULL, NULL),
(122, NULL, 'Bintou Epouse Ohin', 'COULIBALY', 'coulibaly-bintou-epouse-ohin', NULL, '0505838562', NULL, NULL, NULL, NULL, NULL),
(123, NULL, 'Philipe Auguste', 'ASSIRIFIX', 'assirifix-philipe-auguste', 'assirifix.philipe@gmail.com', '0707577849', NULL, NULL, NULL, NULL, NULL),
(124, NULL, 'ANDRE MARIE CHRISTIAN', 'EGUE', 'egue-andre-marie-christian', 'cabconsegue@yahoo.fr', '0708588094', NULL, NULL, NULL, NULL, NULL),
(125, 3, 'Jean Junior', 'KABLAN', 'kablan-jean-junior', 'abgkablan@gmail.com', '0708442442', NULL, NULL, NULL, 'https://cncj-ci.ci/wp-content/uploads/2025/10/Me-KABLAN-JEAN-JUNIOR-scaled.jpg', NULL),
(126, NULL, 'KABLAN DEGNAN Michel Benson', 'KOFFI', 'koffi-kablan-degnan-michel-benson', 'miange2010@yahoo.fr', '0757562313', NULL, NULL, NULL, NULL, NULL),
(127, NULL, 'GBAYERE IKA Raymond', 'ANI', 'ani-gbayere-ika-raymond', 'any.raymond01@gmail.com', '0709606008', NULL, NULL, NULL, NULL, NULL),
(128, NULL, 'Hien', 'DOUBIRE', 'doubire-hien', 'doubire.hien@alocohien.com', '0707737415', NULL, NULL, NULL, 'https://cncj-ci.ci/wp-content/uploads/2025/10/Me-DOUBIRE-HIEN-scaled.jpg', NULL),
(129, NULL, 'Dougnimata Ahmed', 'OUATTARA', 'ouattara-dougnimata-ahmed', 'ouattara.ahmed@hotmail.com', '2721245682', NULL, NULL, NULL, NULL, NULL),
(130, NULL, 'AKUE Marius Ahouo', 'ABEY', 'abey-akue-marius-ahouo', 'abeymarius0019@gmail.com', '0707576444', NULL, NULL, NULL, NULL, NULL),
(131, NULL, 'BAHI Charles', 'GOGOUA', 'gogoua-bahi-charles', 'cgogoua@alltaxci.com', '0707491510', NULL, NULL, NULL, NULL, NULL),
(132, NULL, 'Jean Claude', 'WOGNIN', 'wognin-jean-claude', 'wognlntaxlaw@gmail.com', '0545005082', NULL, NULL, NULL, NULL, NULL),
(133, NULL, 'JULIEN Eymard', 'MOSSO', 'mosso-julien-eymard', 'mosso_julien@yahoo.fr', '0707580567', NULL, NULL, NULL, NULL, NULL),
(134, NULL, 'Kouassi', 'AMANI', 'amani-kouassi', 'amaniconseil38@gmail.com', '2720326285', NULL, NULL, NULL, NULL, NULL),
(135, NULL, 'ECRA Serge Yves', 'KOUAMELAN', 'kouamelan-ecra-serge-yves', 'ksergeyves@gmail.com', '0707200272', NULL, NULL, NULL, NULL, NULL),
(136, NULL, 'OUEKAN Hubert', 'OUATTARA', 'ouattara-ouekan-hubert', 'ouattara_hubert@yahoo.fr', '0707969698', NULL, NULL, NULL, NULL, NULL),
(259, 1, 'KRAGBA ARTHUR', 'DJEKOURI', 'me-dje-kouri-kragba-arthur', 'arthurdjekouri@yahoo.com', '225 0707919679', 'Abidjan Marcory, Rue Laurraine, face à l\'Église Ste-Thérèse.', NULL, NULL, 'https://cncj-ci.ci/wp-content/uploads/2025/10/Me-DJEKOURI-KRAGBA-ARTHUR-scaled.jpg', 128),
(260, 6, 'TIENDAGA', 'SIGATA', 'sigata-tiendaga', 'info@markus-group.com', '+225 0709857829 / 0566018866', 'Bingerville Feh Kesse, Résidence Nehemie, Appt B5 (1er étage)', NULL, NULL, 'https://cncj-ci.ci/wp-content/uploads/2024/11/LOGO-SITE-INTERNET-copie.png', 132),
(261, 5, 'Guy Pascal Nakhan', 'TOURE', 'toure-guy-pascal-nakhan', 'nakhanconsulting@gmail.com', '+225 2722209325', 'Abidjan Cocody 9ème Tranche, Immeuble KLAS M en face de CGK', NULL, NULL, 'https://cncj-ci.ci/wp-content/uploads/2025/10/Me-TOURE-GUY-PASCAL-NAKHAN-scaled.jpg', NULL),
(262, 9, 'JONAS', 'ATTOUNGBRE', 'attoungbre-yao-jonas', 'maitre.attoungbre@pro-juris.ci', '+225 27 22 28 89 48/ +225 07 59 73 04 59', 'Cocody Faya, Quartier Génie 2000, Rue Cloud Stéroids, Résidence Fasma, Immeuble abritant la Supérette SUPMARKET, Escalie', NULL, NULL, 'https://cncj-ci.ci/wp-content/uploads/2025/10/Me-ATTOUNGBRE-YAO-JONAS-scaled.jpg', 135);

--
-- Déchargement des données de la table `phone`
--

INSERT INTO `phone` (`id`, `lawyer_id`, `cabinet_id`, `label`, `number`, `is_primary`, `position`) VALUES
(1, 5, NULL, 'principal', '0506337347', 1, 0),
(2, 6, NULL, 'principal', '0707039519', 1, 0),
(3, 90, NULL, 'principal', '0709392019', 1, 0),
(4, 7, NULL, 'principal', '0707012561', 1, 0),
(5, 91, NULL, 'principal', '2721230190', 1, 0),
(6, 8, NULL, 'principal', '0777430625', 1, 0),
(7, 9, NULL, 'principal', '0748377773', 1, 0),
(8, 92, NULL, 'principal', '2721241760', 1, 0),
(9, 10, NULL, 'principal', '0707968355', 1, 0),
(10, 11, NULL, 'principal', '0707931335', 1, 0),
(11, 93, NULL, 'principal', '0505090056', 1, 0),
(12, 12, NULL, 'principal', '0101100662', 1, 0),
(13, 13, NULL, 'principal', '0708659900', 1, 0),
(14, 14, NULL, 'principal', '0544766512', 1, 0),
(15, 94, NULL, 'principal', '0708390191', 1, 0),
(16, 15, NULL, 'principal', '0708190677', 1, 0),
(17, 16, NULL, 'principal', '0506468624', 1, 0),
(18, 95, NULL, 'principal', '0505951232', 1, 0),
(19, 17, NULL, 'principal', '0708904987', 1, 0),
(20, 18, NULL, 'principal', '0506312731', 1, 0),
(21, 19, NULL, 'principal', '2722466971', 1, 0),
(22, 20, NULL, 'principal', '0708085727', 1, 0),
(23, 21, NULL, 'principal', '0707648107', 1, 0),
(24, 22, NULL, 'principal', '0758742335', 1, 0),
(25, 23, NULL, 'principal', '2520009149', 1, 0),
(26, 24, NULL, 'principal', '0707905232', 1, 0),
(27, 25, NULL, 'principal', '0102503756', 1, 0),
(28, 26, NULL, 'principal', '0707058971', 1, 0),
(29, 27, NULL, 'principal', '0747068393', 1, 0),
(30, 29, NULL, 'principal', '0748336368', 1, 0),
(31, 96, NULL, 'principal', '0758421919', 1, 0),
(32, 97, NULL, 'principal', '0759051771', 1, 0),
(33, 30, NULL, 'principal', '2722599270', 1, 0),
(34, 31, NULL, 'principal', '2722016099', 1, 0),
(35, 98, NULL, 'principal', '0101463847', 1, 0),
(36, 32, NULL, 'principal', '0101318180', 1, 0),
(37, 33, NULL, 'principal', '0505089973', 1, 0),
(38, 99, NULL, 'principal', '0707498282', 1, 0),
(39, 34, NULL, 'principal', '2722428823', 1, 0),
(40, 35, NULL, 'principal', '2722536971', 1, 0),
(41, 36, NULL, 'principal', '0748355483', 1, 0),
(42, 37, NULL, 'principal', '2720355469', 1, 0),
(43, 38, NULL, 'principal', '0747230650', 1, 0),
(44, 100, NULL, 'principal', '2721230190', 1, 0),
(45, 28, NULL, 'principal', '0709387777', 1, 0),
(46, 39, NULL, 'principal', '0708426364', 1, 0),
(47, 40, NULL, 'principal', '0707408852', 1, 0),
(48, 41, NULL, 'principal', '0759441154', 1, 0),
(49, 42, NULL, 'principal', '0749266226', 1, 0),
(50, 44, NULL, 'principal', '0708141351', 1, 0),
(51, 101, NULL, 'principal', '0102744971', 1, 0),
(52, 45, NULL, 'principal', '2720216007', 1, 0),
(53, 43, NULL, 'principal', '0789825515', 1, 0),
(54, 46, NULL, 'principal', '0707260699', 1, 0),
(55, 47, NULL, 'principal', '0708303003', 1, 0),
(56, 48, NULL, 'principal', '0708404049', 1, 0),
(57, 49, NULL, 'principal', '0505481646', 1, 0),
(58, 102, NULL, 'principal', '0708168410', 1, 0),
(59, 103, NULL, 'principal', '0707007981', 1, 0),
(60, 104, NULL, 'principal', '0505714604', 1, 0),
(61, 50, NULL, 'principal', '0759777777', 1, 0),
(62, 51, NULL, 'principal', '0101418665', 1, 0),
(63, 52, NULL, 'principal', '2720236955', 1, 0),
(64, 53, NULL, 'principal', '0748234552', 1, 0),
(65, 105, NULL, 'principal', '0708301270', 1, 0),
(66, 54, NULL, 'principal', '0789739644', 1, 0),
(67, 55, NULL, 'principal', '0709571525', 1, 0),
(68, 56, NULL, 'principal', '0747077030', 1, 0),
(69, 106, NULL, 'principal', '0747505152', 1, 0),
(70, 57, NULL, 'principal', '0708714373', 1, 0),
(71, 107, NULL, 'principal', '2722435890', 1, 0),
(72, 58, NULL, 'principal', '2722478395', 1, 0),
(73, 108, NULL, 'principal', '0708092041', 1, 0),
(74, 109, NULL, 'principal', '0747717171', 1, 0),
(75, 59, NULL, 'principal', '0707900090', 1, 0),
(76, 60, NULL, 'principal', '0505282222', 1, 0),
(77, 61, NULL, 'principal', '0707144999', 1, 0),
(78, 62, NULL, 'principal', '2720327626', 1, 0),
(79, 110, NULL, 'principal', '0707216021', 1, 0),
(80, 111, NULL, 'principal', '2720236955', 1, 0),
(81, 63, NULL, 'principal', '0707994800', 1, 0),
(82, 64, NULL, 'principal', '0707037402', 1, 0),
(83, 65, NULL, 'principal', '0709136464', 1, 0),
(84, 112, NULL, 'principal', '0101860036', 1, 0),
(85, 66, NULL, 'principal', '0757531733', 1, 0),
(86, 113, NULL, 'principal', '2721245682', 1, 0),
(87, 114, NULL, 'principal', '2522004263', 1, 0),
(88, 115, NULL, 'principal', '0708025038', 1, 0),
(89, 116, NULL, 'principal', '0748313893', 1, 0),
(90, 67, NULL, 'principal', '0506292000', 1, 0),
(91, 68, NULL, 'principal', '0707522561', 1, 0),
(92, 117, NULL, 'principal', '0709170477', 1, 0),
(93, 69, NULL, 'principal', '0709607525', 1, 0),
(94, 70, NULL, 'principal', '0707565739', 1, 0),
(95, 71, NULL, 'principal', '0707058970', 1, 0),
(96, 118, NULL, 'principal', '0789552164', 1, 0),
(97, 72, NULL, 'principal', '0707701582', 1, 0),
(98, 119, NULL, 'principal', '0506372071', 1, 0),
(99, 73, NULL, 'principal', '0707574026', 1, 0),
(100, 120, NULL, 'principal', '0546107759', 1, 0),
(101, 74, NULL, 'principal', '0506305192', 1, 0),
(102, 75, NULL, 'principal', '2720236955', 1, 0),
(103, 121, NULL, 'principal', '0757108101', 1, 0),
(104, 76, NULL, 'principal', '2722007728', 1, 0),
(105, 122, NULL, 'principal', '0505838562', 1, 0),
(106, 123, NULL, 'principal', '0707577849', 1, 0),
(107, 124, NULL, 'principal', '0708588094', 1, 0),
(108, 77, NULL, 'principal', '0708977772', 1, 0),
(109, 78, NULL, 'principal', '0584333621', 1, 0),
(110, 125, NULL, 'principal', '0708442442', 1, 0),
(111, 126, NULL, 'principal', '0757562313', 1, 0),
(112, 127, NULL, 'principal', '0709606008', 1, 0),
(113, 128, NULL, 'principal', '0707737415', 1, 0),
(114, 79, NULL, 'principal', '0505539983', 1, 0),
(115, 129, NULL, 'principal', '2721245682', 1, 0),
(116, 80, NULL, 'principal', '2722422088', 1, 0),
(117, 81, NULL, 'principal', '0748154316', 1, 0),
(118, 82, NULL, 'principal', '0708961616', 1, 0),
(119, 130, NULL, 'principal', '0707576444', 1, 0),
(120, 83, NULL, 'principal', '0707330732', 1, 0),
(121, 131, NULL, 'principal', '0707491510', 1, 0),
(122, 84, NULL, 'principal', '0707525389', 1, 0),
(123, 132, NULL, 'principal', '0545005082', 1, 0),
(124, 85, NULL, 'principal', '2722416053', 1, 0),
(125, 133, NULL, 'principal', '0707580567', 1, 0),
(126, 134, NULL, 'principal', '2720326285', 1, 0),
(127, 135, NULL, 'principal', '0707200272', 1, 0),
(128, 86, NULL, 'principal', '0777430088', 1, 0),
(129, 136, NULL, 'principal', '0707969698', 1, 0),
(130, 87, NULL, 'principal', '0707361523', 1, 0),
(131, 88, NULL, 'principal', '0707084724', 1, 0),
(132, 89, NULL, 'principal', '0708087878', 1, 0);

--
-- Déchargement des données de la table `specialty`
--

INSERT INTO `specialty` (`id`, `name`, `slug`, `description`) VALUES
(1, 'Fiscal', 'fiscal', NULL),
(2, 'Affaires', 'affaires', NULL),
(3, 'Pénal', 'penal', NULL),
(4, 'Social', 'social', NULL),
(5, 'Immobilier', 'immobilier', NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `address`
--
ALTER TABLE `address`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `cabinet`
--
ALTER TABLE `cabinet`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_4CED05B0989D9B62` (`slug`),
  ADD KEY `IDX_CABINET_TYPE` (`type_id`),
  ADD KEY `IDX_CABINET_MP` (`managing_partner_id`),
  ADD KEY `IDX_CABINET_ADDRESS` (`address_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `address`
--
ALTER TABLE `address`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=136;

--
-- AUTO_INCREMENT pour la table `cabinet`
--
ALTER TABLE `cabinet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `cabinet`
--
ALTER TABLE `cabinet`
  ADD CONSTRAINT `FK_CABINET_ADDRESS` FOREIGN KEY (`address_id`) REFERENCES `address` (`id`),
  ADD CONSTRAINT `FK_CABINET_MP` FOREIGN KEY (`managing_partner_id`) REFERENCES `lawyer` (`id`),
  ADD CONSTRAINT `FK_CABINET_TYPE` FOREIGN KEY (`type_id`) REFERENCES `cabinet_type` (`id`);
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
