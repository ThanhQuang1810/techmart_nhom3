-- -- phpMyAdmin SQL Dump
-- -- version 4.9.0.1
-- -- https://www.phpmyadmin.net/
-- --
-- -- Host: sql100.byetcluster.com
-- -- Generation Time: Apr 28, 2026 at 10:27 AM
-- -- Server version: 11.4.10-MariaDB
-- -- PHP Version: 7.2.22

-- SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
-- SET AUTOCOMMIT = 0;
-- START TRANSACTION;
-- SET time_zone = "+00:00";


-- /*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
-- /*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
-- /*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
-- /*!40101 SET NAMES utf8mb4 */;

-- --
-- -- Database: `if0_41729792_techmart`
-- --

-- -- --------------------------------------------------------

-- --
-- -- Table structure for table `cart`
-- --

-- CREATE TABLE `cart` (
--   `id` int(11) NOT NULL,
--   `user_id` int(11) NOT NULL,
--   `product_id` int(11) NOT NULL,
--   `selected_color` varchar(80) NOT NULL DEFAULT '',
--   `selected_image` varchar(255) DEFAULT NULL,
--   `quantity` int(11) NOT NULL DEFAULT 1,
--   `created_at` timestamp NOT NULL DEFAULT current_timestamp()
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --
-- -- Dumping data for table `cart`
-- --

-- INSERT INTO `cart` (`id`, `user_id`, `product_id`, `selected_color`, `selected_image`, `quantity`, `created_at`) VALUES
-- (28, 11, 23, 'Mặc định', 'assets/images/Máy tính bảng Xiaomi Pad 7 Pro - Xám.webp', 1, '2026-04-28 12:30:32'),
-- (29, 11, 21, 'Mặc định', 'assets/images/Máy tính bảng Samsung Galaxy Tab S10 FE 5G(Silver).webp', 1, '2026-04-28 12:30:39');

-- -- --------------------------------------------------------

-- --
-- -- Table structure for table `categories`
-- --

-- CREATE TABLE `categories` (
--   `id` int(11) NOT NULL,
--   `name` varchar(100) NOT NULL,
--   `description` text DEFAULT NULL,
--   `created_at` timestamp NOT NULL DEFAULT current_timestamp()
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --
-- -- Dumping data for table `categories`
-- --

-- INSERT INTO `categories` (`id`, `name`, `description`, `created_at`) VALUES
-- (1, 'Điện thoại', 'Các dòng điện thoại thông minh chính hãng', '2026-04-23 05:53:31'),
-- (2, 'Laptop', 'Laptop học tập, văn phòng và sáng tạo nội dung', '2026-04-23 05:53:31'),
-- (3, 'Tablet', 'Máy tính bảng phục vụ giải trí và học tập', '2026-04-23 05:53:31'),
-- (4, 'Phụ kiện', 'Tai nghe, chuột và phụ kiện công nghệ', '2026-04-23 05:53:31');

-- -- --------------------------------------------------------

-- --
-- -- Table structure for table `contacts`
-- --

-- CREATE TABLE `contacts` (
--   `id` int(11) NOT NULL,
--   `name` varchar(100) NOT NULL,
--   `email` varchar(100) NOT NULL,
--   `phone` varchar(20) DEFAULT NULL,
--   `subject` varchar(150) DEFAULT NULL,
--   `message` text NOT NULL,
--   `is_read` tinyint(1) NOT NULL DEFAULT 0,
--   `created_at` timestamp NOT NULL DEFAULT current_timestamp()
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --
-- -- Dumping data for table `contacts`
-- --

-- INSERT INTO `contacts` (`id`, `name`, `email`, `phone`, `subject`, `message`, `is_read`, `created_at`) VALUES
-- (4, 'Jang Won Joung', 'nguyenloan781.2023@gmail.com', '0963452660', 'giao hàng chậm', 'giao nhan hcho tôi', 0, '2026-04-28 14:27:49');

-- -- --------------------------------------------------------

-- --
-- -- Table structure for table `orders`
-- --

-- CREATE TABLE `orders` (
--   `id` int(11) NOT NULL,
--   `user_id` int(11) NOT NULL,
--   `full_name` varchar(120) NOT NULL,
--   `email` varchar(100) NOT NULL,
--   `phone` varchar(20) NOT NULL,
--   `address` varchar(255) NOT NULL,
--   `note` text DEFAULT NULL,
--   `total` decimal(12,2) NOT NULL DEFAULT 0.00,
--   `status` enum('Chờ xác nhận','Đang xử lý','Đang giao','Đã giao','Huỷ') NOT NULL DEFAULT 'Chờ xác nhận',
--   `created_at` timestamp NOT NULL DEFAULT current_timestamp()
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --
-- -- Dumping data for table `orders`
-- --

-- INSERT INTO `orders` (`id`, `user_id`, `full_name`, `email`, `phone`, `address`, `note`, `total`, `status`, `created_at`) VALUES
-- (10, 13, 'Loan Nguyen', 'nguyenloan781.2023@gmail.com', '0963452660', 'Phan Thiết, Bình Thuận', '', '11990000.00', 'Đang giao', '2026-04-24 13:39:58'),
-- (11, 14, 'Thie was taken', 'thie11@gmail.con', '0845451845', 'Aa', 'Aa', '11990000.00', 'Đã giao', '2026-04-24 17:49:43'),
-- (12, 13, 'Loan Nguyen', 'nguyenloan781.2023@gmail.com', '0963452660', 'binh thanh', '', '39990000.00', 'Chờ xác nhận', '2026-04-28 11:20:57'),
-- (13, 13, 'Loan Nguyen', 'nguyenloan781.2023@gmail.com', '0963452660', 'BINH THUAN', '', '11990000.00', 'Chờ xác nhận', '2026-04-28 13:39:16'),
-- (14, 13, 'Loan Nguyen', 'nguyenloan781.2023@gmail.com', '0963452660', 'BINH THUAN', '', '12990000.00', 'Chờ xác nhận', '2026-04-28 13:40:27'),
-- (15, 13, 'Loan Nguyen', 'nguyenloan781.2023@gmail.com', '0963452660', 'BINH THUAN', '', '12990000.00', 'Chờ xác nhận', '2026-04-28 14:26:02'),
-- (16, 13, 'Loan Nguyen', 'nguyenloan781.2023@gmail.com', '0963452660', 'BINH THUAN', '', '93950000.00', 'Chờ xác nhận', '2026-04-28 14:26:54');

-- -- --------------------------------------------------------

-- --
-- -- Table structure for table `order_items`
-- --

-- CREATE TABLE `order_items` (
--   `id` int(11) NOT NULL,
--   `order_id` int(11) NOT NULL,
--   `product_id` int(11) NOT NULL,
--   `product_name` varchar(150) NOT NULL,
--   `selected_color` varchar(80) NOT NULL DEFAULT '',
--   `selected_image` varchar(255) DEFAULT NULL,
--   `quantity` int(11) NOT NULL DEFAULT 1,
--   `price` decimal(12,2) NOT NULL DEFAULT 0.00
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --
-- -- Dumping data for table `order_items`
-- --

-- INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `selected_color`, `selected_image`, `quantity`, `price`) VALUES
-- (23, 10, 23, 'Xiaomi Pad 7 Pro - Xám', 'Mặc định', 'assets/images/Máy tính bảng Xiaomi Pad 7 Pro - Xám.webp', 1, '11990000.00'),
-- (24, 11, 23, 'Xiaomi Pad 7 Pro - Xám', 'Mặc định', 'assets/images/Máy tính bảng Xiaomi Pad 7 Pro - Xám.webp', 1, '11990000.00'),
-- (25, 12, 16, 'iPhone 17 Pro Max 1TB', 'Mặc định', 'assets/images/iPhone 17 Pro Max 1TB.webp', 1, '39990000.00'),
-- (26, 13, 23, 'Xiaomi Pad 7 Pro - Xám', 'Mặc định', 'assets/images/Máy tính bảng Xiaomi Pad 7 Pro - Xám.webp', 1, '11990000.00'),
-- (27, 14, 21, 'Samsung Galaxy Tab S10 FE 5G', 'Mặc định', 'assets/images/Máy tính bảng Samsung Galaxy Tab S10 FE 5G(Silver).webp', 1, '12990000.00'),
-- (28, 15, 21, 'Samsung Galaxy Tab S10 FE 5G', 'Mặc định', 'assets/images/Máy tính bảng Samsung Galaxy Tab S10 FE 5G(Silver).webp', 1, '12990000.00'),
-- (29, 16, 20, 'Máy tính bảng Lenovo (Xám)', 'Mặc định', 'assets/images/Máy tính bảng Lenov (Xám).webp', 1, '4990000.00'),
-- (30, 16, 23, 'Xiaomi Pad 7 Pro - Xám', 'Mặc định', 'assets/images/Máy tính bảng Xiaomi Pad 7 Pro - Xám.webp', 1, '11990000.00'),
-- (31, 16, 3, 'Xiaomi 14 12GB/256GB', 'Mặc định', 'assets/images/xiaomi14-xamtitan.webp', 1, '16990000.00'),
-- (32, 16, 2, 'Samsung Galaxy S24 256GB', 'Mặc định', 'assets/images/galaxy-s24-5g-den.jpg', 1, '19990000.00'),
-- (33, 16, 16, 'iPhone 17 Pro Max 1TB', 'Mặc định', 'assets/images/iPhone 17 Pro Max 1TB.webp', 1, '39990000.00');

-- -- --------------------------------------------------------

-- --
-- -- Table structure for table `products`
-- --

-- CREATE TABLE `products` (
--   `id` int(11) NOT NULL,
--   `category_id` int(11) NOT NULL,
--   `name` varchar(150) NOT NULL,
--   `brand` varchar(80) DEFAULT NULL,
--   `description` text DEFAULT NULL,
--   `price` decimal(12,2) NOT NULL DEFAULT 0.00,
--   `old_price` decimal(12,2) NOT NULL DEFAULT 0.00,
--   `stock` int(11) NOT NULL DEFAULT 0,
--   `image` varchar(255) NOT NULL,
--   `screen` varchar(120) DEFAULT NULL,
--   `chip` varchar(120) DEFAULT NULL,
--   `ram` varchar(80) DEFAULT NULL,
--   `storage` varchar(120) DEFAULT NULL,
--   `camera` varchar(120) DEFAULT NULL,
--   `battery` varchar(120) DEFAULT NULL,
--   `rating` decimal(2,1) NOT NULL DEFAULT 5.0,
--   `sold` int(11) NOT NULL DEFAULT 0,
--   `featured` tinyint(1) NOT NULL DEFAULT 0,
--   `status` tinyint(1) NOT NULL DEFAULT 1,
--   `created_at` timestamp NOT NULL DEFAULT current_timestamp()
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --
-- -- Dumping data for table `products`
-- --

-- INSERT INTO `products` (`id`, `category_id`, `name`, `brand`, `description`, `price`, `old_price`, `stock`, `image`, `screen`, `chip`, `ram`, `storage`, `camera`, `battery`, `rating`, `sold`, `featured`, `status`, `created_at`) VALUES
-- (1, 1, 'iPhone 15 128GB', 'Apple', 'Mẫu điện thoại cao cấp với hiệu năng mạnh, camera sắc nét và thiết kế sang trọng.', '21990000.00', '23990000.00', 20, 'assets/images/iphone 15.jpg', '6.1 inch Super Retina XDR', 'Apple A16 Bionic', '6GB', '128GB', '48MP + 12MP', '3349mAh', '4.9', 323, 1, 1, '2026-04-23 05:53:31'),
-- (2, 1, 'Samsung Galaxy S24 256GB', 'Samsung', 'Flagship Android hiện đại, màn hình đẹp, camera chất lượng và nhiều tính năng AI.', '19990000.00', '22990000.00', 21, 'assets/images/galaxy-s24-5g-den.jpg', '6.2 inch Dynamic AMOLED 2X', 'Exynos 2400', '8GB', '256GB', '50MP + 12MP + 10MP', '4000mAh', '4.8', 215, 1, 1, '2026-04-23 05:53:31'),
-- (3, 1, 'Xiaomi 14 12GB/256GB', 'Xiaomi', 'Hiệu năng mạnh mẽ, tối ưu cho game và chụp ảnh với hệ camera hợp tác Leica.', '16990000.00', '18990000.00', 14, 'assets/images/xiaomi14-xamtitan.webp', '6.36 inch AMOLED 120Hz', 'Snapdragon 8 Gen 3', '12GB', '256GB', '50MP Leica', '4610mAh', '4.7', 160, 1, 1, '2026-04-23 05:53:31'),
-- (4, 2, 'MacBook Air M2 13.6 inch', 'Apple', 'Laptop mỏng nhẹ dành cho học tập và công việc văn phòng, pin bền bỉ.', '26990000.00', '28990000.00', 31, 'assets/images/macbook_air.webp', '13.6 inch Liquid Retina', 'Apple M2', '8GB', '256GB SSD', '1080p FaceTime HD', 'Lên đến 18 giờ', '4.9', 98, 1, 1, '2026-04-23 05:53:31'),
-- (5, 3, 'iPad Air 5 WiFi 64GB', 'Apple', 'Máy tính bảng đa dụng, đáp ứng giải trí, học tập và làm việc linh hoạt.', '14990000.00', '16990000.00', 35, 'assets/images/ipad-xam.webp', '10.9 inch Liquid Retina', 'Apple M1', '8GB', '64GB', '12MP', '28.6 Wh', '4.8', 188, 0, 1, '2026-04-23 05:53:31'),
-- (6, 4, 'Sony WH-1000XM5', 'Sony', 'Tai nghe chống ồn cao cấp với chất âm chi tiết, phù hợp di chuyển và làm việc.', '7490000.00', '8490000.00', 38, 'assets/images/sony-den.webp', 'Không áp dụng', 'QN1', 'Không áp dụng', 'Không áp dụng', 'Không áp dụng', '30 giờ', '4.9', 132, 0, 1, '2026-04-23 05:53:31'),
-- (7, 4, 'AirPods Pro 2 USB-C', 'Apple', 'Tai nghe không dây chống ồn chủ động, tương thích hệ sinh thái Apple.', '5990000.00', '6490000.00', 41, 'assets/images/airpods-trang.webp', 'Không áp dụng', 'H2', 'Không áp dụng', 'Không áp dụng', 'Không áp dụng', '6 giờ / 30 giờ với hộp sạc', '4.8', 260, 0, 1, '2026-04-23 05:53:31'),
-- (8, 4, 'Logitech MX Master 3S', 'Logitech', 'Chuột văn phòng cao cấp, cuộn mượt, hỗ trợ đa thiết bị.', '2290000.00', '2590000.00', 44, 'assets/images/chuot-den.jpg', 'Không áp dụng', 'Không áp dụng', 'Không áp dụng', 'Không áp dụng', 'Không áp dụng', 'Sạc USB-C, dùng đến 70 ngày', '4.7', 121, 0, 1, '2026-04-23 05:53:31'),
-- (9, 4, 'Apple Watch Series 9 GPS 4G 41mm Vỏ Thép Dây thép', 'Apple', 'Đồng hồ thông minh cao cấp, hỗ trợ 4G, theo dõi sức khỏe và thể thao.', '15990000.00', '17990000.00', 20, 'assets/images/Apple Watch Series 9 GPS 4G 41mm Vỏ Thép Dây thép.webp', '1.9 inch', 'Apple S9', '1GB', '32GB', 'Không', '18h', '4.8', 10, 1, 1, '2026-04-23 09:54:00'),
-- (10, 4, 'Balancer 36.32mm Kim loại Leather', 'Balancer', 'Đồng hồ thời trang kim loại dây da sang trọng.', '2990000.00', '3490000.00', 30, 'assets/images/Balancer 36.32mm Kim loại Leather.webp', '1.4 inch', 'Không', 'Không', 'Không', 'Không', '48h', '4.5', 5, 0, 1, '2026-04-23 09:54:00'),
-- (11, 4, 'Bút cảm ứng Baseus Smooth Writing 2 Lite - Trắng', 'Baseus', 'Bút cảm ứng hỗ trợ viết mượt, sạc USB-C tiện lợi.', '590000.00', '690000.00', 50, 'assets/images/Bút cảm ứng Baseus Smooth Writing 2 Lite with LED Indicator (Sạc USB-C) - Trắng.webp', 'Không', 'Không', 'Không', 'Không', 'Không', '10h', '4.6', 20, 0, 1, '2026-04-23 09:54:00'),
-- (12, 1, 'Honor Magic V5 (16GB/512GB) - Vàng', 'Honor', 'Điện thoại gập cao cấp, hiệu năng mạnh mẽ, thiết kế sang trọng.', '32990000.00', '34990000.00', 10, 'assets/images/Điện thoại di động Honor Magic V5 (16+512GB) - Vàng.webp', '7.9 inch', 'Snapdragon 8 Gen 3', '16GB', '512GB', '50MP', '5000mAh', '4.9', 8, 1, 1, '2026-04-23 09:54:00'),
-- (13, 1, 'Honor X8d (8GB/128GB)', 'Honor', 'Smartphone tầm trung, thiết kế mỏng nhẹ, pin tốt.', '6990000.00', '7990000.00', 25, 'assets/images/Điện thoại di động Honor X8d (8+128GB).webp', '6.7 inch', 'Snapdragon 680', '8GB', '128GB', '50MP', '4500mAh', '4.5', 15, 0, 1, '2026-04-23 09:54:00'),
-- (14, 1, 'Xiaomi Redmi 12C 128GB (Xám đen)', 'Xiaomi', 'Điện thoại giá rẻ, pin lớn, phù hợp nhu cầu cơ bản.', '3490000.00', '3990000.00', 40, 'assets/images/Điện thoại Xiaomi Redmi 12C 128GB (Xám đen).webp', '6.71 inch', 'Helio G85', '4GB', '128GB', '50MP', '5000mAh', '4.3', 25, 0, 1, '2026-04-23 09:54:00'),
-- (15, 4, 'Kospet Tank X2 Ultra GPS 34mm Bluetooth', 'Kospet', 'Đồng hồ thông minh chống nước, GPS chính xác.', '4990000.00', '5490000.00', 15, 'assets/images/Đồng hồ Kospet Tank X2 Ultra GPS 34mm Bluetooth (Vỏ Thép Dây Silicon).webp', '1.8 inch', 'Không', 'Không', 'Không', 'Không', '72h', '4.6', 12, 0, 1, '2026-04-23 09:54:00'),
-- (16, 1, 'iPhone 17 Pro Max 1TB', 'Apple', 'Flagship mới nhất của Apple, camera cực mạnh.', '39990000.00', '42990000.00', 6, 'assets/images/iPhone 17 Pro Max 1TB.webp', '6.7 inch', 'Apple A18', '8GB', '1TB', '48MP', '4500mAh', '5.0', 9, 1, 1, '2026-04-23 09:54:00'),
-- (17, 4, 'Kính cường lực ZAGG iPhone 17 Pro Max', 'ZAGG', 'Kính cường lực chống trầy xước cao cấp.', '390000.00', '490000.00', 60, 'assets/images/Kính cường lực ZAGG InvisibleShield XTR5 iPhone 17 Pro Max.webp', 'Không', 'Không', 'Không', 'Không', 'Không', 'Không', '4.5', 30, 0, 1, '2026-04-23 09:54:00'),
-- (18, 4, 'Loa Bluetooth mini', 'Generic', 'Loa nhỏ gọn, âm thanh rõ ràng, kết nối nhanh.', '590000.00', '790000.00', 35, 'assets/images/loa.webp', 'Không', 'Không', 'Không', 'Không', 'Không', '8h', '4.4', 18, 0, 1, '2026-04-23 09:54:00'),
-- (20, 3, 'Máy tính bảng Lenovo (Xám)', 'Lenovo', 'Tablet giá tốt cho nhu cầu cơ bản.', '4990000.00', '5590000.00', 19, 'assets/images/Máy tính bảng Lenov (Xám).webp', '10 inch', 'MediaTek', '4GB', '64GB', '8MP', '6000mAh', '4.4', 13, 0, 1, '2026-04-23 09:54:00'),
-- (21, 3, 'Samsung Galaxy Tab S10 FE 5G', 'Samsung', 'Tablet mạnh mẽ hỗ trợ 5G.', '12990000.00', '13990000.00', 13, 'assets/images/Máy tính bảng Samsung Galaxy Tab S10 FE 5G(Silver).webp', '10.9 inch', 'Exynos', '8GB', '128GB', '12MP', '8000mAh', '4.7', 11, 1, 1, '2026-04-23 09:54:00'),
-- (22, 3, 'Samsung Galaxy Tab S10 FE Wifi', 'Samsung', 'Phiên bản Wifi hiệu năng tốt.', '10990000.00', '11990000.00', 18, 'assets/images/Máy tính bảng Samsung Galaxy Tab S10 FE Wifi.webp', '10.9 inch', 'Exynos', '6GB', '128GB', '12MP', '8000mAh', '4.6', 11, 0, 1, '2026-04-23 09:54:00'),
-- (23, 3, 'Xiaomi Pad 7 Pro - Xám', 'Xiaomi', 'Tablet cấu hình mạnh, màn hình đẹp.', '11990000.00', '12990000.00', 8, 'assets/images/Máy tính bảng Xiaomi Pad 7 Pro - Xám.webp', '11 inch', 'Snapdragon', '8GB', '256GB', '13MP', '8600mAh', '4.7', 12, 1, 1, '2026-04-23 09:54:00'),
-- (24, 3, 'Xiaomi Redmi Pad SE Wifi - Tím', 'Xiaomi', 'Tablet giá rẻ, pin trâu.', '5990000.00', '6990000.00', 25, 'assets/images/Máy tính bảng Xiaomi Redmi Pad SE Wifi - Tím.webp', '10 inch', 'Snapdragon', '4GB', '128GB', '8MP', '8000mAh', '4.5', 14, 0, 1, '2026-04-23 09:54:00'),
-- (25, 4, 'Tai nghe Xiaomi Redmi Buds 6 Play', 'Xiaomi', 'Tai nghe bluetooth nhỏ gọn, pin lâu.', '690000.00', '890000.00', 50, 'assets/images/Tai nghe Bluetooth TWS Xiaomi Redmi Buds 6 Play Xanh (BHR9283GL ).webp', 'Không', 'Không', 'Không', 'Không', 'Không', '20h', '4.6', 22, 0, 1, '2026-04-23 09:54:00'),
-- (36, 3, 'Honor Pad X7 Wifi Xám', 'Honor', 'Máy tính bảng nhỏ gọn phục vụ học tập.', '3990000.00', '4590000.00', 20, 'assets/images/Máy tính bảng Honor Pad X7 Wifi Xám.webp', '8 inch', 'Snapdragon', '4GB', '64GB', '8MP', '5100mAh', '4.5', 10, 0, 1, '2026-04-23 09:54:09');

-- -- --------------------------------------------------------

-- --
-- -- Table structure for table `product_images`
-- --

-- CREATE TABLE `product_images` (
--   `id` int(11) NOT NULL,
--   `product_id` int(11) NOT NULL,
--   `color` varchar(80) DEFAULT NULL,
--   `image_url` varchar(255) NOT NULL,
--   `is_main` tinyint(1) NOT NULL DEFAULT 0,
--   `created_at` timestamp NOT NULL DEFAULT current_timestamp()
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --
-- -- Dumping data for table `product_images`
-- --

-- INSERT INTO `product_images` (`id`, `product_id`, `color`, `image_url`, `is_main`, `created_at`) VALUES
-- (1, 1, 'Đen', 'assets/images/iphone 15.jpg', 1, '2026-04-23 05:53:31'),
-- (2, 1, 'Vàng', 'assets/images/iphone-15-mauvang.jpg', 0, '2026-04-23 05:53:31'),
-- (3, 1, 'Hồng', 'assets/images/iphone-15-mauhong.jpg', 0, '2026-04-23 05:53:31'),
-- (4, 1, 'Xanh Lá', 'assets/images/iphone-15-mauxanhla.jpg', 0, '2026-04-23 05:53:31'),
-- (5, 1, 'Xanh Dương', 'assets/images/iphone-15-mauxanhduong.jpg', 0, '2026-04-23 05:53:31'),
-- (6, 2, 'Đen', 'assets/images/galaxy-s24-5g-den.jpg', 1, '2026-04-23 05:53:31'),
-- (7, 2, 'Tím', 'assets/images/galaxy-s24-5g-tim.jpg', 0, '2026-04-23 05:53:31'),
-- (8, 2, 'Xám Marble', 'assets/images/galaxy-s24-5g-xam.jpg', 0, '2026-04-23 05:53:31'),
-- (9, 3, 'Xám Titan', 'assets/images/xiaomi14-xamtitan.webp', 1, '2026-04-23 05:53:31'),
-- (10, 3, 'Đen', 'assets/images/xiaomi14-den.webp', 0, '2026-04-23 05:53:31'),
-- (11, 3, 'Xanh Băng', 'assets/images/xiaomi14-xanhbang.webp', 0, '2026-04-23 05:53:31'),
-- (12, 4, 'Vàng', 'assets/images/macbook_air.webp', 1, '2026-04-23 05:53:31'),
-- (13, 5, 'Xám', 'assets/images/ipad-xam.webp', 1, '2026-04-23 05:53:31'),
-- (14, 5, 'Xanh Dương', 'assets/images/ipad-xanhduong.webp', 0, '2026-04-23 05:53:31'),
-- (15, 5, 'Trắng', 'assets/images/ipad-trang.webp', 0, '2026-04-23 05:53:31'),
-- (16, 5, 'Tím', 'assets/images/ipad-tim.webp', 0, '2026-04-23 05:53:31'),
-- (17, 6, 'Đen', 'assets/images/sony-den.webp', 1, '2026-04-23 05:53:31'),
-- (18, 6, 'Hồng Khói', 'assets/images/sony-hongkhoi.webp', 0, '2026-04-23 05:53:31'),
-- (19, 7, 'Trắng', 'assets/images/airpods-trang.webp', 1, '2026-04-23 05:53:31'),
-- (20, 8, 'Đen', 'assets/images/chuot-den.jpg', 1, '2026-04-23 05:53:31'),
-- (21, 8, 'Trắng', 'assets/images/chuot-trang.jpg', 0, '2026-04-23 05:53:31'),
-- (22, 9, 'Mặc định', 'assets/images/Apple Watch Series 9 GPS 4G 41mm Vỏ Thép Dây thép.webp', 1, '2026-04-23 09:55:04'),
-- (23, 10, 'Mặc định', 'assets/images/Balancer 36.32mm Kim loại Leather.webp', 1, '2026-04-23 09:55:53'),
-- (24, 11, 'Mặc định', 'assets/images/Bút cảm ứng Baseus Smooth Writing 2 Lite with LED Indicator (Sạc USB-C) - Trắng.webp', 1, '2026-04-23 09:56:28'),
-- (25, 12, 'Mặc định', 'assets/images/Điện thoại di động Honor Magic V5 (16+512GB) - Vàng.webp', 1, '2026-04-23 09:56:48'),
-- (26, 13, 'Mặc định', 'assets/images/Điện thoại di động Honor X8d (8+128GB).webp', 1, '2026-04-23 09:57:17'),
-- (27, 14, 'Mặc định', 'assets/images/Điện thoại Xiaomi Redmi 12C 128GB (Xám đen).webp', 1, '2026-04-23 09:57:57'),
-- (28, 15, 'Mặc định', 'assets/images/Đồng hồ Kospet Tank X2 Ultra GPS 34mm Bluetooth (Vỏ Thép Dây Silicon).webp', 1, '2026-04-23 09:58:23'),
-- (29, 16, 'Mặc định', 'assets/images/iPhone 17 Pro Max 1TB.webp', 1, '2026-04-23 09:58:48'),
-- (30, 17, 'Mặc định', 'assets/images/Kính cường lực ZAGG InvisibleShield XTR5 iPhone 17 Pro Max.webp', 1, '2026-04-23 09:59:29'),
-- (31, 36, 'Mặc định', 'assets/images/Máy tính bảng Honor Pad X7 Wifi Xám.webp', 1, '2026-04-23 10:00:27'),
-- (32, 20, 'Mặc định', 'assets/images/Máy tính bảng Lenov (Xám).webp', 1, '2026-04-23 10:00:52'),
-- (33, 22, 'Mặc định', 'assets/images/Máy tính bảng Samsung Galaxy Tab S10 FE Wifi.webp', 1, '2026-04-23 10:02:13'),
-- (34, 21, 'Mặc định', 'assets/images/Máy tính bảng Samsung Galaxy Tab S10 FE 5G(Silver).webp', 1, '2026-04-23 10:03:16'),
-- (35, 23, 'Mặc định', 'assets/images/Máy tính bảng Xiaomi Pad 7 Pro - Xám.webp', 1, '2026-04-23 10:03:33'),
-- (36, 24, 'Mặc định', 'assets/images/Máy tính bảng Xiaomi Redmi Pad SE Wifi - Tím.webp', 1, '2026-04-23 10:03:55'),
-- (37, 25, 'Mặc định', 'assets/images/Tai nghe Bluetooth TWS Xiaomi Redmi Buds 6 Play Xanh (BHR9283GL ).webp', 1, '2026-04-23 10:04:14');

-- -- --------------------------------------------------------

-- --
-- -- Table structure for table `users`
-- --

-- CREATE TABLE `users` (
--   `id` int(11) NOT NULL,
--   `name` varchar(100) NOT NULL,
--   `email` varchar(100) NOT NULL,
--   `password` varchar(255) NOT NULL,
--   `phone` varchar(20) DEFAULT NULL,
--   `address` varchar(255) DEFAULT NULL,
--   `avatar` varchar(255) NOT NULL DEFAULT 'assets/images/avatar.jpg',
--   `role` enum('admin','user') NOT NULL DEFAULT 'user',
--   `status` tinyint(1) NOT NULL DEFAULT 1,
--   `created_at` timestamp NOT NULL DEFAULT current_timestamp()
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --
-- -- Dumping data for table `users`
-- --

-- INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `address`, `avatar`, `role`, `status`, `created_at`) VALUES
-- (1, 'Admin TechMart', 'admin@gmail.com', '$2y$12$M2ZjwFEfhIDR6KvjrFmFOe.dHKspML4Gx4xCkd0R4GSY9/sHZaZ2C', '0909000001', '1 Võ Văn Ngân, TP.HCM', 'assets/images/avatar.jpg', 'admin', 1, '2026-04-23 05:53:31'),
-- (11, 'lê minh anh', 'leminhanh09122005@gmail.com', '$2y$10$VMa1wt7Lv87bzR6DZlWNHOWjzqCYfYwfIR4eoxS4x0XyL/glwV86q', '0794715940', NULL, 'assets/images/avatardefault.jpg', 'user', 1, '2026-04-23 10:23:04'),
-- (13, 'Jang Won Joung', 'nguyenloan781.2023@gmail.com', '$2y$10$17Ce.NuyRUa7hsIRsgI7S.H6Kxsr80MLNLqCkrUXlgYwPWgEKzJ42', '0963452660', '', 'uploads/img_69f0c3baab2278.20038141.jpg', 'user', 1, '2026-04-24 13:39:36'),
-- (14, 'Thie was taken', 'thie11@gmail.con', '$2y$10$C8pvCNDOnvOQ1CLy3O4nWejQSfUgRSfghqj.wKeMpQFqfUB5PXiby', '0845451845', NULL, 'assets/images/avatardefault.jpg', 'user', 1, '2026-04-24 17:46:55');

-- --
-- -- Indexes for dumped tables
-- --

-- --
-- -- Indexes for table `cart`
-- --
-- ALTER TABLE `cart`
--   ADD PRIMARY KEY (`id`),
--   ADD UNIQUE KEY `uniq_cart_user_product_color` (`user_id`,`product_id`,`selected_color`),
--   ADD KEY `fk_cart_product` (`product_id`);

-- --
-- -- Indexes for table `categories`
-- --
-- ALTER TABLE `categories`
--   ADD PRIMARY KEY (`id`),
--   ADD UNIQUE KEY `name` (`name`);

-- --
-- -- Indexes for table `contacts`
-- --
-- ALTER TABLE `contacts`
--   ADD PRIMARY KEY (`id`);

-- --
-- -- Indexes for table `orders`
-- --
-- ALTER TABLE `orders`
--   ADD PRIMARY KEY (`id`),
--   ADD KEY `fk_orders_user` (`user_id`);

-- --
-- -- Indexes for table `order_items`
-- --
-- ALTER TABLE `order_items`
--   ADD PRIMARY KEY (`id`),
--   ADD KEY `fk_order_items_order` (`order_id`),
--   ADD KEY `fk_order_items_product` (`product_id`);

-- --
-- -- Indexes for table `products`
-- --
-- ALTER TABLE `products`
--   ADD PRIMARY KEY (`id`),
--   ADD KEY `fk_products_category` (`category_id`);

-- --
-- -- Indexes for table `product_images`
-- --
-- ALTER TABLE `product_images`
--   ADD PRIMARY KEY (`id`),
--   ADD KEY `fk_product_images_product` (`product_id`);

-- --
-- -- Indexes for table `users`
-- --
-- ALTER TABLE `users`
--   ADD PRIMARY KEY (`id`),
--   ADD UNIQUE KEY `email` (`email`);

-- --
-- -- AUTO_INCREMENT for dumped tables
-- --

-- --
-- -- AUTO_INCREMENT for table `cart`
-- --
-- ALTER TABLE `cart`
--   MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

-- --
-- -- AUTO_INCREMENT for table `categories`
-- --
-- ALTER TABLE `categories`
--   MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

-- --
-- -- AUTO_INCREMENT for table `contacts`
-- --
-- ALTER TABLE `contacts`
--   MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

-- --
-- -- AUTO_INCREMENT for table `orders`
-- --
-- ALTER TABLE `orders`
--   MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

-- --
-- -- AUTO_INCREMENT for table `order_items`
-- --
-- ALTER TABLE `order_items`
--   MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

-- --
-- -- AUTO_INCREMENT for table `products`
-- --
-- ALTER TABLE `products`
--   MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

-- --
-- -- AUTO_INCREMENT for table `product_images`
-- --
-- ALTER TABLE `product_images`
--   MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

-- --
-- -- AUTO_INCREMENT for table `users`
-- --
-- ALTER TABLE `users`
--   MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

-- --
-- -- Constraints for dumped tables
-- --

-- --
-- -- Constraints for table `cart`
-- --
-- ALTER TABLE `cart`
--   ADD CONSTRAINT `fk_cart_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
--   ADD CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- --
-- -- Constraints for table `orders`
-- --
-- ALTER TABLE `orders`
--   ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

-- --
-- -- Constraints for table `order_items`
-- --
-- ALTER TABLE `order_items`
--   ADD CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
--   ADD CONSTRAINT `fk_order_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON UPDATE CASCADE;

-- --
-- -- Constraints for table `products`
-- --
-- ALTER TABLE `products`
--   ADD CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON UPDATE CASCADE;

-- --
-- -- Constraints for table `product_images`
-- --
-- ALTER TABLE `product_images`
--   ADD CONSTRAINT `fk_product_images_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
-- COMMIT;

-- /*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
-- /*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
-- /*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
