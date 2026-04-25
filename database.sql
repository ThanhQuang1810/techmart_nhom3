CREATE DATABASE IF NOT EXISTS webbanhang CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE webbanhang;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS cart;
DROP TABLE IF EXISTS product_images;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS contacts;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  phone VARCHAR(20) DEFAULT NULL,
  address VARCHAR(255) DEFAULT NULL,
  avatar VARCHAR(255) DEFAULT NULL,
  role ENUM('admin','user') NOT NULL DEFAULT 'user',
  status TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE,
  description TEXT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NOT NULL,
  name VARCHAR(150) NOT NULL,
  brand VARCHAR(80) DEFAULT NULL,
  description TEXT DEFAULT NULL,
  price DECIMAL(12,2) NOT NULL DEFAULT 0,
  old_price DECIMAL(12,2) NOT NULL DEFAULT 0,
  stock INT NOT NULL DEFAULT 0,
  image VARCHAR(255) NOT NULL,
  screen VARCHAR(120) DEFAULT NULL,
  chip VARCHAR(120) DEFAULT NULL,
  ram VARCHAR(80) DEFAULT NULL,
  storage VARCHAR(120) DEFAULT NULL,
  camera VARCHAR(120) DEFAULT NULL,
  battery VARCHAR(120) DEFAULT NULL,
  rating DECIMAL(2,1) NOT NULL DEFAULT 5.0,
  sold INT NOT NULL DEFAULT 0,
  featured TINYINT(1) NOT NULL DEFAULT 0,
  status TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE product_images (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  color VARCHAR(80) DEFAULT NULL,
  image_url VARCHAR(255) NOT NULL,
  is_main TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_product_images_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE cart (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  product_id INT NOT NULL,
  quantity INT NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_cart_user_product (user_id, product_id),
  CONSTRAINT fk_cart_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_cart_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  full_name VARCHAR(120) NOT NULL,
  email VARCHAR(100) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  address VARCHAR(255) NOT NULL,
  note TEXT DEFAULT NULL,
  total DECIMAL(12,2) NOT NULL DEFAULT 0,
  status ENUM('Chờ xác nhận','Đang xử lý','Đang giao','Đã giao','Huỷ') NOT NULL DEFAULT 'Chờ xác nhận',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  product_name VARCHAR(150) NOT NULL,
  quantity INT NOT NULL DEFAULT 1,
  price DECIMAL(12,2) NOT NULL DEFAULT 0,
  CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_order_items_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE contacts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  phone VARCHAR(20) DEFAULT NULL,
  subject VARCHAR(150) DEFAULT NULL,
  message TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO users (id, name, email, password, phone, address, avatar, role, status) VALUES
  (1, 'Admin TechMart', 'admin@gmail.com', '$2y$12$M2ZjwFEfhIDR6KvjrFmFOe.dHKspML4Gx4xCkd0R4GSY9/sHZaZ2C', '0909000001', '1 Võ Văn Ngân, TP.HCM', NULL, 'admin', 1),
  (2, 'Nguyễn Văn A', 'user@gmail.com', '$2y$12$M2ZjwFEfhIDR6KvjrFmFOe.dHKspML4Gx4xCkd0R4GSY9/sHZaZ2C', '0909123456', '12 Nguyễn Trãi, Quận 1, TP.HCM', 'assets/images/avatar.jpg', 'user', 1),
  (3, 'Trần Thị B', 'user2@gmail.com', '$2y$12$M2ZjwFEfhIDR6KvjrFmFOe.dHKspML4Gx4xCkd0R4GSY9/sHZaZ2C', '0911222333', '25 Lê Lợi, Quận 3, TP.HCM', NULL, 'user', 1);

INSERT INTO categories (id, name, description) VALUES
  (1, 'Điện thoại', 'Các dòng điện thoại thông minh chính hãng'),
  (2, 'Laptop', 'Laptop học tập, văn phòng và sáng tạo nội dung'),
  (3, 'Tablet', 'Máy tính bảng phục vụ giải trí và học tập'),
  (4, 'Phụ kiện', 'Tai nghe, chuột và phụ kiện công nghệ');

INSERT INTO products (id, category_id, name, brand, description, price, old_price, stock, image, screen, chip, ram, storage, camera, battery, rating, sold, featured, status) VALUES
  (1, 1, 'iPhone 15 128GB', 'Apple', 'Mẫu điện thoại cao cấp với hiệu năng mạnh, camera sắc nét và thiết kế sang trọng.', 21990000, 23990000, 23, 'assets/images/iphone 15.jpg', '6.1 inch Super Retina XDR', 'Apple A16 Bionic', '6GB', '128GB', '48MP + 12MP', '3349mAh', 4.9, 320, 1, 1),
  (2, 1, 'Samsung Galaxy S24 256GB', 'Samsung', 'Flagship Android hiện đại, màn hình đẹp, camera chất lượng và nhiều tính năng AI.', 19990000, 22990000, 26, 'assets/images/galaxy-s24-5g-den.jpg', '6.2 inch Dynamic AMOLED 2X', 'Exynos 2400', '8GB', '256GB', '50MP + 12MP + 10MP', '4000mAh', 4.8, 210, 1, 1),
  (3, 1, 'Xiaomi 14 12GB/256GB', 'Xiaomi', 'Hiệu năng mạnh mẽ, tối ưu cho game và chụp ảnh với hệ camera hợp tác Leica.', 16990000, 18990000, 29, 'assets/images/xiaomi14-xamtitan.webp', '6.36 inch AMOLED 120Hz', 'Snapdragon 8 Gen 3', '12GB', '256GB', '50MP Leica', '4610mAh', 4.7, 145, 1, 1),
  (4, 2, 'MacBook Air M2 13.6 inch', 'Apple', 'Laptop mỏng nhẹ dành cho học tập và công việc văn phòng, pin bền bỉ.', 26990000, 28990000, 32, 'assets/images/macbook_air.webp', '13.6 inch Liquid Retina', 'Apple M2', '8GB', '256GB SSD', '1080p FaceTime HD', 'Lên đến 18 giờ', 4.9, 97, 1, 1),
  (5, 3, 'iPad Air 5 WiFi 64GB', 'Apple', 'Máy tính bảng đa dụng, đáp ứng giải trí, học tập và làm việc linh hoạt.', 14990000, 16990000, 35, 'assets/images/ipad-xam.webp', '10.9 inch Liquid Retina', 'Apple M1', '8GB', '64GB', '12MP', '28.6 Wh', 4.8, 188, 0, 1),
  (6, 4, 'Sony WH-1000XM5', 'Sony', 'Tai nghe chống ồn cao cấp với chất âm chi tiết, phù hợp di chuyển và làm việc.', 7490000, 8490000, 38, 'assets/images/sony-den.webp', 'Không áp dụng', 'QN1', 'Không áp dụng', 'Không áp dụng', 'Không áp dụng', '30 giờ', 4.9, 132, 0, 1),
  (7, 4, 'AirPods Pro 2 USB-C', 'Apple', 'Tai nghe không dây chống ồn chủ động, tương thích hệ sinh thái Apple.', 5990000, 6490000, 41, 'assets/images/airpods-trang.webp', 'Không áp dụng', 'H2', 'Không áp dụng', 'Không áp dụng', 'Không áp dụng', '6 giờ / 30 giờ với hộp sạc', 4.8, 260, 0, 1),
  (8, 4, 'Logitech MX Master 3S', 'Logitech', 'Chuột văn phòng cao cấp, cuộn mượt, hỗ trợ đa thiết bị.', 2290000, 2590000, 44, 'assets/images/chuot-den.jpg', 'Không áp dụng', 'Không áp dụng', 'Không áp dụng', 'Không áp dụng', 'Không áp dụng', 'Sạc USB-C, dùng đến 70 ngày', 4.7, 121, 0, 1);

INSERT INTO product_images (product_id, color, image_url, is_main) VALUES
  (1, 'Đen', 'assets/images/iphone 15.jpg', 1),
  (1, 'Vàng', 'assets/images/iphone-15-mauvang.jpg', 0),
  (1, 'Hồng', 'assets/images/iphone-15-mauhong.jpg', 0),
  (1, 'Xanh Lá', 'assets/images/iphone-15-mauxanhla.jpg', 0),
  (1, 'Xanh Dương', 'assets/images/iphone-15-mauxanhduong.jpg', 0),
  (2, 'Đen', 'assets/images/galaxy-s24-5g-den.jpg', 1),
  (2, 'Tím', 'assets/images/galaxy-s24-5g-tim.jpg', 0),
  (2, 'Xám Marble', 'assets/images/galaxy-s24-5g-xam.jpg', 0),
  (3, 'Xám Titan', 'assets/images/xiaomi14-xamtitan.webp', 1),
  (3, 'Đen', 'assets/images/xiaomi14-den.webp', 0),
  (3, 'Xanh Băng', 'assets/images/xiaomi14-xanhbang.webp', 0),
  (4, 'Vàng', 'assets/images/macbook_air.webp', 1),
  (5, 'Xám', 'assets/images/ipad-xam.webp', 1),
  (5, 'Xanh Dương', 'assets/images/ipad-xanhduong.webp', 0),
  (5, 'Trắng', 'assets/images/ipad-trang.webp', 0),
  (5, 'Tím', 'assets/images/ipad-tim.webp', 0),
  (6, 'Đen', 'assets/images/sony-den.webp', 1),
  (6, 'Hồng Khói', 'assets/images/sony-hongkhoi.webp', 0),
  (7, 'Trắng', 'assets/images/airpods-trang.webp', 1),
  (8, 'Đen', 'assets/images/chuot-den.jpg', 1),
  (8, 'Trắng', 'assets/images/chuot-trang.jpg', 0);

INSERT INTO cart (user_id, product_id, quantity) VALUES
  (2, 1, 1),
  (2, 7, 2);

INSERT INTO orders (id, user_id, full_name, email, phone, address, note, total, status, created_at) VALUES
  (1, 2, 'Nguyễn Văn A', 'user@gmail.com', '0909123456', '12 Nguyễn Trãi, Quận 1, TP.HCM', 'Gọi trước khi giao', 21990000, 'Đã giao', '2026-04-18 10:00:00'),
  (2, 3, 'Trần Thị B', 'user2@gmail.com', '0911222333', '25 Lê Lợi, Quận 3, TP.HCM', 'Giao giờ hành chính', 14990000, 'Đang giao', '2026-04-20 15:30:00');

INSERT INTO order_items (order_id, product_id, product_name, quantity, price) VALUES
  (1, 1, 'iPhone 15 128GB', 1, 21990000),
  (2, 5, 'iPad Air 5 WiFi 64GB', 1, 14990000);

INSERT INTO contacts (name, email, phone, subject, message, created_at) VALUES
  ('Lê Minh', 'leminh@gmail.com', '0988888888', 'Hỏi về bảo hành', 'Sản phẩm này được bảo hành bao lâu?', '2026-04-21 09:15:00'),
  ('Phạm Hoa', 'phamhoa@gmail.com', '0977777777', 'Tư vấn mua laptop', 'Mình cần laptop học tập tầm giá 20 triệu.', '2026-04-21 11:20:00');

ALTER TABLE users AUTO_INCREMENT = 4;
ALTER TABLE categories AUTO_INCREMENT = 5;
ALTER TABLE products AUTO_INCREMENT = 9;
ALTER TABLE product_images AUTO_INCREMENT = 22;
ALTER TABLE cart AUTO_INCREMENT = 3;
ALTER TABLE orders AUTO_INCREMENT = 3;
ALTER TABLE order_items AUTO_INCREMENT = 3;
ALTER TABLE contacts AUTO_INCREMENT = 3;