-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 07, 2025 at 07:28 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `booking_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `booking_id` int(12) NOT NULL,
  `client_id` int(12) NOT NULL,
  `package` varchar(250) NOT NULL,
  `price` varchar(250) NOT NULL,
  `event` varchar(250) NOT NULL,
  `date_event` date NOT NULL,
  `address_event` varchar(250) NOT NULL,
  `stat` varchar(250) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`booking_id`, `client_id`, `package`, `price`, `event`, `date_event`, `address_event`, `stat`, `date_created`) VALUES
(2, 9, 'E1', '100.00', 'Debut Party', '2025-01-21', 'mangapo', 'approved', '2025-01-28 00:18:41'),
(3, 9, 'E2', '100.00', 'Debut Party', '2025-01-21', 'mangapo', 'approved', '2025-01-30 22:30:11'),
(4, 9, 'E1', '100.00', 'Debut Party', '2025-01-21', 'mangapo', 'rejected', '2025-01-28 00:18:52'),
(5, 9, 'E1', '100.00', 'Birthday Party', '2025-01-21', 'dsfasf', 'approved', '2025-02-03 20:45:58'),
(6, 9, 'E1', '100.00', 'Wedding', '2025-02-12', '1', 'approved', '2025-02-01 22:51:16'),
(7, 9, 'E2', '100.00', 'Debut Party', '2025-01-22', '12', 'approved', '2025-01-27 23:34:46'),
(8, 10, 'E1', '100.00', 'Christening', '2025-01-22', 'mangapo', 'approved', '2025-01-28 00:11:03'),
(9, 9, 'E1', '100.00', 'Christening', '2025-01-24', 'Libmanan, Camarines Sur', 'approved', '2025-01-26 22:51:05'),
(10, 9, 'E1', '100.00', 'Wedding', '2025-01-31', 'sipocot, camarines sur', 'approved', '2025-02-03 20:49:42'),
(11, 9, 'E1', '100.00', 'Wedding', '2025-01-26', '1', 'cancelled', '2025-02-04 12:11:30'),
(12, 9, 'E1', '100.00', 'Wedding', '2025-01-30', 'fsdafasd', 'processing', '2025-02-04 11:17:03'),
(13, 9, 'E1', '100.00', 'Wedding', '2025-02-05', 'mangapo', 'cancelled', '2025-02-04 12:57:06'),
(14, 9, 'E1', '100.00', 'Wedding', '2025-02-05', 'mangapo', 'cancelled', '2025-02-06 10:37:00'),
(15, 9, 'E1', '100.00', 'Wedding', '2025-02-05', 'mangapo', 'pending', '2025-02-01 22:57:16'),
(16, 9, 'E1', '100.00', 'Wedding', '2025-02-05', 'mangapo', 'pending', '2025-02-01 22:57:16'),
(17, 9, 'E2', '100.00', 'Wedding', '2025-02-05', 'mangapo', 'pending', '2025-02-01 22:58:33'),
(18, 9, 'E1', '100.00', 'Wedding', '2025-02-06', 'mangapo', 'processing', '2025-02-03 20:52:30'),
(19, 9, 'E1', '999.00', 'Debut Party', '2025-02-07', 'fsdafasd', 'processing', '2025-02-05 20:37:46');

-- --------------------------------------------------------

--
-- Table structure for table `client`
--

CREATE TABLE `client` (
  `client_id` int(12) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `c_fullname` varchar(250) NOT NULL,
  `c_email` varchar(250) NOT NULL,
  `c_contactnum` varchar(250) NOT NULL,
  `c_address` varchar(250) NOT NULL,
  `c_password` varchar(250) NOT NULL,
  `date_created` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `client`
--

INSERT INTO `client` (`client_id`, `emp_id`, `c_fullname`, `c_email`, `c_contactnum`, `c_address`, `c_password`, `date_created`) VALUES
(9, 1, 'florence Bermejo', 'berm1234@gmail.com', '0712345678', 'mangapo', '$2y$10$gCBsK7HjLGdgiAAjIWWdOevLUIf5rMPbMfNKTV7Ff0NhOaSQHSsa6', '2025-01-01'),
(10, 1, 'Alex Diaz', 'alex@gmail.com', '0912345678', 'libmanan', '$2y$10$C7C73X7yvNHNysl0Xb9gS.7NtsGtcF11YCiu2PqkqZ7gBzNe.yq5K', '2025-01-02'),
(11, 1, 'alu card', 'alucard@gmail.com', '0712345678', 'mangapo', '$2y$10$Ng5Wm1ARl1FMzeVOhAg13Oo5AN3GZ18lo0ytnqnlOcyMT1fymFqXe', '2025-02-02'),
(12, 1, 'aldog aldous', 'aldog@gmail.com', '0912345678', 'mangapo', '$2y$10$NkLeFQwM1sYkM6RDyWd/kuNiQ8BWKDcNiAXAjofThEb8NrrJmQhSS', '2025-02-02'),
(13, 1, 'alex diaz', 'alexberm946@gmail.com', '0712345678', 'Mangapo', '$2y$10$E2wtUCkw7GNZoERzPICCLuHESTaW8VS2XlIfijCiWinMPQU3BQzvO', '2025-02-05');

-- --------------------------------------------------------

--
-- Table structure for table `contact_info`
--

CREATE TABLE `contact_info` (
  `info_id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `client_name` varchar(255) NOT NULL,
  `phone_num` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `message` varchar(1000) NOT NULL,
  `date_created` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_info`
--

INSERT INTO `contact_info` (`info_id`, `emp_id`, `client_name`, `phone_num`, `email`, `message`, `date_created`) VALUES
(1, 1, 'florence Berm', '0712345678', 'berm@gmail.com', 'May tanong ako', '2025-02-02');

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `emp_id` int(11) NOT NULL,
  `emp_email` varchar(250) NOT NULL,
  `emp_password` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee`
--

INSERT INTO `employee` (`emp_id`, `emp_email`, `emp_password`) VALUES
(1, 'florencebermejo09@gmail.com', '$2y$10$.0doWs.fUUVRH.ex01c/Zu/MAvPiTUUmkU.FP3/nHy.J.QdgyM5M.'),
(3, 'admin@gmail.com', '123');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedback_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `rating` varchar(255) NOT NULL,
  `comment` varchar(255) NOT NULL,
  `date_created` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`feedback_id`, `client_id`, `rating`, `comment`, `date_created`) VALUES
(1, 9, 'Very Satisfied', 'Napakaganda ng mga picture', '2025-02-01'),
(2, 9, 'Dissatisfied', 'medyo okay lang ang picture d ako nagandahan', '2025-02-01');

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `payment_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `transac_num` varchar(250) NOT NULL,
  `amt_payment` varchar(250) NOT NULL,
  `payment_status` varchar(250) NOT NULL,
  `reference_no` varchar(250) NOT NULL,
  `receipt_no` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`payment_id`, `booking_id`, `transac_num`, `amt_payment`, `payment_status`, `reference_no`, `receipt_no`) VALUES
(1, 2, '6551381514', '100', 'Fully Paid', 'f564534163416', '716375'),
(2, 3, '9618996806', '100', 'No Payment', 'walkinpayment', '684820'),
(3, 9, '4440498942', '100', 'Fully Paid', 'f564534163416', '883032'),
(4, 4, '1618902249', '100', 'No Payment', 'walkinpayment', '304040'),
(5, 5, '3983097454', '100', 'No Payment', 'walkinpayment', '532968'),
(6, 6, '8337290359', '100', 'No Payment', 'walkinpayment', '416456'),
(7, 7, '5224956953', '100', 'No Payment', 'walkinpayment', '489443'),
(8, 5, '5353298560', '100', 'No Payment', 'walkinpayment', '532968'),
(9, 10, '2280775522', '100', 'Fully Paid', '321321331321', '206480'),
(10, 12, '2640616467', '100', 'No Payment', 'walkinpayment', ''),
(11, 19, '3430023847', '999', 'No Payment', 'walkinpayment', '');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(12) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `total_sales` decimal(65,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `emp_id`, `date`, `total_sales`) VALUES
(1, 1, '2019-01-01', 53000),
(2, 1, '2019-02-01', 52000),
(3, 1, '2019-03-01', 54000),
(4, 1, '2019-04-01', 53000),
(5, 1, '2019-05-01', 58000),
(6, 1, '2019-06-01', 60000),
(7, 1, '2019-07-01', 62000),
(8, 1, '2019-08-01', 63000),
(9, 1, '2019-09-01', 61000),
(10, 1, '2019-10-01', 65000),
(11, 1, '2019-11-01', 70000),
(12, 1, '2019-12-01', 75000),
(13, 1, '2020-01-01', 52000),
(14, 1, '2020-02-01', 54000),
(15, 1, '2020-03-01', 58000),
(16, 1, '2020-04-01', 56000),
(17, 1, '2020-05-01', 59000),
(18, 1, '2020-06-01', 61000),
(19, 1, '2020-07-01', 64000),
(20, 1, '2020-08-01', 67000),
(21, 1, '2020-09-01', 60000),
(22, 1, '2020-10-01', 66000),
(23, 1, '2020-11-01', 71000),
(24, 1, '2020-12-01', 74000),
(25, 1, '2021-01-01', 53000),
(26, 1, '2021-02-01', 55000),
(27, 1, '2021-03-01', 60000),
(28, 1, '2021-04-01', 58000),
(29, 1, '2021-05-01', 59000),
(30, 1, '2021-06-01', 61000),
(31, 1, '2021-07-01', 63000),
(32, 1, '2021-08-01', 62000),
(33, 1, '2021-09-01', 61000),
(34, 1, '2021-10-01', 67000),
(35, 1, '2021-11-01', 71000),
(36, 1, '2021-12-01', 79000),
(37, 1, '2022-01-01', 55000),
(38, 1, '2022-02-01', 53000),
(39, 1, '2022-03-01', 59000),
(40, 1, '2022-04-01', 59000),
(41, 1, '2022-05-01', 60000),
(42, 1, '2022-06-01', 62000),
(43, 1, '2022-07-01', 60000),
(44, 1, '2022-08-01', 68000),
(45, 1, '2022-09-01', 65000),
(46, 1, '2022-10-01', 68000),
(47, 1, '2022-11-01', 74000),
(48, 1, '2022-12-01', 71000),
(49, 1, '2023-01-01', 55000),
(50, 1, '2023-02-01', 58000),
(51, 1, '2023-03-01', 60000),
(52, 1, '2023-04-01', 58000),
(53, 1, '2023-05-01', 60000),
(54, 1, '2023-06-01', 63000),
(55, 1, '2023-07-01', 64000),
(56, 1, '2023-08-01', 66000),
(57, 1, '2023-09-01', 65000),
(58, 1, '2023-10-01', 68000),
(59, 1, '2023-11-01', 74000),
(60, 1, '2023-12-01', 76000),
(61, 1, '2024-01-01', 58000),
(62, 1, '2024-02-01', 51000),
(63, 1, '2024-03-01', 57000),
(64, 1, '2024-04-01', 59000),
(65, 1, '2024-05-01', 52000),
(66, 1, '2024-06-01', 62000),
(67, 1, '2024-07-01', 64000),
(68, 1, '2024-08-01', 60000),
(69, 1, '2024-09-01', 62000),
(70, 1, '2024-10-01', 66000),
(71, 1, '2024-11-01', 72000),
(72, 1, '2024-12-01', 77000),
(73, 1, '2025-01-30', 61000),
(74, 1, '2025-02-01', 67000);

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `service_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `details` varchar(255) NOT NULL,
  `price` varchar(255) NOT NULL,
  `date_created` date NOT NULL,
  `emp_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`service_id`, `name`, `details`, `price`, `date_created`, `emp_id`) VALUES
(1, 'E1', '1-PAX|15 Minutes Photoshoot|15 Minutes Photo Selection|2 Backdrops (Carnation, Pink, Alpine Blue|4 Digital Copy', '999', '2025-02-01', 1),
(2, 'E2', '1-PAX|15 Minutes Photoshoot|15 Minutes Photo Selection|2 Backdrops (Carnation, Pink, Alpine Blue)|7 Digital Copies', '1099', '2025-02-01', 1),
(3, 'E3', '1-PAX|30 Minutes Photoshoot|15 Minutes Photo Selection|2 Backdrops (Carnation, Pink, Alpine Blue|10 Digital Copies', '1199', '2025-02-01', 1),
(4, 'E4', '1-5 PAX|30 Minutes Photoshoot|15 Minutes Photo Selection|3 Backdrops (Carnation, Pink, Alpine Blue|15 Digital Copies', '1399', '2025-02-01', 1);

-- --------------------------------------------------------

--
-- Table structure for table `webuser`
--

CREATE TABLE `webuser` (
  `email` varchar(250) NOT NULL,
  `usertype` varchar(250) NOT NULL,
  `reset_token` varchar(255) NOT NULL,
  `reset_expiry` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `webuser`
--

INSERT INTO `webuser` (`email`, `usertype`, `reset_token`, `reset_expiry`) VALUES
('aldog@gmail.com', 'p', '', '0000-00-00 00:00:00'),
('alex@gmail.com', 'p', '', '0000-00-00 00:00:00'),
('alexberm946@gmail.com', 'p', '', '0000-00-00 00:00:00'),
('alucard@gmail.com', 'p', '', '0000-00-00 00:00:00'),
('berm1234@gmail.com', 'p', '', '0000-00-00 00:00:00'),
('berm123@gmail.com', 'p', '', '0000-00-00 00:00:00'),
('florencebermejo09@gmail.com', 'a', '', '0000-00-00 00:00:00'),
('florencebermejo109@gmail.com', '123', '13ed2aaab9d402f66f6f9812c6b1a882f5e2dfc520184365a872f607f8741e93b6fb2b177d6665abeb4ad0e9a0509c364da7', '2025-02-05 05:08:15');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `booking_ibfk_1` (`client_id`);

--
-- Indexes for table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`client_id`),
  ADD KEY `client_ibfk_1` (`emp_id`);

--
-- Indexes for table `contact_info`
--
ALTER TABLE `contact_info`
  ADD PRIMARY KEY (`info_id`),
  ADD KEY `emp_id` (`emp_id`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`emp_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `emp_id` (`emp_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`service_id`),
  ADD KEY `emp_id` (`emp_id`);

--
-- Indexes for table `webuser`
--
ALTER TABLE `webuser`
  ADD PRIMARY KEY (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `booking_id` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `client`
--
ALTER TABLE `client`
  MODIFY `client_id` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `contact_info`
--
ALTER TABLE `contact_info`
  MODIFY `info_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `employee`
--
ALTER TABLE `employee`
  MODIFY `emp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `service_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `booking_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `client` (`client_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `client`
--
ALTER TABLE `client`
  ADD CONSTRAINT `client_ibfk_1` FOREIGN KEY (`emp_id`) REFERENCES `employee` (`emp_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `contact_info`
--
ALTER TABLE `contact_info`
  ADD CONSTRAINT `contact_info_ibfk_1` FOREIGN KEY (`emp_id`) REFERENCES `employee` (`emp_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `client` (`client_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`booking_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`emp_id`) REFERENCES `employee` (`emp_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `services`
--
ALTER TABLE `services`
  ADD CONSTRAINT `services_ibfk_1` FOREIGN KEY (`emp_id`) REFERENCES `employee` (`emp_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
