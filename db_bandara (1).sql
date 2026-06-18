-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 08, 2026 at 06:13 AM
-- Server version: 8.0.45-0ubuntu0.24.04.1
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_bandara`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_refund_ticket` (IN `p_ticket_id` INT)   BEGIN
    UPDATE ticket
    SET ticket_status = 'REFUNDED'
    WHERE ticket_id = p_ticket_id
      AND refundable = TRUE$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_reschedule_ticket` (IN `p_ticket_id` INT, IN `p_new_flight_id` INT)   BEGIN
    UPDATE ticket
    SET flight_id = p_new_flight_id,
        ticket_status = 'RESCHEDULED'
    WHERE ticket_id = p_ticket_id$$

--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_flight_duration` (`dep` TIME, `arr` TIME) RETURNS INT DETERMINISTIC BEGIN
    RETURN TIMESTAMPDIFF(HOUR, dep, arr)$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `airline`
--

CREATE TABLE `airline` (
  `airline_id` int NOT NULL,
  `airline_code` varchar(5) NOT NULL,
  `airline_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `airline`
--

INSERT INTO `airline` (`airline_id`, `airline_code`, `airline_name`) VALUES
(1, 'IU', 'Super Air Jet'),
(2, 'GA', 'Garuda Indonesia'),
(3, 'JT', 'Lion Air'),
(4, 'ID', 'Batik Air'),
(5, 'QG', 'Citilink'),
(6, 'AK', 'AirAsia'),
(7, 'SQ', 'Singapore Airlines'),
(8, 'MH', 'Malaysia Airlines'),
(9, 'EK', 'Emirates'),
(10, 'QR', 'Qatar Airways'),
(11, 'TG', 'Thai Airways'),
(12, 'NH', 'All Nippon Airways'),
(13, 'JL', 'Japan Airlines'),
(14, 'CX', 'Cathay Pacific'),
(15, 'KE', 'Korean Air'),
(16, 'OZ', 'Asiana Airlines'),
(17, 'SV', 'Saudi Airlines'),
(18, 'EY', 'Etihad Airways'),
(19, 'AF', 'Air France'),
(20, 'LH', 'Lufthansa');

-- --------------------------------------------------------

--
-- Table structure for table `airport`
--

CREATE TABLE `airport` (
  `airport_id` int NOT NULL,
  `airport_code` char(3) NOT NULL,
  `airport_name` varchar(150) NOT NULL,
  `city` varchar(100) NOT NULL,
  `terminal` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `airport`
--

INSERT INTO `airport` (`airport_id`, `airport_code`, `airport_name`, `city`, `terminal`) VALUES
(1, 'CGK', 'Soekarno Hatta Intl', 'Jakarta', '2E'),
(2, 'HLP', 'Halim Perdanakusuma', 'Jakarta', 'A'),
(3, 'DPS', 'Ngurah Rai', 'Denpasar', 'I'),
(4, 'SUB', 'Juanda', 'Surabaya', '1'),
(5, 'PLW', 'Mutiara SIS Aljufri', 'Palu', '1'),
(6, 'UPG', 'Sultan Hasanuddin', 'Makassar', 'D'),
(7, 'KNO', 'Kualanamu', 'Medan', '1'),
(8, 'BPN', 'Sepinggan', 'Balikpapan', '1'),
(9, 'BDO', 'Husein Sastranegara', 'Bandung', '1'),
(10, 'YIA', 'Yogyakarta Intl', 'Yogyakarta', '1'),
(11, 'SOC', 'Adi Soemarmo', 'Solo', '1'),
(12, 'SRG', 'Ahmad Yani', 'Semarang', 'A'),
(13, 'PNK', 'Supadio', 'Pontianak', '1'),
(14, 'PKU', 'Sultan Syarif Kasim', 'Pekanbaru', '1'),
(15, 'JOG', 'Adisutjipto', 'Yogyakarta', '2'),
(16, 'AMI', 'Selaparang', 'Mataram', '1'),
(17, 'LOP', 'Lombok Intl', 'Lombok', '1'),
(18, 'TTE', 'Sultan Babullah', 'Ternate', '1'),
(19, 'DJJ', 'Sentani', 'Jayapura', '1'),
(20, 'BIK', 'Frans Kaisiepo', 'Biak', '1');

-- --------------------------------------------------------

--
-- Table structure for table `baggage`
--

CREATE TABLE `baggage` (
  `baggage_id` int NOT NULL,
  `ticket_id` int NOT NULL,
  `cabin_weight_kg` int DEFAULT '0',
  `checked_weight_kg` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `baggage`
--

INSERT INTO `baggage` (`baggage_id`, `ticket_id`, `cabin_weight_kg`, `checked_weight_kg`) VALUES
(1, 1, 7, 20),
(2, 2, 7, 30),
(3, 3, 7, 15),
(4, 4, 7, 20),
(5, 5, 7, 25),
(6, 6, 7, 10),
(7, 7, 7, 30),
(8, 8, 7, 20),
(9, 9, 7, 40),
(10, 10, 7, 30),
(11, 11, 7, 20),
(12, 12, 7, 20),
(13, 13, 7, 25),
(14, 14, 7, 20),
(15, 15, 7, 20),
(16, 16, 7, 15),
(17, 17, 7, 20),
(18, 18, 7, 30),
(19, 19, 7, 20),
(20, 20, 7, 20);

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `booking_id` int NOT NULL,
  `booking_code` varchar(10) NOT NULL,
  `platform_booking_id` varchar(20) DEFAULT NULL,
  `booking_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `booking_status` enum('ACTIVE','CANCELLED','COMPLETED') DEFAULT 'ACTIVE'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`booking_id`, `booking_code`, `platform_booking_id`, `booking_date`, `booking_status`) VALUES
(1, 'PNR001', 'TRX001', '2026-01-24 17:00:24', 'ACTIVE'),
(2, 'PNR002', 'TRX002', '2026-01-24 17:00:24', 'ACTIVE'),
(3, 'PNR003', 'TRX003', '2026-01-24 17:00:24', 'ACTIVE'),
(4, 'PNR004', 'TRX004', '2026-01-24 17:00:24', 'ACTIVE'),
(5, 'PNR005', 'TRX005', '2026-01-24 17:00:24', 'ACTIVE'),
(6, 'PNR006', 'TRX006', '2026-01-24 17:00:24', 'ACTIVE'),
(7, 'PNR007', 'TRX007', '2026-01-24 17:00:24', 'ACTIVE'),
(8, 'PNR008', 'TRX008', '2026-01-24 17:00:24', 'ACTIVE'),
(9, 'PNR009', 'TRX009', '2026-01-24 17:00:24', 'ACTIVE'),
(10, 'PNR010', 'TRX010', '2026-01-24 17:00:24', 'ACTIVE'),
(11, 'PNR011', 'TRX011', '2026-01-24 17:00:24', 'ACTIVE'),
(12, 'PNR012', 'TRX012', '2026-01-24 17:00:24', 'ACTIVE'),
(13, 'PNR013', 'TRX013', '2026-01-24 17:00:24', 'ACTIVE'),
(14, 'PNR014', 'TRX014', '2026-01-24 17:00:24', 'ACTIVE'),
(15, 'PNR015', 'TRX015', '2026-01-24 17:00:24', 'ACTIVE'),
(16, 'PNR016', 'TRX016', '2026-01-24 17:00:24', 'ACTIVE'),
(17, 'PNR017', 'TRX017', '2026-01-24 17:00:24', 'ACTIVE'),
(18, 'PNR018', 'TRX018', '2026-01-24 17:00:24', 'ACTIVE'),
(19, 'PNR019', 'TRX019', '2026-01-24 17:00:24', 'ACTIVE'),
(20, 'PNR020', 'TRX020', '2026-01-24 17:00:24', 'ACTIVE');

-- --------------------------------------------------------

--
-- Table structure for table `checkin`
--

CREATE TABLE `checkin` (
  `checkin_id` int NOT NULL,
  `ticket_id` int NOT NULL,
  `checkin_code` varchar(10) DEFAULT NULL,
  `checkin_qr` text,
  `checkin_deadline` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `checkin`
--

INSERT INTO `checkin` (`checkin_id`, `ticket_id`, `checkin_code`, `checkin_qr`, `checkin_deadline`) VALUES
(1, 1, 'CI001', 'QR001', '2025-02-19 05:30:00'),
(2, 2, 'CI002', 'QR002', '2025-02-20 06:30:00'),
(3, 3, 'CI003', 'QR003', '2025-02-21 05:00:00'),
(4, 4, 'CI004', 'QR004', '2025-02-22 07:30:00'),
(5, 5, 'CI005', 'QR005', '2025-02-23 11:30:00'),
(6, 6, 'CI006', 'QR006', '2025-02-24 13:30:00'),
(7, 7, 'CI007', 'QR007', '2025-02-25 16:30:00'),
(8, 8, 'CI008', 'QR008', '2025-02-26 05:30:00'),
(9, 9, 'CI009', 'QR009', '2025-02-26 23:00:00'),
(10, 10, 'CI010', 'QR010', '2025-02-27 23:30:00'),
(11, 11, 'CI011', 'QR011', '2025-03-01 08:30:00'),
(12, 12, 'CI012', 'QR012', '2025-03-02 21:30:00'),
(13, 13, 'CI013', 'QR013', '2025-03-03 19:30:00'),
(14, 14, 'CI014', 'QR014', '2025-03-04 20:30:00'),
(15, 15, 'CI015', 'QR015', '2025-03-04 22:40:00'),
(16, 16, 'CI016', 'QR016', '2025-03-06 00:00:00'),
(17, 17, 'CI017', 'QR017', '2025-03-07 01:30:00'),
(18, 18, 'CI018', 'QR018', '2025-03-08 00:30:00'),
(19, 19, 'CI019', 'QR019', '2025-03-09 21:30:00'),
(20, 20, 'CI020', 'QR020', '2025-03-10 21:00:00');

--
-- Triggers `checkin`
--
DELIMITER $$
CREATE TRIGGER `trg_set_checkin_deadline` BEFORE INSERT ON `checkin` FOR EACH ROW BEGIN
    DECLARE dep_time DATETIME$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `flight`
--

CREATE TABLE `flight` (
  `flight_id` int NOT NULL,
  `airline_id` int NOT NULL,
  `flight_number` varchar(10) NOT NULL,
  `flight_date` date NOT NULL,
  `departure_time` time NOT NULL,
  `arrival_time` time NOT NULL,
  `origin_airport_id` int NOT NULL,
  `destination_airport_id` int NOT NULL,
  `aircraft_class` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `flight`
--

INSERT INTO `flight` (`flight_id`, `airline_id`, `flight_number`, `flight_date`, `departure_time`, `arrival_time`, `origin_airport_id`, `destination_airport_id`, `aircraft_class`) VALUES
(1, 1, 'IU101', '2025-02-19', '07:00:00', '10:40:00', 1, 5, 'Economy'),
(2, 2, 'GA202', '2025-02-20', '08:00:00', '09:30:00', 1, 4, 'Business'),
(3, 3, 'JT303', '2025-02-21', '06:30:00', '08:00:00', 3, 1, 'Economy'),
(4, 4, 'ID404', '2025-02-22', '09:00:00', '11:00:00', 4, 3, 'Economy'),
(5, 5, 'QG505', '2025-02-23', '13:00:00', '14:30:00', 1, 10, 'Economy'),
(6, 6, 'AK606', '2025-02-24', '15:00:00', '17:00:00', 10, 3, 'Economy'),
(7, 7, 'SQ707', '2025-02-25', '18:00:00', '21:00:00', 1, 8, 'Business'),
(8, 8, 'MH808', '2025-02-26', '07:00:00', '09:30:00', 8, 1, 'Economy'),
(9, 9, 'EK909', '2025-02-27', '00:30:00', '08:00:00', 1, 9, 'First'),
(10, 10, 'QR010', '2025-02-28', '01:00:00', '07:30:00', 1, 10, 'Business'),
(11, 11, 'TG111', '2025-03-01', '10:00:00', '14:00:00', 1, 6, 'Economy'),
(12, 12, 'NH222', '2025-03-02', '23:00:00', '06:00:00', 1, 11, 'Economy'),
(13, 13, 'JL333', '2025-03-03', '21:00:00', '05:00:00', 1, 12, 'Business'),
(14, 14, 'CX444', '2025-03-04', '22:00:00', '04:30:00', 1, 13, 'Economy'),
(15, 15, 'KE555', '2025-03-05', '00:10:00', '07:00:00', 1, 14, 'Economy'),
(16, 16, 'OZ666', '2025-03-06', '01:30:00', '08:10:00', 1, 15, 'Economy'),
(17, 17, 'SV777', '2025-03-07', '03:00:00', '11:00:00', 1, 16, 'Economy'),
(18, 18, 'EY888', '2025-03-08', '02:00:00', '09:00:00', 1, 17, 'Business'),
(19, 19, 'AF999', '2025-03-09', '23:00:00', '08:00:00', 1, 18, 'Economy'),
(20, 20, 'LH100', '2025-03-10', '22:30:00', '07:30:00', 1, 19, 'Economy');

-- --------------------------------------------------------

--
-- Table structure for table `passenger`
--

CREATE TABLE `passenger` (
  `passenger_id` int NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `passenger_type` enum('ADULT','CHILD','INFANT') NOT NULL,
  `identity_number` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `passenger`
--

INSERT INTO `passenger` (`passenger_id`, `full_name`, `passenger_type`, `identity_number`) VALUES
(1, 'Andi Pratama', 'ADULT', '3201010001'),
(2, 'Budi Santoso', 'ADULT', '3201010002'),
(3, 'Citra Lestari', 'ADULT', '3201010003'),
(4, 'Dewi Anggraini', 'ADULT', '3201010004'),
(5, 'Eko Saputra', 'ADULT', '3201010005'),
(6, 'Fajar Nugroho', 'ADULT', '3201010006'),
(7, 'Gita Rahma', 'ADULT', '3201010007'),
(8, 'Hendra Wijaya', 'ADULT', '3201010008'),
(9, 'Intan Permata', 'ADULT', '3201010009'),
(10, 'Joko Susilo', 'ADULT', '3201010010'),
(11, 'Kiki Amalia', 'ADULT', '3201010011'),
(12, 'Lukman Hakim', 'ADULT', '3201010012'),
(13, 'Maya Putri', 'ADULT', '3201010013'),
(14, 'Nanda Prakoso', 'ADULT', '3201010014'),
(15, 'Oka Mahendra', 'ADULT', '3201010015'),
(16, 'Putri Ayu', 'ADULT', '3201010016'),
(17, 'Rizki Ramadhan', 'ADULT', '3201010017'),
(18, 'Sari Wulandari', 'ADULT', '3201010018'),
(19, 'Taufik Hidayat', 'ADULT', '3201010019'),
(20, 'Yoga Pranata', 'ADULT', '3201010020');

-- --------------------------------------------------------

--
-- Table structure for table `ticket`
--

CREATE TABLE `ticket` (
  `ticket_id` int NOT NULL,
  `ticket_number` varchar(20) NOT NULL,
  `booking_id` int NOT NULL,
  `passenger_id` int NOT NULL,
  `flight_id` int NOT NULL,
  `seat_class` varchar(20) DEFAULT NULL,
  `ticket_status` enum('ACTIVE','REFUNDED','RESCHEDULED') DEFAULT 'ACTIVE',
  `refundable` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `ticket`
--

INSERT INTO `ticket` (`ticket_id`, `ticket_number`, `booking_id`, `passenger_id`, `flight_id`, `seat_class`, `ticket_status`, `refundable`, `created_at`) VALUES
(1, 'TCK001', 1, 1, 1, 'Economy', 'ACTIVE', 1, '2026-01-25 08:54:21'),
(2, 'TCK002', 2, 2, 2, 'Business', 'ACTIVE', 1, '2026-01-25 08:54:21'),
(3, 'TCK003', 3, 3, 3, 'Economy', 'ACTIVE', 0, '2026-01-25 08:54:21'),
(4, 'TCK004', 4, 4, 4, 'Economy', 'ACTIVE', 1, '2026-01-25 08:54:21'),
(5, 'TCK005', 5, 5, 5, 'Economy', 'ACTIVE', 1, '2026-01-25 08:54:21'),
(6, 'TCK006', 6, 6, 6, 'Economy', 'ACTIVE', 0, '2026-01-25 08:54:21'),
(7, 'TCK007', 7, 7, 7, 'Business', 'ACTIVE', 1, '2026-01-25 08:54:21'),
(8, 'TCK008', 8, 8, 8, 'Economy', 'ACTIVE', 1, '2026-01-25 08:54:21'),
(9, 'TCK009', 9, 9, 9, 'First', 'ACTIVE', 0, '2026-01-25 08:54:21'),
(10, 'TCK010', 10, 10, 10, 'Business', 'ACTIVE', 1, '2026-01-25 08:54:21'),
(11, 'TCK011', 11, 11, 11, 'Economy', 'ACTIVE', 1, '2026-01-25 08:54:21'),
(12, 'TCK012', 12, 12, 12, 'Economy', 'ACTIVE', 1, '2026-01-25 08:54:21'),
(13, 'TCK013', 13, 13, 13, 'Business', 'ACTIVE', 0, '2026-01-25 08:54:21'),
(14, 'TCK014', 14, 14, 14, 'Economy', 'ACTIVE', 1, '2026-01-25 08:54:21'),
(15, 'TCK015', 15, 15, 15, 'Economy', 'ACTIVE', 1, '2026-01-25 08:54:21'),
(16, 'TCK016', 16, 16, 16, 'Economy', 'ACTIVE', 0, '2026-01-25 08:54:21'),
(17, 'TCK017', 17, 17, 17, 'Economy', 'ACTIVE', 1, '2026-01-25 08:54:21'),
(18, 'TCK018', 18, 18, 18, 'Business', 'ACTIVE', 1, '2026-01-25 08:54:21'),
(19, 'TCK019', 19, 19, 19, 'Economy', 'ACTIVE', 1, '2026-01-25 08:54:21'),
(20, 'TCK020', 20, 20, 20, 'Economy', 'ACTIVE', 1, '2026-01-25 08:54:21');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('ADMIN','STAFF') NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'admin', '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9', 'ADMIN', '2026-01-25 00:42:33'),
(2, 'staff', '10176e7b7b24d317acfcf8d2064cfd2f24e154f7b5a96603077d5ef813d6a6b6', 'STAFF', '2026-01-25 00:42:33');

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_ticket_detail`
-- (See below for the actual view)
--
CREATE TABLE `vw_ticket_detail` (
`ticket_number` varchar(20)
,`full_name` varchar(150)
,`airline_name` varchar(100)
,`flight_date` date
,`origin` char(3)
,`destination` char(3)
,`departure_time` time
,`arrival_time` time
,`ticket_status` enum('ACTIVE','REFUNDED','RESCHEDULED')
,`cabin_weight_kg` int
,`checked_weight_kg` int
);

-- --------------------------------------------------------

--
-- Structure for view `vw_ticket_detail`
--
DROP TABLE IF EXISTS `vw_ticket_detail`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_ticket_detail`  AS SELECT `t`.`ticket_number` AS `ticket_number`, `p`.`full_name` AS `full_name`, `a`.`airline_name` AS `airline_name`, `f`.`flight_date` AS `flight_date`, `ao`.`airport_code` AS `origin`, `ad`.`airport_code` AS `destination`, `f`.`departure_time` AS `departure_time`, `f`.`arrival_time` AS `arrival_time`, `t`.`ticket_status` AS `ticket_status`, `b`.`cabin_weight_kg` AS `cabin_weight_kg`, `b`.`checked_weight_kg` AS `checked_weight_kg` FROM ((((((`ticket` `t` join `passenger` `p` on((`t`.`passenger_id` = `p`.`passenger_id`))) join `flight` `f` on((`t`.`flight_id` = `f`.`flight_id`))) join `airline` `a` on((`f`.`airline_id` = `a`.`airline_id`))) join `airport` `ao` on((`f`.`origin_airport_id` = `ao`.`airport_id`))) join `airport` `ad` on((`f`.`destination_airport_id` = `ad`.`airport_id`))) left join `baggage` `b` on((`t`.`ticket_id` = `b`.`ticket_id`))) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `airline`
--
ALTER TABLE `airline`
  ADD PRIMARY KEY (`airline_id`),
  ADD UNIQUE KEY `airline_code` (`airline_code`);

--
-- Indexes for table `airport`
--
ALTER TABLE `airport`
  ADD PRIMARY KEY (`airport_id`),
  ADD UNIQUE KEY `airport_code` (`airport_code`);

--
-- Indexes for table `baggage`
--
ALTER TABLE `baggage`
  ADD PRIMARY KEY (`baggage_id`),
  ADD KEY `fk_baggage_ticket` (`ticket_id`);

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`booking_id`);

--
-- Indexes for table `checkin`
--
ALTER TABLE `checkin`
  ADD PRIMARY KEY (`checkin_id`),
  ADD KEY `fk_checkin_ticket` (`ticket_id`);

--
-- Indexes for table `flight`
--
ALTER TABLE `flight`
  ADD PRIMARY KEY (`flight_id`),
  ADD KEY `fk_flight_airline` (`airline_id`),
  ADD KEY `fk_flight_origin` (`origin_airport_id`),
  ADD KEY `fk_flight_destination` (`destination_airport_id`);

--
-- Indexes for table `passenger`
--
ALTER TABLE `passenger`
  ADD PRIMARY KEY (`passenger_id`);

--
-- Indexes for table `ticket`
--
ALTER TABLE `ticket`
  ADD PRIMARY KEY (`ticket_id`),
  ADD KEY `fk_ticket_booking` (`booking_id`),
  ADD KEY `fk_ticket_passenger` (`passenger_id`),
  ADD KEY `fk_ticket_flight` (`flight_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `airline`
--
ALTER TABLE `airline`
  MODIFY `airline_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `airport`
--
ALTER TABLE `airport`
  MODIFY `airport_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `baggage`
--
ALTER TABLE `baggage`
  MODIFY `baggage_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `booking_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `checkin`
--
ALTER TABLE `checkin`
  MODIFY `checkin_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `flight`
--
ALTER TABLE `flight`
  MODIFY `flight_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `passenger`
--
ALTER TABLE `passenger`
  MODIFY `passenger_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `ticket`
--
ALTER TABLE `ticket`
  MODIFY `ticket_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `baggage`
--
ALTER TABLE `baggage`
  ADD CONSTRAINT `fk_baggage_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `ticket` (`ticket_id`);

--
-- Constraints for table `checkin`
--
ALTER TABLE `checkin`
  ADD CONSTRAINT `fk_checkin_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `ticket` (`ticket_id`);

--
-- Constraints for table `flight`
--
ALTER TABLE `flight`
  ADD CONSTRAINT `fk_flight_airline` FOREIGN KEY (`airline_id`) REFERENCES `airline` (`airline_id`),
  ADD CONSTRAINT `fk_flight_destination` FOREIGN KEY (`destination_airport_id`) REFERENCES `airport` (`airport_id`),
  ADD CONSTRAINT `fk_flight_origin` FOREIGN KEY (`origin_airport_id`) REFERENCES `airport` (`airport_id`);

--
-- Constraints for table `ticket`
--
ALTER TABLE `ticket`
  ADD CONSTRAINT `fk_ticket_booking` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`booking_id`),
  ADD CONSTRAINT `fk_ticket_flight` FOREIGN KEY (`flight_id`) REFERENCES `flight` (`flight_id`),
  ADD CONSTRAINT `fk_ticket_passenger` FOREIGN KEY (`passenger_id`) REFERENCES `passenger` (`passenger_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
