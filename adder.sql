-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Creato il: Gen 24, 2025 alle 20:30
-- Versione del server: 8.0.31
-- Versione PHP: 8.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `amzrecen_adder`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `query_salvate`
--

CREATE TABLE `query_salvate` (
  `id` int NOT NULL,
  `gruppo` varchar(33) NOT NULL,
  `q` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dump dei dati per la tabella `query_salvate`
--

INSERT INTO `query_salvate` (`id`, `gruppo`, `q`) VALUES
(2, 'gratuitosamazo', 'alv'),
(3, 'nicofreeamazon', 'pr'),
(2, 'recensione_gratis', 'a'),
(4, 'AmznTester5stelleugualegratis', 'w'),
(2, 'recensioni_amazon_prodotti', 'sis'),
(3, 'prodottigratisofficial', 'law'),
(3, 'testiamolitutti', 'st'),
(1, 'codiciscontowikideal', 'a'),
(2, 'tuttogr', 'fl'),
(3, 'totorereviewit', 'ang'),
(4, 'barbyreviews', 'lucae'),
(3, 'gruppoofferteghost', 'a'),
(5, 'itarisparmio', 'gim'),
(1, 'offerteadaltafrequenza', 'a'),
(5, 'offerteadaltafrequenza', 'emq'),
(1, 'itarisparmio', 'a'),
(5, 'sconti5', 'ab'),
(4, 'offerteitalia8', 'a'),
(3, 'angeboterabatte', 'a'),
(4, 'reviews_dee', 'nc'),
(3, 'kostenlosamazonn', 'mc'),
(3, 'mundo_amazon', 'a'),
(4, 'gigadescuentosamazon', 'a'),
(5, 'offerteitalia8', 'val'),
(2, 'testiamolitutti', 'giusa'),
(2, 'testeramzn', 'cn'),
(4, '-1001693185194', 'a'),
(3, 'testenamazonde', 'hq'),
(4, 'testenamazonde', 'a'),
(4, 'recensioni_gratis', 'i'),
(2, 'recensoriamazon5stelle', 'claf'),
(4, 'futurefootballfinanceitaly', 'lu'),
(4, 'amazontestergratuitifly', 'ad'),
(3, 'futurefootballfinanceitaly', 'mattz'),
(5, 'solosconti23', 'nex'),
(4, 'amazontop_new', 'pej'),
(5, 'testenamazonde', 'dd'),
(4, 'pioggiadic', 'alb'),
(4, '1619280531', 'a'),
(5, 'offerteamazonoffer', 'aled'),
(4, '-1001619280531', 'antonie'),
(4, 'shoppingbyfree3', 'a'),
(2, 'amazotnvip', 'a'),
(2, 'split_file_2', 'a'),
(2, 'testenamazondepart1', 'a'),
(4, 'amazotnvip', 'he'),
(2, 'kostenloseproduktedeutschland', 'bq'),
(4, 'netflix_serie_tv_gratis_ita', 'michelm'),
(4, 'testprodottiitalia', 'f'),
(4, 'superofferte_online', 'paa'),
(2, 'superofferte_online', 'carme'),
(5, 'superofferte_online', 'valer');

-- --------------------------------------------------------

--
-- Struttura della tabella `utenti`
--

CREATE TABLE `utenti` (
  `id` int NOT NULL,
  `token` varchar(32) NOT NULL,
  `bot` json DEFAULT NULL,
  `membri` json DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dump dei dati per la tabella `utenti`
--

INSERT INTO `utenti` (`id`, `token`, `bot`, `membri`) VALUES
(1, 'd94dda89ed7132a9c6759173bdd8966d', NULL, NULL),
(2, 'd279471638fcfdd60ad5231bbe938c00', '{\"proxy\": \"EU\", \"metodo\": \"database\", \"riavvia\": \"0\", \"sorgenti\": \"testitaliaPart1\", \"attesa_max\": \"2\", \"attesa_min\": \"1\", \"max_riavvii\": \"0\", \"destinazione\": \"testprodottiitalia\", \"nomi_bannati\": \"\", \"status_consentiti\": [\"userStatusOffline\", \"userStatusOnline\", \"userStatusRecently\", \"userStatusLastWeek\", \"userStatusLastMonth\"], \"data_online_minima\": \"\", \"data_online_massima\": \"+ 50 years\", \"massima_aggiunta_per_account\": \"4455\"}', '{\"proxy\": \"EU\", \"riavvia\": \"0\", \"sorgente\": \"superofferte_online\", \"attesa_max\": \"3\", \"attesa_min\": \"1\", \"max_riavvii\": \"\", \"account_da_non_usare\": [\"+393761656610\"]}'),
(3, 'c6076ae0a9e278d4ed642530f267be4a', '{\"proxy\": \"EU\", \"metodo\": \"database\", \"riavvia\": \"0\", \"sorgenti\": \"nicofreeamazonPart3\", \"attesa_max\": \"20\", \"attesa_min\": \"15\", \"max_riavvii\": \"0\", \"destinazione\": \"testprodottiitalia\", \"nomi_bannati\": \"\", \"status_consentiti\": [\"userStatusOffline\", \"userStatusOnline\", \"userStatusRecently\", \"userStatusLastWeek\", \"userStatusLastMonth\"], \"data_online_minima\": \"\", \"data_online_massima\": \"+ 50 years\", \"account_da_non_usare\": [\"+40721413903\"], \"evita_caratteri_non_latini\": \"on\", \"massima_aggiunta_per_account\": \"40\", \"salta_controllo_membro_faceva_gia_parte_del_gruppo\": \"on\"}', '{\"proxy\": \"EU\", \"riavvia\": \"\", \"sorgente\": \"futurefootballfinanceitaly\", \"attesa_max\": \"3\", \"attesa_min\": \"1\", \"account_da_non_usare\": [\"+212654744596\", \"+359885616237\", \"+393273846483\", \"+40721413903\", \"+543813379925\", \"+543816251945\", \"+5492284531965\", \"+66619681357\", \"+66628591054\", \"+66827459291\", \"+917667378858\"]}'),
(4, 'cf53ed0550e7e8425ded37abf335bd88', '{\"proxy\": \"AM\", \"metodo\": \"database\", \"riavvia\": \"0\", \"sorgenti\": \"testitaliaPart1\", \"attesa_max\": \"40\", \"attesa_min\": \"15\", \"max_riavvii\": \"0\", \"destinazione\": \"testprodottiitalia\", \"nomi_bannati\": \"\", \"status_consentiti\": [\"userStatusOffline\", \"userStatusOnline\", \"userStatusRecently\", \"userStatusLastWeek\", \"userStatusLastMonth\"], \"data_online_minima\": \"\", \"data_online_massima\": \"+ 50 years\", \"massima_aggiunta_per_account\": \"45\"}', '{\"proxy\": \"EU\", \"riavvia\": \"1\", \"sorgente\": \"Ninja_Tester\", \"attesa_max\": \"2\", \"attesa_min\": \"1\", \"max_riavvii\": \"2\", \"account_da_non_usare\": [\"+16193827731\", \"+18034102169\", \"+212615827555\", \"+393283158672\", \"+393761437518\", \"+40701222501\", \"+40720912044\", \"+639359006009\", \"+639959255941\"]}'),
(5, '6b75367f5b33e31197dd23ee97df8259', '{\"proxy\": \"EU\", \"metodo\": \"database\", \"riavvia\": \"10\", \"sorgenti\": \"sop1\", \"attesa_max\": \"20\", \"attesa_min\": \"10\", \"max_riavvii\": \"0\", \"destinazione\": \"araregroup\", \"nomi_bannati\": \"Mohammed,md,k,j,x,h,w, abdul,seller, amazon\", \"status_consentiti\": [\"userStatusOffline\", \"userStatusOnline\", \"userStatusRecently\", \"userStatusLastWeek\", \"userStatusLastMonth\"], \"data_online_minima\": \"\", \"data_online_massima\": \"+ 50 years\", \"controlla_gruppi_in_comune\": \"on\", \"evita_caratteri_non_latini\": \"on\", \"massima_aggiunta_per_account\": \"50\", \"aggiungi_solo_membri_con_username\": \"on\", \"salta_controllo_membro_faceva_gia_parte_del_gruppo\": \"on\"}', '{\"proxy\": \"EU\", \"riavvia\": \"\", \"sorgente\": \"amazonfreeforgermany\", \"attesa_max\": \"3\", \"attesa_min\": \"1\", \"max_riavvii\": \"\", \"account_da_non_usare\": [\"+917667378858\"]}'),
(6, '571d3659c2d9691af9ff84bb0991eb9b', NULL, NULL);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `utenti`
--
ALTER TABLE `utenti`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `utenti`
--
ALTER TABLE `utenti`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
