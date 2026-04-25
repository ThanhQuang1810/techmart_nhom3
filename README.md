# TechMart - PHP + MySQL

Đây là bản source đã được ghép từ:
- **demo-01.zip**: giao diện HTML/CSS/JS của bạn
- **config.zip**: khung PHP kết nối database

Mình đã nối lại thành một website PHP + MySQL chạy được, gồm:
- Trang chủ
- Danh mục / tìm kiếm / lọc sản phẩm
- Chi tiết sản phẩm + gallery ảnh
- Đăng ký / đăng nhập / đăng xuất
- Giỏ hàng dùng database
- Thanh toán / tạo đơn hàng
- Lịch sử đơn hàng / hủy đơn chờ xác nhận
- Hồ sơ người dùng
- Trang giới thiệu + form liên hệ
- Khu vực **Admin** quản lý danh mục, sản phẩm, đơn hàng, người dùng

## 1) Cách chạy trên XAMPP

### Bước 1: Chép source vào `htdocs`
Ví dụ:

```text
C:\xampp\htdocs\techmart-linked
```

### Bước 2: Bật Apache + MySQL
Mở **XAMPP Control Panel** và bật:
- Apache
- MySQL

### Bước 3: Import database
1. Mở `http://localhost/phpmyadmin`
2. Chọn tab **Import**
3. Import file `database.sql`

Database mặc định là:

```sql
webbanhang
```

### Bước 4: Kiểm tra file `config.php`
Thông số mặc định:

```php
DB_HOST = 'localhost'
DB_NAME = 'webbanhang'
DB_USER = 'root'
DB_PASS = ''
```

Nếu máy bạn dùng mật khẩu MySQL khác thì chỉnh lại `DB_PASS`.

### Bước 5: Mở website
User site:

```text
http://localhost/techmart-linked/
```

Admin site:

```text
http://localhost/techmart-linked/admin/index.php
```

## 2) Tài khoản demo

### Admin
- Email: `admin@gmail.com`
- Password: `123456`

### User
- Email: `user@gmail.com`
- Password: `123456`

## 3) Các file chính

- `config.php`: kết nối PDO + session + helper functions
- `database.sql`: cấu trúc CSDL + dữ liệu mẫu
- `index.php`: trang chủ
- `category.php`: danh sách sản phẩm + bộ lọc
- `product-detail.php`: chi tiết sản phẩm
- `cart.php`, `checkout.php`, `orders.php`
- `login.php`, `register.php`, `profile.php`
- `about.php`
- `admin/`: dashboard + CRUD cơ bản

## 4) Bảng dữ liệu

- `users`
- `categories`
- `products`
- `product_images`
- `cart`
- `orders`
- `order_items`
- `contacts`

## 5) Lưu ý

- Source này đã **ghép giao diện HTML sang PHP** và dùng dữ liệu thật từ MySQL.
- `config.zip` bạn gửi không có file SQL hoàn chỉnh, nên mình đã tạo **`database.sql`** tương ứng với giao diện và chức năng web để bạn import chạy luôn.
- Nếu bạn muốn giữ đúng tên database khác, chỉ cần sửa `DB_NAME` trong `config.php`.

## 6) Gợi ý nộp bài

Bạn có thể demo theo luồng sau:
1. Vào trang chủ
2. Chọn sản phẩm → xem chi tiết
3. Đăng nhập user
4. Thêm vào giỏ → thanh toán → xem lịch sử đơn hàng
5. Đăng nhập admin → quản lý danh mục / sản phẩm / đơn hàng / người dùng
