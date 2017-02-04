-- phpMyAdmin SQL Dump
-- version 4.6.6
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 04, 2017 at 11:31 AM
-- Server version: 10.1.20-MariaDB
-- PHP Version: 7.0.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `id517966_ichimoku`
--

-- --------------------------------------------------------

--
-- Table structure for table `ichimokueav2_2jcs_alert`
--

CREATE TABLE `ichimokueav2_2jcs_alert` (
  `id` bigint(20) NOT NULL,
  `timestamp` text COLLATE utf8_unicode_ci NOT NULL,
  `period` text COLLATE utf8_unicode_ci NOT NULL,
  `symbol` text COLLATE utf8_unicode_ci NOT NULL,
  `buy` double NOT NULL,
  `sell` double NOT NULL,
  `h1_ls_validated` text COLLATE utf8_unicode_ci NOT NULL,
  `m1_ls_validated` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `ichimokueav2_2jcs_alert`
--

INSERT INTO `ichimokueav2_2jcs_alert` (`id`, `timestamp`, `period`, `symbol`, `buy`, `sell`, `h1_ls_validated`, `m1_ls_validated`) VALUES
(3, '2017020314343621902337', 'PERIOD_M1', '#FR40', 4843.07, 4841.17, 'false', 'unknown'),
(6, '2017020315001423440367', 'PERIOD_M15', 'NZDCAD', 0.95155, 0.95118, 'false', 'unknown'),
(7, '2017020315001523440741', 'PERIOD_M15', 'NZDJPY', 82.401, 82.374, 'false', 'unknown'),
(8, '2017020315001823444251', 'PERIOD_M15', 'NZDDKK', 5.05171, 5.04987, 'false', 'unknown'),
(9, '2017020315001923444719', 'PERIOD_M15', 'SGDHKD', 5.50887, 5.50723, 'false', 'unknown'),
(10, '2017020315001923445546', 'PERIOD_M15', 'TRYJPY', 30.5, 30.44, 'false', 'unknown'),
(11, '2017020315002123446934', 'PERIOD_M15', 'XAUCHF', 1209.96, 1209.5, 'false', 'unknown'),
(12, '2017020315002123447215', 'PERIOD_M15', 'XAUEUR', 1130.911, 1130.538, 'false', 'unknown'),
(13, '2017020315002123447527', 'PERIOD_M15', 'XAUGBP', 973.927, 973.563, 'false', 'unknown'),
(14, '2017020315300325229168', 'PERIOD_M15', 'SGDJPY', 80.151, 80.125, 'false', 'unknown'),
(15, '2017020315300725233255', 'PERIOD_M15', 'NOKJPY', 13.71792, 13.71228, 'false', 'unknown'),
(16, '2017020315303125256842', 'PERIOD_M15', 'USDCHF', 0.99457, 0.99437, 'false', 'unknown'),
(17, '2017020316452129746847', 'PERIOD_M5', 'XAUAUD', 1587.464, 1586.936, 'true', 'true'),
(18, '2017020316551530340852', 'PERIOD_M5', 'AUDCHF', 0.76272, 0.76249, 'true', 'true'),
(19, '2017020317102431250182', 'PERIOD_M5', '#CH', 8361.58, 8358.12, 'true', 'false'),
(20, '2017020317301632442170', 'PERIOD_M15', '#DE30', 11662.82, 11659.92, 'true', 'false'),
(21, '2017020318002334248693', 'PERIOD_M15', 'CHFHUF', 289.713, 289.487, 'true', 'false'),
(22, '2017020318002734252562', 'PERIOD_M15', 'XAUAUD', 1588.454, 1587.896, 'true', 'false'),
(23, '2017020318301236038149', 'PERIOD_M15', 'NOKJPY', 13.71352, 13.70878, 'true', 'false'),
(24, '2017020318450736932409', 'PERIOD_M15', '#DE30', 11658.85, 11655.95, 'true', 'false'),
(25, '2017020319452540550556', 'PERIOD_M15', 'AUDJPY', 86.669, 86.644, 'true', 'false'),
(26, '2017020320002941454223', 'PERIOD_M15', 'SGDJPY', 80.123, 80.094, 'false', 'false'),
(27, '2017020321000745033651', 'PERIOD_M15', 'XAUCHF', 1209.11, 1208.63, 'false', 'false'),
(28, '2017020321003645061980', 'PERIOD_M30', 'USDJPY', 112.962, 112.943, 'false', 'false'),
(29, '2017020321050445329288', 'PERIOD_M5', 'USDPLN', 3.99596, 3.99326, 'false', 'false'),
(30, '2017020321050945334171', 'PERIOD_M5', '#SP500', 2297.04, 2296.08, 'false', 'false'),
(31, '2017020321104045665158', 'PERIOD_M5', 'AUDCAD', 1.00003, 0.99975, 'false', 'true'),
(32, '2017020321105045675127', 'PERIOD_M5', 'XAUGBP', 975.547, 975.173, 'false', 'true'),
(33, '2017020321150845933542', 'PERIOD_M5', 'AUDCHF', 0.7628, 0.76251, 'false', 'true'),
(34, '2017020321151845943152', 'PERIOD_M5', 'XAUCHF', 1210, 1209.57, 'false', 'true'),
(35, '2017020321250446529045', 'PERIOD_M5', 'NZDCAD', 0.95272, 0.95238, 'true', 'false'),
(36, '2017020321251046535659', 'PERIOD_M5', 'XAUCHF', 1210.06, 1209.65, 'false', 'false'),
(37, '2017020321303146856678', 'PERIOD_M30', 'NZDUSD', 0.73132, 0.73106, 'true', 'true'),
(38, '2017020321350947134797', 'PERIOD_M5', 'XAGGBP', 14.0168, 14.0012, 'true', 'true'),
(39, '2017020321402847453772', 'PERIOD_M5', 'XAGCHF', 17.3604, 17.3379, 'true', 'true'),
(40, '2017020321451647741329', 'PERIOD_M5', 'XAUEUR', 1131.371, 1130.948, 'true', 'false'),
(41, '2017020321552048345630', 'PERIOD_M5', 'CHFPLN', 4.0242, 4.02129, 'true', 'true'),
(42, '2017020322000148626400', 'PERIOD_H1', 'NZDCAD', 0.95327, 0.95292, 'true', 'false'),
(43, '2017020322000948634388', 'PERIOD_M30', 'XAGGBP', 14.0088, 13.9912, 'true', 'false'),
(44, '2017020322002748652421', 'PERIOD_M5', 'XAUUSD', 1219.39, 1218.929, 'true', 'false'),
(45, '2017020322003448659707', 'PERIOD_H1', 'NZDUSD', 0.73162, 0.73134, 'true', 'false'),
(46, '2017020322051048935111', 'PERIOD_M5', 'XAUAUD', 1587.614, 1586.976, 'true', 'false'),
(47, '2017020322051048935548', 'PERIOD_M5', 'XAUEUR', 1131.461, 1131.038, 'true', 'false'),
(48, '2017020322103249256972', 'PERIOD_M5', '#DJ30', 20068.92, 20054.42, 'true', 'true'),
(49, '2017020322103249257736', 'PERIOD_M5', '#SP500', 2297.3, 2295.34, 'true', 'true'),
(50, '2017020322103349258704', 'PERIOD_M5', 'XAGCHF', 17.3654, 17.3426, 'true', 'true'),
(51, '2017020322103449259234', 'PERIOD_M5', 'XAGAUD', 22.7676, 22.7454, 'true', 'true'),
(52, '2017020322103449259546', 'PERIOD_M5', 'XAGUSD', 17.4929, 17.477, 'true', 'true'),
(53, '2017020322153549560066', 'PERIOD_M5', 'XAGEUR', 16.2295, 16.2105, 'true', 'false'),
(54, '2017020322303450459584', 'PERIOD_M30', 'XAUUSD', 1219.91, 1219.569, 'true', 'false'),
(55, '2017020322351650741649', 'PERIOD_M5', 'EURSGD', 1.51791, 1.51733, 'true', 'false'),
(56, '2017020322351850743490', 'PERIOD_M5', '#DJ30', 20065.72, 20051.22, 'true', 'false'),
(57, '2017020322351950744145', 'PERIOD_M5', '#NAS100', 5161.91, 5156.75, 'true', 'false'),
(58, '2017020322451951344172', 'PERIOD_M5', '#DJ30', 20066.2, 20051.69, 'true', 'false');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ichimokueav2_2jcs_alert`
--
ALTER TABLE `ichimokueav2_2jcs_alert`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ichimokueav2_2jcs_alert`
--
ALTER TABLE `ichimokueav2_2jcs_alert`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
