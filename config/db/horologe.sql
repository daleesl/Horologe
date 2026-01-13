-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 11, 2026 at 06:00 AM
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
-- Database: `horologe`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` varchar(50) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `user_id`, `created_at`) VALUES
('cart_6962408579372', 'USR004', '2026-01-10 20:05:25'),
('cart_69632579dfd40', 'USR003', '2026-01-11 12:22:17');

-- --------------------------------------------------------

--
-- Table structure for table `cartitems`
--

CREATE TABLE `cartitems` (
  `cart_item_id` varchar(50) NOT NULL,
  `watch_id` varchar(50) NOT NULL,
  `cart_id` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cartitems`
--

INSERT INTO `cartitems` (`cart_item_id`, `watch_id`, `cart_id`, `quantity`, `subtotal`) VALUES
('ci_6962408a59adf', 'W001', 'cart_6962408579372', 1, 10000.00),
('ci_69624095e21c2', 'W002', 'cart_6962408579372', 1, 9500.00);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` varchar(50) NOT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `user_name` varchar(120) NOT NULL,
  `user_email` varchar(120) NOT NULL,
  `user_phone` varchar(20) DEFAULT NULL,
  `watch_id` varchar(50) DEFAULT NULL,
  `product_name` varchar(150) DEFAULT NULL,
  `product_description` text DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `price_at_purchase` decimal(10,2) DEFAULT NULL,
  `ship_full_name` varchar(150) NOT NULL,
  `ship_phone_number` varchar(20) DEFAULT NULL,
  `ship_street_address` varchar(255) NOT NULL,
  `ship_city` varchar(100) NOT NULL,
  `ship_province_state` varchar(100) DEFAULT NULL,
  `ship_postal_code` varchar(20) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `order_date`, `total_amount`, `user_id`, `user_name`, `user_email`, `user_phone`, `watch_id`, `product_name`, `product_description`, `quantity`, `price_at_purchase`, `ship_full_name`, `ship_phone_number`, `ship_street_address`, `ship_city`, `ship_province_state`, `ship_postal_code`, `payment_method`, `created_at`, `updated_at`) VALUES
('ORD696224c16aa98', '2026-01-10 18:06:57', 10000.00, 'USR003', 'John Jester Luciriaga', 'johnjesterluciriaga4@gmail.com', NULL, NULL, NULL, NULL, 1, NULL, 'John Jester Luciriaga', NULL, '1111', 'Calamba', 'Philippines', '123', '0', '2026-01-10 18:06:57', '2026-01-10 18:06:57'),
('ORD6962256a7c705', '2026-01-10 18:09:46', 10000.00, 'USR003', 'John Jester Luciriaga', 'johnjesterluciriaga4@gmail.com', NULL, NULL, NULL, NULL, 1, NULL, 'John Jester Luciriaga', NULL, '1111', 'Calamba', 'Philippines', '123', '0', '2026-01-10 18:09:46', '2026-01-10 18:09:46'),
('ORD69623ce3a6145', '2026-01-10 19:49:55', 9500.00, 'USR003', 'John Jester Luciriaga', 'johnjesterluciriaga4@gmail.com', NULL, NULL, NULL, NULL, 1, NULL, 'John Jester Luciriaga', NULL, '1111', 'Calamba', 'Philippines', '123', '0', '2026-01-10 19:49:55', '2026-01-10 19:49:55'),
('ORD69623d25e8d84', '2026-01-10 19:51:01', 9500.00, 'USR003', 'John Jester Luciriaga', 'johnjesterluciriaga4@gmail.com', NULL, NULL, NULL, NULL, 1, NULL, 'John Jester Luciriaga', NULL, '1111', 'Calamba', 'Philippines', '123', '0', '2026-01-10 19:51:01', '2026-01-10 19:51:01'),
('ORD69623df2c543f', '2026-01-10 19:54:26', 9500.00, 'USR003', 'John Jester Luciriaga', 'johnjesterluciriaga4@gmail.com', NULL, NULL, NULL, NULL, 1, NULL, 'John Jester Luciriaga', NULL, '1111', 'Calamba', 'Philippines', '123', '0', '2026-01-10 19:54:26', '2026-01-10 19:54:26'),
('ORD69623f449e7c6', '2026-01-10 20:00:04', 9500.00, 'USR003', 'John Jester Luciriaga', 'johnjesterluciriaga4@gmail.com', NULL, NULL, NULL, NULL, 1, NULL, 'John Jester Luciriaga', NULL, '1111', 'Calamba', 'Philippines', '123', '0', '2026-01-10 20:00:04', '2026-01-10 20:00:04'),
('ORD69623fce941eb', '2026-01-10 20:02:22', 10000.00, 'USR003', 'Kiko Luciriaga', 'johnjesterluciriaga4@gmail.com', NULL, NULL, NULL, NULL, 1, NULL, 'Kiko Luciriaga', NULL, '1111', 'Calamba', 'Philippines', '123', '0', '2026-01-10 20:02:22', '2026-01-10 20:02:22'),
('ORD696241db8601e', '2026-01-10 20:11:07', 10000.00, 'USR003', 'John Jester Luciriaga', 'johnjesterluciriaga4@gmail.com', NULL, NULL, NULL, NULL, 1, NULL, 'John Jester Luciriaga', NULL, '1111', 'Calamba', 'Philippines', '123', '0', '2026-01-10 20:11:07', '2026-01-10 20:11:07'),
('ORD69624dd31afd8', '2026-01-10 21:02:11', 9500.00, 'USR003', 'John Jester Luciriaga', 'johnjesterluciriaga4@gmail.com', NULL, NULL, NULL, NULL, 1, NULL, 'John Jester Luciriaga', NULL, '1111', 'Calamba', 'Philippines', '123', '0', '2026-01-10 21:02:11', '2026-01-10 21:02:11'),
('ORD696250af78bea', '2026-01-10 21:14:23', 123123.00, 'USR003', 'John Jester Luciriaga', 'johnjesterluciriaga4@gmail.com', NULL, NULL, NULL, NULL, 1, NULL, 'John Jester Luciriaga', NULL, '1111', 'Calamba', 'Philippines', '123', '0', '2026-01-10 21:14:23', '2026-01-10 21:14:23'),
('ORD696250e3227fe', '2026-01-10 21:15:15', 133123.00, 'USR003', 'John Jester Luciriaga', 'johnjesterluciriaga4@gmail.com', NULL, NULL, NULL, NULL, 1, NULL, 'John Jester Luciriaga', NULL, '1111', 'Calamba', 'Philippines', '123', '0', '2026-01-10 21:15:15', '2026-01-10 21:15:15'),
('ORD6962520fc4d58', '2026-01-10 21:20:15', 123123.00, 'USR003', 'John Jester Luciriaga', 'johnjesterluciriaga4@gmail.com', NULL, NULL, NULL, NULL, 1, NULL, 'John Jester Luciriaga', NULL, '1111', 'Calamba', 'Philippines', '123', '0', '2026-01-10 21:20:15', '2026-01-10 21:20:15'),
('ORD69628000df8d9', '2026-01-11 00:36:16', 123123.00, 'USR003', 'John Jester Luciriaga', 'johnjesterluciriaga4@gmail.com', NULL, NULL, NULL, NULL, 1, NULL, 'John Jester Luciriaga', NULL, '1111', 'Calamba', 'Philippines', '123', '0', '2026-01-11 00:36:16', '2026-01-11 00:36:16'),
('ORD69628001', '2026-01-11 00:55:03', 10000.00, 'USR003', 'Michelle Olfindo', 'michelle@gmail.com', NULL, NULL, NULL, NULL, 1, NULL, 'Michelle Olfindo', NULL, '2222', 'Calamba', 'Philippines', '123', '0', '2026-01-11 00:55:03', '2026-01-11 00:55:03'),
('ORD69628002', '2026-01-11 00:59:59', 133123.00, 'USR003', 'John Jester Luciriaga', 'johnjesterluciriaga4@gmail.com', NULL, NULL, NULL, NULL, 1, NULL, 'John Jester Luciriaga', NULL, '1111', 'Calamba', 'Philippines', '123', '0', '2026-01-11 00:59:59', '2026-01-11 00:59:59'),
('ORD69628003', '2026-01-11 01:38:18', 10000.00, 'USR003', 'John Jester Luciriaga', 'johnjesterluciriaga4@gmail.com', NULL, NULL, NULL, NULL, 1, NULL, 'John Jester Luciriaga', NULL, '1111', 'Calamba', 'Philippines', '123', '0', '2026-01-11 01:38:18', '2026-01-11 01:38:18'),
('ORD69628004', '2026-01-11 12:22:04', 10000.00, 'USR003', 'John Jester Luciriaga', 'johnjesterluciriaga4@gmail.com', NULL, NULL, NULL, NULL, 1, NULL, 'John Jester Luciriaga', NULL, '1111', 'Calamba', 'Philippines', '123', '0', '2026-01-11 12:22:04', '2026-01-11 12:22:04');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` varchar(50) NOT NULL,
  `watch_id` varchar(50) NOT NULL,
  `product_name` varchar(150) DEFAULT NULL,
  `product_description` text DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price_at_purchase` decimal(10,2) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `watch_id`, `product_name`, `product_description`, `quantity`, `price_at_purchase`, `created_at`) VALUES
(1, 'ORD696224c16aa98', 'W001', 'Rolex Yacht-Master 40 Everose Black Dial 116655 2017', '', 1, 10000.00, '2026-01-10 18:06:57'),
(2, 'ORD6962256a7c705', 'W001', 'Rolex Yacht-Master 40 Everose Black Dial 116655 2017', NULL, 1, 10000.00, '2026-01-10 18:09:46'),
(3, 'ORD69623ce3a6145', 'W002', 'Cartier Americane Rose Gold W2607456', 'A graceful expression of Cartier\'s signature elegance, this Tank Américaine in rose gold features a sleek rectangular case and classic Roman numerals. Its refined profile and polished finish make it a quintessential dress watch for those who appreciate subtle sophistication.', 1, 9500.00, '2026-01-10 19:49:55'),
(4, 'ORD69623d25e8d84', 'W002', 'Cartier Americane Rose Gold W2607456', 'A graceful expression of Cartier\'s signature elegance, this Tank Américaine in rose gold features a sleek rectangular case and classic Roman numerals. Its refined profile and polished finish make it a quintessential dress watch for those who appreciate subtle sophistication.', 1, 9500.00, '2026-01-10 19:51:01'),
(5, 'ORD69623df2c543f', 'W002', 'Cartier Americane Rose Gold W2607456', 'A graceful expression of Cartier\'s signature elegance, this Tank Américaine in rose gold features a sleek rectangular case and classic Roman numerals. Its refined profile and polished finish make it a quintessential dress watch for those who appreciate subtle sophistication.', 1, 9500.00, '2026-01-10 19:54:26'),
(6, 'ORD69623f449e7c6', 'W002', 'Cartier Americane Rose Gold W2607456', 'A graceful expression of Cartier\'s signature elegance, this Tank Américaine in rose gold features a sleek rectangular case and classic Roman numerals. Its refined profile and polished finish make it a quintessential dress watch for those who appreciate subtle sophistication.', 1, 9500.00, '2026-01-10 20:00:04'),
(7, 'ORD69623fce941eb', 'W001', 'Rolex Yacht-Master 40 Everose Black Dial 116655 2017', 'The Yacht-Master 40 in Everose gold epitomizes the perfect union of sport and luxury. Its bold black dial contrasts beautifully with the warm glow of Everose, while its Oysterflex bracelet ensures comfort and durability. Designed for maritime elegance and refined adventures, it is a statement of both performance and prestige.', 1, 10000.00, '2026-01-10 20:02:22'),
(8, 'ORD696241db8601e', 'W001', 'Rolex Yacht-Master 40 Everose Black Dial 116655 2017', 'The Yacht-Master 40 in Everose gold epitomizes the perfect union of sport and luxury. Its bold black dial contrasts beautifully with the warm glow of Everose, while its Oysterflex bracelet ensures comfort and durability. Designed for maritime elegance and refined adventures, it is a statement of both performance and prestige.', 1, 10000.00, '2026-01-10 20:11:07'),
(9, 'ORD69624dd31afd8', 'W002', 'Cartier Americane Rose Gold W2607456', 'A graceful expression of Cartier\'s signature elegance, this Tank Américaine in rose gold features a sleek rectangular case and classic Roman numerals. Its refined profile and polished finish make it a quintessential dress watch for those who appreciate subtle sophistication.', 1, 9500.00, '2026-01-10 21:02:11'),
(10, 'ORD696250af78bea', 'W003', 'Rolex test', 'asdadw', 1, 123123.00, '2026-01-10 21:14:23'),
(11, 'ORD696250e3227fe', 'W001', 'Rolex Yacht-Master 40 Everose Black Dial 116655 2017', 'The Yacht-Master 40 in Everose gold epitomizes the perfect union of sport and luxury. Its bold black dial contrasts beautifully with the warm glow of Everose, while its Oysterflex bracelet ensures comfort and durability. Designed for maritime elegance and refined adventures, it is a statement of both performance and prestige.', 1, 10000.00, '2026-01-10 21:15:15'),
(12, 'ORD696250e3227fe', 'W003', 'Rolex test', 'asdadw', 1, 123123.00, '2026-01-10 21:15:15'),
(13, 'ORD6962520fc4d58', 'W003', 'Rolex test', 'asdadw', 1, 123123.00, '2026-01-10 21:20:15'),
(14, 'ORD69628000df8d9', 'W003', 'Rolex test', 'asdadw', 1, 123123.00, '2026-01-11 00:36:16'),
(15, 'ORD69628001', 'W001', 'Rolex Yacht-Master 40 Everose Black Dial 116655 2017', 'The Yacht-Master 40 in Everose gold epitomizes the perfect union of sport and luxury. Its bold black dial contrasts beautifully with the warm glow of Everose, while its Oysterflex bracelet ensures comfort and durability. Designed for maritime elegance and refined adventures, it is a statement of both performance and prestige.', 1, 10000.00, '2026-01-11 00:55:03'),
(16, 'ORD69628002', 'W001', 'Rolex Yacht-Master 40 Everose Black Dial 116655 2017', 'The Yacht-Master 40 in Everose gold epitomizes the perfect union of sport and luxury. Its bold black dial contrasts beautifully with the warm glow of Everose, while its Oysterflex bracelet ensures comfort and durability. Designed for maritime elegance and refined adventures, it is a statement of both performance and prestige.', 1, 10000.00, '2026-01-11 00:59:59'),
(17, 'ORD69628002', 'W003', 'Rolex test', 'asdadw', 1, 123123.00, '2026-01-11 00:59:59'),
(18, 'ORD69628003', 'W001', 'Rolex Yacht-Master 40 Everose Black Dial 116655 2017', 'The Yacht-Master 40 in Everose gold epitomizes the perfect union of sport and luxury. Its bold black dial contrasts beautifully with the warm glow of Everose, while its Oysterflex bracelet ensures comfort and durability. Designed for maritime elegance and refined adventures, it is a statement of both performance and prestige.', 1, 10000.00, '2026-01-11 01:38:18'),
(19, 'ORD69628004', 'W001', 'Rolex Yacht-Master 40 Everose Black Dial 116655 2017', 'The Yacht-Master 40 in Everose gold epitomizes the perfect union of sport and luxury. Its bold black dial contrasts beautifully with the warm glow of Everose, while its Oysterflex bracelet ensures comfort and durability. Designed for maritime elegance and refined adventures, it is a statement of both performance and prestige.', 1, 10000.00, '2026-01-11 12:22:04');

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `id` int(11) NOT NULL,
  `payment_id` varchar(50) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `payment_status` varchar(50) NOT NULL,
  `payment_date` datetime DEFAULT current_timestamp(),
  `amount` decimal(10,2) NOT NULL,
  `order_id` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`id`, `payment_id`, `payment_method`, `payment_status`, `payment_date`, `amount`, `order_id`, `created_at`, `updated_at`) VALUES
(1, 'PAY-1768036894-9400', 'PAYPAL', 'COMPLETED', '2026-01-10 17:21:45', 10000.00, NULL, '2026-01-10 17:21:34', '2026-01-10 17:21:45'),
(3, 'PAY-1768037003-1127', 'PAYPAL', 'COMPLETED', '2026-01-10 17:23:35', 10000.00, NULL, '2026-01-10 17:23:23', '2026-01-10 17:23:35'),
(5, 'PAY-1768037140-6077', 'PAYPAL', 'COMPLETED', '2026-01-10 17:25:52', 10000.00, NULL, '2026-01-10 17:25:40', '2026-01-10 17:25:52'),
(6, 'PAY-1768037182-8874', 'PAYPAL', 'COMPLETED', '2026-01-10 17:26:32', 10000.00, NULL, '2026-01-10 17:26:22', '2026-01-10 17:26:32'),
(7, 'PAY-1768037317-5467', 'PAYPAL', 'PENDING', '2026-01-10 17:28:37', 10000.00, NULL, '2026-01-10 17:28:37', '2026-01-10 17:28:37'),
(8, 'PAY-1768037333-6044', 'PAYPAL', 'PENDING', '2026-01-10 17:28:53', 10000.00, NULL, '2026-01-10 17:28:53', '2026-01-10 17:28:53'),
(9, 'PAY-1768037346-6305', 'PAYPAL', 'COMPLETED', '2026-01-10 17:29:29', 10000.00, NULL, '2026-01-10 17:29:06', '2026-01-10 17:29:29'),
(10, 'PAY-1768037989-2313', 'PAYPAL', 'COMPLETED', '2026-01-10 17:40:01', 10000.00, NULL, '2026-01-10 17:39:49', '2026-01-10 17:40:01'),
(12, 'PAY-1768039107-9386', 'PAYPAL', 'COMPLETED', '2026-01-10 17:59:41', 10000.00, NULL, '2026-01-10 17:58:27', '2026-01-10 17:59:41'),
(13, 'PAY-1768039428-2102', 'PAYPAL', 'COMPLETED', '2026-01-10 18:03:59', 10000.00, NULL, '2026-01-10 18:03:48', '2026-01-10 18:03:59'),
(14, 'PAY-1768039486-6382', 'PAYPAL', 'COMPLETED', '2026-01-10 18:04:59', 10000.00, NULL, '2026-01-10 18:04:46', '2026-01-10 18:04:59'),
(15, 'PAY-1768039606-1466', 'PAYPAL', 'COMPLETED', '2026-01-10 18:06:57', 10000.00, NULL, '2026-01-10 18:06:46', '2026-01-10 18:06:57'),
(16, '8N399668TH4462343', 'PAYPAL', 'COMPLETED', '2026-01-10 18:06:57', 10000.00, 'ORD696224c16aa98', '2026-01-10 18:06:57', '2026-01-10 18:06:57'),
(17, 'PAY-1768039774-1469', 'PAYPAL', 'COMPLETED', '2026-01-10 18:09:46', 10000.00, NULL, '2026-01-10 18:09:34', '2026-01-10 18:09:46'),
(18, '8F186912V75302807', 'PAYPAL', 'COMPLETED', '2026-01-10 18:09:46', 10000.00, 'ORD6962256a7c705', '2026-01-10 18:09:46', '2026-01-10 18:09:46'),
(19, 'PAY-1768045686-7122', 'PAYPAL', 'COMPLETED', '2026-01-10 19:49:55', 9500.00, NULL, '2026-01-10 19:48:06', '2026-01-10 19:49:55'),
(20, '4JR68969WU844320E', 'PAYPAL', 'COMPLETED', '2026-01-10 19:49:55', 9500.00, 'ORD69623ce3a6145', '2026-01-10 19:49:55', '2026-01-10 19:49:55'),
(21, 'PAY-1768045821-7876', 'PAYPAL', 'COMPLETED', '2026-01-10 19:51:01', 9500.00, NULL, '2026-01-10 19:50:21', '2026-01-10 19:51:01'),
(22, '2BF22250WB5111242', 'PAYPAL', 'COMPLETED', '2026-01-10 19:51:01', 9500.00, 'ORD69623d25e8d84', '2026-01-10 19:51:01', '2026-01-10 19:51:01'),
(23, 'PAY-1768046056-7564', 'PAYPAL', 'COMPLETED', '2026-01-10 19:54:26', 9500.00, NULL, '2026-01-10 19:54:16', '2026-01-10 19:54:26'),
(24, '6W80238242495231A', 'PAYPAL', 'COMPLETED', '2026-01-10 19:54:26', 9500.00, 'ORD69623df2c543f', '2026-01-10 19:54:26', '2026-01-10 19:54:26'),
(25, 'PAY-1768046392-7641', 'PAYPAL', 'COMPLETED', '2026-01-10 20:00:04', 9500.00, NULL, '2026-01-10 19:59:52', '2026-01-10 20:00:04'),
(26, '0DW70195GT062783R', 'PAYPAL', 'COMPLETED', '2026-01-10 20:00:04', 9500.00, 'ORD69623f449e7c6', '2026-01-10 20:00:04', '2026-01-10 20:00:04'),
(27, 'PAY-1768046526-7622', 'PAYPAL', 'COMPLETED', '2026-01-10 20:02:22', 10000.00, NULL, '2026-01-10 20:02:06', '2026-01-10 20:02:22'),
(28, '5XC908755C914010K', 'PAYPAL', 'COMPLETED', '2026-01-10 20:02:22', 10000.00, 'ORD69623fce941eb', '2026-01-10 20:02:22', '2026-01-10 20:02:22'),
(29, 'PAY-1768047056-5384', 'PAYPAL', 'COMPLETED', '2026-01-10 20:11:07', 10000.00, NULL, '2026-01-10 20:10:56', '2026-01-10 20:11:07'),
(30, '16M28086EG099530W', 'PAYPAL', 'COMPLETED', '2026-01-10 20:11:07', 10000.00, 'ORD696241db8601e', '2026-01-10 20:11:07', '2026-01-10 20:11:07'),
(31, 'PAY-1768048540-1138', 'PAYPAL', 'PENDING', '2026-01-10 20:35:40', 9500.00, NULL, '2026-01-10 20:35:40', '2026-01-10 20:35:40'),
(32, 'PAY-1768048803-9844', 'PAYPAL', 'PENDING', '2026-01-10 20:40:03', 9500.00, NULL, '2026-01-10 20:40:03', '2026-01-10 20:40:03'),
(33, 'PAY-1768049021-1260', 'PAYPAL', 'PENDING', '2026-01-10 20:43:41', 9500.00, NULL, '2026-01-10 20:43:41', '2026-01-10 20:43:41'),
(34, 'PAY-1768049029-1212', 'PAYPAL', 'PENDING', '2026-01-10 20:43:49', 9500.00, NULL, '2026-01-10 20:43:49', '2026-01-10 20:43:49'),
(35, 'PAY-1768049701-4896', 'PAYPAL', 'PENDING', '2026-01-10 20:55:01', 9500.00, NULL, '2026-01-10 20:55:01', '2026-01-10 20:55:01'),
(36, 'PAY-1768049830-4863', 'PAYPAL', 'PENDING', '2026-01-10 20:57:10', 9500.00, NULL, '2026-01-10 20:57:10', '2026-01-10 20:57:10'),
(37, 'PAY-1768049846-9317', 'PAYPAL', 'PENDING', '2026-01-10 20:57:26', 9500.00, NULL, '2026-01-10 20:57:26', '2026-01-10 20:57:26'),
(38, 'PAY-1768049905-3402', 'PAYPAL', 'PENDING', '2026-01-10 20:58:25', 9500.00, NULL, '2026-01-10 20:58:25', '2026-01-10 20:58:25'),
(39, 'PAY-1768050066-5100', 'PAYPAL', 'COMPLETED', '2026-01-10 21:02:11', 9500.00, NULL, '2026-01-10 21:01:06', '2026-01-10 21:02:11'),
(40, '4KX78700LX360374V', 'PAYPAL', 'COMPLETED', '2026-01-10 21:02:11', 9500.00, 'ORD69624dd31afd8', '2026-01-10 21:02:11', '2026-01-10 21:02:11'),
(41, 'PAY-1768050327-5879', 'PAYPAL', 'PENDING', '2026-01-10 21:05:27', 123123.00, NULL, '2026-01-10 21:05:27', '2026-01-10 21:05:27'),
(42, 'PAY-1768050806-5595', 'PAYPAL', 'PENDING', '2026-01-10 21:13:26', 123123.00, NULL, '2026-01-10 21:13:26', '2026-01-10 21:13:26'),
(43, 'PAY-1768050853-3273', 'PAYPAL', 'COMPLETED', '2026-01-10 21:14:23', 123123.00, NULL, '2026-01-10 21:14:13', '2026-01-10 21:14:23'),
(44, '2WS26930TN638181W', 'PAYPAL', 'COMPLETED', '2026-01-10 21:14:23', 123123.00, 'ORD696250af78bea', '2026-01-10 21:14:23', '2026-01-10 21:14:23'),
(45, 'PAY-1768050903-4889', 'PAYPAL', 'COMPLETED', '2026-01-10 21:15:15', 133123.00, NULL, '2026-01-10 21:15:03', '2026-01-10 21:15:15'),
(46, '03J296906X004872U', 'PAYPAL', 'COMPLETED', '2026-01-10 21:15:15', 133123.00, 'ORD696250e3227fe', '2026-01-10 21:15:15', '2026-01-10 21:15:15'),
(47, 'PAY-1768051204-5309', 'PAYPAL', 'COMPLETED', '2026-01-10 21:20:15', 123123.00, NULL, '2026-01-10 21:20:04', '2026-01-10 21:20:15'),
(48, '9E574312BN497235G', 'PAYPAL', 'COMPLETED', '2026-01-10 21:20:15', 123123.00, 'ORD6962520fc4d58', '2026-01-10 21:20:15', '2026-01-10 21:20:15'),
(49, 'PAY-1768062686-4221', 'PAYPAL', 'PENDING', '2026-01-11 00:31:26', 123123.00, NULL, '2026-01-11 00:31:26', '2026-01-11 00:31:26'),
(50, 'PAY-1768062730-7093', 'PAYPAL', 'COMPLETED', '2026-01-11 00:36:16', 123123.00, NULL, '2026-01-11 00:32:10', '2026-01-11 00:36:16'),
(51, '0R2605166F3486805', 'PAYPAL', 'COMPLETED', '2026-01-11 00:36:16', 123123.00, 'ORD69628000df8d9', '2026-01-11 00:36:16', '2026-01-11 00:36:16'),
(52, 'PAY-1768064050-4721', 'PAYPAL', 'COMPLETED', '2026-01-11 00:55:03', 10000.00, NULL, '2026-01-11 00:54:10', '2026-01-11 00:55:03'),
(53, '2T139502PC553500C', 'PAYPAL', 'COMPLETED', '2026-01-11 00:55:03', 10000.00, 'ORD69628001', '2026-01-11 00:55:03', '2026-01-11 00:55:03'),
(54, 'PAY-1768064329-6720', 'PAYPAL', 'PENDING', '2026-01-11 00:58:49', 133123.00, NULL, '2026-01-11 00:58:49', '2026-01-11 00:58:49'),
(55, 'PAY-1768064388-2419', 'PAYPAL', 'COMPLETED', '2026-01-11 00:59:59', 133123.00, NULL, '2026-01-11 00:59:48', '2026-01-11 00:59:59'),
(56, '4U495771P7228384U', 'PAYPAL', 'COMPLETED', '2026-01-11 00:59:59', 133123.00, 'ORD69628002', '2026-01-11 00:59:59', '2026-01-11 00:59:59'),
(57, 'PAY-1768066648-7618', 'PAYPAL', 'COMPLETED', '2026-01-11 01:38:18', 10000.00, NULL, '2026-01-11 01:37:28', '2026-01-11 01:38:18'),
(58, '92953637FT7974248', 'PAYPAL', 'COMPLETED', '2026-01-11 01:38:18', 10000.00, 'ORD69628003', '2026-01-11 01:38:18', '2026-01-11 01:38:18'),
(59, 'PAY-1768105240-7251', 'PAYPAL', 'COMPLETED', '2026-01-11 12:22:04', 10000.00, NULL, '2026-01-11 12:20:40', '2026-01-11 12:22:04'),
(60, '0SS13286R12268604', 'PAYPAL', 'COMPLETED', '2026-01-11 12:22:04', 10000.00, 'ORD69628004', '2026-01-11 12:22:04', '2026-01-11 12:22:04'),
(61, 'PAY-1768107056-5903', 'PAYPAL', 'PENDING', '2026-01-11 12:50:56', 10000.00, NULL, '2026-01-11 12:50:56', '2026-01-11 12:50:56');

-- --------------------------------------------------------

--
-- Table structure for table `paypalpayment`
--

CREATE TABLE `paypalpayment` (
  `id` int(11) NOT NULL,
  `paypal_transaction_id` varchar(100) NOT NULL,
  `payer_email` varchar(100) NOT NULL,
  `transaction_date` datetime DEFAULT current_timestamp(),
  `payment_status` varchar(50) NOT NULL,
  `payment_id` varchar(50) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `paypalpayment`
--

INSERT INTO `paypalpayment` (`id`, `paypal_transaction_id`, `payer_email`, `transaction_date`, `payment_status`, `payment_id`, `created_at`) VALUES
(1, '7WA87504SA4259814', 'doe-john123@personal.example.com', '2026-01-10 17:21:45', 'COMPLETED', 'PAY-1768036894-9400', '2026-01-10 17:21:45'),
(2, '1VM969028L0473703', 'doe-john123@personal.example.com', '2026-01-10 17:23:35', 'COMPLETED', 'PAY-1768037003-1127', '2026-01-10 17:23:35'),
(3, '7B619680ET908053K', 'doe-john123@personal.example.com', '2026-01-10 17:25:52', 'COMPLETED', 'PAY-1768037140-6077', '2026-01-10 17:25:52'),
(4, '94A42960T1700180R', 'doe-john123@personal.example.com', '2026-01-10 17:26:32', 'COMPLETED', 'PAY-1768037182-8874', '2026-01-10 17:26:32'),
(5, '9SK334070U2092158', 'doe-john123@personal.example.com', '2026-01-10 17:29:29', 'COMPLETED', 'PAY-1768037346-6305', '2026-01-10 17:29:29'),
(6, '77H694249Y439754L', 'doe-john123@personal.example.com', '2026-01-10 17:40:01', 'COMPLETED', 'PAY-1768037989-2313', '2026-01-10 17:40:01'),
(7, '2GM862408E334683U', 'sb-47o3z347849561@personal.example.com', '2026-01-10 17:59:41', 'COMPLETED', 'PAY-1768039107-9386', '2026-01-10 17:59:41'),
(8, '6A798027H9648070Y', 'sb-47o3z347849561@personal.example.com', '2026-01-10 18:03:59', 'COMPLETED', 'PAY-1768039428-2102', '2026-01-10 18:03:59'),
(9, '03534134G1573101T', 'sb-47o3z347849561@personal.example.com', '2026-01-10 18:04:59', 'COMPLETED', 'PAY-1768039486-6382', '2026-01-10 18:04:59'),
(10, '8N399668TH4462343', 'sb-47o3z347849561@personal.example.com', '2026-01-10 18:06:57', 'COMPLETED', 'PAY-1768039606-1466', '2026-01-10 18:06:57'),
(11, '8F186912V75302807', 'sb-47o3z347849561@personal.example.com', '2026-01-10 18:09:46', 'COMPLETED', 'PAY-1768039774-1469', '2026-01-10 18:09:46'),
(12, '4JR68969WU844320E', 'sb-47o3z347849561@personal.example.com', '2026-01-10 19:49:55', 'COMPLETED', 'PAY-1768045686-7122', '2026-01-10 19:49:55'),
(13, '2BF22250WB5111242', 'sb-47o3z347849561@personal.example.com', '2026-01-10 19:51:01', 'COMPLETED', 'PAY-1768045821-7876', '2026-01-10 19:51:01'),
(14, '6W80238242495231A', 'sb-47o3z347849561@personal.example.com', '2026-01-10 19:54:26', 'COMPLETED', 'PAY-1768046056-7564', '2026-01-10 19:54:26'),
(15, '0DW70195GT062783R', 'sb-47o3z347849561@personal.example.com', '2026-01-10 20:00:04', 'COMPLETED', 'PAY-1768046392-7641', '2026-01-10 20:00:04'),
(16, '5XC908755C914010K', 'sb-47o3z347849561@personal.example.com', '2026-01-10 20:02:22', 'COMPLETED', 'PAY-1768046526-7622', '2026-01-10 20:02:22'),
(17, '16M28086EG099530W', 'sb-47o3z347849561@personal.example.com', '2026-01-10 20:11:07', 'COMPLETED', 'PAY-1768047056-5384', '2026-01-10 20:11:07'),
(18, '4KX78700LX360374V', 'sb-47o3z347849561@personal.example.com', '2026-01-10 21:02:11', 'COMPLETED', 'PAY-1768050066-5100', '2026-01-10 21:02:11'),
(19, '2WS26930TN638181W', 'sb-47o3z347849561@personal.example.com', '2026-01-10 21:14:23', 'COMPLETED', 'PAY-1768050853-3273', '2026-01-10 21:14:23'),
(20, '03J296906X004872U', 'sb-47o3z347849561@personal.example.com', '2026-01-10 21:15:15', 'COMPLETED', 'PAY-1768050903-4889', '2026-01-10 21:15:15'),
(21, '9E574312BN497235G', 'sb-47o3z347849561@personal.example.com', '2026-01-10 21:20:15', 'COMPLETED', 'PAY-1768051204-5309', '2026-01-10 21:20:15'),
(22, '0R2605166F3486805', 'sb-47o3z347849561@personal.example.com', '2026-01-11 00:36:16', 'COMPLETED', 'PAY-1768062730-7093', '2026-01-11 00:36:16'),
(23, '2T139502PC553500C', 'sb-47o3z347849561@personal.example.com', '2026-01-11 00:55:03', 'COMPLETED', 'PAY-1768064050-4721', '2026-01-11 00:55:03'),
(24, '4U495771P7228384U', 'sb-47o3z347849561@personal.example.com', '2026-01-11 00:59:59', 'COMPLETED', 'PAY-1768064388-2419', '2026-01-11 00:59:59'),
(25, '92953637FT7974248', 'doe-john123@personal.example.com', '2026-01-11 01:38:18', 'COMPLETED', 'PAY-1768066648-7618', '2026-01-11 01:38:18'),
(26, '0SS13286R12268604', 'doe-john123@personal.example.com', '2026-01-11 12:22:04', 'COMPLETED', 'PAY-1768105240-7251', '2026-01-11 12:22:04');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` varchar(50) NOT NULL,
  `fname` varchar(50) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `role` enum('user','admin') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `fname`, `lname`, `email`, `password`, `phone_number`, `status`, `created_at`, `updated_at`) VALUES
('USR001', 'test', '123', 'test123@gmail.com', '$2y$10$vBZhszJkAzFugvvhHjPDP.p8z/uPQZ//f0heKgu0nUh9bYG3nS5SK', '123123123123', 'active', '2026-01-10 00:27:45', '2026-01-10 00:27:45'),
('USR002', 'Dale', 'Lee', 'daleandrewslee@gmail.com', '$2y$10$x6op2KosYBu3vO6muMP3q.Pn14u3N5PhcOttyigLZGo1A.Oxop6EG', '09466292876', 'active', '2026-01-10 01:48:28', '2026-01-10 01:48:28'),
('USR003', 'John Jester', 'Luciriaga', 'johnjesterluciriaga4@gmail.com', '$2y$10$Sz9RObWDcclY9yLFIMcwJuCMY7LizOId6rlR68q3Cszc7Sg5UvdMC', '09278679012', 'active', '2026-01-10 03:20:44', '2026-01-10 03:20:44'),
('USR004', 'Michelle', 'Baho', 'michelle@gmail.com', '$2y$10$k6rZlwENcWXFeRlgxKBCquHvSUcV.EA1GEIbmvAbi6OcSoqge/SLS', '09332113388', 'active', '2026-01-10 20:05:24', '2026-01-10 20:05:24');

-- --------------------------------------------------------

--
-- Table structure for table `watch`
--

CREATE TABLE `watch` (
  `watch_id` varchar(50) NOT NULL,
  `brand` varchar(50) NOT NULL,
  `model` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `image_file` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `watch`
--

INSERT INTO `watch` (`watch_id`, `brand`, `model`, `description`, `price`, `stock_quantity`, `image_file`, `created_at`, `updated_at`) VALUES
('W001', 'Rolex', 'Yacht-Master 40 Everose Black Dial 116655 2017', 'The Yacht-Master 40 in Everose gold epitomizes the perfect union of sport and luxury. Its bold black dial contrasts beautifully with the warm glow of Everose, while its Oysterflex bracelet ensures comfort and durability. Designed for maritime elegance and refined adventures, it is a statement of both performance and prestige.', 10000.00, 1, 'assets/uploads/prod_69615749cdc1a7.14454203_Rolex5_-_Rolex_Yacht-Master_40_Everose_Black_Dial_116655_2017.png', '2026-01-10 03:30:17', '2026-01-11 12:22:04'),
('W002', 'Cartier', 'Americane Rose Gold W2607456', 'A graceful expression of Cartier\'s signature elegance, this Tank Américaine in rose gold features a sleek rectangular case and classic Roman numerals. Its refined profile and polished finish make it a quintessential dress watch for those who appreciate subtle sophistication.', 9500.00, 0, 'assets/uploads/prod_6961577facfa98.81746719_Cartier1_-____Cartier_Americane_Rose_Gold_W2607456_Cartier_Americane_Rose_Gold_W2607456.png', '2026-01-10 03:31:11', '2026-01-10 21:02:11'),
('W003', 'Rolex', 'test', 'asdadw', 123123.00, 0, 'assets/uploads/prod_69624e2e8d6668.22813492_Rolex1_-_Rolex_Explorer_I_36_14270_1996.png', '2026-01-10 21:03:42', '2026-01-11 00:59:59');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `fk_cart_user` (`user_id`);

--
-- Indexes for table `cartitems`
--
ALTER TABLE `cartitems`
  ADD PRIMARY KEY (`cart_item_id`),
  ADD KEY `fk_cartitems_watch` (`watch_id`),
  ADD KEY `fk_cartitems_cart` (`cart_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`) USING BTREE,
  ADD KEY `fk_orders_user` (`user_id`),
  ADD KEY `idx_orders_order_date` (`order_date`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `watch_id` (`watch_id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payment_id` (`payment_id`),
  ADD KEY `fk_payment_order` (`order_id`);

--
-- Indexes for table `paypalpayment`
--
ALTER TABLE `paypalpayment`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `paypal_transaction_id` (`paypal_transaction_id`),
  ADD KEY `payment_id` (`payment_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `watch`
--
ALTER TABLE `watch`
  ADD PRIMARY KEY (`watch_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `paypalpayment`
--
ALTER TABLE `paypalpayment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `cartitems`
--
ALTER TABLE `cartitems`
  ADD CONSTRAINT `fk_cartitems_cart` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`cart_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cartitems_watch` FOREIGN KEY (`watch_id`) REFERENCES `watch` (`watch_id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `fk_orders_watch` FOREIGN KEY (`watch_id`) REFERENCES `watch` (`watch_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_order_items_watch` FOREIGN KEY (`watch_id`) REFERENCES `watch` (`watch_id`);

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `fk_payment_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- Constraints for table `paypalpayment`
--
ALTER TABLE `paypalpayment`
  ADD CONSTRAINT `fk_paypalpayment_payment` FOREIGN KEY (`payment_id`) REFERENCES `payment` (`payment_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
