-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 07, 2025 at 12:21 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `project_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `kontakt_poruke`
--

CREATE TABLE `kontakt_poruke` (
  `id` int(11) NOT NULL,
  `ime` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `poruka` text NOT NULL,
  `vrijeme` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kontakt_poruke`
--

INSERT INTO `kontakt_poruke` (`id`, `ime`, `email`, `poruka`, `vrijeme`) VALUES
(10, 'Safet Srna', 'safet.srna@fpmoz.sum.ba', 'Ovo je prva poruka', '2025-06-21 12:58:28');

-- --------------------------------------------------------

--
-- Table structure for table `korisnici`
--

CREATE TABLE `korisnici` (
  `id` int(10) UNSIGNED NOT NULL,
  `ime` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `korisnici`
--

INSERT INTO `korisnici` (`id`, `ime`) VALUES
(1, 'safet srna'),
(2, 'test 123'),
(3, '123 234'),
(4, '555 54'),
(5, 'sada 123'),
(6, '654 231'),
(8, '34 34'),
(9, '3434 34'),
(10, 'sadas dsad'),
(11, 'asdasd asd'),
(12, 'asdas dasdasd'),
(13, 'asds d');

-- --------------------------------------------------------

--
-- Table structure for table `pitanja`
--

CREATE TABLE `pitanja` (
  `id` int(11) NOT NULL,
  `naziv` varchar(255) NOT NULL,
  `tip` enum('text','truefalse') NOT NULL DEFAULT 'text',
  `odgovor` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pitanja`
--

INSERT INTO `pitanja` (`id`, `naziv`, `tip`, `odgovor`) VALUES
(61, 'Tekst pitanje', 'text', 'sada'),
(62, 'tekst opet', 'text', '123'),
(63, 'opet isto tekst', 'text', '12333'),
(64, 'dodatno pitanje', 'text', 'tekst'),
(65, 'da ili ne da', 'truefalse', '1'),
(66, 'ne ili da ne', 'truefalse', '0'),
(67, 'pitanje 1', 'truefalse', '1'),
(68, 'pitanje 12323', 'truefalse', '1'),
(69, 'da', 'truefalse', '1'),
(70, 'ne', 'truefalse', '0'),
(71, 'cek dal ovo radi sada', 'text', 'radi'),
(72, 'cak dal ovo radi sada 2', 'truefalse', '1');

-- --------------------------------------------------------

--
-- Table structure for table `prijave`
--

CREATE TABLE `prijave` (
  `id` int(10) UNSIGNED NOT NULL,
  `ucenik_id` int(10) UNSIGNED NOT NULL,
  `rok_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rezultati_testova`
--

CREATE TABLE `rezultati_testova` (
  `id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL,
  `ime` varchar(100) DEFAULT NULL,
  `prezime` varchar(100) DEFAULT NULL,
  `ucenik_id` int(11) NOT NULL,
  `tocno` int(11) NOT NULL,
  `ukupno` int(11) NOT NULL,
  `procenat` decimal(5,2) NOT NULL,
  `ocjena` decimal(3,1) NOT NULL,
  `datum` datetime DEFAULT current_timestamp(),
  `ime_ucenika` varchar(100) DEFAULT NULL,
  `prezime_ucenika` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rezultati_testova`
--

INSERT INTO `rezultati_testova` (`id`, `test_id`, `ime`, `prezime`, `ucenik_id`, `tocno`, `ukupno`, `procenat`, `ocjena`, `datum`, `ime_ucenika`, `prezime_ucenika`) VALUES
(15, 18, '3434', '34', 9, 3, 6, 50.00, 2.0, '2025-08-15 14:59:30', '3434', '34'),
(16, 18, 'safet', 'srna', 1, 3, 6, 50.00, 4.0, '2025-08-15 15:00:00', 'safet', 'srna'),
(17, 18, 'sadas', 'dsad', 10, 4, 7, 57.14, 5.0, '2025-08-15 15:14:52', 'sadas', 'dsad'),
(18, 25, 'safet', 'srna', 1, 4, 8, 50.00, 5.0, '2025-08-15 15:32:21', 'safet', 'srna'),
(19, 18, 'asdasd', 'asd', 11, 5, 8, 62.50, 1.0, '2025-08-16 17:13:53', 'asdasd', 'asd'),
(20, 18, 'asdas', 'dasdasd', 12, 2, 8, 25.00, 3.0, '2025-08-16 17:27:47', 'asdas', 'dasdasd'),
(21, 18, 'asdas', 'dasdasd', 12, 3, 8, 37.50, 2.0, '2025-08-16 17:27:56', 'asdas', 'dasdasd'),
(22, 18, 'asdas', 'dasdasd', 12, 3, 8, 37.50, 2.0, '2025-08-16 17:28:45', 'asdas', 'dasdasd'),
(23, 18, 'asds', 'd', 13, 7, 10, 70.00, 5.0, '2025-08-17 16:04:58', 'asds', 'd');

-- --------------------------------------------------------

--
-- Table structure for table `rokovi`
--

CREATE TABLE `rokovi` (
  `id` int(10) UNSIGNED NOT NULL,
  `datum` date NOT NULL,
  `vrijeme` time NOT NULL,
  `napomena` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rokovi`
--

INSERT INTO `rokovi` (`id`, `datum`, `vrijeme`, `napomena`) VALUES
(8, '2025-06-25', '01:00:00', 'Rok 1');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`) VALUES
(1, 'student'),
(2, 'professor'),
(3, 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `testovi`
--

CREATE TABLE `testovi` (
  `id` int(11) NOT NULL,
  `naziv` varchar(255) NOT NULL,
  `datum_kreiranja` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `testovi`
--

INSERT INTO `testovi` (`id`, `naziv`, `datum_kreiranja`) VALUES
(18, 'test123', '2025-08-15 12:59:04'),
(19, 'novo', '2025-08-15 13:02:37'),
(20, 'sada', '2025-08-15 13:02:46'),
(21, 'fgfffd', '2025-08-15 13:02:58'),
(22, '45455', '2025-08-15 13:03:32'),
(23, '22222222', '2025-08-15 13:05:12'),
(24, 'test 1', '2025-08-15 13:10:07'),
(25, 'test broj 1', '2025-08-15 13:31:44'),
(26, 'test122423', '2025-08-16 15:46:17'),
(27, 'novi test', '2025-08-17 14:06:04'),
(28, 'nov', '2025-08-17 14:06:35'),
(29, 'radil123', '2025-08-17 14:14:37'),
(30, 'sve', '2025-08-17 14:14:59'),
(31, 'opet sve', '2025-08-17 14:17:34');

-- --------------------------------------------------------

--
-- Table structure for table `test_pitanja`
--

CREATE TABLE `test_pitanja` (
  `id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL,
  `pitanje_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `test_pitanja`
--

INSERT INTO `test_pitanja` (`id`, `test_id`, `pitanje_id`) VALUES
(1, 18, 65),
(2, 18, 64),
(3, 18, 66),
(4, 19, 65),
(5, 19, 64),
(6, 19, 66),
(7, 19, 63),
(8, 19, 62),
(9, 19, 61),
(10, 20, 65),
(11, 20, 64),
(12, 20, 66),
(13, 20, 63),
(14, 20, 62),
(15, 20, 61),
(16, 21, 65),
(17, 21, 64),
(18, 21, 66),
(19, 21, 63),
(20, 21, 62),
(21, 21, 61),
(22, 22, 65),
(23, 22, 64),
(24, 22, 66),
(25, 22, 63),
(26, 22, 62),
(27, 22, 61),
(28, 23, 65),
(29, 23, 64),
(30, 23, 66),
(31, 23, 63),
(32, 23, 62),
(33, 23, 61),
(34, 24, 65),
(35, 24, 64),
(36, 24, 66),
(37, 24, 63),
(38, 24, 67),
(39, 24, 62),
(40, 24, 61),
(41, 25, 65),
(42, 25, 64),
(43, 25, 66),
(44, 25, 63),
(45, 25, 67),
(46, 25, 68),
(47, 25, 62),
(48, 25, 61),
(49, 26, 65),
(50, 26, 66),
(51, 26, 67),
(52, 26, 62),
(53, 27, 69),
(54, 27, 65),
(55, 27, 63),
(56, 27, 62),
(57, 28, 69),
(58, 28, 70),
(59, 28, 62),
(60, 29, 72),
(61, 29, 64),
(62, 29, 66),
(63, 29, 62),
(64, 30, 72),
(65, 30, 71),
(66, 30, 69),
(67, 30, 65),
(68, 30, 64),
(69, 30, 70),
(70, 30, 66),
(71, 30, 63),
(72, 30, 67),
(73, 30, 68),
(74, 30, 62),
(75, 30, 61),
(76, 31, 72),
(77, 31, 71),
(78, 31, 69),
(79, 31, 65),
(80, 31, 64),
(81, 31, 70),
(82, 31, 66),
(83, 31, 63),
(84, 31, 67),
(85, 31, 68),
(86, 31, 62),
(87, 31, 61);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_role` enum('super_admin','admin','user','guest') DEFAULT 'user',
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `user_role`, `status`) VALUES
(72, 'student', 'student@gmail.com', '$2y$10$gVgrVS4wBS1Gwumq0SKh3Oh3qM7Dc6Jt08HAYW2L3Bb2hk/crxW0a', 'user', 'active'),
(73, 'profesor', 'profesor@gmail.com', '$2y$10$xJKTLqHKr/cEzwnq66J35u5/snCjV/mu/VRNrre.IK52geweuSX1i', 'user', 'active'),
(74, 'admin', 'admin@gmail.com', '$2y$10$vkXJc4fZz/SKsgIk0oQ2aOpIoQAudwwQQLSPLwMBzK6tZ1GoOWj1C', 'user', 'active'),
(75, 'novi', 'novi@gmail.com', '$2y$10$mP1JJoPvMr5l9lOHtKeX9u5XbtCuDI6.EXZ0zbazCP63wTtxJ75Vy', 'user', 'active'),
(76, 'safet', 'safet@gmail.com', '$2y$10$KUwIEja7dQqmt7FrJ.tBluY2gLb7pDklnKPuPOxIHlTkllUIr8Vhy', 'user', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `user_id` int(11) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES
(72, 1),
(73, 2),
(74, 3),
(75, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kontakt_poruke`
--
ALTER TABLE `kontakt_poruke`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `korisnici`
--
ALTER TABLE `korisnici`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pitanja`
--
ALTER TABLE `pitanja`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `naziv` (`naziv`);

--
-- Indexes for table `prijave`
--
ALTER TABLE `prijave`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ucenik_id` (`ucenik_id`,`rok_id`),
  ADD KEY `rok_id` (`rok_id`);

--
-- Indexes for table `rezultati_testova`
--
ALTER TABLE `rezultati_testova`
  ADD PRIMARY KEY (`id`),
  ADD KEY `test_id` (`test_id`);

--
-- Indexes for table `rokovi`
--
ALTER TABLE `rokovi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `testovi`
--
ALTER TABLE `testovi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `test_pitanja`
--
ALTER TABLE `test_pitanja`
  ADD PRIMARY KEY (`id`),
  ADD KEY `test_id` (`test_id`),
  ADD KEY `pitanje_id` (`pitanje_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD KEY `user_id` (`user_id`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kontakt_poruke`
--
ALTER TABLE `kontakt_poruke`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `korisnici`
--
ALTER TABLE `korisnici`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `pitanja`
--
ALTER TABLE `pitanja`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `prijave`
--
ALTER TABLE `prijave`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `rezultati_testova`
--
ALTER TABLE `rezultati_testova`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `rokovi`
--
ALTER TABLE `rokovi`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `testovi`
--
ALTER TABLE `testovi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `test_pitanja`
--
ALTER TABLE `test_pitanja`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `prijave`
--
ALTER TABLE `prijave`
  ADD CONSTRAINT `prijave_ibfk_1` FOREIGN KEY (`ucenik_id`) REFERENCES `korisnici` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `prijave_ibfk_2` FOREIGN KEY (`rok_id`) REFERENCES `rokovi` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rezultati_testova`
--
ALTER TABLE `rezultati_testova`
  ADD CONSTRAINT `rezultati_testova_ibfk_1` FOREIGN KEY (`test_id`) REFERENCES `testovi` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `test_pitanja`
--
ALTER TABLE `test_pitanja`
  ADD CONSTRAINT `test_pitanja_ibfk_1` FOREIGN KEY (`test_id`) REFERENCES `testovi` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `test_pitanja_ibfk_2` FOREIGN KEY (`pitanje_id`) REFERENCES `pitanja` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
