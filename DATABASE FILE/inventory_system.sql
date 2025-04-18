-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 18, 2025 at 01:12 PM
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
-- Database: `inventory_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `borrowed_items`
--

CREATE TABLE `borrowed_items` (
  `id` int(11) NOT NULL,
  `borrow_id` int(11) DEFAULT NULL,
  `borrower_name` varchar(255) NOT NULL,
  `borrowed_date` datetime DEFAULT current_timestamp(),
  `return_date` datetime DEFAULT NULL,
  `stat` enum('Borrowed','Returned') DEFAULT NULL,
  `due_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrowed_items`
--

INSERT INTO `borrowed_items` (`id`, `borrow_id`, `borrower_name`, `borrowed_date`, `return_date`, `stat`, `due_date`) VALUES
(16, 2, 'q', '2025-03-18 16:09:56', '2025-03-19 10:46:04', 'Returned', '2025-03-25'),
(17, 3, 'marco', '2025-03-18 16:15:21', '2025-03-18 21:46:05', 'Returned', '2025-03-25'),
(18, 4, 'richard', '2025-03-18 16:50:40', '2025-03-19 10:33:13', 'Returned', '2025-03-25'),
(19, 10, 'dwd', '2025-03-18 17:57:59', '2025-03-18 21:27:31', 'Returned', '2025-03-25'),
(20, 11, 'qwewqe', '2025-03-18 17:58:16', '2025-03-18 21:27:33', 'Returned', '2025-03-25'),
(21, 14, 'dadada', '2025-03-18 21:04:19', '2025-03-18 21:27:35', 'Returned', '2025-03-25'),
(22, 2, 'dadadw', '2025-03-18 21:31:55', '2025-03-19 10:46:04', 'Returned', '2025-03-25'),
(23, 4, 'wqweqw', '2025-03-18 21:42:26', '2025-03-19 10:33:13', 'Returned', '2025-03-25'),
(24, 3, 'wayn', '2025-03-18 21:46:02', '2025-03-18 21:46:05', 'Returned', '2025-03-25'),
(25, 2, 'q', '2025-03-18 21:46:25', '2025-03-19 10:46:04', 'Returned', '2025-03-25'),
(26, 4, 'we', '2025-03-18 21:49:58', '2025-03-19 10:33:13', 'Returned', '2025-03-25'),
(27, 2, 'dadaw', '2025-03-18 22:15:14', '2025-03-19 10:46:04', 'Returned', '2025-03-25'),
(28, 2, 'richard', '2025-03-19 10:29:48', '2025-03-19 10:46:04', 'Returned', '2025-03-26'),
(29, 2, 'we', '2025-03-19 10:30:19', '2025-03-19 10:46:04', 'Returned', '2025-03-26'),
(30, 4, 'marco', '2025-03-19 10:33:00', '2025-03-19 10:33:13', 'Returned', '2025-03-26'),
(31, 2, 'polko', '2025-03-19 10:45:48', '2025-03-19 10:46:04', 'Returned', '2025-03-26'),
(32, 2, 'polko', '2025-03-19 10:45:58', '2025-03-19 10:46:04', 'Returned', '2025-03-26'),
(34, 15, 'jd', '2025-03-26 13:38:40', '2025-03-26 13:45:49', 'Returned', '2025-04-02'),
(35, 1, 'jd', '2025-03-26 13:49:59', NULL, 'Borrowed', '2025-04-02'),
(36, 40, 'Blackie', '2025-03-27 10:44:03', '2025-03-27 10:44:37', 'Returned', '2025-04-03'),
(37, 6, 'dad', '2025-03-27 00:00:00', NULL, 'Borrowed', '2025-04-03'),
(38, 7, 'earl', '2025-03-27 11:54:32', NULL, 'Borrowed', '2025-03-28');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(3, 'Finished Goods'),
(5, 'Machinery'),
(4, 'Packing Materials'),
(2, 'Raw Materials'),
(12, 'School Supplies'),
(9, 'Sports'),
(8, 'Stationery Items'),
(10, 'Tools'),
(6, 'Work in Progress');

-- --------------------------------------------------------

--
-- Table structure for table `item_requests`
--

CREATE TABLE `item_requests` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `categorie_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `status` enum('Pending','Approved','Denied','Added') DEFAULT 'Pending',
  `request_date` datetime DEFAULT current_timestamp(),
  `added_to_inventory` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `item_requests`
--

INSERT INTO `item_requests` (`id`, `user_id`, `item_name`, `categorie_id`, `quantity`, `status`, `request_date`, `added_to_inventory`) VALUES
(2, 6, 'Shuttlecock', 9, 5, 'Added', '2025-04-05 00:12:24', 1),
(3, 6, 'Mark', 5, 1, 'Denied', '2025-04-15 11:46:46', 0),
(4, 6, 'Charger', 5, 1, 'Added', '2025-04-15 11:47:16', 1),
(5, 6, 'Notebook', 12, 10, 'Added', '2025-04-18 18:42:13', 1),
(6, 6, 'Alcohol ', 2, 6, 'Denied', '2025-04-18 19:07:01', 0);

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE `location` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `location`
--

INSERT INTO `location` (`id`, `name`) VALUES
(1, 'Faculty room'),
(2, 'Computer Laboratory'),
(3, 'Guidance office'),
(4, 'Clinic'),
(5, 'Library'),
(6, 'Nihongo Center'),
(8, 'OSA'),
(9, 'BSIS Laboratory');

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE `media` (
  `id` int(11) UNSIGNED NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_type` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `quantity` varchar(50) DEFAULT NULL,
  `categorie_id` int(11) UNSIGNED NOT NULL,
  `media_id` int(11) DEFAULT 0,
  `date` datetime NOT NULL,
  `location_id` varchar(255) DEFAULT NULL,
  `status_id` varchar(50) DEFAULT NULL,
  `barcode` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `quantity`, `categorie_id`, `media_id`, `date`, `location_id`, `status_id`, `barcode`) VALUES
(18, 'Keyboard', '2', 5, 0, '2025-03-20 10:03:53', '2', '1', '6800b2af5f7f6'),
(19, 'Laptop', '5', 5, 0, '2025-03-20 11:49:50', '3', '1', '6800b2af5fdc7'),
(22, 'Richard', '7', 3, 0, '2025-03-22 09:16:19', '1', '3', NULL),
(23, 'Chairs', '5', 8, 0, '2025-03-25 02:57:41', '3', '1', '6800b2af6010a'),
(24, 'Remote', '6', 10, 0, '2025-03-25 14:40:42', '8', '1', '6800b2af6047c'),
(25, 'Aircon', '2', 8, 0, '2025-03-25 14:42:45', '1', '1', '6800b2af60d59'),
(26, 'Basketball', '5', 9, 0, '2025-03-25 14:45:51', '8', '1', '6800b2af6109c'),
(27, 'Volleyball', '2', 9, 0, '2025-03-25 14:46:54', '8', '1', '6800b2af613e1'),
(29, 'Tennis Racket', '5', 9, 0, '2025-03-25 14:56:00', '8', '1', '6800b2af61725'),
(30, 'Soccerball', '2', 9, 0, '2025-03-26 06:55:21', NULL, NULL, NULL),
(31, 'Table', '5', 8, 0, '2025-03-26 11:26:20', NULL, NULL, NULL),
(32, 'Screwdriver', '5', 10, 0, '2025-03-26 11:39:29', NULL, NULL, NULL),
(33, 'Iphone X', '3', 5, 0, '2025-03-27 10:45:38', NULL, NULL, NULL),
(34, 'Cabinet', '7', 8, 0, '2025-03-27 11:46:41', NULL, NULL, NULL),
(38, 'Bond Paper', '89', 12, 0, '2025-04-17 17:38:31', NULL, NULL, '6800cf8a3d21d'),
(39, 'Pencils', '182', 12, 0, '2025-04-17 18:21:58', NULL, NULL, '6800d64b21eed'),
(40, 'Ballpen', '48', 12, 0, '2025-04-17 20:30:27', NULL, NULL, '6800f4a445c9d'),
(41, 'Folder', '35', 12, 0, '2025-04-17 20:30:59', NULL, NULL, '6800f4a446813'),
(42, 'Charger', '1', 5, 0, '2025-04-18 18:26:19', '', NULL, NULL),
(43, 'Shuttlecock', '5', 9, 0, '2025-04-18 18:31:38', '', NULL, NULL),
(44, 'Notebook', '10', 12, 0, '2025-04-18 18:48:48', '', NULL, '6802302307a29');

-- --------------------------------------------------------

--
-- Table structure for table `product_usage`
--

CREATE TABLE `product_usage` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_usage`
--

INSERT INTO `product_usage` (`id`, `product_id`, `quantity`, `user_id`, `date`) VALUES
(1, 39, 3, 0, '2025-04-17 14:08:43'),
(2, 38, 1, 0, '2025-04-17 14:08:43'),
(3, 40, 1, 0, '2025-04-17 14:46:26'),
(4, 41, 1, 0, '2025-04-17 14:46:26'),
(5, 41, 5, 0, '2025-04-17 14:49:59'),
(6, 39, 5, 0, '2025-04-18 06:14:13'),
(7, 40, 2, 0, '2025-04-18 06:14:13'),
(8, 38, 1, 0, '2025-04-18 06:14:13'),
(9, 39, 10, 0, '2025-04-18 06:20:07'),
(10, 38, 9, 0, '2025-04-18 06:20:07');

-- --------------------------------------------------------

--
-- Table structure for table `recent_actions`
--

CREATE TABLE `recent_actions` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `action` varchar(255) NOT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recent_actions`
--

INSERT INTO `recent_actions` (`id`, `user_id`, `action`, `timestamp`) VALUES
(3, 1, 'Added new product: Screwdriver', '2025-03-26 18:39:29'),
(4, 1, 'Added new product: Iphone X', '2025-03-27 10:45:38'),
(5, 1, 'Added new product: Cabinet', '2025-03-27 11:46:41'),
(6, 1, 'Added new product: Bond Paper', '2025-04-17 17:38:31'),
(7, 1, 'Added new product: Pencils', '2025-04-17 18:21:58'),
(8, 1, 'Added new product: Ballpen', '2025-04-17 20:30:27'),
(9, 1, 'Added new product: Folder', '2025-04-17 20:30:59'),
(10, 1, 'Added requested product: Charger', '2025-04-18 18:26:19'),
(11, 1, 'Added requested product: Shuttlecock', '2025-04-18 18:31:38'),
(12, 1, 'Approved request #5', '2025-04-18 18:48:44'),
(13, 1, 'Added requested product: Notebook', '2025-04-18 18:48:48'),
(14, 1, 'Denied request #6', '2025-04-18 19:07:23');

-- --------------------------------------------------------

--
-- Table structure for table `report_history`
--

CREATE TABLE `report_history` (
  `id` int(10) UNSIGNED NOT NULL,
  `generated_by` int(10) UNSIGNED DEFAULT NULL,
  `location_id` varchar(255) DEFAULT NULL,
  `report_type` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `generated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `report_history`
--

INSERT INTO `report_history` (`id`, `generated_by`, `location_id`, `report_type`, `file_path`, `generated_at`) VALUES
(1, 1, '', 'Inventory Report in All Locations', '/path/to/report/folder/report_1742991936.pdf', '2025-03-26 12:25:36'),
(2, 1, '1', 'Inventory Report in Faculty room', '/path/to/report/folder/report_1742992048.pdf', '2025-03-26 12:27:28'),
(3, 1, '4', 'Inventory Report in Clinic', '/path/to/report/folder/report_1743043613.pdf', '2025-03-27 02:46:53'),
(4, 1, '', 'Inventory Report in All Locations', '/path/to/report/folder/report_1744688609.pdf', '2025-04-15 03:43:29'),
(5, 1, '', 'Inventory Report in All Locations', '/path/to/report/folder/report_1744701365.pdf', '2025-04-15 07:16:05'),
(6, 1, '', 'Inventory Report in All Locations', '/path/to/report/folder/report_1744870119.pdf', '2025-04-17 06:08:39'),
(7, 1, '1', 'Inventory Report in Faculty room', '/path/to/report/folder/report_1744960130.pdf', '2025-04-18 07:08:50');

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

CREATE TABLE `status` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `status`
--

INSERT INTO `status` (`id`, `name`) VALUES
(1, 'Available'),
(2, 'Borrowed'),
(3, 'Missing'),
(4, 'Lost'),
(5, 'Maintenance'),
(6, 'Placed');

-- --------------------------------------------------------

--
-- Table structure for table `stock`
--

CREATE TABLE `stock` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `stock_number` varchar(50) NOT NULL,
  `location_id` int(11) UNSIGNED DEFAULT NULL,
  `status_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock`
--

INSERT INTO `stock` (`id`, `product_id`, `stock_number`, `location_id`, `status_id`) VALUES
(1, 18, 'Keyboard-001', 1, 1),
(2, 18, 'Keyboard-002', 2, 1),
(4, 19, 'Laptop-001', 1, 3),
(5, 19, 'Laptop-002', 2, 6),
(6, 19, 'Laptop-003', 4, 1),
(7, 19, 'Laptop-004', NULL, 1),
(8, 19, 'Laptop-005', NULL, 1),
(9, 22, 'Richard-001', 2, 1),
(10, 22, 'Richard-002', 4, 1),
(11, 22, 'Richard-003', 1, 6),
(12, 22, 'Richard-004', 4, 6),
(13, 22, 'Richard-005', 5, 6),
(14, 23, 'Chairs-001', 4, 1),
(15, 23, 'Chairs-002', NULL, 1),
(16, 23, 'Chairs-003', NULL, 1),
(17, 23, 'Chairs-004', NULL, 1),
(18, 23, 'Chairs-005', NULL, 1),
(19, 24, 'Remote-001', NULL, 1),
(20, 24, 'Remote-002', NULL, 1),
(21, 24, 'Remote-003', NULL, 1),
(22, 24, 'Remote-004', NULL, 1),
(23, 24, 'Remote-005', NULL, 1),
(24, 24, 'Remote-006', NULL, 1),
(25, 25, 'Aircon-001', NULL, 1),
(26, 25, 'Aircon-002', NULL, 1),
(27, 26, 'Basketball-001', NULL, 1),
(28, 26, 'Basketball-002', NULL, 1),
(29, 26, 'Basketball-003', NULL, 1),
(30, 26, 'Basketball-004', NULL, 1),
(31, 26, 'Basketball-005', NULL, 1),
(32, 27, 'Volleyball-001', NULL, 1),
(33, 27, 'Volleyball-002', NULL, 1),
(35, 29, 'Tennis Racket-001', NULL, 1),
(36, 29, 'Tennis Racket-002', NULL, 1),
(37, 29, 'Tennis Racket-003', NULL, 1),
(38, 29, 'Tennis Racket-004', NULL, 1),
(39, 29, 'Tennis Racket-005', NULL, 1),
(40, 30, 'Soccer-001', 1, 1),
(41, 30, 'Soccer-002', NULL, 1),
(45, 22, 'Richard-006', NULL, 1),
(47, 22, 'Richard-007', NULL, 1),
(48, 32, 'Screwdriver-001', NULL, 1),
(49, 32, 'Screwdriver-002', NULL, 1),
(50, 32, 'Screwdriver-003', NULL, 1),
(51, 32, 'Screwdriver-004', NULL, 1),
(52, 32, 'Screwdriver-005', NULL, 1),
(53, 33, 'Iphone X-001', NULL, 1),
(54, 33, 'Iphone X-002', NULL, 1),
(55, 33, 'Iphone X-003', NULL, 1),
(56, 34, 'Cabinet-001', NULL, 1),
(57, 34, 'Cabinet-002', NULL, 1),
(58, 34, 'Cabinet-003', NULL, 1),
(59, 34, 'Cabinet-004', NULL, 1),
(60, 34, 'Cabinet-005', NULL, 1),
(61, 34, 'Cabinet-006', NULL, 1),
(62, 34, 'Cabinet-007', NULL, 1),
(63, 38, 'Bond Paper-001', NULL, 1),
(64, 38, 'Bond Paper-002', NULL, 1),
(65, 38, 'Bond Paper-003', NULL, 1),
(66, 38, 'Bond Paper-004', NULL, 1),
(67, 38, 'Bond Paper-005', NULL, 1),
(68, 38, 'Bond Paper-006', NULL, 1),
(69, 38, 'Bond Paper-007', NULL, 1),
(70, 38, 'Bond Paper-008', NULL, 1),
(71, 38, 'Bond Paper-009', NULL, 1),
(72, 38, 'Bond Paper-010', NULL, 1),
(73, 38, 'Bond Paper-011', NULL, 1),
(74, 38, 'Bond Paper-012', NULL, 1),
(75, 38, 'Bond Paper-013', NULL, 1),
(76, 38, 'Bond Paper-014', NULL, 1),
(77, 38, 'Bond Paper-015', NULL, 1),
(78, 38, 'Bond Paper-016', NULL, 1),
(79, 38, 'Bond Paper-017', NULL, 1),
(80, 38, 'Bond Paper-018', NULL, 1),
(81, 38, 'Bond Paper-019', NULL, 1),
(82, 38, 'Bond Paper-020', NULL, 1),
(83, 38, 'Bond Paper-021', NULL, 1),
(84, 38, 'Bond Paper-022', NULL, 1),
(85, 38, 'Bond Paper-023', NULL, 1),
(86, 38, 'Bond Paper-024', NULL, 1),
(87, 38, 'Bond Paper-025', NULL, 1),
(88, 38, 'Bond Paper-026', NULL, 1),
(89, 38, 'Bond Paper-027', NULL, 1),
(90, 38, 'Bond Paper-028', NULL, 1),
(91, 38, 'Bond Paper-029', NULL, 1),
(92, 38, 'Bond Paper-030', NULL, 1),
(93, 38, 'Bond Paper-031', NULL, 1),
(94, 38, 'Bond Paper-032', NULL, 1),
(95, 38, 'Bond Paper-033', NULL, 1),
(96, 38, 'Bond Paper-034', NULL, 1),
(97, 38, 'Bond Paper-035', NULL, 1),
(98, 38, 'Bond Paper-036', NULL, 1),
(99, 38, 'Bond Paper-037', NULL, 1),
(100, 38, 'Bond Paper-038', NULL, 1),
(101, 38, 'Bond Paper-039', NULL, 1),
(102, 38, 'Bond Paper-040', NULL, 1),
(103, 38, 'Bond Paper-041', NULL, 1),
(104, 38, 'Bond Paper-042', NULL, 1),
(105, 38, 'Bond Paper-043', NULL, 1),
(106, 38, 'Bond Paper-044', NULL, 1),
(107, 38, 'Bond Paper-045', NULL, 1),
(108, 38, 'Bond Paper-046', NULL, 1),
(109, 38, 'Bond Paper-047', NULL, 1),
(110, 38, 'Bond Paper-048', NULL, 1),
(111, 38, 'Bond Paper-049', NULL, 1),
(112, 38, 'Bond Paper-050', NULL, 1),
(113, 38, 'Bond Paper-051', NULL, 1),
(114, 38, 'Bond Paper-052', NULL, 1),
(115, 38, 'Bond Paper-053', NULL, 1),
(116, 38, 'Bond Paper-054', NULL, 1),
(117, 38, 'Bond Paper-055', NULL, 1),
(118, 38, 'Bond Paper-056', NULL, 1),
(119, 38, 'Bond Paper-057', NULL, 1),
(120, 38, 'Bond Paper-058', NULL, 1),
(121, 38, 'Bond Paper-059', NULL, 1),
(122, 38, 'Bond Paper-060', NULL, 1),
(123, 38, 'Bond Paper-061', NULL, 1),
(124, 38, 'Bond Paper-062', NULL, 1),
(125, 38, 'Bond Paper-063', NULL, 1),
(126, 38, 'Bond Paper-064', NULL, 1),
(127, 38, 'Bond Paper-065', NULL, 1),
(128, 38, 'Bond Paper-066', NULL, 1),
(129, 38, 'Bond Paper-067', NULL, 1),
(130, 38, 'Bond Paper-068', NULL, 1),
(131, 38, 'Bond Paper-069', NULL, 1),
(132, 38, 'Bond Paper-070', NULL, 1),
(133, 38, 'Bond Paper-071', NULL, 1),
(134, 38, 'Bond Paper-072', NULL, 1),
(135, 38, 'Bond Paper-073', NULL, 1),
(136, 38, 'Bond Paper-074', NULL, 1),
(137, 38, 'Bond Paper-075', NULL, 1),
(138, 38, 'Bond Paper-076', NULL, 1),
(139, 38, 'Bond Paper-077', NULL, 1),
(140, 38, 'Bond Paper-078', NULL, 1),
(141, 38, 'Bond Paper-079', NULL, 1),
(142, 38, 'Bond Paper-080', NULL, 1),
(143, 38, 'Bond Paper-081', NULL, 1),
(144, 38, 'Bond Paper-082', NULL, 1),
(145, 38, 'Bond Paper-083', NULL, 1),
(146, 38, 'Bond Paper-084', NULL, 1),
(147, 38, 'Bond Paper-085', NULL, 1),
(148, 38, 'Bond Paper-086', NULL, 1),
(149, 38, 'Bond Paper-087', NULL, 1),
(150, 38, 'Bond Paper-088', NULL, 1),
(151, 38, 'Bond Paper-089', NULL, 1),
(152, 38, 'Bond Paper-090', NULL, 1),
(163, 39, 'Pencils-001', NULL, 1),
(164, 39, 'Pencils-002', NULL, 1),
(165, 39, 'Pencils-003', NULL, 1),
(166, 39, 'Pencils-004', NULL, 1),
(167, 39, 'Pencils-005', NULL, 1),
(168, 39, 'Pencils-006', NULL, 1),
(169, 39, 'Pencils-007', NULL, 1),
(170, 39, 'Pencils-008', NULL, 1),
(171, 39, 'Pencils-009', NULL, 1),
(172, 39, 'Pencils-010', NULL, 1),
(173, 39, 'Pencils-011', NULL, 1),
(174, 39, 'Pencils-012', NULL, 1),
(175, 39, 'Pencils-013', NULL, 1),
(176, 39, 'Pencils-014', NULL, 1),
(177, 39, 'Pencils-015', NULL, 1),
(178, 39, 'Pencils-016', NULL, 1),
(179, 39, 'Pencils-017', NULL, 1),
(180, 39, 'Pencils-018', NULL, 1),
(181, 39, 'Pencils-019', NULL, 1),
(182, 39, 'Pencils-020', NULL, 1),
(183, 39, 'Pencils-021', NULL, 1),
(184, 39, 'Pencils-022', NULL, 1),
(185, 39, 'Pencils-023', NULL, 1),
(186, 39, 'Pencils-024', NULL, 1),
(187, 39, 'Pencils-025', NULL, 1),
(188, 39, 'Pencils-026', NULL, 1),
(189, 39, 'Pencils-027', NULL, 1),
(190, 39, 'Pencils-028', NULL, 1),
(191, 39, 'Pencils-029', NULL, 1),
(192, 39, 'Pencils-030', NULL, 1),
(193, 39, 'Pencils-031', NULL, 1),
(194, 39, 'Pencils-032', NULL, 1),
(195, 39, 'Pencils-033', NULL, 1),
(196, 39, 'Pencils-034', NULL, 1),
(197, 39, 'Pencils-035', NULL, 1),
(198, 39, 'Pencils-036', NULL, 1),
(199, 39, 'Pencils-037', NULL, 1),
(200, 39, 'Pencils-038', NULL, 1),
(201, 39, 'Pencils-039', NULL, 1),
(202, 39, 'Pencils-040', NULL, 1),
(203, 39, 'Pencils-041', NULL, 1),
(204, 39, 'Pencils-042', NULL, 1),
(205, 39, 'Pencils-043', NULL, 1),
(206, 39, 'Pencils-044', NULL, 1),
(207, 39, 'Pencils-045', NULL, 1),
(208, 39, 'Pencils-046', NULL, 1),
(209, 39, 'Pencils-047', NULL, 1),
(210, 39, 'Pencils-048', NULL, 1),
(211, 39, 'Pencils-049', NULL, 1),
(212, 39, 'Pencils-050', NULL, 1),
(213, 39, 'Pencils-051', NULL, 1),
(214, 39, 'Pencils-052', NULL, 1),
(215, 39, 'Pencils-053', NULL, 1),
(216, 39, 'Pencils-054', NULL, 1),
(217, 39, 'Pencils-055', NULL, 1),
(218, 39, 'Pencils-056', NULL, 1),
(219, 39, 'Pencils-057', NULL, 1),
(220, 39, 'Pencils-058', NULL, 1),
(221, 39, 'Pencils-059', NULL, 1),
(222, 39, 'Pencils-060', NULL, 1),
(223, 39, 'Pencils-061', NULL, 1),
(224, 39, 'Pencils-062', NULL, 1),
(225, 39, 'Pencils-063', NULL, 1),
(226, 39, 'Pencils-064', NULL, 1),
(227, 39, 'Pencils-065', NULL, 1),
(228, 39, 'Pencils-066', NULL, 1),
(229, 39, 'Pencils-067', NULL, 1),
(230, 39, 'Pencils-068', NULL, 1),
(231, 39, 'Pencils-069', NULL, 1),
(232, 39, 'Pencils-070', NULL, 1),
(233, 39, 'Pencils-071', NULL, 1),
(234, 39, 'Pencils-072', NULL, 1),
(235, 39, 'Pencils-073', NULL, 1),
(236, 39, 'Pencils-074', NULL, 1),
(237, 39, 'Pencils-075', NULL, 1),
(238, 39, 'Pencils-076', NULL, 1),
(239, 39, 'Pencils-077', NULL, 1),
(240, 39, 'Pencils-078', NULL, 1),
(241, 39, 'Pencils-079', NULL, 1),
(242, 39, 'Pencils-080', NULL, 1),
(243, 39, 'Pencils-081', NULL, 1),
(244, 39, 'Pencils-082', NULL, 1),
(245, 39, 'Pencils-083', NULL, 1),
(246, 39, 'Pencils-084', NULL, 1),
(247, 39, 'Pencils-085', NULL, 1),
(248, 39, 'Pencils-086', NULL, 1),
(249, 39, 'Pencils-087', NULL, 1),
(250, 39, 'Pencils-088', NULL, 1),
(251, 39, 'Pencils-089', NULL, 1),
(252, 39, 'Pencils-090', NULL, 1),
(253, 39, 'Pencils-091', NULL, 1),
(254, 39, 'Pencils-092', NULL, 1),
(255, 39, 'Pencils-093', NULL, 1),
(256, 39, 'Pencils-094', NULL, 1),
(257, 39, 'Pencils-095', NULL, 1),
(258, 39, 'Pencils-096', NULL, 1),
(259, 39, 'Pencils-097', NULL, 1),
(260, 39, 'Pencils-098', NULL, 1),
(261, 39, 'Pencils-099', NULL, 1),
(262, 39, 'Pencils-100', NULL, 1),
(263, 39, 'Pencils-101', NULL, 1),
(264, 39, 'Pencils-102', NULL, 1),
(265, 39, 'Pencils-103', NULL, 1),
(266, 39, 'Pencils-104', NULL, 1),
(267, 39, 'Pencils-105', NULL, 1),
(268, 39, 'Pencils-106', NULL, 1),
(269, 39, 'Pencils-107', NULL, 1),
(270, 39, 'Pencils-108', NULL, 1),
(271, 39, 'Pencils-109', NULL, 1),
(272, 39, 'Pencils-110', NULL, 1),
(273, 39, 'Pencils-111', NULL, 1),
(274, 39, 'Pencils-112', NULL, 1),
(275, 39, 'Pencils-113', NULL, 1),
(276, 39, 'Pencils-114', NULL, 1),
(277, 39, 'Pencils-115', NULL, 1),
(278, 39, 'Pencils-116', NULL, 1),
(279, 39, 'Pencils-117', NULL, 1),
(280, 39, 'Pencils-118', NULL, 1),
(281, 39, 'Pencils-119', NULL, 1),
(282, 39, 'Pencils-120', NULL, 1),
(283, 39, 'Pencils-121', NULL, 1),
(284, 39, 'Pencils-122', NULL, 1),
(285, 39, 'Pencils-123', NULL, 1),
(286, 39, 'Pencils-124', NULL, 1),
(287, 39, 'Pencils-125', NULL, 1),
(288, 39, 'Pencils-126', NULL, 1),
(289, 39, 'Pencils-127', NULL, 1),
(290, 39, 'Pencils-128', NULL, 1),
(291, 39, 'Pencils-129', NULL, 1),
(292, 39, 'Pencils-130', NULL, 1),
(293, 39, 'Pencils-131', NULL, 1),
(294, 39, 'Pencils-132', NULL, 1),
(295, 39, 'Pencils-133', NULL, 1),
(296, 39, 'Pencils-134', NULL, 1),
(297, 39, 'Pencils-135', NULL, 1),
(298, 39, 'Pencils-136', NULL, 1),
(299, 39, 'Pencils-137', NULL, 1),
(300, 39, 'Pencils-138', NULL, 1),
(301, 39, 'Pencils-139', NULL, 1),
(302, 39, 'Pencils-140', NULL, 1),
(303, 39, 'Pencils-141', NULL, 1),
(304, 39, 'Pencils-142', NULL, 1),
(305, 39, 'Pencils-143', NULL, 1),
(306, 39, 'Pencils-144', NULL, 1),
(307, 39, 'Pencils-145', NULL, 1),
(308, 39, 'Pencils-146', NULL, 1),
(309, 39, 'Pencils-147', NULL, 1),
(310, 39, 'Pencils-148', NULL, 1),
(311, 39, 'Pencils-149', NULL, 1),
(312, 39, 'Pencils-150', NULL, 1),
(313, 39, 'Pencils-151', NULL, 1),
(314, 39, 'Pencils-152', NULL, 1),
(315, 39, 'Pencils-153', NULL, 1),
(316, 39, 'Pencils-154', NULL, 1),
(317, 39, 'Pencils-155', NULL, 1),
(318, 39, 'Pencils-156', NULL, 1),
(319, 39, 'Pencils-157', NULL, 1),
(320, 39, 'Pencils-158', NULL, 1),
(321, 39, 'Pencils-159', NULL, 1),
(322, 39, 'Pencils-160', NULL, 1),
(323, 39, 'Pencils-161', NULL, 1),
(324, 39, 'Pencils-162', NULL, 1),
(325, 39, 'Pencils-163', NULL, 1),
(326, 39, 'Pencils-164', NULL, 1),
(327, 39, 'Pencils-165', NULL, 1),
(328, 39, 'Pencils-166', NULL, 1),
(329, 39, 'Pencils-167', NULL, 1),
(330, 39, 'Pencils-168', NULL, 1),
(331, 39, 'Pencils-169', NULL, 1),
(332, 39, 'Pencils-170', NULL, 1),
(333, 39, 'Pencils-171', NULL, 1),
(334, 39, 'Pencils-172', NULL, 1),
(335, 39, 'Pencils-173', NULL, 1),
(336, 39, 'Pencils-174', NULL, 1),
(337, 39, 'Pencils-175', NULL, 1),
(338, 39, 'Pencils-176', NULL, 1),
(339, 39, 'Pencils-177', NULL, 1),
(340, 39, 'Pencils-178', NULL, 1),
(341, 39, 'Pencils-179', NULL, 1),
(342, 39, 'Pencils-180', NULL, 1),
(343, 39, 'Pencils-181', NULL, 1),
(344, 39, 'Pencils-182', NULL, 1),
(345, 39, 'Pencils-183', NULL, 1),
(346, 39, 'Pencils-184', NULL, 1),
(347, 39, 'Pencils-185', NULL, 1),
(363, 40, 'Ballpen-001', NULL, 1),
(364, 40, 'Ballpen-002', NULL, 1),
(365, 40, 'Ballpen-003', NULL, 1),
(366, 40, 'Ballpen-004', NULL, 1),
(367, 40, 'Ballpen-005', NULL, 1),
(368, 40, 'Ballpen-006', NULL, 1),
(369, 40, 'Ballpen-007', NULL, 1),
(370, 40, 'Ballpen-008', NULL, 1),
(371, 40, 'Ballpen-009', NULL, 1),
(372, 40, 'Ballpen-010', NULL, 1),
(373, 40, 'Ballpen-011', NULL, 1),
(374, 40, 'Ballpen-012', NULL, 1),
(375, 40, 'Ballpen-013', NULL, 1),
(376, 40, 'Ballpen-014', NULL, 1),
(377, 40, 'Ballpen-015', NULL, 1),
(378, 40, 'Ballpen-016', NULL, 1),
(379, 40, 'Ballpen-017', NULL, 1),
(380, 40, 'Ballpen-018', NULL, 1),
(381, 40, 'Ballpen-019', NULL, 1),
(382, 40, 'Ballpen-020', NULL, 1),
(383, 40, 'Ballpen-021', NULL, 1),
(384, 40, 'Ballpen-022', NULL, 1),
(385, 40, 'Ballpen-023', NULL, 1),
(386, 40, 'Ballpen-024', NULL, 1),
(387, 40, 'Ballpen-025', NULL, 1),
(388, 40, 'Ballpen-026', NULL, 1),
(389, 40, 'Ballpen-027', NULL, 1),
(390, 40, 'Ballpen-028', NULL, 1),
(391, 40, 'Ballpen-029', NULL, 1),
(392, 40, 'Ballpen-030', NULL, 1),
(393, 40, 'Ballpen-031', NULL, 1),
(394, 40, 'Ballpen-032', NULL, 1),
(395, 40, 'Ballpen-033', NULL, 1),
(396, 40, 'Ballpen-034', NULL, 1),
(397, 40, 'Ballpen-035', NULL, 1),
(398, 40, 'Ballpen-036', NULL, 1),
(399, 40, 'Ballpen-037', NULL, 1),
(400, 40, 'Ballpen-038', NULL, 1),
(401, 40, 'Ballpen-039', NULL, 1),
(402, 40, 'Ballpen-040', NULL, 1),
(403, 40, 'Ballpen-041', NULL, 1),
(404, 40, 'Ballpen-042', NULL, 1),
(405, 40, 'Ballpen-043', NULL, 1),
(406, 40, 'Ballpen-044', NULL, 1),
(407, 40, 'Ballpen-045', NULL, 1),
(408, 40, 'Ballpen-046', NULL, 1),
(409, 40, 'Ballpen-047', NULL, 1),
(410, 40, 'Ballpen-048', NULL, 1),
(413, 41, 'Folder-001', NULL, 1),
(414, 41, 'Folder-002', NULL, 1),
(415, 41, 'Folder-003', NULL, 1),
(416, 41, 'Folder-004', NULL, 1),
(417, 41, 'Folder-005', NULL, 1),
(418, 41, 'Folder-006', NULL, 1),
(419, 41, 'Folder-007', NULL, 1),
(420, 41, 'Folder-008', NULL, 1),
(421, 41, 'Folder-009', NULL, 1),
(422, 41, 'Folder-010', NULL, 1),
(423, 41, 'Folder-011', NULL, 1),
(424, 41, 'Folder-012', NULL, 1),
(425, 41, 'Folder-013', NULL, 1),
(426, 41, 'Folder-014', NULL, 1),
(427, 41, 'Folder-015', NULL, 1),
(428, 41, 'Folder-016', NULL, 1),
(429, 41, 'Folder-017', NULL, 1),
(430, 41, 'Folder-018', NULL, 1),
(431, 41, 'Folder-019', NULL, 1),
(432, 41, 'Folder-020', NULL, 1),
(433, 41, 'Folder-021', NULL, 1),
(434, 41, 'Folder-022', NULL, 1),
(435, 41, 'Folder-023', NULL, 1),
(436, 41, 'Folder-024', NULL, 1),
(437, 41, 'Folder-025', NULL, 1),
(438, 41, 'Folder-026', NULL, 1),
(439, 41, 'Folder-027', NULL, 1),
(440, 41, 'Folder-028', NULL, 1),
(441, 41, 'Folder-029', NULL, 1),
(442, 41, 'Folder-030', NULL, 1),
(443, 41, 'Folder-031', NULL, 1),
(444, 41, 'Folder-032', NULL, 1),
(445, 41, 'Folder-033', NULL, 1),
(446, 41, 'Folder-034', NULL, 1),
(447, 41, 'Folder-035', NULL, 1),
(455, 42, 'Charger-001', NULL, 1),
(456, 43, 'Shuttlecock-001', NULL, 1),
(457, 43, 'Shuttlecock-002', NULL, 1),
(458, 43, 'Shuttlecock-003', NULL, 1),
(459, 43, 'Shuttlecock-004', NULL, 1),
(460, 43, 'Shuttlecock-005', NULL, 1),
(461, 44, 'Notebook-001', NULL, 1),
(462, 44, 'Notebook-002', NULL, 1),
(463, 44, 'Notebook-003', NULL, 1),
(464, 44, 'Notebook-004', NULL, 1),
(465, 44, 'Notebook-005', NULL, 1),
(466, 44, 'Notebook-006', NULL, 1),
(467, 44, 'Notebook-007', NULL, 1),
(468, 44, 'Notebook-008', NULL, 1),
(469, 44, 'Notebook-009', NULL, 1),
(470, 44, 'Notebook-010', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(60) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_level` int(11) NOT NULL,
  `image` varchar(255) DEFAULT 'no_image.jpg',
  `status` int(1) NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `is_logged_in` tinyint(1) DEFAULT 0,
  `location_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `password`, `user_level`, `image`, `status`, `last_login`, `is_logged_in`, `location_id`) VALUES
(1, 'Phoebe', 'Admin', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 1, 'vrwmaf001.jpg', 1, '2025-04-18 17:38:56', 1, 0),
(6, 'richard', 'richard', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 3, 'e8vl8j3a6.jpg', 1, '2025-04-18 17:43:59', 0, 2);

-- --------------------------------------------------------

--
-- Table structure for table `user_groups`
--

CREATE TABLE `user_groups` (
  `id` int(11) NOT NULL,
  `group_name` varchar(150) NOT NULL,
  `group_level` int(11) NOT NULL,
  `group_status` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `user_groups`
--

INSERT INTO `user_groups` (`id`, `group_name`, `group_level`, `group_status`) VALUES
(1, 'Admin', 1, 1),
(2, 'special', 2, 1),
(3, 'User', 3, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `borrowed_items`
--
ALTER TABLE `borrowed_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `item_requests`
--
ALTER TABLE `item_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `location`
--
ALTER TABLE `location`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `barcode` (`barcode`),
  ADD KEY `categorie_id` (`categorie_id`),
  ADD KEY `media_id` (`media_id`);

--
-- Indexes for table `product_usage`
--
ALTER TABLE `product_usage`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `recent_actions`
--
ALTER TABLE `recent_actions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `report_history`
--
ALTER TABLE `report_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock`
--
ALTER TABLE `stock`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `fk_location` (`location_id`),
  ADD KEY `fk_status` (`status_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_level` (`user_level`);

--
-- Indexes for table `user_groups`
--
ALTER TABLE `user_groups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `group_level` (`group_level`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `borrowed_items`
--
ALTER TABLE `borrowed_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `item_requests`
--
ALTER TABLE `item_requests`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `location`
--
ALTER TABLE `location`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `product_usage`
--
ALTER TABLE `product_usage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `recent_actions`
--
ALTER TABLE `recent_actions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `report_history`
--
ALTER TABLE `report_history`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `status`
--
ALTER TABLE `status`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `stock`
--
ALTER TABLE `stock`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=471;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_groups`
--
ALTER TABLE `user_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `FK_products` FOREIGN KEY (`categorie_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `recent_actions`
--
ALTER TABLE `recent_actions`
  ADD CONSTRAINT `recent_actions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock`
--
ALTER TABLE `stock`
  ADD CONSTRAINT `fk_location` FOREIGN KEY (`location_id`) REFERENCES `location` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_status` FOREIGN KEY (`status_id`) REFERENCES `status` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_status_id` FOREIGN KEY (`status_id`) REFERENCES `status` (`id`),
  ADD CONSTRAINT `stock_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `FK_user` FOREIGN KEY (`user_level`) REFERENCES `user_groups` (`group_level`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
