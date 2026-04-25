<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ====== CONFIG DATABASE (ĐÃ SỬA) ======
define('DB_HOST', 'sql100.infinityfree.com');
define('DB_NAME', 'if0_41729792_techmart');
define('DB_USER', 'if0_41729792');
define('DB_PASS', 'quang18102005'); // password hosting của bạn

function getDB(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
    return $pdo;
}

// ====== URL PROJECT ======
function project_url(): string
{
    static $base = null;
    if ($base !== null) {
        return $base;
    }

    $base = ''; // trên hosting để trống luôn cho sạch URL
    return $base;
}

function url(string $path = ''): string
{
    $path = ltrim($path, '/');
    if ($path === '') {
        return '/';
    }
    return '/' . $path;
}

// ====== HELPER ======
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): never
{
    if (preg_match('#^https?://#', $path)) {
        header('Location: ' . $path);
    } elseif (str_starts_with($path, '/')) {
        header('Location: ' . $path);
    } else {
        header('Location: ' . url($path));
    }
    exit;
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

function current_user(): ?array
{
    static $userLoaded = false;
    static $user = null;

    if ($userLoaded) {
        return $user;
    }

    $userLoaded = true;
    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        return null;
    }

    $stmt = getDB()->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([$userId]);
    $found = $stmt->fetch();

    if (!$found || (int) $found['status'] !== 1) {
        unset($_SESSION['user_id']);
        return null;
    }

    $user = $found;
    return $user;
}

function is_logged_in(): bool
{
    return current_user() !== null;
}

function is_admin(): bool
{
    $user = current_user();
    return $user !== null && $user['role'] === 'admin';
}

function require_login(): void
{
    if (!is_logged_in()) {
        set_flash('error', 'Vui lòng đăng nhập để tiếp tục.');
        $current = $_SERVER['REQUEST_URI'] ?? url('index.php');
        redirect('login.php?redirect=' . urlencode($current));
    }
}

function require_admin(): void
{
    if (!is_admin()) {
        set_flash('error', 'Bạn không có quyền truy cập trang quản trị.');
        redirect('login.php?redirect=' . urlencode(url('admin/index.php')));
    }
}

function format_price(float|int|string $price): string
{
    return number_format((float) $price, 0, ',', '.') . 'đ';
}

function cart_count(): int
{
    $user = current_user();
    if (!$user) {
        return 0;
    }

    $stmt = getDB()->prepare('SELECT COALESCE(SUM(quantity), 0) FROM cart WHERE user_id = ?');
    $stmt->execute([$user['id']]);
    return (int) $stmt->fetchColumn();
}

function fetch_categories(): array
{
    return getDB()->query('SELECT * FROM categories ORDER BY name')->fetchAll();
}

function fetch_order_statuses(): array
{
    return ['Chờ xác nhận', 'Đang xử lý', 'Đang giao', 'Đã giao', 'Huỷ'];
}

function handle_upload(string $fieldName, string $existing = ''): string
{
    if (!isset($_FILES[$fieldName]) || !is_array($_FILES[$fieldName])) {
        return $existing;
    }

    $file = $_FILES[$fieldName];
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return $existing;
    }

    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Tải ảnh lên không thành công.');
    }

    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'avif'];
    $ext = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed, true)) {
        throw new RuntimeException('Chỉ chấp nhận ảnh JPG, PNG, WEBP hoặc AVIF.');
    }

    $uploadDir = __DIR__ . '/uploads';
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true) && !is_dir($uploadDir)) {
        throw new RuntimeException('Không tạo được thư mục uploads.');
    }

    $filename = uniqid('img_', true) . '.' . $ext;
    $destination = $uploadDir . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new RuntimeException('Không lưu được ảnh tải lên.');
    }

    return 'uploads/' . $filename;
}
function productImage(string $image = ''): string
{
    if ($image === '') {
        return '/assets/images/no-image.png';
    }
    if (str_starts_with($image, 'http')) {
        return $image;
    }
    return '/' . ltrim($image, '/');
}

function render_product_card(array $product): string
{
    ob_start();
    $url     = url('product-detail.php?id=' . (int)$product['id']);
    $cartUrl = url('add_to_cart.php');
    $img     = productImage($product['image'] ?? '');
    $name    = e($product['name'] ?? '');
    $brand   = e($product['brand'] ?? '');
    $price   = format_price($product['price'] ?? 0);
    $oldPrice = (float)($product['old_price'] ?? 0);
    $curPrice = (float)($product['price'] ?? 0);
    $rating  = e($product['rating'] ?? '5.0');
    $sold    = e($product['sold'] ?? '0');
    $id      = (int)$product['id'];
    ?>
    <article class="product-card" data-id="<?= $id ?>">
      <div class="product-thumb-wrap">
        <a href="<?= $url ?>">
          <img src="<?= $img ?>" alt="<?= $name ?>" class="product-thumb" />
        </a>
      </div>
      <div class="product-info">
        <h3 class="product-name">
          <a href="<?= $url ?>"><?= $name ?></a>
        </h3>
        <p class="product-brand"><?= $brand ?: 'TechMart' ?></p>
        <div class="price-row">
          <strong class="price-current"><?= $price ?></strong>
          <?php if ($oldPrice > $curPrice): ?>
            <span class="price-old"><?= format_price($oldPrice) ?></span>
          <?php endif; ?>
        </div>
        <div class="meta-row">
          <span><i class="fa-solid fa-star"></i> <?= $rating ?></span>
          <span>Đã bán <?= $sold ?></span>
        </div>
        <div class="card-actions">
          <form method="post" action="<?= url('add_to_cart.php') ?>" class="card-action-form">
            <input type="hidden" name="product_id" value="<?= $id ?>">
            <input type="hidden" name="redirect" value="<?= e($_SERVER['REQUEST_URI'] ?? '/') ?>">
            <button class="btn btn-primary btn-add-cart" type="submit" style="flex:1;">
              Thêm vào giỏ
            </button>
          </form>
          <a class="btn btn-outline btn-view-detail" href="<?= $url ?>" style="flex:1;text-align:center;">
            Mua ngay
          </a>
        </div>
      </div>
    </article>
    <?php
    return (string)ob_get_clean();
}
function safe_redirect_target(string $target): string
{
    if ($target === '') return '';
    // Chỉ cho redirect về cùng domain
    if (str_starts_with($target, '/') && !str_starts_with($target, '//')) {
        return $target;
    }
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $parsed = parse_url($target);
    if (isset($parsed['host']) && $parsed['host'] === $host) {
        return $target;
    }
    return '';
}