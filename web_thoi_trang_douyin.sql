-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 18, 2026 at 12:45 PM
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
-- Database: `web_thoi_trang_douyin`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `description`) VALUES
(1, 'Áo quần', 'Danh mục thời trang áo quần');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `contact_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `contact_info` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reply` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`contact_id`, `name`, `contact_info`, `message`, `created_at`, `reply`) VALUES
(1, 'E', '0564733689', 'SHOP OI', '2026-10-09 05:36:20', 'Dạ Flower Fashion Store xin lắng nghe ạ!\r\n'),
(2, 'THU', '0785433455', 'ALO CHO E HỎI SẢN PHẨM NÀY CÒN KHÔNG ạ\r\n\r\n', '2026-10-09 05:40:33', 'Dạ còn ạ, cho shop xin chiều cao và cân nặng để shop tư vấn cho bạn ạ ^^\r\n\r\n');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `customer_name`, `phone`, `address`, `total_price`, `created_at`) VALUES
(1, 'HIEN', '0987654211', 'HA NOI', 118000.00, '2026-10-08 10:38:25'),
(2, 'E', '0785644567', 'HÀ NỘI', 782000.00, '2026-10-09 05:22:16'),
(3, 'E', '0785644567', 'THÁI NGUYÊN', 250000.00, '2026-10-09 05:23:47');

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `detail_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`detail_id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 8, 1, 118000.00),
(2, 2, 6, 2, 159000.00),
(3, 2, 7, 2, 232000.00),
(4, 3, 5, 1, 250000.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `size` varchar(50) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `stock` int(11) DEFAULT 10
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `category_id`, `product_name`, `price`, `image`, `description`, `size`, `color`, `stock`) VALUES
(3, 1, 'Áo Sweater Chất Nỉ Dày Dặn', 120000.00, 'ao_kitty.jpg', 'Áo hello kitty\r\n', 'Freesize', 'Trắng', 10),
(5, 1, ' Áo Len Nữ', 250000.00, 'ao_len.jpg', ' Áo Len Nữ Retro Thu Đông, Dệt Kim Off-The-Shoulder Dài Tay In Hình Đen', 'S, M, L, XL', 'Đen', 10),
(6, 1, 'Áo Sweater Thu Đông Phong Cách Dễ Thương', 159000.00, 'ao_ni.jpg', 'Áo Nỉ Thể Thao Nam Nữ Cổ Tròn Áo Đôi In Hình Con Mèo Tia Chớp Siêu Trẻ Trung Chất Liệu Nỉ Ngoại Siêu Dày Dặn', 'S, M, L, XL', 'Trắng', 10),
(7, 1, 'Áo Nỉ Trễ Vai', 232000.00, '1791518801_ao_trevai.jpg', 'Áo sweater Trễ Vai – Tay Dài, Form Nữ Tính, Màu xanh dương dễ mix đồ', 'S, M, L, XL', 'Xanh Dương', 10),
(8, 1, 'Áo Thun Lệch Vai Ôm Body', 118000.00, 'aothun.jpg', 'Áo Thun TAY DÀI LỆCH VAI PHỐI YẾM IN 02 Phong Cách DD166, Áo Thun Nữ Hở Vai Thun Borip Kiểu In Số Flo\'ra', 'S, M, L, XL', 'Trắng Đỏ', 10),
(9, 1, 'Quần Jeans Ống Rộng', 279000.00, 'quan_jean.jpg', 'Quần Jeans Ống Rộng Suông Basic MIAA Ống  30-33cm Dễ Mix Đồ Năng Động,Quần Jean Ver.3 Dài chất liệu Denim form nam nữ', 'S, M, L, XL', 'Xanh Nhạt Retro', 10),
(10, 1, 'Áo Khoác Jeans', 378000.00, 'aokhoac_jeancroptop.jpg', 'Áo Khoác Jeans Nữ Túi Hộp Viền Chỉ Nồi Màu Xanh Than; Form Rộng Phong Cách Cá Tính', 'S, M, L, XL, Freesize', 'Xanh đậm', 10),
(11, 1, 'Áo Phao Trần Bông 2 Lớp Dày Dặn, Ấm Áp', 459000.00, '1791520279_AoPhao_2lop.jpg', 'Áo Khoác Phao Ấm Áp, Thiết Kế Xinh, Màu Dễ Phối, Ấm Áp Mùa Đông', 'S, M, L, XL, Freesize', 'Xanh Dương Pastel', 10),
(12, 1, 'Quần Jeans Ống Loe Màu Retro ', 258000.00, 'Quan_OngLoe.jpg', 'Quần Jeans co giãn, dễ  phối đồ, tôn dáng', 'S, M, L', 'Xanh Nhạt', 10),
(13, 1, 'Quần Jeans Ống Rộng Họa Tiết Cún Xinh Kết Hợp Nơ Kẻ Caro', 189000.00, 'Quan_OngRong.jpg', 'Quần Jeans Ống Rộng, Phong Cách Y2K, Họa Tiết Xinh, Dễ Phối Đồ, Hack Dáng Cực Đỉnh', 'S, M, L, XL', 'Xanh Đậm', 10),
(14, 1, 'Váy Jeans Ôm Body Xếp Ly Phong Cách Y2K', 230000.00, 'Vay_bo.jpg', 'Váy Quây Ôm Body Cực Chất, Phong Cách Y2k, Tôn Dáng Xinh Xỉu', 'S, M, L', 'Xanh ReTro', 10);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) DEFAULT 'admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `role`) VALUES
(1, 'admin', '123456', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`contact_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`detail_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`);

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
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `contact_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
