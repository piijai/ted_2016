-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Erstellungszeit: 04. Feb 2017 um 15:26
-- Server-Version: 10.1.16-MariaDB
-- PHP-Version: 7.0.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `webengineering_sa`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ted2016_hj_antwort`
--

CREATE TABLE `ted2016_hj_antwort` (
  `id` int(11) NOT NULL,
  `antwort` varchar(100) NOT NULL,
  `anzahl` int(11) NOT NULL,
  `umfrage_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `ted2016_hj_antwort`
--

INSERT INTO `ted2016_hj_antwort` (`id`, `antwort`, `anzahl`, `umfrage_id`) VALUES
(120, 'Ja', 32, 32),
(121, 'Nein', 123, 32),
(122, 'Vielleicht', 5543, 32),
(128, 'Huhn', 6234, 35),
(129, 'Ei', 643, 35),
(130, 'Ja', 32, 36),
(131, 'Nein', 17, 36),
(132, 'Vielleicht', 1, 36),
(133, 'Huhn', 28, 37),
(134, 'Ei', 1, 37),
(149, 'Ja', 0, 43),
(150, 'Nein', 0, 43),
(151, 'Vielleicht', 0, 43),
(152, '1', 0, 34),
(153, '2', 0, 34),
(154, '3', 0, 34),
(155, 'Welt', 0, 44),
(156, '2', 0, 44);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ted2016_hj_dozent`
--

CREATE TABLE `ted2016_hj_dozent` (
  `id` int(11) NOT NULL,
  `name` varchar(10) NOT NULL,
  `pass` varchar(64) NOT NULL,
  `link` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `ted2016_hj_dozent`
--

INSERT INTO `ted2016_hj_dozent` (`id`, `name`, `pass`, `link`) VALUES
(5, 'Nico', '7b70d3ab4c7641542e1f158b458eeae7cfb7bdb815d4110cc6178bafcfdf43f8', 'nico'),
(7, 'admin', '82a79f11b4acb52a642ef7e339dfce4aa92ff65ed2e7ab702d798dbe10eca0b8', 'admin');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ted2016_hj_umfrage`
--

CREATE TABLE `ted2016_hj_umfrage` (
  `id` int(11) NOT NULL,
  `frage` text NOT NULL,
  `dozent_id` int(11) NOT NULL,
  `aktiv` tinyint(1) NOT NULL,
  `visualisierung` smallint(6) NOT NULL,
  `timer` int(11) NOT NULL,
  `date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `ted2016_hj_umfrage`
--

INSERT INTO `ted2016_hj_umfrage` (`id`, `frage`, `dozent_id`, `aktiv`, `visualisierung`, `timer`, `date`) VALUES
(32, 'Willst du mit mir gehen?', 7, -1, 0, 10, '2016-09-15 17:19:50'),
(34, '1,2 oder 3?', 7, 0, 0, 20, '2016-09-15 17:27:13'),
(35, 'Huhn oder Ei?', 7, -1, 1, 10, '2016-09-15 17:20:14'),
(36, 'Willst du mit mir gehen?', 7, -1, 0, 10, '2016-09-15 17:27:21'),
(37, 'Huhn oder Ei?', 7, 0, 1, 10, '2016-09-15 17:25:53'),
(43, 'Willst du mit mir gehen?', 7, 0, 0, 10, '2017-01-11 14:45:54'),
(44, 'Hallo', 7, 0, 0, 10, NULL);

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `ted2016_hj_antwort`
--
ALTER TABLE `ted2016_hj_antwort`
  ADD PRIMARY KEY (`id`),
  ADD KEY `umfrage_id` (`umfrage_id`);

--
-- Indizes für die Tabelle `ted2016_hj_dozent`
--
ALTER TABLE `ted2016_hj_dozent`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `link` (`link`);

--
-- Indizes für die Tabelle `ted2016_hj_umfrage`
--
ALTER TABLE `ted2016_hj_umfrage`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dozent_id` (`dozent_id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `ted2016_hj_antwort`
--
ALTER TABLE `ted2016_hj_antwort`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=157;
--
-- AUTO_INCREMENT für Tabelle `ted2016_hj_dozent`
--
ALTER TABLE `ted2016_hj_dozent`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT für Tabelle `ted2016_hj_umfrage`
--
ALTER TABLE `ted2016_hj_umfrage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;
--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `ted2016_hj_antwort`
--
ALTER TABLE `ted2016_hj_antwort`
  ADD CONSTRAINT `antwort_umfrage_fk` FOREIGN KEY (`umfrage_id`) REFERENCES `ted2016_hj_umfrage` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `ted2016_hj_umfrage`
--
ALTER TABLE `ted2016_hj_umfrage`
  ADD CONSTRAINT `umfrage_dozent_fk` FOREIGN KEY (`dozent_id`) REFERENCES `ted2016_hj_dozent` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
